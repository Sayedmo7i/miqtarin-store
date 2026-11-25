import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/repositories/pickupLocationRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../models/location.dart';

abstract class GetPickupLocationState {}

class GetPickupLocationInitial extends GetPickupLocationState {}

class GetPickupLocationFetchInProgress extends GetPickupLocationState {}

class GetPickupLocationFetchSuccess extends GetPickupLocationState {
  final int total;
  final List<Location> locations;
  final bool fetchMoreError;
  final bool fetchMoreInProgress;

  GetPickupLocationFetchSuccess({
    required this.locations,
    required this.fetchMoreError,
    required this.fetchMoreInProgress,
    required this.total,
  });

  GetPickupLocationFetchSuccess copyWith({
    bool? fetchMoreError,
    bool? fetchMoreInProgress,
    int? total,
    List<Location>? locations,
  }) {
    return GetPickupLocationFetchSuccess(
      locations: locations ?? this.locations,
      fetchMoreError: fetchMoreError ?? this.fetchMoreError,
      fetchMoreInProgress: fetchMoreInProgress ?? this.fetchMoreInProgress,
      total: total ?? this.total,
    );
  }
}

class GetPickupLocationFetchFailure extends GetPickupLocationState {
  final String errorMessage;

  GetPickupLocationFetchFailure(this.errorMessage);
}

class GetPickupLocationCubit extends Cubit<GetPickupLocationState> {
  final PickupLocationRepository _pickupLocationRepository = PickupLocationRepository();

  GetPickupLocationCubit() : super(GetPickupLocationInitial());

  void getPickupLocations({
    required Map<String, dynamic> params,
  }) async {
    emit(GetPickupLocationFetchInProgress());
    try {
      final result = await _pickupLocationRepository.getPickupLocations(
        params: params,
      );
      emit(GetPickupLocationFetchSuccess(
        locations: result.locations,
        fetchMoreError: false,
        fetchMoreInProgress: false,
        total: result.total,
      ));
    } catch (e) {
      emit(GetPickupLocationFetchFailure(e.toString()));
    }
  }

  List<Location> getLocationList() {
    if (state is GetPickupLocationFetchSuccess) {
      return (state as GetPickupLocationFetchSuccess).locations;
    }
    return [];
  }

  bool fetchMoreError() {
    if (state is GetPickupLocationFetchSuccess) {
      return (state as GetPickupLocationFetchSuccess).fetchMoreError;
    }
    return false;
  }

  bool hasMore() {
    if (state is GetPickupLocationFetchSuccess) {
      return (state as GetPickupLocationFetchSuccess).locations.length <
          (state as GetPickupLocationFetchSuccess).total;
    }
    return false;
  }

  void loadMore({
    required Map<String, dynamic> params,
  }) async {
    if (state is GetPickupLocationFetchSuccess) {
      if ((state as GetPickupLocationFetchSuccess).fetchMoreInProgress) {
        return;
      }
      try {
        emit((state as GetPickupLocationFetchSuccess).copyWith(fetchMoreInProgress: true));
        params.addAll(
            {ApiURL.offsetApiKey: (state as GetPickupLocationFetchSuccess).locations.length});
        final moreLocations = await _pickupLocationRepository.getPickupLocations(
          params: params,
        );

        final currentState = (state as GetPickupLocationFetchSuccess);

        List<Location> locations = currentState.locations;

        locations.addAll(moreLocations.locations);

        emit(GetPickupLocationFetchSuccess(
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: moreLocations.total,
          locations: locations,
        ));
      } catch (e) {
        emit((state as GetPickupLocationFetchSuccess)
            .copyWith(fetchMoreInProgress: false, fetchMoreError: true));
      }
    }
  }

 
emisSuccessState(List<Location> locations, {int? total}) {
      if (state is GetPickupLocationFetchSuccess) {
      emit((state as GetPickupLocationFetchSuccess)
          .copyWith(locations: locations, total: total));
      return;
    }
    emit(GetPickupLocationFetchSuccess(
      fetchMoreError: false,
      fetchMoreInProgress: false,
      total: locations.length,
      locations: locations,
    ));
    return;
  }
  resetState() {
    emit(GetPickupLocationInitial());
  }
}
