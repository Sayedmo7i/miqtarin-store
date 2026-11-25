import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/features/orders/models/deliveryBoy.dart';
import 'package:eshopplus_seller/utils/utils.dart';

class DeliveryBoyRepository {
  Future<({List<DeliveryBoy> deliveryBoys, int total})> getDeliveryBoys({
    required Map<String, dynamic> params,
  }) async {
    try {
      params.addAll({ApiURL.limitApiKey: limit});

      final result = await Api.get(
        url: ApiURL.getDeliveryBoys,
        useAuthToken: true,
        queryParameters: params,
      );

      return (
        deliveryBoys: ((result[ApiURL.dataKey] ?? []) as List)
            .map((deliveryBoy) => DeliveryBoy.fromJson(Map.from(deliveryBoy ?? {})))
            .toList(),
        total: int.parse((result[ApiURL.totalKey] ?? 0).toString()),
      );
    } catch (e) {
      return Utils.throwApiException(e);
    }
  }
}
