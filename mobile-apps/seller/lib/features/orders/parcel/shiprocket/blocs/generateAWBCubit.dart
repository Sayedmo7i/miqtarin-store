import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class GenerateAWBState {}

class GenerateAWBInitial extends GenerateAWBState {}

class GenerateAWBProgress extends GenerateAWBState {}

class GenerateAWBFailure extends GenerateAWBState {
  final String errorMessage;
  GenerateAWBFailure(this.errorMessage);
}

class GenerateAWBSuccess extends GenerateAWBState {
  final String successMessage;
  final Parcel parcel;

  GenerateAWBSuccess({required this.successMessage, required this.parcel});
}

class GenerateAWBCubit extends Cubit<GenerateAWBState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  GenerateAWBCubit() : super(GenerateAWBInitial());
  void generateAWB({required String shipmentId}) async {
    emit(GenerateAWBProgress());

    try {
      final result = await shiprocketRepository.generateAWB(
        shipmentId: shipmentId,
      );

      emit(
        GenerateAWBSuccess(
          successMessage: result.message,
          parcel: result.parcel,
        ),
      );
    } catch (e) {
      emit(GenerateAWBFailure(e.toString()));
    }
  }
}
