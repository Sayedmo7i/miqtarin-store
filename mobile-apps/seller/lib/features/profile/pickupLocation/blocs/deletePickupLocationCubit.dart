import 'package:eshopplus_seller/features/profile/pickupLocation/repositories/pickupLocationRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class DeletePickupLocationState {}

class DeletePickupLocationInitial extends DeletePickupLocationState {}

class DeletePickupLocationProgress extends DeletePickupLocationState {}

class DeletePickupLocationFailure extends DeletePickupLocationState {
  final String errorMessage;
  DeletePickupLocationFailure(this.errorMessage);
}

class DeletePickupLocationSuccess extends DeletePickupLocationState {
  final String successMessage;
  final String pickupLocationId;
  DeletePickupLocationSuccess({required this.successMessage, required this.pickupLocationId});
}

class DeletePickupLocationCubit extends Cubit<DeletePickupLocationState> {
  DeletePickupLocationCubit() : super(DeletePickupLocationInitial());

  void deletePickupLocation({required String pickupLocationId}) async {
    emit(DeletePickupLocationProgress());

    try {
      await PickupLocationRepository().deletePickupLocation(pickupLocationId: pickupLocationId).then((value) {
        emit(DeletePickupLocationSuccess(successMessage: value, pickupLocationId: pickupLocationId));
      });
    } catch (e) {
      emit(DeletePickupLocationFailure(e.toString()));
    }
  }
}
