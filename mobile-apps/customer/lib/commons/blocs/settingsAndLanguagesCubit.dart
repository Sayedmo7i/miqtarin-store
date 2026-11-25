import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/core/localization/appLocalizations.dart';
import 'package:eshop_plus/main.dart';
import 'package:eshop_plus/commons/models/language.dart';
import 'package:eshop_plus/commons/models/settings.dart';
import 'package:eshop_plus/ui/auth/repositories/authRepository.dart';
import 'package:eshop_plus/commons/repositories/settingsRepository.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class SettingsAndLanguagesState {}

class SettingsAndLanguagesInitial extends SettingsAndLanguagesState {}

class SettingsAndLanguagesFetchInProgress extends SettingsAndLanguagesState {}

class SettingsAndLanguagesFetchSuccess extends SettingsAndLanguagesState {
  final Language currentAppLanguage;
  final bool showOnBoardingScreen;

  final List<Language> languages;
  final Settings settings;
  final Map<String, String> currentLanguageTranslatedValues;

  SettingsAndLanguagesFetchSuccess({
    required this.currentAppLanguage,
    required this.showOnBoardingScreen,
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
      currentLanguageTranslatedValues:
          currentLanguageTranslatedValues ??
          this.currentLanguageTranslatedValues,
      currentAppLanguage: currentAppLanguage ?? this.currentAppLanguage,
      languages: languages ?? this.languages,
      settings: settings ?? this.settings,
      showOnBoardingScreen: showOnBoardingScreen ?? this.showOnBoardingScreen,
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
  final SettingsRepository _settingsRepository = SettingsRepository();

  SettingsAndLanguagesCubit() : super(SettingsAndLanguagesInitial());

  Future<void> fetchSettingsAndLanguages() async {
    emit(SettingsAndLanguagesFetchInProgress());

    Map<String, String> translatedValues = {};
    Settings settings;
    List<Language> languages = [];

    try {
      settings = await _settingsRepository.getSettings();
      languages = settings.languages ?? [];
    } catch (_) {
      settings = Settings.fromJson({});
    }

    final storedLanguage = _settingsRepository.getCurrentAppLanguage();

    Language languageToUse = storedLanguage;

    try {
      // ✅ Decide language BEFORE emitting state
      if (storedLanguage.code == null && languages.isNotEmpty) {
        languageToUse = languages.firstWhere(
          (l) => l.code == defaultLanguageCode,
          orElse: () => languages.first,
        );

        translatedValues = await _getLanguageLabelsWithCache(
          languageToUse.code!,
          languages,
        );

        _settingsRepository.setCurrentAppLanguage(languageToUse);
      } else if (storedLanguage.code != null) {
        final langFromList = languages.firstWhere(
          (l) => l.code == storedLanguage.code,
          orElse: () => storedLanguage,
        );

        languageToUse = storedLanguage.copyWith(
          updatedAt: langFromList.updatedAt,
        );

        translatedValues = await _getLanguageLabelsWithCache(
          languageToUse.code!,
          languages,
        );

        _settingsRepository.setCurrentAppLanguage(languageToUse);
      } else {
        translatedValues = await _settingsRepository.getLocalLanguageLabels();
      }

      emit(
        SettingsAndLanguagesFetchSuccess(
          currentAppLanguage: languageToUse,
          showOnBoardingScreen: _settingsRepository.getOnBoardingScreen(),
          languages: languages,
          settings: settings,
          currentLanguageTranslatedValues: translatedValues,
        ),
      );
    } catch (e) {
      translatedValues = await _settingsRepository.getLocalLanguageLabels();

      emit(
        SettingsAndLanguagesFetchFailure(
          e.toString(),
          currentLanguageTranslatedValues: translatedValues,
        ),
      );
    }
  }

  // Helper method to get language labels with caching logic
  Future<Map<String, String>> _getLanguageLabelsWithCache(
    String languageCode,
    List<Language> languages,
  ) async {
    debugPrint('🔍 Fetching language labels for: $languageCode');

    // 1. Get language from settings list (has updated_at from API)
    final language = languages.firstWhere(
      (lang) => lang.code == languageCode,
      orElse: () => Language.fromJson({}),
    );

    // 2. Get stored timestamp from Hive
    final storedUpdatedAt = _settingsRepository.getStoredLanguageUpdatedAt(
      languageCode,
    );
    final apiUpdatedAt = language.updatedAt;

    debugPrint('📦 Stored updated_at: $storedUpdatedAt');
    debugPrint('🌐 API updated_at: $apiUpdatedAt');

    // 3. Compare timestamps
    if (storedUpdatedAt != null &&
        apiUpdatedAt != null &&
        storedUpdatedAt == apiUpdatedAt) {
      // Use cached labels if timestamps match
      final cachedLabels = _settingsRepository.getStoredLanguageLabels(
        languageCode,
      );
      if (cachedLabels != null && cachedLabels.isNotEmpty) {
        debugPrint('✅ Using cached labels for $languageCode');
        return cachedLabels; // ✅ CACHE HIT
      }
    }

    debugPrint('🔄 Fetching fresh labels from API for $languageCode');

    try {
      // 4. Fetch fresh labels from API
      final result = await _settingsRepository.getLanguageLables(languageCode);
      final labels = result['labels'] as Map<String, String>;
      final updatedAt = result['updated_at'] as String;

      // 5. Store in cache
      await _settingsRepository.storeLanguageLabels(
        languageCode: languageCode,
        labels: labels,
        updatedAt: updatedAt,
      );

      debugPrint(
        '💾 Stored labels in cache for $languageCode with timestamp: $updatedAt',
      );

      return labels;
    } catch (e) {
      debugPrint('⚠️ API call failed: $e');
      debugPrint('📁 Attempting to load labels from local JSON file');

      // 6. If API fails, try to load from local JSON file
      final localLabels = await _settingsRepository.getLocalLanguageLabels();
      if (localLabels.isNotEmpty) {
        debugPrint('✅ Using local JSON file as fallback');
        return localLabels;
      }

      // 7. If local JSON also fails, rethrow the error
      debugPrint('❌ Failed to load from both API and local JSON');
      rethrow;
    }
  }

  Future<bool> changeLanguage(Language currentAppLanguage) async {
    try {
      Map<String, String> translatedValues;
      final languages = (state as SettingsAndLanguagesFetchSuccess).languages;

      if (currentAppLanguage.code != null) {
        try {
          // Get language with updated_at from list
          final languageFromList = languages.firstWhere(
            (lang) => lang.code == currentAppLanguage.code,
            orElse: () => currentAppLanguage,
          );

          final languageToStore = currentAppLanguage.copyWith(
            updatedAt: languageFromList.updatedAt,
          );

          // Fetch/cache labels using caching logic for ALL languages (including English)
          translatedValues = await _getLanguageLabelsWithCache(
            currentAppLanguage.code!,
            languages,
          );

          // Store language with updated_at
          _settingsRepository.setCurrentAppLanguage(languageToStore);

          emit(
            (state as SettingsAndLanguagesFetchSuccess).copyWith(
              currentAppLanguage: languageToStore,
              currentLanguageTranslatedValues: translatedValues,
            ),
          );
        } catch (e) {
          debugPrint('⚠️ Error changing language: $e');

          // Try to use cached labels as fallback
          final cachedLabels = _settingsRepository.getStoredLanguageLabels(
            currentAppLanguage.code!,
          );
          if (cachedLabels != null && cachedLabels.isNotEmpty) {
            translatedValues = cachedLabels;
            _settingsRepository.setCurrentAppLanguage(currentAppLanguage);
            emit(
              (state as SettingsAndLanguagesFetchSuccess).copyWith(
                currentAppLanguage: currentAppLanguage,
                currentLanguageTranslatedValues: translatedValues,
              ),
            );
            debugPrint('✅ Using cached labels as fallback');
          } else {
            // If API and cache fail, try local JSON file as last resort
            debugPrint('📁 Loading labels from local JSON file');
            translatedValues = await _settingsRepository
                .getLocalLanguageLabels();

            if (translatedValues.isNotEmpty) {
              _settingsRepository.setCurrentAppLanguage(currentAppLanguage);
              emit(
                (state as SettingsAndLanguagesFetchSuccess).copyWith(
                  currentAppLanguage: currentAppLanguage,
                  currentLanguageTranslatedValues: translatedValues,
                ),
              );
              debugPrint('✅ Using local JSON file as fallback');
            } else {
              // If all methods fail, show error and return false
              debugPrint('⚠️ Failed to load language: No labels available');
              Utils.showSnackBar(
                message: AppLocalizations.get(
                  navigatorKey.currentContext!,
                  languageLoadFailureKey,
                  placeholders: {'language': currentAppLanguage.language!},
                ),
              );
              return false;
            }
          }
        }
      }
      return true;
    } catch (e) {
      return false;
    }
  }

  void changeShowOnBoardingScreen(bool showOnBoardingScreen) {
    _settingsRepository.setOnBoardingScreen(showOnBoardingScreen);

    emit(
      (state as SettingsAndLanguagesFetchSuccess).copyWith(
        showOnBoardingScreen: showOnBoardingScreen,
      ),
    );
  }

  String getTranslatedValue({required String labelKey}) {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return ((state as SettingsAndLanguagesFetchSuccess)
              .currentLanguageTranslatedValues[labelKey]) ??
          labelKey;
    }
    if (state is SettingsAndLanguagesFetchFailure) {
      return ((state as SettingsAndLanguagesFetchFailure)
              .currentLanguageTranslatedValues[labelKey]) ??
          labelKey;
    }
    return labelKey;
  }

  List<Language> getLanguages() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).languages;
    }
    return [Language(id: 1, language: 'English', code: 'en')];
  }

  Language getCurrentAppLanguage() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).currentAppLanguage;
    }
    return Language.fromJson({});
  }

  bool getshowOnBoardingScreen() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).showOnBoardingScreen;
    }
    return true;
  }

  bool getIsFirebaseAuthentication() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings
              ?.authenticationMethod ==
          'firebase';
    }
    return false;
  }

  bool appUnderMaintenance() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings!
              .customerAppMaintenanceStatus ==
          1;
    }
    return false;
  }

  Settings getSettings() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess).settings;
    }
    return Settings.fromJson({});
  }

  String getPusherAppKey() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .pusherSettings!
              .pusherAppKey ??
          '';
    }
    return '';
  }

  String getPusherCluster() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .pusherSettings!
              .pusherAppCluster ??
          '';
    }
    return '';
  }

  String getPusherChannerName() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      String channelName =
          (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .pusherSettings!
              .pusherChannelName ??
          '';
      return '$channelName.${AuthRepository.getUserDetails().id}';
    }
    return '';
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
                      .currentVersionOfAndroidApp!,
                ) ||
            defaultTargetPlatform == TargetPlatform.iOS &&
                needsUpdate(
                  (state as SettingsAndLanguagesFetchSuccess)
                      .settings
                      .systemSettings!
                      .currentVersionOfIosApp!,
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

  String getCountryCode() {
    String countryCode = defaultCountryCode;
    if (state is SettingsAndLanguagesFetchSuccess) {
      countryCode =
          (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings
              ?.countryCode ??
          defaultCountryCode;
    }

    return countryCode.startsWith('+') ? countryCode : '+$countryCode';
  }

  bool isStandardShippingEnabled() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .shippingMethods
              ?.shiprocketShippingMethod ==
          1;
    }
    return false;
  }

  getDeeplinkScheme() {
    if (state is SettingsAndLanguagesFetchSuccess) {
      return (state as SettingsAndLanguagesFetchSuccess)
              .settings
              .systemSettings
              ?.deepLinkScheme ??
          '';
    }
    return '';
  }
}
