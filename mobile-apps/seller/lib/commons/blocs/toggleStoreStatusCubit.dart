import 'package:eshopplus_seller/commons/repositories/storeRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class ToggleStoreStatusState {}

class ToggleStoreStatusInitial extends ToggleStoreStatusState {}

class ToggleStoreStatusProgress extends ToggleStoreStatusState {}

class ToggleStoreStatusFailure extends ToggleStoreStatusState {
  final String errorMessage;
  ToggleStoreStatusFailure(this.errorMessage);
}

class ToggleStoreStatusSuccess extends ToggleStoreStatusState {
  final String successMessage;
  final bool isActive;
  ToggleStoreStatusSuccess({
    required this.successMessage,
    required this.isActive,
  });
}

class ToggleStoreStatusCubit extends Cubit<ToggleStoreStatusState> {
  final StoreRepository _storeRepository = StoreRepository();

  ToggleStoreStatusCubit() : super(ToggleStoreStatusInitial());

  void toggleStoreStatus({
    required int storeId,
    required int currentStatus,
  }) async {
    emit(ToggleStoreStatusProgress());
    try {
      // If current status is 1 (active), we deactivate (activate = false)
      // If current status is 0 (inactive), we activate (activate = true)
      final bool shouldActivate = currentStatus == 0;

      final result = await _storeRepository.toggleStoreStatus(
        storeId: storeId,
        activate: shouldActivate,
      );

      emit(ToggleStoreStatusSuccess(
        successMessage: result,
        isActive: shouldActivate,
      ));
    } catch (e) {
      emit(ToggleStoreStatusFailure(e.toString()));
    }
  }
}
