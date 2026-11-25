import 'package:eshopplus_deliveryboy/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_deliveryboy/core/constants/appAssets.dart';
import 'package:eshopplus_deliveryboy/utils/designConfig.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';
import 'package:eshopplus_deliveryboy/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class ErrorScreen extends StatelessWidget {
  final VoidCallback onPressed;
  final Widget? child;
  final String? text;
  final String? image;
  final buttonText;
  const ErrorScreen(
      {Key? key,
      required this.onPressed,
      this.child,
      this.text,
      this.buttonText,
      this.image})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsetsDirectional.all(35),
      alignment: Alignment.center,
      color: Theme.of(context).colorScheme.primaryContainer,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          if (image != null)
            Flexible(
              child: SizedBox(
                height: 400,
                width: MediaQuery.of(context).size.width,
                child: FittedBox(
                  child: Utils.setSvgImage(image!, height: 400),
                ),
              ),
            )
          else ...[
            if (text == noInternetKey)
              Flexible(
                child: SizedBox(
                    height: 400,
                    width: MediaQuery.of(context).size.width,
                    child: FittedBox(
                        child: Utils.setSvgImage(AppAssets.noInternet,
                            height: 400))),
              ),
            if (text == dataNotAvailableKey ||
                text == "data_not_found" ||
                text ==
                    context
                        .read<SettingsAndLanguagesCubit>()
                        .getTranslatedValue(labelKey: "no_data_found") ||
                text == "Data Not Found")
              Flexible(
                child: SizedBox(
                    height: 400,
                    width: MediaQuery.of(context).size.width,
                    child: FittedBox(
                        child: Utils.setSvgImage(AppAssets.noDataFound,
                            height: 400,
                            width: MediaQuery.of(context).size.width))),
              ),
          ],
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: CustomTextContainer(
              textKey: text ?? defaultErrorMessageKey,
              textAlign: TextAlign.center,
              style: Theme.of(context)
                  .textTheme
                  .bodyMedium!
                  .copyWith(color: Theme.of(context).colorScheme.secondary),
            ),
          ),
          DesignConfig.defaultHeightSizedBox,
          child ??
              CustomRoundedButton(
                widthPercentage: 0.35,
                buttonTitle: buttonText ?? retryKey,
                showBorder: false,
                onTap: onPressed,
              )
        ],
      ),
    );
  }
}
