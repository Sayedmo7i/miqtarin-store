import 'package:eshopplus_deliveryboy/commons/models/language.dart';
import 'package:eshopplus_deliveryboy/commons/models/settings.dart';
import 'package:eshopplus_deliveryboy/commons/repositories/settingsRepository.dart';
import 'package:eshopplus_deliveryboy/core/configs/appConfig.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';
import 'package:eshopplus_deliveryboy/main.dart';
import 'package:eshopplus_deliveryboy/utils/utils.dart';

import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class SettingsAndLanguagesState {}

class SettingsAndLanguagesInitial extends SettingsAndLanguagesState {}

class SettingsAndLanguagesFetchInProgress extends SettingsAndLanguagesState {
  final Map<String, String> currentLanguageTranslatedValues;

  SettingsAndLanguagesFetchInProgress({
    this.currentLanguageTranslatedValues = const {},
  });
}

class SettingsAndLanguagesFetchSuccess extends SettingsAndLanguagesState {
  final Language currentAppLanguage;
  final List<Language> languages;
  final Settings settings;
  final Map<String, String> currentLanguageTranslatedValues;

  SettingsAndLanguagesFetchSuccess({
    required this.currentAppLanguage,
    required this.languages,
    required this.settings,
    required this.currentLanguageTranslatedValues,
  });

  SettingsAndLanguagesFetchSuccess copyWith({
    Language? currentAppLanguage,
    bool? showOnBoardingScreen,
    List<Language>? languages,
    Settings? settings,
    Map<String, String>? currentLanguageTranslatedValues,
  }) {
    return SettingsAndLanguagesFetchSuccess(
      currentLanguageTranslatedValues: currentLanguageTranslatedValues ??
          this.currentLanguageTranslatedValues,
      currentAppLanguage: currentAppLanguage ?? this.currentAppLanguage,
      languages: languages ?? this.languages,
      settings: settings ?? this.settings,
    );
  }
}

class SettingsAndLanguagesFetchFailure extends SettingsAndLanguagesState {
  final String errorMessage;
  final Map<String, String> currentLanguageTranslatedValues;

  SettingsAndLanguagesFetchFailure(
    this.errorMessage, {
    this.currentLanguageTranslatedValues = const {},
  });
}

class SettingsAndLanguagesCubit extends Cubit<SettingsAndLanguagesState> {
  final SettingsRepository _settingsRepository;

  SettingsAndLanguagesCubit(this._settingsRepository)
      : super(SettingsAndLanguagesInitial());

