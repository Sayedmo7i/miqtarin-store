import 'package:dio/dio.dart';
import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/models/systemSettings.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customCuperinoSwitch.dart';
import 'package:eshopplus_seller/commons/widgets/customLabelContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customTextFieldContainer.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class AiPromptField extends StatefulWidget {
  final TextEditingController descriptionController;
  final TextEditingController productNameController;
  final String selectedLanguage;
  final Function callback;
  final bool isShortDescription;
  final String title;
  final bool isFieldValueMandatory;
  const AiPromptField({
    super.key,
    required this.descriptionController,
    required this.productNameController,
    required this.selectedLanguage,
    required this.callback,
    required this.isShortDescription,
    required this.title,
    required this.isFieldValueMandatory,
  });

  @override
  State<AiPromptField> createState() => _AiPromptFieldState();
}

class _AiPromptFieldState extends State<AiPromptField> {
  final TextEditingController promptController = TextEditingController();
  bool isLoading = false;
  late SystemSettings settings;

  String _cleanAIResponse(String response) {
    String cleaned = response.trim();

    // Remove common AI tokens and formatting
    cleaned = cleaned.replaceAll(RegExp(r'<s>.*?</s>', dotAll: true), '');
    cleaned = cleaned.replaceAll(
      RegExp(r'<s>\s*\[?OUT\]?\s*', caseSensitive: false),
      '',
    );
    cleaned = cleaned.replaceAll(RegExp(r'</s>', caseSensitive: false), '');
    cleaned = cleaned.replaceAll(
      RegExp(r'^\"|\"$'),
      '',
    ); // Remove quotes at start/end
    cleaned = cleaned.replaceAll(
      RegExp(r'^\s*-\s*'),
      '',
    ); // Remove leading dash
    cleaned = cleaned.replaceAll(
      RegExp(r'\[OUT\]', caseSensitive: false),
      '',
    ); // Remove [OUT] tokens

    // For short descriptions, ensure no HTML tags
    if (widget.isShortDescription) {
      cleaned = cleaned.replaceAll(RegExp(r'<[^>]*>'), '');
    }

    return cleaned.trim();
  }

  Future<void> generateDescription() async {
    FocusScope.of(context).unfocus();
    if (widget.productNameController.text.isEmpty) {
      Utils.showSnackBar(message: pleaseEnterProductNameFirstKey);
      return;
    }
    final prompt = promptController.text.trim();
    if (_showPrompt && prompt.isEmpty) {
      Utils.showSnackBar(message: pleaseEnterPromptKey);
      return;
    }

    String input = prompt.isNotEmpty
        ? prompt
        : widget.productNameController.text;
    String content = widget.isShortDescription
        ? '''
Generate a short product description.

Product name: "$input"
Language: ${widget.selectedLanguage}

Requirements:
- Plain text only
- No HTML
- No markdown
- No emojis
- No special formatting
- Around 90–110 characters
- One single sentence

Return only the description text.
'''
        : '''
Generate a detailed product description in HTML format.

Product name: "$input"
Language: ${widget.selectedLanguage}

Requirements:
- Valid clean HTML only
- Use <p>, <ul>, <li>, <strong> where appropriate
- No inline CSS
- No <html>, <head>, or <body> tags
- No markdown
- Professional tone
- 200–400 words

Return only the HTML content.
''';
    setState(() => isLoading = true);
    try {
      final dio = Dio();

      Response response;

      if (settings.AISetting!['ai_method'] == 'openrouter_api') {
        response = await dio.post(
          'https://openrouter.ai/api/v1/chat/completions',
          options: Options(
            headers: {
              'Authorization':
                  'Bearer ${context.read<SettingsAndLanguagesCubit>().getSettings().systemSettings!.AISetting!['openrouter_api_key']}',
              'Content-Type': 'application/json',
              'HTTP-Referer': baseUrl, // update with your app URL
            },
          ),
          data: {
            'model': 'openai/gpt-3.5-turbo',
            'messages': [
              {'role': 'user', 'content': content},
            ],
            'max_tokens': 300,
          },
        );
        final aiText = response.data['choices'][0]['message']['content'];
        final cleanedText = _cleanAIResponse(aiText);
        widget.descriptionController.text = cleanedText;
        widget.callback(cleanedText);
      } else {
        response = await dio.post(
          'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${context.read<SettingsAndLanguagesCubit>().getSettings().systemSettings!.AISetting!['gemini_api_key']}',
          options: Options(headers: {'Content-Type': 'application/json'}),
          data: {
            'contents': [
              {
                'parts': [
                  {'text': content},
                ],
              },
            ],
          },
        );

        final aiText =
            response.data['candidates'][0]['content']['parts'][0]['text'];
        final cleanedText = _cleanAIResponse(aiText);
        widget.descriptionController.text = cleanedText;
        widget.callback(cleanedText);
      }
    } catch (e) {
      Utils.showSnackBar(message: defaultErrorMessageKey);
    } finally {
      setState(() => isLoading = false);
    }
  }

  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    Future.delayed(Duration.zero, () {
      settings = context
          .read<SettingsAndLanguagesCubit>()
          .getSettings()
          .systemSettings!;
    });
  }

  bool _showPrompt = false;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: .end,
      children: [
        DesignConfig.smallHeightSizedBox,
        Row(
          children: <Widget>[
            Flexible(
              child: CustomLabelContainer(
                textKey: widget.title,
                isFieldValueMandatory: widget.isFieldValueMandatory,
              ),
            ),
            DesignConfig.smallWidthSizedBox,
            isLoading
                ? CustomCircularProgressIndicator(
                    indicatorColor: Theme.of(context).colorScheme.primary,
                  )
                : GestureDetector(
                    onTap: generateDescription,
                    child: Container(
                      padding: .all(4),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(4),
                        color: Theme.of(
                          context,
                        ).colorScheme.primary.withValues(alpha: 0.2),
                      ),
                      child: CustomTextContainer(
                        textKey: generateDescriptionWithAIKey,
                        style: Theme.of(context).textTheme.bodySmall!.copyWith(
                          color: Theme.of(context).colorScheme.primary,
                        ),
                      ),
                    ),
                  ),
          ],
        ),
        Row(
          mainAxisAlignment: .spaceBetween,
          children: <Widget>[
            CustomTextContainer(textKey: customPromptKey),
            CustomCuperinoSwitch(
              value: _showPrompt,
              onChanged: (value) {
                setState(() {
                  _showPrompt = value;
                });
              },
            ),
          ],
        ),
        if (_showPrompt) ...[
          CustomTextFieldContainer(
            textEditingController: promptController,
            hintTextKey: enterYourOwnAIPromptKey,
            labelKey: '',
          ),
        ],
      ],
    );
  }
}
