import 'dart:convert';

import 'package:eshopplus_seller/commons/models/language.dart';
import 'package:eshopplus_seller/commons/models/settings.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/core/constants/hiveConstants.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
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
      final result = await Api.get(
          url: ApiURL.getSettings,
          useAuthToken: true,
         );
      return Settings.fromJson(Map.from(result[ApiURL.dataKey] ?? {}));
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<Map<String, dynamic>> getLanguageLables(String languageCode) async {
    try {
      final result = await Api.get(
          url: ApiURL.getLanguageLabels,
          queryParameters: {ApiURL.languageCodeApiKey: languageCode},
          useAuthToken: false);

      // Return both data and metadata
      return {
        'labels': Map<String, String>.from(result[ApiURL.dataKey] ?? {}),
        'updated_at': result['metadata']?['updated_at'] ?? '',
      };
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  // Store language labels in Hive with updated_at timestamp
  Future<void> storeLanguageLabels({
    required String languageCode,
    required Map<String, String> labels,
    required String updatedAt,
  }) async {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      await box.put('languageLabels_$languageCode', labels);
      await box.put('languageLabelsUpdatedAt_$languageCode', updatedAt);
    } catch (e) {
      // Silently fail if storage fails
    }
  }

  // Get stored language labels from Hive
  Map<String, String>? getStoredLanguageLabels(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      final labels = box.get('languageLabels_$languageCode');
      if (labels == null) return null;
      return Map<String, String>.from(labels);
    } catch (e) {
      return null;
    }
  }

  // Get stored updated_at timestamp from Hive
  String? getStoredLanguageUpdatedAt(String languageCode) {
    try {
      final box = Hive.box(languageLabelsBoxKey);
      return box.get('languageLabelsUpdatedAt_$languageCode');
    } catch (e) {
      return null;
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

  // Load language labels from local JSON file as fallback
  Future<Map<String, String>> getLocalLanguageLabels() async {
    try {
      final String jsonString = await rootBundle.loadString('lib/core/localization/seller.json');
      final Map<String, dynamic> jsonMap = jsonDecode(jsonString);
      return Map<String, String>.from(jsonMap);
    } catch (e) {
      debugPrint('⚠️ Error loading local seller.json file: $e');
      return {};
    }
  }
}
