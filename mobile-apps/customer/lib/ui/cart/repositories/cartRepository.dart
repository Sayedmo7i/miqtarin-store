import 'package:eshop_plus/core/api/apiService.dart';
import 'package:eshop_plus/ui/cart/models/cart.dart';
import 'package:eshop_plus/ui/cart/models/deliverabilityResponse.dart';

import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/ui/cart/widgets/cartProductList.dart';

class CartRepository {
  Future<({Cart cart, List<CartProduct>? savedForLaterProducts})>
  fetchUserCart({
    required Map<String, dynamic> params,
    bool isCallForSavedForLater = true,
  }) async {
    try {
      params.addAll({ApiURL.onlyDeliveryChargeApiKey: 0});
      final result = await Api.get(
        queryParameters: params,
        url: ApiURL.getUserCart,
        useAuthToken: true,
      );
      Cart cart;
      List<CartProduct> saveForLaterProducts = [];
      if (result[ApiURL.errorKey] == true) {
        cart = Cart();
      } else {
        cart = Cart.fromJson(Map.from(result));
      }
      if (isCallForSavedForLater) {
        // Update params to fetch "Save for Later" items
        params[ApiURL.isSavedForLaterApiKey] = 1;
        final savedForLaterResponse = await Api.get(
          queryParameters: params,
          url: ApiURL.getUserCart,
          useAuthToken: true,
        );
        if (result[ApiURL.errorKey] == true &&
            savedForLaterResponse[ApiURL.errorKey] == true) {
          throw ApiException(result[ApiURL.messageKey]);
        }

        // Check if the response contains "cart" data
        if (savedForLaterResponse['cart'] != null) {
          // Ensure saveForLaterProducts is initialized

          // Iterate over the items and add them to the saveForLaterProducts list
          Map.from(savedForLaterResponse)['cart'].forEach((v) {
            saveForLaterProducts!.add(CartProduct.fromJson(v));
          });
        }
      }

      return (cart: cart, savedForLaterProducts: saveForLaterProducts);
    } catch (e) {
      // If an error occurs, check if it's an ApiException or a different type of error
      if (e is ApiException) {
        throw ApiException(
          e.toString(),
        ); // Re-throw the API exception with the backend message
      } else {
        // Handle any other exceptions
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<({Cart cart, List<CartProduct>? savedForLaterProducts})>
  manageUserCart({required Map<String, dynamic> params}) async {
    try {
      final result = await Api.post(
        body: params,
        url: ApiURL.manageCart,
        useAuthToken: true,
      );
      List<CartProduct> savedForLaterProducts = [];
      if (result['save_for_later_data'] != null) {
        for (var item in result['save_for_later_data']) {
          savedForLaterProducts.add(CartProduct.fromJson(item));
        }
      }
      return (
        cart: Cart.fromJson(Map.from(result)),
        savedForLaterProducts: savedForLaterProducts,
      );
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<String> clearCart() async {
    try {
      final result = await Api.post(
        body: {},
        url: ApiURL.clearCart,
        useAuthToken: true,
      );
      return result[ApiURL.messageKey];
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<Cart> manageCart({required Map<String, dynamic> params}) async {
    try {
      final result = await Api.post(
        body: params,
        url: ApiURL.manageCart,
        useAuthToken: true,
      );

      return Cart.fromJson(Map.from(result));
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<({Cart cart, String successMessage})> removeProductFromCart({
    required Map<String, dynamic> params,
  }) async {
    try {
      final result = await Api.delete(
        queryParameters: params,
        url: ApiURL.removeFromCart,
        useAuthToken: true,
      );
      final data = result[ApiURL.dataKey];
      if (data == null ||
          (data is List && data.isEmpty) ||
          (data is Map && data.isEmpty)) {
        return (
          cart: Cart(cartProducts: []),
          successMessage: result[ApiURL.messageKey].toString(),
        );
      }
      return (
        cart: Cart.fromJson(Map.from(result)),
        successMessage: result[ApiURL.messageKey].toString(),
      );
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<({String message, DeliverabilityResponse? deliverabilityData})>
  checkDeliverability({
    required int storeId,
    required int addressId,
    required bool isStandardShippingEnabled,
    String? shippingOption,
    String? promocode,
  }) async {
    try {
      final result = await Api.post(
        body: {
          ApiURL.storeIdApiKey: storeId,
          ApiURL.addressIdApiKey: addressId,
          if (shippingOption != null) 'shipping_option': shippingOption,
          if (promocode != null) ApiURL.promoCodeApiKey: promocode,
        },
        url: ApiURL.checkCartProductsDelivarable,
        useAuthToken: true,
      );
      if (result[ApiURL.errorKey] == true) {
        List<Map<String, dynamic>> nonDeliverableItems = [];

        if (result[ApiURL.dataKey] != null) {
          List<dynamic> data = result[ApiURL.dataKey];
          for (var item in data) {
            if (item['is_deliverable'] == false) {
              nonDeliverableItems.add(Map<String, dynamic>.from(item));
            }
          }
        }

        throw ApiException(
          result[ApiURL.messageKey],
          errorData: nonDeliverableItems,
        );
      } else {
        return (
          message: result[ApiURL.messageKey].toString(),
          deliverabilityData: isStandardShippingEnabled
              ? DeliverabilityResponse.fromJson(result)
              : null,
        );
      }
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString(), errorData: e.errorData);
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }

  Future<({int orderId, double finalTotal, double walletBalance})> placeOrder({
    required Map<String, dynamic> params,
  }) async {
    try {
      final result = await Api.post(
        body: params,
        url: ApiURL.placeOrder,
        useAuthToken: true,
      );

      return (
        orderId: int.parse(result['order_id'].toString()),
        finalTotal: double.parse(result['final_total'].toString()),
        walletBalance: double.parse(result['balance'][0]['balance'].toString()),
      );
    } catch (e) {
      if (e is ApiException) {
        throw ApiException(e.toString());
      } else {
        throw ApiException(defaultErrorMessageKey);
      }
    }
  }
}
