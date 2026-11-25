import 'package:eshop_plus/core/api/apiService.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/configs/appConfig.dart';
import '../models/blog.dart';
import '../models/blogCategory.dart';

class BlogRepository {
  Future<({List<Blog> blogs, int total})> getBlogs({
    int? offset,

    int? storeId,
    int? categoryId,
    String? storeSlug,
    String? blogSlug,
  }) async {
    try {
      Map<String, dynamic> queryParameters = {
        ApiURL.offsetApiKey: offset ?? 0,
        ApiURL.limitApiKey: limit,
      };

      void addIfNotNull(String key, dynamic value) {
        if (value != null) {
          queryParameters[key] = value;
        }
      }

      addIfNotNull(ApiURL.categoryIdApiKey, categoryId);
      addIfNotNull('store_slug', storeSlug);
      addIfNotNull('slug', blogSlug);
      addIfNotNull('store_id', storeId);

      final result = await Api.get(
        url: ApiURL.getBlogs,
        useAuthToken: false,
        queryParameters: queryParameters,
      );

      return (
        blogs: ((result[ApiURL.dataKey] ?? []) as List)
            .map((blog) => Blog.fromJson(Map.from(blog ?? {})))
            .toList(),
        total: int.parse((result[ApiURL.totalKey] ?? 0).toString()),
      );
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<Blog> getBlogDetails({
    required int storeId,
    required int blogId,
  }) async {
    try {
      Map<String, dynamic> queryParameters = {
        ApiURL.storeIdApiKey: storeId,
        ApiURL.blogIdApiKey: blogId,
      };

      final result = await Api.get(
        url: ApiURL.getBlogDetails,
        useAuthToken: false,
        queryParameters: queryParameters,
      );

      return Blog.fromJson(Map.from(result[ApiURL.dataKey] ?? {}));
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<List<BlogCategory>> getBlogCategories({required int storeId}) async {
    try {
      Map<String, dynamic> queryParameters = {ApiURL.storeIdApiKey: storeId};

      final result = await Api.get(
        url: ApiURL.getBlogCategories,
        useAuthToken: false,
        queryParameters: queryParameters,
      );

      return ((result[ApiURL.dataKey] ?? []) as List)
          .map((category) => BlogCategory.fromJson(Map.from(category ?? {})))
          .toList();
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }
}
