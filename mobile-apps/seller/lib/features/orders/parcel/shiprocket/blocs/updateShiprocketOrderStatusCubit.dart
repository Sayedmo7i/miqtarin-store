import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class UpdateShiprocketOrderStatusState {}

class UpdateShiprocketOrderStatusInitial
    extends UpdateShiprocketOrderStatusState {}

class UpdateShiprocketOrderStatusProgress
    extends UpdateShiprocketOrderStatusState {}

class UpdateShiprocketOrderStatusFailure
    extends UpdateShiprocketOrderStatusState {
  final String errorMessage;
  UpdateShiprocketOrderStatusFailure(this.errorMessage);
}

class UpdateShiprocketOrderStatusSuccess
    extends UpdateShiprocketOrderStatusState {
  final String successMessage;
  final Parcel parcel;

  UpdateShiprocketOrderStatusSuccess({
    required this.successMessage,
    required this.parcel,
  });
}

class UpdateShiprocketOrderStatusCubit
    extends Cubit<UpdateShiprocketOrderStatusState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  UpdateShiprocketOrderStatusCubit()
    : super(UpdateShiprocketOrderStatusInitial());
  void updateShiprocketOrderStatus({required String trackingId}) async {
    emit(UpdateShiprocketOrderStatusProgress());

    try {
      final result = await shiprocketRepository.updateShiprocketOrderStatus(
        trackingId: trackingId,
      );

      emit(
        UpdateShiprocketOrderStatusSuccess(
          successMessage: result.message,
          parcel: result.parcel,
        ),
      );
    } catch (e) {
      emit(UpdateShiprocketOrderStatusFailure(e.toString()));
    }
  }
}
