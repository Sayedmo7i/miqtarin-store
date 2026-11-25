import 'dart:async';
import 'package:eshopplus_deliveryboy/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_deliveryboy/core/constants/appConstants.dart';
import 'package:eshopplus_deliveryboy/core/theme/colors.dart';
import 'package:eshopplus_deliveryboy/features/orders/blocs/orderCubit.dart';
import 'package:eshopplus_deliveryboy/features/orders/models/parcel.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';
import 'package:eshopplus_deliveryboy/features/orders/widgets/orderListWidget.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../commons/blocs/settingsAndLanguagesCubit.dart';

import '../../../utils/designConfig.dart';
import '../../../utils/utils.dart';
import '../../../commons/widgets/customAppbar.dart';
import '../../../commons/widgets/errorScreen.dart';

class OrderScreen extends StatefulWidget {
  const OrderScreen({Key? key}) : super(key: key);

  @override
  OrderScreenState createState() => OrderScreenState();
}

class OrderScreenState extends State<OrderScreen> {
  final scrollController = ScrollController();
  Map<String, String>? apiParameter;

  @override
  void initState() {
    super.initState();
    apiParameter = {};
    setupScrollController();
    orderStatusFilter.remove(receivedStatusType);
    Future.delayed(Duration.zero, () {
      getOrders();
    });
  }

  getOrders() {
    Map<String, dynamic> params = {};
    if (apiParameter != null) {
      params.addAll(apiParameter!);
    }
    context.read<OrdersCubit>().getOrders(params: params);
  }

  void loadMoreOrders() {
    Map<String, dynamic> params = {};
    if (apiParameter != null) {
      params.addAll(apiParameter!);
    }
    context.read<OrdersCubit>().loadMore(params: params);
  }

  setupScrollController() {
    // Remove the scroll listener since we'll use NotificationListener in the ListView
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: const CustomAppbar(
        titleKey: myDeliveryKey,
        showBackButton: false,
      ),
      body: BlocBuilder<OrdersCubit, OrdersState>(
        builder: (context, state) {
          return Column(
            children: <Widget>[
/*OrderSearchBar(
filterCallback: filterOrder, mainFilterValue: apiParameter),
DesignConfig.defaultHeightSizedBox,
buildOrderSatusSection(state),*/
              DesignConfig.defaultHeightSizedBox,
              filterStatusListWidget(),
              DesignConfig.defaultHeightSizedBox,
              Expanded(child: contentWidget(state))
            ],
          );
        },
      ),
    );
  }

  filterOrder(Map<String, String> filterValue, {bool isClearFilter = false}) {
    apiParameter!.clear();
    if (!isClearFilter) {
      apiParameter!.addAll(filterValue);
    }
    getOrders();
  }

  contentWidget(OrdersState state) {
    if (state is OrdersFetchInProgress) {
      return CustomCircularProgressIndicator(indicatorColor: Theme.of(context).colorScheme.primary,);
    } else if (state is OrdersFetchFailure) {
      return ErrorScreen(
          text: state.errorMessage,
          onPressed: getOrders);
    }
    return RefreshIndicator(onRefresh: refreshList, child: listContent(state));
  }

  Future<void> refreshList() async {
    await Future.delayed(const Duration(seconds: 2), () {
      getOrders();
    });
  }

  listContent(OrdersState state) {
    if (state is OrdersFetchSuccess) {
      if (state.parcelList.isEmpty) {
        return ErrorScreen(onPressed: () {}, text: noDataFoundKey);
      }

      bool hasMore = context.read<OrdersCubit>().hasMore();
      bool isLoadingMore = state.fetchMoreInProgress;

      return NotificationListener<ScrollUpdateNotification>(
        onNotification: (notification) {
          if (notification.metrics.pixels >= notification.metrics.maxScrollExtent) {
            if (hasMore && !isLoadingMore) {
              loadMoreOrders();
            }
          }
          return true;
        },
        child: OrderListWidget(
          parcelList: state.parcelList,
          isLoading: hasMore && isLoadingMore,
          scrollController: scrollController,
          callback: (Parcel parcel) {
            updateOrderItem(parcel);
          },
        ),
      );
    }
    return Container();
  }

  updateOrderItem(Parcel parcel) {
    List<Parcel> currentList = context.read<OrdersCubit>().getOrderList();
    int index = currentList.indexWhere((element) => element.id == parcel.id);
    if (index != -1) {
      currentList[index] = parcel;
      context.read<OrdersCubit>().emitSuccessState(currentList);
    }
  }

  filterStatusListWidget() {
    List keys = orderStatusFilter.keys.toList();
    List currFilterStatus = [];
    if (apiParameter!.containsKey("active_status")) {
      currFilterStatus = apiParameter!["active_status"]!.split(",");
    }
    return SizedBox(
      height: 40,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: EdgeInsets.zero,
        separatorBuilder: (context, index) => const SizedBox(width: 8),
        itemCount: orderStatusFilter.length,
        itemBuilder: (context, index) {
          String statustype = keys[index];
          String lbl = orderStatusFilter[statustype] ?? "";
          bool isSelected =
              (statustype.trim().isEmpty && currFilterStatus.isEmpty) ||
                  currFilterStatus.contains(statustype);
          return Padding(
            padding: EdgeInsetsDirectional.only(
                start: index == 0 ? 8 : 0,
                end: index == (orderStatusFilter.length - 1) ? 8 : 0),
            child: ElevatedButton(
              onPressed: () {
                if (!isSelected) {
                  String filterstatus = statustype;
                  if (filterstatus.trim().isNotEmpty &&
                      currFilterStatus.isNotEmpty) {
                    currFilterStatus.clear();
                    currFilterStatus.add(statustype);

                    filterstatus = currFilterStatus.join(",");
                  }

                  filterOrder({"active_status": filterstatus},
                      isClearFilter: filterstatus.trim().isEmpty);
                }
              },
              style: ElevatedButton.styleFrom(
                textStyle: Theme.of(context).textTheme.bodyMedium,
                foregroundColor: isSelected
                    ?whiteColor
                    : Theme.of(context).textTheme.bodyMedium!.color!,
                backgroundColor: isSelected
                    ? Theme.of(context).primaryColor
                    : Theme.of(context).scaffoldBackgroundColor,
                shape: RoundedRectangleBorder(
                    side: BorderSide(
                        color: isSelected
                            ? Theme.of(context).primaryColor
                            : Theme.of(context).textTheme.bodyMedium!.color!),
                    borderRadius: BorderRadius.circular(30)),
              ),
              child: Text(context
                  .read<SettingsAndLanguagesCubit>()
                  .getTranslatedValue(labelKey: lbl)),
            ),
          );
        },
      ),
    );
  }
}
