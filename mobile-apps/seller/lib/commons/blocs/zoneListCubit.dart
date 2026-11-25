import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../models/zone.dart';

abstract class ZoneListState {}

class ZoneListInitial extends ZoneListState {}

class ZoneListFetchInProgress extends ZoneListState {}

class ZoneListFetchSuccess extends ZoneListState {
  final int total;
  final List<Zone> zones;
  final bool fetchMoreError;
  final bool fetchMoreInProgress;

  ZoneListFetchSuccess({
    required this.zones,
    required this.fetchMoreError,
    required this.fetchMoreInProgress,
    required this.total,
  });

  ZoneListFetchSuccess copyWith({
    bool? fetchMoreError,
    bool? fetchMoreInProgress,
    int? total,
    List<Zone>? zones,
  }) {
    return ZoneListFetchSuccess(
      zones: zones ?? this.zones,
      fetchMoreError: fetchMoreError ?? this.fetchMoreError,
      fetchMoreInProgress: fetchMoreInProgress ?? this.fetchMoreInProgress,
      total: total ?? this.total,
    );
  }
}

class ZoneListFetchFailure extends ZoneListState {
  final String errorMessage;

  ZoneListFetchFailure(this.errorMessage);
}

class ZoneListCubit extends Cubit<ZoneListState> {
  ZoneListCubit() : super(ZoneListInitial());

  void getZoneList(
    Map<String, String?> params) async {
    
     
    

    emit(ZoneListFetchInProgress());
    try {
      final result = await getZoneListProcess(params);

      List data = result[ApiURL.dataKey];
      List<Zone> zones = data.map((e) => Zone.fromJson(e)).toList();
      int total = result[ApiURL.totalKey];

      emit(ZoneListFetchSuccess(
        zones: zones,
        fetchMoreError: false,
        fetchMoreInProgress: false,
        total: total,
      ));
    } catch (e) {
      emit(ZoneListFetchFailure(e.toString()));
    }
  }

  List<Zone> getZonesList() {
    if (state is ZoneListFetchSuccess) {
      return (state as ZoneListFetchSuccess).zones;
    }
    return [];
  }

  bool fetchMoreError() {
    if (state is ZoneListFetchSuccess) {
      return (state as ZoneListFetchSuccess).fetchMoreError;
    }
    return false;
  }

  bool hasMore() {
    if (state is ZoneListFetchSuccess) {
      return (state as ZoneListFetchSuccess).zones.length <
          (state as ZoneListFetchSuccess).total;
    }
    return false;
  }

  void loadMore({
    required Map<String, String?> params,
  }) async {
    if (state is ZoneListFetchSuccess) {
      if ((state as ZoneListFetchSuccess).fetchMoreInProgress) {
        return;
      }
      try {
        emit((state as ZoneListFetchSuccess).copyWith(fetchMoreInProgress: true));
        params[ApiURL.offsetApiKey] = (state as ZoneListFetchSuccess).zones.length.toString();

        final result = await getZoneListProcess(params);

        final currentState = (state as ZoneListFetchSuccess);

        List<Zone> zones = List.from(currentState.zones);

        List data = result[ApiURL.dataKey];
        List<Zone> newZones = data.map((e) => Zone.fromJson(e)).toList();

        zones.addAll(newZones);
        int total = result[ApiURL.totalKey];

        emit(ZoneListFetchSuccess(
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: total,
          zones: zones,
        ));
      } catch (e) {
        emit((state as ZoneListFetchSuccess)
            .copyWith(fetchMoreInProgress: false, fetchMoreError: true));
      }
    }
  }

  emitSuccessState(List<Zone> zones) {
    if (state is ZoneListFetchSuccess) {
      emit((state as ZoneListFetchSuccess).copyWith(zones: zones));
      return;
    }
    emit(ZoneListFetchSuccess(
      fetchMoreError: false,
      fetchMoreInProgress: false,
      total: zones.length,
      zones: zones,
    ));
    return;
  }

  resetState() {
    emit(ZoneListInitial());
  }

  Future getZoneListProcess(Map<String, String?> parameter) async {
    try {
      final result = await Api.get(
          url: ApiURL.getZones, useAuthToken: true, queryParameters: parameter);
      return result;
    } catch (e) {
      throw ApiException(e.toString());
    }
  }
}