  Future<void> fetchSettingsAndLanguages() async {
    Map<String, String> translatedValues = {}; // Move outside try block
    try {
      // Load local translations BEFORE emitting InProgress state
      // This ensures translations are available immediately on fresh install
      final currentLang = _settingsRepository.getCurrentAppLanguage();
      final currentLangCode = currentLang.code ?? defaultLanguageCode;

      // Try to load from cache first, if not available load from local JSON
      translatedValues =
          _settingsRepository.getStoredLanguageLabels(currentLangCode) ?? {};
      if (translatedValues.isEmpty) {
        translatedValues = await _settingsRepository.getLocalLanguageLabels();
        debugPrint(
          '📁 Loaded initial translations from local deliveryboy.json file',
        );
      }

      emit(
        SettingsAndLanguagesFetchInProgress(
          currentLanguageTranslatedValues: translatedValues,
        ),
      );

      // Fetch settings which now includes languages
      final settings = await _settingsRepository.getSettings();
      final languages = settings.languages ?? [];

      // Find the language object from settings to get updated_at
      final languageFromSettings = languages.firstWhere(
        (lang) => lang.code == currentLangCode,
        orElse: () => Language(code: currentLangCode),
      );

      final latestUpdatedAt = languageFromSettings.updatedAt ?? '';

      // Check if we need to refresh labels
      if (_settingsRepository.shouldRefreshLanguageLabels(
        languageCode: currentLangCode,
        latestUpdatedAt: latestUpdatedAt,
      )) {
        // Fetch from API
        try {
          final result = await _settingsRepository.getLanguageLables(
            currentLangCode,
          );
          translatedValues = result['labels'] as Map<String, String>;
          final updatedAt = result['updated_at'] as String;

          // Store in Hive
          await _settingsRepository.storeLanguageLabels(
            languageCode: currentLangCode,
            labels: translatedValues,
            updatedAt: updatedAt,
          );
        } catch (e) {
          debugPrint('⚠️ Language labels API failed: $e');
          debugPrint(
            '📁 Attempting to load labels from local deliveryboy.json file',
          );

          // If API fails, try to use cached values first
          translatedValues =
              _settingsRepository.getStoredLanguageLabels(currentLangCode) ??
                  {};

          // If no cache, load from local JSON file
          if (translatedValues.isEmpty) {
            translatedValues =
                await _settingsRepository.getLocalLanguageLabels();
            debugPrint('✅ Using local deliveryboy.json file as fallback');
          } else {
            debugPrint('✅ Using cached labels as fallback');
          }
        }
      } else {
        // Use cached labels
        translatedValues =
            _settingsRepository.getStoredLanguageLabels(currentLangCode) ?? {};
      }

      emit(
        SettingsAndLanguagesFetchSuccess(
          currentAppLanguage: currentLang,
          languages: languages,
          settings: settings,
          currentLanguageTranslatedValues: translatedValues,
        ),
      );

      if (currentLang.code == null) {
        if (languages.isNotEmpty) {
          changeLanguage(
            languages.firstWhere(
              (element) => element.code == defaultLanguageCode,
              orElse: () => languages.first,
            ),
          );
        }
      }
    } catch (e) {
      debugPrint('⚠️ Settings API failed completely: $e');
      debugPrint('📁 Loading labels from local deliveryboy.json file');

      // Load from local JSON when settings API fails completely
      translatedValues = await _settingsRepository.getLocalLanguageLabels();

      emit(
        SettingsAndLanguagesFetchFailure(
          e.toString(),
          currentLanguageTranslatedValues: translatedValues,
        ),
      );
    }
  }

