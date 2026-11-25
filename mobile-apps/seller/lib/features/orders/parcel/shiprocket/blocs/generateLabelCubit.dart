import 'package:eshopplus_seller/features/orders/parcel/shiprocket/repositories/shiprocketRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class GenerateLabelState {}

class GenerateLabelInitial extends GenerateLabelState {}

class GenerateLabelProgress extends GenerateLabelState {}

class GenerateLabelFailure extends GenerateLabelState {
  final String errorMessage;
  GenerateLabelFailure(this.errorMessage);  
}

class GenerateLabelSuccess extends GenerateLabelState {
  final String label;

  GenerateLabelSuccess({required this.label});
}

class GenerateLabelCubit extends Cubit<GenerateLabelState> {
  final ShiprocketRepository shiprocketRepository = ShiprocketRepository();
  GenerateLabelCubit() : super(GenerateLabelInitial());
  void generateLabel({required String shipmentId}) async {
    emit(GenerateLabelProgress());

    try {
      final result = await shiprocketRepository.generateLabel(
        shipmentId: shipmentId,
      );

      emit(GenerateLabelSuccess(label: result));
    } catch (e) {
      emit(GenerateLabelFailure(e.toString()));
    }
  }
}
