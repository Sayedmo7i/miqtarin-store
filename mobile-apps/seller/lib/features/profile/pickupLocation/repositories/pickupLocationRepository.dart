import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/models/location.dart';
import 'package:eshopplus_seller/utils/utils.dart';

class PickupLocationRepository {
  Future<({List<Location> locations, int total})> getPickupLocations({
    required Map<String, dynamic> params,
  }) async {
    try {
      params.addAll({ApiURL.limitApiKey: limit});

      final result = await Api.get(
        url: ApiURL.getPickupLocation,
        useAuthToken: true,
        queryParameters: params,
      );

      return (
        locations: ((result[ApiURL.dataKey] ?? []) as List)
            .map((location) => Location.fromJson(Map.from(location ?? {})))
            .toList(),
        total: int.parse((result[ApiURL.totalKey] ?? 0).toString()),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<String> deletePickupLocation({required String pickupLocationId}) async {
    try {
      final result = await Api.delete(
          url: ApiURL.deletePickupLocation,
          useAuthToken: true,
          queryParameters: {ApiURL.pickupLocationIdApiKey: pickupLocationId});
      return result[ApiURL.messageKey];
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<String> bulkUpdatePickupLocation({
    required Map<String, dynamic> params,
    required String apiUrl,
  }) async {
    try {
      final result =
          await Api.post(body: params, url: apiUrl, useAuthToken: true);

      return result[ApiURL.messageKey].toString();
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }
}
