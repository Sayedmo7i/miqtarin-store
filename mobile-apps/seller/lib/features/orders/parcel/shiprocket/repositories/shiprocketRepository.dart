import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';

import 'package:eshopplus_seller/utils/utils.dart';

class ShiprocketRepository {
  Future<({Parcel parcel, String message})> createShiprocketOrder(
    Map<String, dynamic> params,
  ) async {
    try {
      final result = await Api.post(
        url: ApiURL.createShiprocketOrder,
        useAuthToken: true,
        body: params,
      );

      return (
        parcel: Parcel.fromJson(
          Map.from(result[ApiURL.dataKey]['parcel'] ?? {}),
        ),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<({Parcel parcel, String message})> updateShiprocketOrderStatus({
    required String trackingId,
  }) async {
    try {
      final result = await Api.put(
        url: ApiURL.updateShiprocketOrderStatus,
        useAuthToken: true,
        queryParameters: {ApiURL.trackingIdApiKey: trackingId},
      );

      return (
        parcel: Parcel.fromJson(Map.from(result[ApiURL.dataKey] ?? {})),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<({Parcel parcel, String message})> cancelShiprocketOrder({
    required String shiprocketId,
  }) async {
    try {
      final result = await Api.put(
        url: ApiURL.cancelShiprocketOrder,
        useAuthToken: true,
        queryParameters: {ApiURL.shiprocketOrderIdApiKey: shiprocketId},
      );

      return (
        parcel: Parcel.fromJson(Map.from(result[ApiURL.dataKey] ?? {})),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<({Parcel parcel, String message})> generateAWB({
    required String shipmentId,
  }) async {
    try {
      final result = await Api.post(
        url: ApiURL.generateAWB,
        useAuthToken: true,
        body: {ApiURL.shipmentIdApiKey: shipmentId},
      );

      return (
        parcel: Parcel.fromJson(
          Map.from(result[ApiURL.dataKey]['parcel'] ?? {}),
        ),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<dynamic> generateLabel({required String shipmentId}) async {
    try {
      final result = await Api.post(
        url: ApiURL.generateLabel,
        useAuthToken: true,
        body: {ApiURL.shipmentIdApiKey: shipmentId},
      );

      return result[ApiURL.dataKey]['label_url'];
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<({Parcel parcel, String message})> sendPickupRequest({
    required String shipmentId,
  }) async {
    try {
      final result = await Api.post(
        url: ApiURL.sendPickupRequest,
        useAuthToken: true,
        body: {ApiURL.shipmentIdApiKey: shipmentId},
      );

      return (
        parcel: Parcel.fromJson(
          Map.from(result[ApiURL.dataKey]['parcel'] ?? {}),
        ),
        message: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

  Future<dynamic> generateInvoice({required String shiprocketId}) async {
    try {
      final result = await Api.post(
        url: ApiURL.generateInvoice,
        useAuthToken: true,
        body: {ApiURL.shiprocketOrderIdApiKey: shiprocketId},
      );

      return result[ApiURL.dataKey]['invoice_url'];
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }
}