  bool appUnderMaintenance() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings!
              .deliveryBoyAppMaintenanceStatus ==
          1;
    }
    return false;
  }

  Future<bool> changeLanguage(Language currentAppLanguage) async {
    try {
      Map<String, String> translatedValues;

      if (currentAppLanguage.code != null) {
        final languageCode = currentAppLanguage.code!;
        final latestUpdatedAt = currentAppLanguage.updatedAt ?? '';

        // Check if we need to refresh labels
        if (_settingsRepository.shouldRefreshLanguageLabels(
          languageCode: languageCode,
          latestUpdatedAt: latestUpdatedAt,
        )) {
          // Fetch from API
          try {
            final result = await _settingsRepository.getLanguageLables(
              languageCode,
            );
            translatedValues = result['labels'] as Map<String, String>;
            final updatedAt = result['updated_at'] as String;

            // Store in Hive
            await _settingsRepository.storeLanguageLabels(
              languageCode: languageCode,
              labels: translatedValues,
              updatedAt: updatedAt,
            );
          } catch (e) {
            debugPrint('⚠️ API call failed during language change: $e');

            // If API fails, try to use cached values
            final cachedLabels = _settingsRepository.getStoredLanguageLabels(
              languageCode,
            );
            if (cachedLabels != null && cachedLabels.isNotEmpty) {
              translatedValues = cachedLabels;
              debugPrint('✅ Using cached labels as fallback');
            } else {
              // If no cache, load from local JSON file
              debugPrint('📁 Loading labels from local deliveryboy.json file');
              translatedValues =
                  await _settingsRepository.getLocalLanguageLabels();

              if (translatedValues.isNotEmpty) {
                debugPrint('✅ Using local deliveryboy.json file as fallback');
              } else {
                debugPrint('❌ Failed to load from all sources');
                Utils.showSnackBar(message: languageLoadFailureKey);
                return false;
              }
            }
          }
        } else {
          // Use cached labels
          final cachedLabels = _settingsRepository.getStoredLanguageLabels(
            languageCode,
          );
          if (cachedLabels != null) {
            translatedValues = cachedLabels;
          } else {
            // No cache available, fetch from API
            try {
              final result = await _settingsRepository.getLanguageLables(
                languageCode,
              );
              translatedValues = result['labels'] as Map<String, String>;
              final updatedAt = result['updated_at'] as String;

              // Store in Hive
              await _settingsRepository.storeLanguageLabels(
                languageCode: languageCode,
                labels: translatedValues,
                updatedAt: updatedAt,
              );
            } catch (e) {
              debugPrint('⚠️ API call failed: $e');
              debugPrint('📁 Loading labels from local deliveryboy.json file');

              // If API fails, load from local JSON file
              translatedValues =
                  await _settingsRepository.getLocalLanguageLabels();

              if (translatedValues.isEmpty) {
                Utils.showSnackBar(message: languageLoadFailureKey);
                return false;
              } else {
                debugPrint('✅ Using local deliveryboy.json file as fallback');
              }
            }
          }
        }
      } else {
        translatedValues = {};
      }

      _settingsRepository.setCurrentAppLanguage(currentAppLanguage);
      emit(
        (state as SettingsAndLanguagesFetchSuccess).copyWith(
          currentAppLanguage: currentAppLanguage,
          currentLanguageTranslatedValues: translatedValues,
        ),
      );
      return true;
    } catch (e) {
      debugPrint('⚠️ Error changing language: $e');
      return false;
    }
  }

  String getTranslatedValue({required String labelKey}) {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return ((state as SettingsAndLanguagesFetchSuccess)
              .currentLanguageTranslatedValues[labelKey]) ??
          labelKey;
    }

    // Return translations even when in failure state
    if (state is SettingsAndLanguagesFetchFailure) {
      return ((state as SettingsAndLanguagesFetchFailure)
              .currentLanguageTranslatedValues[labelKey]) ??
          labelKey;
    }

    // Return translations even when in progress state (for fresh install)
    if (state is SettingsAndLanguagesFetchInProgress) {
      return ((state as SettingsAndLanguagesFetchInProgress)
              .currentLanguageTranslatedValues[labelKey]) ??
          labelKey;
    }

    return labelKey;
  }

  Language getCurrentAppLanguage() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).currentAppLanguage;
    }
    return Language.fromJson({});
  }

  Settings getSettings() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).settings;
    }
    return Settings.fromJson({});
  }

  bool isUpdateRequired() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      if ((state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings!
              .versionSystemStatus ==
          1) {
        if (defaultTargetPlatform == TargetPlatform.android &&
                needsUpdate(
                  (state as SettingsAndLanguagesFetchSuccess)
                      .settings
                      .systemSettings!
                      .currentVersionOfAndroidAppForDeliveryBoy!,
                ) ||
            defaultTargetPlatform == TargetPlatform.iOS &&
                needsUpdate(
                  (state as SettingsAndLanguagesFetchSuccess)
                      .settings
                      .systemSettings!
                      .currentVersionOfIosAppForDeliveryBoy!,
                )) {
          return true;
        }
      } else {
        return false;
      }
    }
    return false;
  }

  bool needsUpdate(String enforceVersion) {
    final List<int> currentVersion = packageInfo.version
        .split('.')
        .map((String number) => int.parse(number))
        .toList();
    final List<int> enforcedVersion = enforceVersion
        .split('.')
        .map((String number) => int.parse(number))
        .toList();

    for (int i = 0; i < 3; i++) {
      if (enforcedVersion[i] > currentVersion[i]) {
        return true;
      } else if (currentVersion[i] > enforcedVersion[i]) {
        return false;
      }
    }
    return false;
  }

  List<Language> getLanguages() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).languages;
    }
    return [Language(id: 1, language: 'English', code: 'en')];
  }

  fetchSettings() async {
    final settings = await _settingsRepository.getSettings();
    emit(
      (state as SettingsAndLanguagesFetchSuccess).copyWith(
        settings: settings,
        languages: settings.languages ?? [],
      ),
    );
  }

  String getCountryCode() {
    String countryCode = defaultCountryCode;
    if (state is SettingsAndLanguagesFetchSuccess) {
      countryCode = (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings
              ?.countryCode ??
          defaultCountryCode;
    }
    return countryCode.startsWith('+') ? countryCode : '+$countryCode';
  }
}
