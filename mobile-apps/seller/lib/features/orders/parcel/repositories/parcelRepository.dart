import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';

import 'package:eshopplus_seller/utils/utils.dart';

class ParcelRepository {
  Future<({List<Parcel> parcels, int total})> getParcels({
    required Map<String, dynamic> queryParameters,
  }) async {
    try {
      queryParameters.addAll({ApiURL.limitApiKey: limit});

      final result = await Api.get(
        url: ApiURL.getAllParcels,
        useAuthToken: true,
        queryParameters: queryParameters,
      );

      return (
        parcels: ((result[ApiURL.dataKey] ?? []) as List)
            .map((parcel) => Parcel.fromJson(Map.from(parcel ?? {})))
            .toList(),
        total: int.parse((result[ApiURL.totalKey] ?? 0).toString()),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<({Parcel parcel, String message})> createOrderParcel(
    Map<String, dynamic> params,
  ) async {
    try {
      final result = await Api.post(
        url: ApiURL.createOrderParcel,
        useAuthToken: true,
        body: params,
      );

      return (
        parcel: Parcel.fromJson(Map.from(result[ApiURL.dataKey][0] ?? {})),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future deleteOrderParcel({required int parcelId}) async {
    try {
      final result = await Api.delete(
        url: ApiURL.deleteOrderParcel,
        useAuthToken: true,
        queryParameters: {ApiURL.idApiKey: parcelId},
      );

      return result[ApiURL.messageKey];
    } catch (e) {
      Utils.throwApiException(e);
    }
  }
}
