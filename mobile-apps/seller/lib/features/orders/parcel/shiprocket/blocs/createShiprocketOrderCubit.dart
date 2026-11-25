import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class CreateShiprocketOrderState {}

class CreateShiprocketOrderInitial extends CreateShiprocketOrderState {}

class CreateShiprocketOrderProgress extends CreateShiprocketOrderState {}

class CreateShiprocketOrderFailure extends CreateShiprocketOrderState {
  final String errorMessage;
  CreateShiprocketOrderFailure(this.errorMessage);
}

class CreateShiprocketOrderSuccess extends CreateShiprocketOrderState {
  final String successMessage;
  final Parcel parcel;

  CreateShiprocketOrderSuccess({
    required this.successMessage,
    required this.parcel,
  });
}

class CreateShiprocketOrderCubit extends Cubit<CreateShiprocketOrderState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  CreateShiprocketOrderCubit() : super(CreateShiprocketOrderInitial());
  void createShiprocketOrder({required Map<String, dynamic> params}) async {
    emit(CreateShiprocketOrderProgress());

    try {
      final result = await shiprocketRepository.createShiprocketOrder(params);

      emit(
        CreateShiprocketOrderSuccess(
          successMessage: result.message,
          parcel: result.parcel,
        ),
      );
    } catch (e) {
      emit(CreateShiprocketOrderFailure(e.toString()));
    }
  }
}
