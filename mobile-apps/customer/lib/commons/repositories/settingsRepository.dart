import 'dart:convert';
import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/api/apiService.dart';
import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/core/constants/hiveConstants.dart';
import 'package:eshop_plus/commons/models/language.dart';
import 'package:eshop_plus/commons/models/settings.dart';

import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';
import 'package:hive_flutter/hive_flutter.dart';

class SettingsRepository {
  Future<void> setCurrentAppLanguage(Language value) async {
    try {
      await Hive.box(settingsBoxKey).put(currentAppLanguageKey, value.toJson());
    } catch (e) {}
  }

  Language getCurrentAppLanguage() {
    try {
      final languageValue = Hive.box(settingsBoxKey).get(currentAppLanguageKey);

      return Language.fromJson(Map.from(languageValue ?? {}));
    } catch (e) {
      return Language.fromJson({});
    }
  }

  Future<List<Language>> getLanguages() async {
    try {
      final result = await Api.get(
        url: '${databaseUrl}get_languages',
        useAuthToken: false,
      );

      return ((result[ApiURL.dataKey] ?? []) as List)
          .map((language) => Language.fromJson(Map.from(language ?? {})))
          .toList();
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<void> setOnBoardingScreen(bool value) async {
    Hive.box(authBoxKey).put(showOnBoardingScreenKey, value);
  }

  bool getOnBoardingScreen() {
    return Hive.box(authBoxKey).get(showOnBoardingScreenKey) ?? true;
  }

  Future<void> setFirstTimeUser(bool value) async {
    Hive.box(authBoxKey).put(isFirstTimeUserKey, value);
  }

  bool getFirstTimeUser() {
    return Hive.box(authBoxKey).get(isFirstTimeUserKey) ?? true;
  }

  Future<Settings> getSettings() async {
    try {
      final result = await Api.get(
        url: ApiURL.getSettings,
        useAuthToken: false,
      );

      return Settings.fromJson(Map.from(result[ApiURL.dataKey] ?? {}));
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<Map<String, dynamic>> getLanguageLables(String languageCode) async {
    try {
      final result = await Api.get(
        url: ApiURL.getLanguageLabels,
        queryParameters: {ApiURL.languageCodeApiKey: languageCode},
        useAuthToken: false,
      );

      final data = result[ApiURL.dataKey] ?? {};
      final metadata = result['metadata'] ?? {};
      final labels = Map<String, String>.from(data);

      return {'labels': labels, 'updated_at': metadata['updated_at'] ?? ''};
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  // Load labels from local JSON file as fallback
  Future<Map<String, String>> getLocalLanguageLabels() async {
    try {
      final String jsonString = await rootBundle.loadString(
        'lib/core/localization/customer.json',
      );
      final Map<String, dynamic> jsonMap = jsonDecode(jsonString);
      return Map<String, String>.from(jsonMap);
    } catch (e) {
      debugPrint('⚠️ Error loading local JSON file: $e');
      return {};
    }
  }

  // Store language labels with updated_at timestamp in Hive
  Future<void> storeLanguageLabels({
    required String languageCode,
    required Map<String, String> labels,
    required String updatedAt,
  }) async {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      await box.put('$languageLabelsPrefix$languageCode', labels);
      await box.put('$languageLabelsUpdatedAtPrefix$languageCode', updatedAt);
    } catch (e) {
      // Handle error silently
    }
  }

  // Get stored language labels from Hive
  Map<String, String>? getStoredLanguageLabels(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      final labels = box.get('$languageLabelsPrefix$languageCode');
      if (labels == null) return null;
      return Map<String, String>.from(labels);
    } catch (e) {
      return null;
    }
  }

  // Get stored updated_at timestamp for a language
  String? getStoredLanguageUpdatedAt(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      return box.get('$languageLabelsUpdatedAtPrefix$languageCode') as String?;
    } catch (e) {
      return null;
    }
  }

  // Clear stored language labels for a specific language
  Future<void> clearStoredLanguageLabels(String languageCode) async {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      await box.delete('$languageLabelsPrefix$languageCode');
      await box.delete('$languageLabelsUpdatedAtPrefix$languageCode');
    } catch (e) {
      // Handle error silently
    }
  }
}
