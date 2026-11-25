import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customDropDownContainer.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/constants/appConstants.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/commons/widgets/customImageWidget.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../blocs/orderUpdateCubit.dart';
import '../models/order.dart';

import '../../../utils/designConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import '../../../utils/utils.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import '../../../commons/widgets/customRoundedButton.dart';
import '../../../commons/widgets/customTextContainer.dart';

class OrderItemsContainer extends StatefulWidget {
  final Order order;
  final List<Parcel> parcels;
  final Function callback;
  final Function editSelectedOrders;

  OrderItemsContainer({
    Key? key,
    required this.order,
    required this.parcels,
    required this.callback,
    required this.editSelectedOrders,
  }) : super(key: key);

  @override
  OrderItemsContainerState createState() => OrderItemsContainerState();
}

class OrderItemsContainerState extends State<OrderItemsContainer> {
  Map<String, TextEditingController> controllers = {};
  String? selectedDeliveryBoyId;

  late OrderItems selectedOrder;
  List<int> selectedOrders = [];
  String? currentSelectedPickupLocation;

  final scrollController = ScrollController();
  late BuildContext customerdialogContext;
  final List formFields = [orderStatusKey];
  Map<String, String> statusTypes = Map.from(orderStatusTypes);
  @override
  void initState() {
    super.initState();
    if (widget.order.type == digitalProductType) {
      statusTypes = {
        '': selectStatusKey,
        receivedStatusType: receivedKey,
        deliveredStatusType: deliveredKey,
      };
    }

    for (var key in formFields) {
      controllers[key] = TextEditingController();
    }

    controllers[orderStatusKey]!.text = context
        .read<SettingsAndLanguagesCubit>()
        .getTranslatedValue(labelKey: statusTypes.entries.first.key);
  }

  changeSelectedItem(int id) {
    selectedOrder = widget.order.orderItems!.firstWhere(
      (element) => element.id == id,
    );

    if (widget.order.shiprocketOrder) {
      // Get the pickup location of the selected item
      String? itemPickupLocation =
          selectedOrder.pickupLocation ?? 'No Pickup Location';

      // If this is a different pickup location than currently selected, clear all selections
      if (currentSelectedPickupLocation != null &&
          currentSelectedPickupLocation != itemPickupLocation) {
        selectedOrders.clear();
      }

      // Update the current selected pickup location
      currentSelectedPickupLocation = itemPickupLocation;

      // Toggle selection for this item
      if (selectedOrders.contains(id)) {
        selectedOrders.remove(id);
        // If no items selected, reset the pickup location
        if (selectedOrders.isEmpty) {
          currentSelectedPickupLocation = null;
        }
      } else {
        selectedOrders.add(id);
      }
    } else {
      // Original behavior when standard shipping is disabled
      if (selectedOrders.contains(id)) {
        selectedOrders.remove(id);
      } else {
        selectedOrders.add(id);
      }
    }

    widget.editSelectedOrders(selectedOrders);
  }

