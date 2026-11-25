import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/features/orders/repositories/deliveryBoyRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../models/deliveryBoy.dart';

abstract class DeliveryBoyState {}

class DeliveryBoyInitial extends DeliveryBoyState {}

class DeliveryBoyFetchInProgress extends DeliveryBoyState {}

class DeliveryBoyFetchSuccess extends DeliveryBoyState {
  final int total;
  final List<DeliveryBoy> deliveryBoys;
  final bool fetchMoreError;
  final bool fetchMoreInProgress;

  DeliveryBoyFetchSuccess({
    required this.deliveryBoys,
    required this.fetchMoreError,
    required this.fetchMoreInProgress,
    required this.total,
  });

  DeliveryBoyFetchSuccess copyWith({
    bool? fetchMoreError,
    bool? fetchMoreInProgress,
    int? total,
    List<DeliveryBoy>? deliveryBoys,
  }) {
    return DeliveryBoyFetchSuccess(
      deliveryBoys: deliveryBoys ?? this.deliveryBoys,
      fetchMoreError: fetchMoreError ?? this.fetchMoreError,
      fetchMoreInProgress: fetchMoreInProgress ?? this.fetchMoreInProgress,
      total: total ?? this.total,
    );
  }
}

class DeliveryBoyFetchFailure extends DeliveryBoyState {
  final String errorMessage;

  DeliveryBoyFetchFailure(this.errorMessage);
}

class DeliveryBoyCubit extends Cubit<DeliveryBoyState> {
  final DeliveryBoyRepository _deliveryBoyRepository = DeliveryBoyRepository();

  DeliveryBoyCubit() : super(DeliveryBoyInitial());

  void getDeliveryBoys({
    required Map<String, dynamic> params,
  }) async {
    emit(DeliveryBoyFetchInProgress());
    try {
      final result = await _deliveryBoyRepository.getDeliveryBoys(
        params: params,
      );
      emit(DeliveryBoyFetchSuccess(
        deliveryBoys: result.deliveryBoys,
        fetchMoreError: false,
        fetchMoreInProgress: false,
        total: result.total,
      ));
    } catch (e) {
      emit(DeliveryBoyFetchFailure(e.toString()));
    }
  }

  List<DeliveryBoy> getDeliveryBoyList() {
    if (state is DeliveryBoyFetchSuccess) {
      return (state as DeliveryBoyFetchSuccess).deliveryBoys;
    }
    return [];
  }

  bool fetchMoreError() {
    if (state is DeliveryBoyFetchSuccess) {
      return (state as DeliveryBoyFetchSuccess).fetchMoreError;
    }
    return false;
  }

  bool hasMore() {
    if (state is DeliveryBoyFetchSuccess) {
      return (state as DeliveryBoyFetchSuccess).deliveryBoys.length <
          (state as DeliveryBoyFetchSuccess).total;
    }
    return false;
  }

  void loadMore({
    required Map<String, dynamic> params,
  }) async {
    if (state is DeliveryBoyFetchSuccess) {
      if ((state as DeliveryBoyFetchSuccess).fetchMoreInProgress) {
        return;
      }
      try {
        emit((state as DeliveryBoyFetchSuccess).copyWith(fetchMoreInProgress: true));
        params.addAll(
            {ApiURL.offsetApiKey: (state as DeliveryBoyFetchSuccess).deliveryBoys.length});
        final moreDeliveryBoys = await _deliveryBoyRepository.getDeliveryBoys(
          params: params,
        );

        final currentState = (state as DeliveryBoyFetchSuccess);

        List<DeliveryBoy> deliveryBoys = currentState.deliveryBoys;

        deliveryBoys.addAll(moreDeliveryBoys.deliveryBoys);

        emit(DeliveryBoyFetchSuccess(
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: moreDeliveryBoys.total,
          deliveryBoys: deliveryBoys,
        ));
      } catch (e) {
        emit((state as DeliveryBoyFetchSuccess)
            .copyWith(fetchMoreInProgress: false, fetchMoreError: true));
      }
    }
  }

  emitSuccessState(List<DeliveryBoy> deliveryBoys) {
    if (state is DeliveryBoyFetchSuccess) {
      emit((state as DeliveryBoyFetchSuccess).copyWith(deliveryBoys: deliveryBoys));
      return;
    }
    emit(DeliveryBoyFetchSuccess(
      fetchMoreError: false,
      fetchMoreInProgress: false,
      total: deliveryBoys.length,
      deliveryBoys: deliveryBoys,
    ));
    return;
  }

  resetState() {
    emit(DeliveryBoyInitial());
  }
}
