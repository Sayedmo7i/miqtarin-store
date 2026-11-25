
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class AppLocalizations {
  static String get(BuildContext context, String key,
      {Map<String, String>? placeholders}) {
    // Get the translations for the current language.
    var translatedString = context
        .read<SettingsAndLanguagesCubit>()
        .getTranslatedValue(labelKey: key);

    // If there are placeholders, replace them.
    if (placeholders != null) {
      placeholders.forEach((placeholder, value) {
        translatedString = translatedString.replaceAll('{$placeholder}', value);
      });
    }

    return translatedString;
  }
}
  