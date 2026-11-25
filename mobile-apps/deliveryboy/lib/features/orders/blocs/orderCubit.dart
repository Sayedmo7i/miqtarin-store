import 'package:eshopplus_deliveryboy/core/api/apiEndPoints.dart';
import 'package:eshopplus_deliveryboy/core/constants/appConstants.dart';
import 'package:eshopplus_deliveryboy/features/orders/models/parcel.dart';
import 'package:eshopplus_deliveryboy/features/orders/repositories/orderRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:eshopplus_deliveryboy/core/configs/appConfig.dart';

abstract class OrdersState {}

class OrdersInitial extends OrdersState {}

class OrdersFetchInProgress extends OrdersState {}

class OrdersFetchSuccess extends OrdersState {
  final int total;
  final List<Parcel> parcelList;
  final bool fetchMoreError;
  final bool fetchMoreInProgress;
  final String awaiting;
  final String received;
  final String shipped;
  final String delivered;
  final String cancelled;
  final String returned;
  final String processed;

  OrdersFetchSuccess({
    required this.parcelList,
    required this.fetchMoreError,
    required this.fetchMoreInProgress,
    required this.total,
    required this.awaiting,
    required this.received,
    required this.shipped,
    required this.delivered,
    required this.cancelled,
    required this.returned,
    required this.processed,
  });

  OrdersFetchSuccess copyWith({
    bool? fetchMoreError,
    bool? fetchMoreInProgress,
    int? total,
    List<Parcel>? parcelList,
    String? awaiting,
    String? received,
    String? shipped,
    String? delivered,
    String? cancelled,
    String? returned,
    String? processed,
  }) {
    return OrdersFetchSuccess(
      parcelList: parcelList ?? this.parcelList,
      fetchMoreError: fetchMoreError ?? this.fetchMoreError,
      fetchMoreInProgress: fetchMoreInProgress ?? this.fetchMoreInProgress,
      total: total ?? this.total,
      awaiting: awaiting ?? this.awaiting,
      received: received ?? this.received,
      shipped: shipped ?? this.shipped,
      delivered: delivered ?? this.delivered,
      cancelled: cancelled ?? this.cancelled,
      returned: returned ?? this.returned,
      processed: processed ?? this.processed,
    );
  }
}

class OrdersFetchFailure extends OrdersState {
  final String errorMessage;

  OrdersFetchFailure(this.errorMessage);
}

class OrdersCubit extends Cubit<OrdersState> {
  final OrderRepository orderRepository;

  OrdersCubit(this.orderRepository) : super(OrdersInitial());

  void getOrders({required Map<String, dynamic> params}) async {
    emit(OrdersFetchInProgress());
    try {
      params[ApiURL.offsetApiKey] = "0";
      params[ApiURL.limitApiKey] = loadLimit.toString();
      
      final result = await orderRepository.getOrders(params);
      
      List<Parcel> parcelList = [];
      List data = result['data'];
      parcelList.addAll(data
          .map((e) => Parcel.fromJson(
              e,
              params.containsKey("active_status") &&
                  params["active_status"] == returnedStatusType))
          .toList());

      emit(OrdersFetchSuccess(
        parcelList: parcelList,
        fetchMoreError: false,
        fetchMoreInProgress: false,
        total: result["total"],
        awaiting: result["awaiting"] ?? "0",
        received: result["received"] ?? "0",
        shipped: result["shipped"] ?? "0",
        delivered: result["delivered"] ?? "0",
        cancelled: result["cancelled"] ?? "0",
        returned: result["returned"] ?? "0",
        processed: result["processed"] ?? "0",
      ));
    } catch (e) {
      emit(OrdersFetchFailure(e.toString()));
    }
  }

  List<Parcel> getOrderList() {
    if (state is OrdersFetchSuccess) {
      return (state as OrdersFetchSuccess).parcelList;
    }
    return [];
  }

  bool fetchMoreError() {
    if (state is OrdersFetchSuccess) {
      return (state as OrdersFetchSuccess).fetchMoreError;
    }
    return false;
  }

  bool hasMore() {
    if (state is OrdersFetchSuccess) {
      return (state as OrdersFetchSuccess).parcelList.length <
          (state as OrdersFetchSuccess).total;
    }
    return false;
  }

  void loadMore({required Map<String, dynamic> params}) async {
    if (state is OrdersFetchSuccess) {
      if ((state as OrdersFetchSuccess).fetchMoreInProgress) {
        return;
      }
      try {
        emit((state as OrdersFetchSuccess).copyWith(fetchMoreInProgress: true));
        params[ApiURL.offsetApiKey] = (state as OrdersFetchSuccess).parcelList.length.toString();
        params[ApiURL.limitApiKey] = loadLimit.toString();
        
        final moreOrders = await orderRepository.getOrders(params);

        final currentState = (state as OrdersFetchSuccess);

        List<Parcel> parcelList = currentState.parcelList;

        List data = moreOrders['data'];
        List<Parcel> newParcelList = data
            .map((e) => Parcel.fromJson(
                e,
                params.containsKey("active_status") &&
                    params["active_status"] == returnedStatusType))
            .toList();

        parcelList.addAll(newParcelList);
        emit(OrdersFetchSuccess(
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: moreOrders["total"],
          parcelList: parcelList,
          awaiting: moreOrders["awaiting"] ?? currentState.awaiting,
          received: moreOrders["received"] ?? currentState.received,
          shipped: moreOrders["shipped"] ?? currentState.shipped,
          delivered: moreOrders["delivered"] ?? currentState.delivered,
          cancelled: moreOrders["cancelled"] ?? currentState.cancelled,
          returned: moreOrders["returned"] ?? currentState.returned,
          processed: moreOrders["processed"] ?? currentState.processed,
        ));
      } catch (e) {
        emit((state as OrdersFetchSuccess)
            .copyWith(fetchMoreInProgress: false, fetchMoreError: true));
      }
    }
  }

  emitSuccessState(List<Parcel> parcelList) {
    if (state is OrdersFetchSuccess) {
      emit((state as OrdersFetchSuccess).copyWith(parcelList: parcelList));
      return;
    }
    emit(OrdersFetchSuccess(
      fetchMoreError: false,
      fetchMoreInProgress: false,
      total: parcelList.length,
      parcelList: parcelList,
      awaiting: "0",
      received: "0",
      shipped: "0",
      delivered: "0",
      cancelled: "0",
      returned: "0",
      processed: "0",
    ));
  }

  resetState() {
    emit(OrdersInitial());
  }
}
