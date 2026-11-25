import 'dart:convert';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/profile/addProduct/models/category.dart';
import 'package:eshopplus_seller/utils/utils.dart';

class AddDeleteCategoryRepository {
  Future<({Category category,String message})> addCategory({
    required String storeId,
    required Map<String, String> names,
    required String imagePath,
    required String bannerPath,
    String? parentId,
  }) async {
    final Map<String, dynamic> data = {
      ApiURL.storeIdApiKey: storeId,
      'name': names['en'] ?? '',
      'category_image': imagePath,
      'banner': bannerPath,
      if (parentId != null) 'parent_id': parentId
    };

    // Prepare translated_category_name
    final translated = Map.of(names)..remove('en');
    if (translated.isNotEmpty) {
      data['translated_category_name'] = jsonEncode(translated);
    }
    try {
      final result = await Api.post(
          body: data, url: ApiURL.addCategories, useAuthToken: true);
      return (
       category: Category.fromJson(Map.from(result[ApiURL.dataKey] ?? {})),
    message:    result[ApiURL.messageKey].toString()
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }
    Future<String> deleteCategory({required String categoryId}) async {
    try {
      final result = await Api.delete(
          url: ApiURL.deleteCategory,
          useAuthToken: true,
          queryParameters: {ApiURL.categoryIdApiKey: categoryId});
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
