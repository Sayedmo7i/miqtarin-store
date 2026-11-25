import 'package:eshop_plus/core/api/apiService.dart';
import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/commons/seller/models/seller.dart';

import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';

class SellerRepository {
  Future<({List<Seller> sellers, int total})> getSellers({
    required int storeId,
    String? storeSlug,
    int? offset,
    List<int>? sellerIds,
  }) async {
    try {
      Map<String, dynamic> queryParameters = {
        ApiURL.offsetApiKey: offset ?? 0,
        ApiURL.limitApiKey: limit,
      };
      if (sellerIds != null && sellerIds.isNotEmpty) {
        queryParameters[ApiURL.sellerIdsApiKey] = sellerIds.join(',');
      }
      if (storeSlug != null && storeSlug.isNotEmpty) {
        queryParameters[ApiURL.storeSlugApiKey] = storeSlug;
      } else {
        queryParameters[ApiURL.storeIdApiKey] = storeId;
      }
      final result = await Api.get(
        url: ApiURL.getSellers,
        useAuthToken: true,
        queryParameters: queryParameters,
      );

      return (
        sellers: ((result[ApiURL.dataKey] ?? []) as List)
            .map((product) => Seller.fromJson(Map.from(product ?? {})))
            .toList(),
        total: int.parse((result[ApiURL.totalKey] ?? 0).toString()),
      );
    } catch (e, _) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<List<Seller>> getBestSellers({
    required int storeId,
    int? offset,
  }) async {
    try {
      final result = await Api.get(
        url: ApiURL.bestSellers,
        useAuthToken: true,
        queryParameters: {
          ApiURL.storeIdApiKey: storeId,
          ApiURL.offsetApiKey: offset ?? 0,
          ApiURL.limitApiKey: limit,
        },
      );
      return (((result[ApiURL.dataKey] ?? []) as List)
          .map((seller) => Seller.fromJson(Map.from(seller ?? {})))
          .toList());
    } catch (e, _) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<List<Seller>> getFeaturedSellers({required int storeId}) async {
    try {
      final result = await Api.get(
        url: ApiURL.topSellers,
        useAuthToken: true,
        queryParameters: {ApiURL.storeIdApiKey: storeId},
      );

      return ((result[ApiURL.dataKey] ?? []) as List)
          .map((seller) => Seller.fromJson(Map.from(seller ?? {})))
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
