import 'package:flutter_bloc/flutter_bloc.dart';
import '../repositories/addDeleteBrandRepository.dart';

abstract class DeleteBrandState {}

class DeleteBrandInitial extends DeleteBrandState {}

class DeleteBrandProgress extends DeleteBrandState {}

class DeleteBrandSuccess extends DeleteBrandState {
  final String message;
  final String brandId;
  DeleteBrandSuccess({required this.message, required this.brandId});
}

class DeleteBrandFailure extends DeleteBrandState {
  final String errorMessage;
  DeleteBrandFailure({required this.errorMessage});
}

class DeleteBrandCubit extends Cubit<DeleteBrandState> {
  DeleteBrandCubit() : super(DeleteBrandInitial());

  void deleteBrand({required String brandId}) {
    emit(DeleteBrandProgress());
    AddDeleteBrandRepository().deleteBrand(brandId: brandId).then((result) {
      if (!isClosed) {
        emit(DeleteBrandSuccess(
            message: result, brandId: brandId));
      }
    }).catchError((e) {
      if (!isClosed) {
        emit(DeleteBrandFailure(errorMessage: e.toString()));
      }
    });
  }
}