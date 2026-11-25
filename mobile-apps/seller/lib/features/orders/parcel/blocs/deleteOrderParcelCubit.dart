import 'package:eshopplus_seller/features/orders/parcel/repositories/parcelRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class DeleteParcelState {}

class DeleteParcelInitial extends DeleteParcelState {}

class DeleteParcelProgress extends DeleteParcelState {
  final int parcelId;
  DeleteParcelProgress({required this.parcelId});
}

class DeleteParcelFailure extends DeleteParcelState {
  final String errorMessage;
  DeleteParcelFailure(this.errorMessage);
}

class DeleteParcelSuccess extends DeleteParcelState {
  final int parcelId;
  final String successMessage;
  DeleteParcelSuccess({required this.parcelId, required this.successMessage});
}

class DeleteParcelCubit extends Cubit<DeleteParcelState> {
  final ParcelRepository parcelRepository = ParcelRepository();
  DeleteParcelCubit() : super(DeleteParcelInitial());
  void deleteParcel({required int parcelId}) async {
    emit(DeleteParcelProgress(parcelId: parcelId));
    var result;
    try {
      result = await parcelRepository.deleteOrderParcel(parcelId: parcelId);
      emit(DeleteParcelSuccess(parcelId: parcelId, successMessage: result));
    } catch (e) {
      emit(DeleteParcelFailure(e.toString()));
    }
  }
}
