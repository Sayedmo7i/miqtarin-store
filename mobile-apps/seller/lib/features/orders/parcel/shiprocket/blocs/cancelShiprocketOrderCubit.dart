import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class CancelShiprocketOrderState {}

class CancelShiprocketOrderInitial extends CancelShiprocketOrderState {}

class CancelShiprocketOrderProgress extends CancelShiprocketOrderState {}

class CancelShiprocketOrderFailure extends CancelShiprocketOrderState {
  final String errorMessage;
  CancelShiprocketOrderFailure(this.errorMessage);
}

class CancelShiprocketOrderSuccess extends CancelShiprocketOrderState {
  final String successMessage;
  final Parcel parcel;

  CancelShiprocketOrderSuccess({
    required this.successMessage,
    required this.parcel,
  });
}

class CancelShiprocketOrderCubit extends Cubit<CancelShiprocketOrderState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  CancelShiprocketOrderCubit() : super(CancelShiprocketOrderInitial());
  void cancelShiprocketOrder({required String shiprocketId}) async {
    emit(CancelShiprocketOrderProgress());

    try {
      final result = await shiprocketRepository.cancelShiprocketOrder(
        shiprocketId: shiprocketId,
      );

      emit(
        CancelShiprocketOrderSuccess(
          successMessage: result.message,
          parcel: result.parcel,
        ),
      );
    } catch (e) {
      emit(CancelShiprocketOrderFailure(e.toString()));
    }
  }
}
