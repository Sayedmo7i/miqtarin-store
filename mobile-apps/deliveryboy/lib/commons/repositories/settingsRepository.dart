import 'dart:convert';
import 'package:eshopplus_deliveryboy/commons/models/language.dart';
import 'package:eshopplus_deliveryboy/commons/models/settings.dart';
import 'package:eshopplus_deliveryboy/core/api/apiService.dart';
import 'package:eshopplus_deliveryboy/core/constants/hiveConstants.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';
import 'package:eshopplus_deliveryboy/core/api/apiEndPoints.dart';
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

  Future<Settings> getSettings() async {
    try {
      final result = await Api.get(url: ApiURL.getSettings, useAuthToken: true);
      return Settings.fromJson(result);
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
          useAuthToken: false);

      final data = result['data'] ?? {};
      final metadata = result['metadata'] ?? {};
      final labels = Map<String, String>.from(data);

      return {
        'labels': labels,
        'updated_at': metadata['updated_at'] ?? '',
      };
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  // Store language labels with timestamp
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
      // Silent fail - caching is not critical
    }
  }

  // Retrieve cached language labels
  Map<String, String>? getStoredLanguageLabels(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      final labels = box.get('$languageLabelsPrefix$languageCode');
      return labels != null ? Map<String, String>.from(labels) : null;
    } catch (e) {
      return null;
    }
  }

  // Retrieve cached language updated_at timestamp
  String? getStoredLanguageUpdatedAt(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      return box.get('$languageLabelsUpdatedAtPrefix$languageCode') as String?;
    } catch (e) {
      return null;
    }
  }

  // Clear cached language labels for specific language
  Future<void> clearStoredLanguageLabels(String languageCode) async {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      await box.delete('$languageLabelsPrefix$languageCode');
      await box.delete('$languageLabelsUpdatedAtPrefix$languageCode');
    } catch (e) {
      // Silent fail
    }
  }

  // Check if language labels need to be refreshed
  bool shouldRefreshLanguageLabels({
    required String languageCode,
    required String latestUpdatedAt,
  }) {
    final storedUpdatedAt = getStoredLanguageUpdatedAt(languageCode);
    if (storedUpdatedAt == null) return true;
    return storedUpdatedAt != latestUpdatedAt;
  }

  // Load labels from local JSON file as fallback
  Future<Map<String, String>> getLocalLanguageLabels() async {
    try {
      final String jsonString =
          await rootBundle.loadString('lib/core/localization/deliveryboy.json');
      final Map<String, dynamic> jsonMap = json.decode(jsonString);
      return Map<String, String>.from(jsonMap);
    } catch (e) {
      debugPrint('⚠️ Error loading local JSON file: $e');
      return {};
    }
  }
}
