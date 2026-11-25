import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/features/orders/models/order.dart';
import 'package:eshopplus_seller/features/orders/repositories/orderRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class OrdersState {}

class OrdersInitial extends OrdersState {}

class OrdersFetchInProgress extends OrdersState {}

class OrdersFetchSuccess extends OrdersState {
  final int total;
  final List<Order> orderList;
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
    required this.orderList,
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
    List<Order>? orderList,
    String? awaiting,
    String? received,
    String? shipped,
    String? delivered,
    String? cancelled,
    String? returned,
    String? processed,
  }) {
    return OrdersFetchSuccess(
      orderList: orderList ?? this.orderList,
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

      List<Order> orderList = [];
      List data = result[ApiURL.dataKey];
      orderList.addAll(data.map((e) => Order.fromJson(e)).toList());

      emit(
        OrdersFetchSuccess(
          orderList: orderList,
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: result[ApiURL.totalKey],
          awaiting: result["awaiting"] ?? "0",
          received: result["received"] ?? "0",
          shipped: result["shipped"] ?? "0",
          delivered: result["delivered"] ?? "0",
          cancelled: result["cancelled"] ?? "0",
          returned: result["returned"] ?? "0",
          processed: result["processed"] ?? "0",
        ),
      );
    } catch (e) {
      emit(OrdersFetchFailure(e.toString()));
    }
  }

  List<Order> getOrderList() {
    if (state is OrdersFetchSuccess) {
      return (state as OrdersFetchSuccess).orderList;
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
      return (state as OrdersFetchSuccess).orderList.length <
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
        params[ApiURL.offsetApiKey] = (state as OrdersFetchSuccess)
            .orderList
            .length
            .toString();
        params[ApiURL.limitApiKey] = loadLimit.toString();

        final moreOrders = await orderRepository.getOrders(params);

        final currentState = (state as OrdersFetchSuccess);

        List<Order> orderList = currentState.orderList;

        List data = moreOrders[ApiURL.dataKey];
        List<Order> newOrderList = data.map((e) => Order.fromJson(e)).toList();

        orderList.addAll(newOrderList);
        emit(
          OrdersFetchSuccess(
            fetchMoreError: false,
            fetchMoreInProgress: false,
            total: moreOrders[ApiURL.totalKey],
            orderList: orderList,
            awaiting: moreOrders["awaiting"] ?? currentState.awaiting,
            received: moreOrders["received"] ?? currentState.received,
            shipped: moreOrders["shipped"] ?? currentState.shipped,
            delivered: moreOrders["delivered"] ?? currentState.delivered,
            cancelled: moreOrders["cancelled"] ?? currentState.cancelled,
            returned: moreOrders["returned"] ?? currentState.returned,
            processed: moreOrders["processed"] ?? currentState.processed,
          ),
        );
      } catch (e) {
        emit(
          (state as OrdersFetchSuccess).copyWith(
            fetchMoreInProgress: false,
            fetchMoreError: true,
          ),
        );
      }
    }
  }

  emitSuccessState(List<Order> orderList) {
    if (state is OrdersFetchSuccess) {
      emit((state as OrdersFetchSuccess).copyWith(orderList: orderList));
      return;
    }
    emit(
      OrdersFetchSuccess(
        fetchMoreError: false,
        fetchMoreInProgress: false,
        total: orderList.length,
        orderList: orderList,
        awaiting: "0",
        received: "0",
        shipped: "0",
        delivered: "0",
        cancelled: "0",
        returned: "0",
        processed: "0",
      ),
    );
  }

  addOrder(int storeId, String orderId) async {
    try {
      Map result = await OrderRepository().getOrders({
        ApiURL.storeIdApiKey: storeId,
        ApiURL.idApiKey: orderId,
      });
      Order order = result[ApiURL.dataKey]
          .map((e) => Order.fromJson(e))
          .toList()
          .first;

      List<Order> orderData = getOrderList();

      int index = orderData.indexWhere((element) => element.id == order.id);
      if (index != -1) {
        orderData[index] = order;
      } else {
        orderData.insert(0, order);
      }

      if (state is OrdersFetchSuccess) {
        emit(
          (state as OrdersFetchSuccess).copyWith(
            orderList: orderData,
            awaiting:
                result["awaiting"] ?? (state as OrdersFetchSuccess).awaiting,
            received:
                result["received"] ?? (state as OrdersFetchSuccess).received,
            shipped: result["shipped"] ?? (state as OrdersFetchSuccess).shipped,
            delivered:
                result["delivered"] ?? (state as OrdersFetchSuccess).delivered,
            cancelled:
                result["cancelled"] ?? (state as OrdersFetchSuccess).cancelled,
            returned:
                result["returned"] ?? (state as OrdersFetchSuccess).returned,
            processed:
                result["processed"] ?? (state as OrdersFetchSuccess).processed,
          ),
        );
      } else {
        emitSuccessState(orderData);
      }
    } catch (e) {}
  }

  resetState() {
    emit(OrdersInitial());
  }
}
