import 'package:dio/dio.dart';

import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';

import 'package:eshopplus_seller/utils/utils.dart';

class OrderRepository {
  Future getOrders(Map<String, dynamic> params) async {
    try {
      final result = await Api.get(
          url: ApiURL.getOrders, useAuthToken: true, queryParameters: params);

      return result;
    } catch (e) {
      Utils.throwApiException(e);
    }
  }

  Future<String> getOrderInvoice(
      {required int id, required String apiUrl}) async {
    try {
      final result = await Api.get(
          url: apiUrl,
          useAuthToken: true,
          queryParameters: apiUrl == ApiURL.downloadOrderInvoice
              ? {ApiURL.orderIdApiKey: id}
              : {ApiURL.idApiKey: id});

      return result['invoice_url'];
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }

 
  Future<void> downloadFile(
      {required String url,
      required String savePath,
      required CancelToken cancelToken,
      required Function updateDownloadedPercentage}) async {
    try {
      await Api.download(
          cancelToken: cancelToken,
          url: url,
          savePath: savePath,
          updateDownloadedPercentage: updateDownloadedPercentage);
    } catch (e) {
      Utils.throwApiException(e);
    }
  }
}
