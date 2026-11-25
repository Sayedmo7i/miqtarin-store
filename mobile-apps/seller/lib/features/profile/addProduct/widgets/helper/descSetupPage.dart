import 'package:eshopplus_seller/commons/widgets/customBottomButtonContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:html_editor_enhanced/html_editor.dart';

class DescSetupPage extends StatefulWidget {
  final String currText;
  final String title;
  final Function(String)? callback;

  const DescSetupPage({
    super.key,
    required this.currText,
    required this.title,
    this.callback,
  });
  static Widget getRouteInstance() => DescSetupPage(
        callback: Get.arguments['callback'],
        currText: Get.arguments['currText'] ?? '',
        title: Get.arguments['title'] ?? '',
      );
  @override
  State<DescSetupPage> createState() => _DescSetupPageState();
}

class _DescSetupPageState extends State<DescSetupPage> {
  final HtmlEditorController controller = HtmlEditorController();

  @override
  void initState() {
    super.initState();
    Api.printLongString('==value====${widget.currText}');
  }

  @override
  void dispose() {
    super.dispose();
  }

  void getHtmlText() async {
    String html = await controller.getText();
    if (widget.callback != null) {
      widget.callback!(html);
    }
    Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: CustomTextContainer(textKey: widget.title),
        actions: [
          IconButton(
            icon: Icon(Icons.check),
            onPressed: getHtmlText,
          )
        ],
      ),
      bottomNavigationBar: appbarBtns(),
      body: Column(
        children: [
          Expanded(
            child: HtmlEditor(
              controller: controller,
              htmlEditorOptions: HtmlEditorOptions(
                hint: widget.title,
                initialText: widget.currText,
                shouldEnsureVisible: true,
                adjustHeightForKeyboard: true,
                spellCheck: true,
              ),
              htmlToolbarOptions: HtmlToolbarOptions(
                toolbarPosition: ToolbarPosition.aboveEditor,
                toolbarType: ToolbarType.nativeGrid,
            
                defaultToolbarButtons: [
                  StyleButtons(),
                  FontSettingButtons(fontSizeUnit: false),
                  FontButtons(clearAll: false),
                  ColorButtons(),
                  ListButtons(listStyles: false),
                  ParagraphButtons(textDirection: false, lineHeight: false, caseConverter: false),
                  InsertButtons(video: false, audio: false, table: false, hr: false, otherFile: false),
                  OtherButtons(copy: false, paste: false),
                ],
              ),
              otherOptions: OtherOptions(
                height: 1000,//MediaQuery.of(context).size.height,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.2),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  appbarBtns() {
    return CustomBottomButtonContainer(
        child: Row(
      children: <Widget>[
        Expanded(
          child: CustomRoundedButton(
            widthPercentage: 1,
            buttonTitle: 'Clear',
            showBorder: true,
            backgroundColor: Theme.of(context).colorScheme.onPrimary,
            borderColor: Theme.of(context).inputDecorationTheme.iconColor,
            style: Theme.of(context)
                .textTheme
                .titleMedium!
                .copyWith(color: Theme.of(context).colorScheme.secondary),
            onTap: () async {
              controller.clear();
            },
          ),
        ),
        const SizedBox(
          width: 16,
        ),
        Expanded(
          child: CustomRoundedButton(
            widthPercentage: 1,
            buttonTitle: 'Done',
            showBorder: false,
            onTap: getHtmlText,
          ),
        ),
      ],
    ));
  }
}