  @override
  void dispose() {
    // Dispose all text editing controllers
    controllers.forEach((key, controller) {
      controller.dispose();
    });

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        if (widget.order.type == digitalProductType)
          Container(
            padding: .symmetric(vertical: appContentVerticalSpace),
            color: Theme.of(context).colorScheme.primaryContainer,
            child: Column(
              crossAxisAlignment: .start,
              children: <Widget>[
                Padding(
                  padding: const .symmetric(
                    horizontal: appContentHorizontalPadding,
                  ),
                  child: CustomTextContainer(
                    textKey: orderStatusKey,
                    style: Theme.of(context).textTheme.titleMedium,
                  ),
                ),
                const Divider(thickness: 0.5),
                Padding(
                  padding: const .symmetric(
                    horizontal: appContentHorizontalPadding,
                  ),
                  child: Column(
                    children: <Widget>[
                      CustomDropDownContainer(
                        labelKey: orderStatusKey,
                        dropDownDisplayLabels: statusTypes.values.toList(),
                        selectedValue: controllers[orderStatusKey]!.text,
                        isFieldValueMandatory: false,
                        onChanged: (value) {
                          setState(() {
                            controllers[orderStatusKey]!.text = value
                                .toString();
                          });
                        },
                        values: statusTypes.keys.toList(),
                      ),
                      DesignConfig.defaultHeightSizedBox,
                      BlocConsumer<OrderUpdateCubit, OrderUpdateState>(
                        listener: (context, state) {
                          if (state is OrderUpdateFailure && state.type == 1) {
                            Navigator.of(context).pop();
                            Utils.showSnackBar(message: state.errorMessage);
                          } else if (state is OrderUpdateSuccess &&
                              state.type == 1) {
                            Navigator.of(context).pop();
                            Utils.showSnackBar(message: state.successMsg);
                            statusUpdateSuccess(state);
                          }
                        },
                        builder: (context, state) {
                          return state is OrderUpdateProgress && state.type == 1
                              ? const CircularProgressIndicator()
                              : CustomRoundedButton(
                                  widthPercentage: 1.0,
                                  buttonTitle: updateKey,
                                  showBorder: false,
                                  backgroundColor: selectedOrders.isEmpty
                                      ? greyColor
                                      : Theme.of(context).colorScheme.primary,
                                  onTap: () {
                                    if (selectedOrders.isNotEmpty) {
                                      updateStatus();
                                    } else {
                                      Utils.showSnackBar(
                                        message: selectProductForStatusKey,
                                      );
                                    }
                                  },
                                );
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        buildOrderItemsContainer(),
      ],
    );
  }

  updateStatus() {
    Map<String, String> params = {
      ApiURL.orderIdApiKey: widget.order.id.toString(),
    };
    if (controllers[orderStatusKey] != null &&
        controllers[orderStatusKey]!.text.trim().isNotEmpty) {
      params[ApiURL.statusApiKey] = controllers[orderStatusKey]!.text;
    } else {
      Utils.showSnackBar(message: pleaseSelectStatusKey);
      return;
    }
    if (widget.order.type == digitalProductType) {
      //for digital product
      //status : received/delivered

      params[ApiURL.orderItemIdsApiKey] = selectedOrders.join(',');
      params[ApiURL.typeApiKey] = 'digital';
    }

    BlocProvider.of<OrderUpdateCubit>(
      context,
    ).updateOrder(context, params, 1, ApiURL.updateParcelOrderStatus);
  }

  statusUpdateSuccess(OrderUpdateSuccess state) {
    List<int> orderItemIds = [];
    if (state.orderItemId != null) {
      orderItemIds.add(int.parse(state.orderItemId!));
    }
    for (int i = 0; i < orderItemIds.length; i++) {
      OrderItems item = widget.order.orderItems!.firstWhere(
        (element) => element.id == orderItemIds[i],
      );

      item.activeStatus = state.status;
      widget.callback(item);
    }
  }

  buildOrderItemsContainer() {
    List<int> parceledItemIds = widget.parcels
        .expand((parcel) => parcel.items!)
        .map((orderItem) => orderItem.orderItemId as int)
        .toList();

    if (widget.order.shiprocketOrder) {
      // Group order items by pickup location
      Map<String, List<OrderItems>> groupedByPickupLocation = {};
      for (var orderItem in widget.order.orderItems!) {
        String pickupLocationKey =
            orderItem.pickupLocation ?? 'No Pickup Location';
        if (!groupedByPickupLocation.containsKey(pickupLocationKey)) {
          groupedByPickupLocation[pickupLocationKey] = [];
        }
        groupedByPickupLocation[pickupLocationKey]!.add(orderItem);
      }

      return Column(
        children: groupedByPickupLocation.entries.map((entry) {
          String pickupLocation = entry.key;
          List<OrderItems> items = entry.value;

          return Container(
            margin: const EdgeInsetsDirectional.only(bottom: 8),
            padding: const EdgeInsetsDirectional.symmetric(
              vertical: appContentHorizontalPadding,
            ),
            color: Theme.of(context).colorScheme.primaryContainer,
            child: Column(
              crossAxisAlignment: .start,
              children: [
                Padding(
                  padding: const EdgeInsetsDirectional.symmetric(
                    horizontal: appContentHorizontalPadding,
                  ),
                  child: Row(
                    children: [
                      CustomTextContainer(
                        textKey: pickupLocationKey,
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        pickupLocation,
                        maxLines: 1,
                        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                          color: Theme.of(context).colorScheme.secondary,
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 12, thickness: 0.5),
                ListView.separated(
                  separatorBuilder: (context, index) =>
                      DesignConfig.smallHeightSizedBox,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: items.length,
                  itemBuilder: (context, index) {
                    OrderItems orderItem = items[index];
                    return buildOrderItem(parceledItemIds, orderItem, context);
                  },
                ),
              ],
            ),
          );
        }).toList(),
      );
    } else {
      // Original ungrouped view
      return Column(
        children: [
          Container(
            padding: const EdgeInsetsDirectional.symmetric(
              vertical: appContentHorizontalPadding,
            ),
            color: Theme.of(context).colorScheme.primaryContainer,
            child: Column(
              crossAxisAlignment: .start,
              children: [
                Padding(
                  padding: const EdgeInsetsDirectional.symmetric(
                    horizontal: appContentHorizontalPadding,
                  ),
                  child: CustomTextContainer(
                    textKey: productsKey,
                    style: Theme.of(context).textTheme.titleMedium,
                  ),
                ),
                const Divider(height: 12, thickness: 0.5),
                ListView.separated(
                  separatorBuilder: (context, index) =>
                      DesignConfig.smallHeightSizedBox,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: widget.order.orderItems!.length,
                  itemBuilder: (context, index) {
                    OrderItems orderItem = widget.order.orderItems![index];

                    return buildOrderItem(parceledItemIds, orderItem, context);
                  },
                ),
              ],
            ),
          ),
        ],
      );
    }
  }

  Widget buildOrderItem(
    List<int> parceledItemIds,
    OrderItems orderItem,
    BuildContext context,
  ) {
    return GestureDetector(
      onTap: () {
        if (!parceledItemIds.contains(orderItem.id) &&
            orderItem.activeStatus != 'cancelled' &&
            orderItem.isAlreadyCancelled != '1') {
          setState(() {
            selectedOrder = orderItem;
            changeSelectedItem(orderItem.id!);
          });
        }
      },
      child: Container(
        padding: const EdgeInsetsDirectional.symmetric(
          horizontal: appContentHorizontalPadding,
        ),
        color: Theme.of(context).colorScheme.primaryContainer,
        child: Row(
          crossAxisAlignment: .start,
          children: [
            Expanded(
              child: Row(
                crossAxisAlignment: .start,
                children: <Widget>[
                  CustomImageWidget(
                    url: orderItem.image ?? "",
                    width: 86,
                    height: 100,
                    borderRadius: 8,
                  ),
                  DesignConfig.smallWidthSizedBox,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: .start,
                      children: <Widget>[
                        //if we are in the selected item, show all the details
                        CustomTextContainer(
                          textKey: orderItem.productName ?? "",
                          maxLines: 2,
                          style: Theme.of(context).textTheme.titleSmall!,
                        ),
                        DesignConfig.smallHeightSizedBox,
                        Utils.buildStatusWidget(
                          context,
                          orderItem.activeStatus!,
                        ),
                        if (orderItem.deliveryBoyName != '')
                          buildTextRichWidget(
                            deliverByKey,
                            orderItem.deliveryBoyName ?? "",
                            TextStyle(
                              color: Theme.of(
                                context,
                              ).colorScheme.secondary.withValues(alpha: 0.8),
                            ),
                            TextStyle(
                              color: Theme.of(
                                context,
                              ).colorScheme.secondary.withValues(alpha: 0.67),
                            ),
                          ),
                        const SizedBox(height: 8),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: [
                            Utils.buildVariantList(context, orderItem),
                            CustomTextContainer(
                              textKey: Utils.priceWithCurrencySymbol(
                                price:
                                    double.tryParse(
                                      orderItem.price.toString(),
                                    ) ??
                                    0,
                                context: context,
                              ),
                              style: Theme.of(context).textTheme.titleMedium
                                  ?.copyWith(
                                    color: Theme.of(
                                      context,
                                    ).colorScheme.primary,
                                  ),
                            ),
                            if (orderItem.attachment != null &&
                                orderItem.attachment!.isNotEmpty)
                              Padding(
                                padding: const EdgeInsetsDirectional.only(
                                  start: 8,
                                ),
                                child: Tooltip(
                                  message: viewAttachmentKey,
                                  child: GestureDetector(
                                    onTap: () {
                                      Utils.launchURL(orderItem.attachment!);
                                    },
                                    child: Icon(
                                      Icons.visibility_outlined,
                                      color: Theme.of(
                                        context,
                                      ).colorScheme.secondary,
                                    ),
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            if (!parceledItemIds.contains(orderItem.id) &&
                orderItem.activeStatus != cancelledStatusType &&
                orderItem.isAlreadyCancelled != '1' &&
                orderItem.activeStatus != deliveredStatusType)
              Checkbox(
                visualDensity: const VisualDensity(
                  vertical: -4,
                  horizontal: -4,
                ),
                value: selectedOrders.contains(orderItem.id),
                onChanged: (value) {
                  setState(() {
                    selectedOrder = orderItem;
                    changeSelectedItem(orderItem.id!);
                  });
                },
              ),
          ],
        ),
      ),
    );
  }

  Text buildTextRichWidget(
    String key,
    String value,
    TextStyle keyStyle,
    TextStyle valueStyle,
  ) {
    return Text.rich(
      TextSpan(
        style: Theme.of(context).textTheme.bodyMedium,
        children: [
          TextSpan(
            text: context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
              labelKey: key,
            ),
            style: keyStyle,
          ),
          const TextSpan(text: ' : '),
          TextSpan(
            text: context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
              labelKey: value,
            ),
            style: valueStyle,
          ),
        ],
      ),
      textAlign: .center,
    );
  }
}
