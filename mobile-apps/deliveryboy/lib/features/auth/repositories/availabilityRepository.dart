import 'package:eshopplus_deliveryboy/core/api/apiEndPoints.dart';
import 'package:eshopplus_deliveryboy/core/api/apiService.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';

class AvailabilityRepository {
  Future<String> toggleAvailability({required int isAvailable}) async {
    try {
      final result = await Api.put(
        url: ApiURL.toggleAvailability,
        queryParameters: {ApiURL.isAvailableApiKey: isAvailable},
        useAuthToken: true,
      );

      return result['message'] ?? 'Availability updated successfully';
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }
}