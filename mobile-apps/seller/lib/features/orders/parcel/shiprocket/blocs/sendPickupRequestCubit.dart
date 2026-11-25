import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class SendPickupRequestState {}

class SendPickupRequestInitial extends SendPickupRequestState {}

class SendPickupRequestProgress extends SendPickupRequestState {}

class SendPickupRequestFailure extends SendPickupRequestState {
  final String errorMessage;
  SendPickupRequestFailure(this.errorMessage);
}

class SendPickupRequestSuccess extends SendPickupRequestState {
  final String successMessage;
  final Parcel parcel;

  SendPickupRequestSuccess({
    required this.successMessage,
    required this.parcel,
  });
}

class SendPickupRequestCubit extends Cubit<SendPickupRequestState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  SendPickupRequestCubit() : super(SendPickupRequestInitial());

  void sendPickupRequest({required String shipmentId}) async {
    emit(SendPickupRequestProgress());

    try {
      final result = await shiprocketRepository.sendPickupRequest(
        shipmentId: shipmentId,
      );

      emit(
        SendPickupRequestSuccess(
          successMessage: result.message,
          parcel: result.parcel,
        ),
      );
    } catch (e) {
      emit(SendPickupRequestFailure(e.toString()));
    }
  }
}
