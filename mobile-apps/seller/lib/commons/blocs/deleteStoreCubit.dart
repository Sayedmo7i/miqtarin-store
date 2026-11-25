import 'package:eshopplus_seller/commons/repositories/storeRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class DeleteStoreState {}

class DeleteStoreInitial extends DeleteStoreState {}

class DeleteStoreProgress extends DeleteStoreState {}

class DeleteStoreFailure extends DeleteStoreState {
  final String errorMessage;
  DeleteStoreFailure(this.errorMessage);
}

class DeleteStoreSuccess extends DeleteStoreState {
  final String successMessage;
  DeleteStoreSuccess({required this.successMessage});
}

class DeleteStoreCubit extends Cubit<DeleteStoreState> {
  final StoreRepository _storeRepository = StoreRepository();

  DeleteStoreCubit() : super(DeleteStoreInitial());

  void deleteStore({required int storeId}) async {
    emit(DeleteStoreProgress());
    try {
      final result = await _storeRepository.deleteStore(storeId: storeId);
      emit(DeleteStoreSuccess(successMessage: result));
    } catch (e) {
      emit(DeleteStoreFailure(e.toString()));
    }
  }
}
