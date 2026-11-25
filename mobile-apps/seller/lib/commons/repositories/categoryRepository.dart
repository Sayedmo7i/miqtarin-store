import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/features/profile/addProduct/models/category.dart';

class CategoryRepository {

  Future<({List<Category> data, List<Category> requested_categories})>
      getCategoryListProcess(
           Map<String, String?> parameter,
          {bool getAllCategories = false}) async {
    try {
      //when we use category API in signup ,then we need all categories of that store
      final result = await Api.get(
          url: getAllCategories
              ? ApiURL.getAllCategoryList
              : ApiURL.getCategories,
          useAuthToken: true,
          queryParameters: parameter);

      List data = result[ApiURL.dataKey] ?? [];
      List requestedData = result['requested_categories'] ?? [];

      return (
        data: data.map((e) => Category.fromJson(e)).toList(),
        requested_categories:
            requestedData.map((e) => Category.fromJson(e)).toList(),
      );
    } catch (e) {
      throw ApiException(e.toString());
    }
  }
}