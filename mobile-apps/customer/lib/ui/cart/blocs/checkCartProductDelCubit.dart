import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/core/api/apiService.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/models/deliverabilityResponse.dart';
import 'package:eshop_plus/ui/cart/repositories/cartRepository.dart';

import 'package:flutter_bloc/flutter_bloc.dart';

abstract class CheckCartProductDeliverabilityState {}

class CheckCartProductDeliverabilityInitial
    extends CheckCartProductDeliverabilityState {}

class CheckCartProductDeliverabilityInProgress
    extends CheckCartProductDeliverabilityState {}

class CheckCartProductDeliverabilitySuccess
    extends CheckCartProductDeliverabilityState {
  final String successMessage;
  final DeliverabilityResponse? deliverabilityData;

  CheckCartProductDeliverabilitySuccess({
    required this.successMessage,
    this.deliverabilityData,
  });
}

class CheckCartProductDeliverabilityFailure
    extends CheckCartProductDeliverabilityState {
  final String errorMessage;
  final List<Map<String, dynamic>>? errorData;
  CheckCartProductDeliverabilityFailure(this.errorMessage, this.errorData);
}

class CheckCartProductDeliverabilityCubit
    extends Cubit<CheckCartProductDeliverabilityState> {
  final CartRepository _cartRepository = CartRepository();

  CheckCartProductDeliverabilityCubit()
    : super(CheckCartProductDeliverabilityInitial());

  Future<void> checkDeliverability({
    required int storeId,
    required int addressId,
    String? shippingOption,
    String? promocode,
    bool? emitProgressState = true,
    required bool isStandardShippingEnabled,
  }) async {
    if (isClosed) return;

    try {
      if (emitProgressState ?? true) {
        emit(CheckCartProductDeliverabilityInProgress());
      }

      if (storeId <= 0 || addressId <= 0) {
        throw Exception('Invalid store ID or address ID');
      }

      final response = await _cartRepository.checkDeliverability(
        storeId: storeId,
        addressId: addressId,
        shippingOption: shippingOption,
        promocode: promocode,
        isStandardShippingEnabled: isStandardShippingEnabled,
      );

      if (isClosed) return;

      emit(
        CheckCartProductDeliverabilitySuccess(
          successMessage: response.message,
          deliverabilityData: response.deliverabilityData,
        ),
      );
    } catch (e) {
      final errorData = e is ApiException && e.errorData != null
          ? e.errorData
          : null;

      emit(CheckCartProductDeliverabilityFailure(e.toString(), errorData));
    }
  }

  resetState() {
    emit(CheckCartProductDeliverabilityInitial());
  }

  
}
