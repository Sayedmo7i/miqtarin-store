import 'dart:convert';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/profile/brands/models/brand.dart';
import 'package:eshopplus_seller/utils/utils.dart';

class AddDeleteBrandRepository {
  Future<({Brand brand, String message})> addBrand({
    required String storeId,
    required Map<String, String> names,
    required String imagePath,
  }) async {
      final Map<String, dynamic> body = {
      'store_id': storeId,
      'brand_name': names['en'] ?? '',
      'image': imagePath,
      'translated_brand_name': jsonEncode(names..remove('en')),
    };

    try {
      final result = await Api.post(
        body: body,
        url: ApiURL.addBrands,
        useAuthToken: true,
      );
      return (
        brand: Brand.fromJson(Map.from(result[ApiURL.dataKey] ?? {})),
        message: result[ApiURL.messageKey].toString()
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }
   Future<String> deleteBrand({required String brandId}) async {
    try {
      final result = await Api.delete(
          url: ApiURL.deleteBrand,
          useAuthToken: true,
          queryParameters: {ApiURL.brandIdApiKey: brandId});
      return result[ApiURL.messageKey];
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }
}
