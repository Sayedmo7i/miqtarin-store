import 'package:eshopplus_seller/features/profile/pickupLocation/repositories/pickupLocationRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class BulkUpdatePickupLocationState {}

class BulkUpdatePickupLocationInitial extends BulkUpdatePickupLocationState {}

class BulkUpdatePickupLocationProgress extends BulkUpdatePickupLocationState {}

class BulkUpdatePickupLocationFailure extends BulkUpdatePickupLocationState {
  final String errorMessage;
  BulkUpdatePickupLocationFailure(this.errorMessage);
}

class BulkUpdatePickupLocationSuccess extends BulkUpdatePickupLocationState {
  final String successMessage;

  BulkUpdatePickupLocationSuccess({required this.successMessage});
}

class BulkUpdatePickupLocationCubit extends Cubit<BulkUpdatePickupLocationState> {
  final PickupLocationRepository pickupLocationRepository = PickupLocationRepository();

  BulkUpdatePickupLocationCubit() : super(BulkUpdatePickupLocationInitial());

  void bulkUpdatePickupLocation(
      {required Map<String, dynamic> params, required String apiUrl}) async {
    emit(BulkUpdatePickupLocationProgress());

    try {
      final result = await pickupLocationRepository.bulkUpdatePickupLocation(
          params: params, apiUrl: apiUrl);

      emit(BulkUpdatePickupLocationSuccess(
        successMessage: result,
      ));
    } catch (e) {
      emit(BulkUpdatePickupLocationFailure(e.toString()));
    }
  }
}
