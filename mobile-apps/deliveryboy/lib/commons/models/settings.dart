import 'package:eshopplus_deliveryboy/commons/models/language.dart';
import 'package:eshopplus_deliveryboy/commons/models/systemSettings.dart';

class Settings {
  String? currency;
  String? termsConditions;
  String? privacyPolicy;
  String? decimalPoint;
  String? aboutUs;
  String? contactUs;
  String? supportedLocals;
  SystemSettings? systemSettings;
  List<Language>? languages;
  Settings({
    this.currency,
    this.termsConditions,
    this.decimalPoint,
    this.supportedLocals,
    this.systemSettings,
    this.aboutUs,
    this.contactUs,
    this.privacyPolicy,
    this.languages,
  });

  Settings.fromJson(Map<String, dynamic> json) {
    currency = json['currency'];
    decimalPoint = json['decimal_point'] ?? "";
    supportedLocals = json['supported_locals'] ?? "";
    if (json['data'] != null) {
      termsConditions = json['data']['delivery_boy_terms_and_conditions'] ?? "";
      privacyPolicy = json['data']['delivery_boy_privacy_policy'] ?? "";
      aboutUs = json['data']['about_us'] ?? "";
      contactUs = json['data']['contact_us'] ?? "";

      // Parse languages from the settings API response
    }
    if (json['languages'] != null) {
      languages = (json['languages'] as List)
          .map((language) => Language.fromJson(language))
          .toList();
    }
    systemSettings = SystemSettings.fromJson((json['system_settings'] ?? {}));
  }
}
