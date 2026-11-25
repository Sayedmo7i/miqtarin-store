import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/constants/appConstants.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/widgets/deliveryBoySelectionDialog.dart';
import 'package:eshopplus_seller/features/orders/widgets/orderTrackingContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customImageWidget.dart';
import 'package:eshopplus_seller/commons/widgets/customTextFieldContainer.dart';
import 'package:eshopplus_seller/utils/inputValidators.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../blocs/orderUpdateCubit.dart';
import '../../models/deliveryBoy.dart';
import '../../models/order.dart';

import '../../../../../utils/designConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import '../../../../utils/utils.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import '../../../../commons/widgets/customRoundedButton.dart';
import '../../../../commons/widgets/customTextContainer.dart';

class ParcelItemsContainer extends StatefulWidget {
  Parcel parcel;
  final Order order;
  final Function callback;

  ParcelItemsContainer({
    Key? key,
    required this.parcel,
    required this.order,
    required this.callback,
  }) : super(key: key);

  @override
  ParcelItemsContainerState createState() => ParcelItemsContainerState();
}

class ParcelItemsContainerState extends State<ParcelItemsContainer> {
  Map<String, TextEditingController> controllers = {};
  String? selectedDeliveryBoyId;
  DeliveryBoy? selectedDeliveryBoy; // Store the selected delivery boy object

  final scrollController = ScrollController();
  late BuildContext customerdialogContext;
  final List formFields = [orderStatusKey, chooseDeliveryBoyKey];
  Map<String, String> statusTypes = {'': selectStatusKey};
  late bool isDelivered = widget.parcel.activeStatus == deliveredStatusType;
  // Status sequence: received -> processed -> shipped -> delivered
  final List<String> statusSequence = [
    receivedStatusType,
    processedStatusType,
    shippedStatusType,
    deliveredStatusType,
  ];
  @override
  void initState() {
    super.initState();

    // Initialize controllers first
    for (var key in formFields) {
      controllers[key] = TextEditingController();
    }

    // Update allowed statuses based on current parcel status
    _updateAllowedStatuses();

    controllers[chooseDeliveryBoyKey]!.text = context
        .read<SettingsAndLanguagesCubit>()
        .getTranslatedValue(labelKey: chooseDeliveryBoyKey);

    // Set the controller to the current status if it exists and is valid in statusTypes
    String initialStatus = widget.parcel.activeStatus ?? '';
    if (initialStatus.isNotEmpty && statusTypes.containsKey(initialStatus)) {
      controllers[orderStatusKey]!.text = initialStatus;
    } else if (statusTypes.isNotEmpty) {
      // If current status is not in the dropdown options, default to the first available option
      controllers[orderStatusKey]!.text = statusTypes.keys.first;
    }
    if (widget.parcel.deliveryBoyDetails != null) {
      controllers[chooseDeliveryBoyKey]!.text =
          widget.parcel.deliveryBoyDetails!.username ??
          widget.parcel.deliveryBoyDetails!.name ??
          '';
      selectedDeliveryBoyId = widget.parcel.deliveryBoyDetails!.id.toString();
      selectedDeliveryBoy =
          widget.parcel.deliveryBoyDetails; // Store the delivery boy object
    }
  }

  /// Updates the allowed statuses to show all statuses
  void _updateAllowedStatuses() {
    statusTypes.clear();

    // Add all statuses from the sequence to show them in dropdown
    for (String status in statusSequence) {
      if (orderStatusTypes.containsKey(status)) {
        statusTypes[status] = orderStatusTypes[status]!;
      }
    }

    // If current status is not in sequence, add it to dropdown for display
    String currentStatus = widget.parcel.activeStatus ?? '';
    if (currentStatus.isNotEmpty &&
        !statusTypes.containsKey(currentStatus) &&
        orderStatusTypes.containsKey(currentStatus)) {
      statusTypes[currentStatus] = orderStatusTypes[currentStatus]!;
    }
  }

  /// Checks if the update button should be disabled
  bool _isUpdateButtonDisabled() {
    String selectedStatus = controllers[orderStatusKey]!.text.trim();
    String currentStatus = widget.parcel.activeStatus ?? '';

    // Disable if no status selected or same status selected
    return selectedStatus.isEmpty || selectedStatus == currentStatus;
  }

  /// Checks if a status should be disabled (previous or current status)
  bool _isStatusDisabled(String status) {
    String currentStatus = widget.parcel.activeStatus ?? '';

    if (currentStatus.isEmpty) {
      return false; // No restrictions if no current status
    }

    int currentIndex = statusSequence.indexOf(currentStatus);
    int statusIndex = statusSequence.indexOf(status);

    // Disable if it's a previous status or current status
    return statusIndex != -1 &&
        currentIndex != -1 &&
        statusIndex <= currentIndex;
  }

  /// Builds the custom status dropdown with disabled items
  Widget _buildStatusDropdown() {
    return Padding(
      padding: const .symmetric(vertical: 7.5),
      child: Column(
        crossAxisAlignment: .start,
        children: [
          CustomTextContainer(
            textKey: parcelStatusKey,
            style: Theme.of(
              context,
            ).textTheme.bodyMedium!.copyWith(fontWeight: .w500),
          ),
          const SizedBox(height: 10),
          Container(
            width: MediaQuery.of(context).size.width,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(borderRadius),
              border: Border.all(color: borderColor.withValues(alpha: 0.4)),
            ),
            padding: const EdgeInsetsDirectional.symmetric(horizontal: 15),
            child: DropdownButton<String>(
              style: Theme.of(context).textTheme.bodyMedium,
              isExpanded: true,
              dropdownColor: whiteColor,
              borderRadius: BorderRadius.circular(8),
              underline: const SizedBox(),
              icon: Icon(
                Icons.keyboard_arrow_down_sharp,
                color: Theme.of(context).colorScheme.secondary,
              ),
              items: statusTypes.keys.map((String statusKey) {
                bool isDisabled = _isStatusDisabled(statusKey);
                return DropdownMenuItem<String>(
                  value: statusKey,
                  enabled: !isDisabled, // Disable previous and current statuses
                  child: CustomTextContainer(
                    textKey: statusTypes[statusKey]!,
                    style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                      color: isDisabled
                          ? Theme.of(
                              context,
                            ).colorScheme.secondary.withValues(alpha: 0.5)
                          : Theme.of(context).colorScheme.secondary,
                    ),
                  ),
                );
              }).toList(),
              onChanged: (String? value) {
                if (value != null && !_isStatusDisabled(value)) {
                  setState(() {
                    controllers[orderStatusKey]!.text = value;
                  });
                }
              },
              value: controllers[orderStatusKey]!.text.isNotEmpty
                  ? controllers[orderStatusKey]!.text
                  : null,
            ),
          ),
        ],
      ),
    );
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
        if (!widget.order.shiprocketOrder)
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
                    textKey: parcelManagementKey,
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
                      _buildStatusDropdown(),

                      _buildDeliveryBoyWidget(),

                      DesignConfig.defaultHeightSizedBox,
                      _buildUpdateButton(),
                      if (!isDelivered) ...[
                        DesignConfig.defaultHeightSizedBox,
                        _buildOrderTrackingButton(),
                      ],
                    ],
                  ),
                ),
              ],
            ),
          ),
        DesignConfig.smallHeightSizedBox,
        buildParcelItemsContainer(),
      ],
    );
  }

  GestureDetector _buildOrderTrackingButton() {
    return GestureDetector(
      onTap: () => Utils.openModalBottomSheet(
        context,
        staticContent: false,
        BlocProvider<OrderUpdateCubit>(
          create: (context) => OrderUpdateCubit(),
          child: OrderTrackingContainer(
            parcel: widget.parcel,
            updatedItem: (updatedparcel) {
              widget.parcel = updatedparcel;
              Navigator.of(context).pop();
            },
          ),
        ),
      ),
      child: Row(
        mainAxisAlignment: .center,
        children: <Widget>[
          Icon(
            Icons.location_on_outlined,
            color: Theme.of(context).colorScheme.primary,
          ),
          CustomTextContainer(
            textKey: addOrderTrackingKey,
            style: Theme.of(context).textTheme.titleMedium!.copyWith(
              color: Theme.of(context).colorScheme.primary,
            ),
          ),
        ],
      ),
    );
  }

  BlocConsumer<OrderUpdateCubit, OrderUpdateState> _buildUpdateButton() {
    return BlocConsumer<OrderUpdateCubit, OrderUpdateState>(
      listener: (context, state) {
        if (state is OrderUpdateFailure && state.type == 1) {
          Utils.showSnackBar(message: state.errorMessage);
        } else if (state is OrderUpdateSuccess && state.type == 1) {
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
                backgroundColor: _isUpdateButtonDisabled()
                    ? Theme.of(
                        context,
                      ).colorScheme.secondary.withValues(alpha: 0.5)
                    : Theme.of(context).colorScheme.primary,
                onTap: _isUpdateButtonDisabled()
                    ? null
                    : () {
                        updateStatus();
                      },
              );
      },
    );
  }

  CustomTextFieldContainer _buildDeliveryBoyWidget() {
    return CustomTextFieldContainer(
      hintTextKey: chooseDeliveryBoyKey,
      textEditingController: controllers[chooseDeliveryBoyKey]!,
      labelKey: chooseDeliveryBoyKey,
      textInputAction: TextInputAction.next,
      isFieldValueMandatory: false,
      focusNode: AlwaysDisabledFocusNode(),
      isSetValidator: true,
      errmsg: selectStoreKey,
      suffixWidget: Icon(
        Icons.arrow_drop_down,
        color: isDelivered
            ? Theme.of(context).colorScheme.secondary.withValues(alpha: 0.5)
            : Theme.of(context).colorScheme.secondary,
      ),
      onTap: isDelivered
          ? null
          : () {
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (BuildContext context) {
                  customerdialogContext = context;

                  return AlertDialog(
                    insetPadding: const .all(appContentHorizontalPadding),
                    backgroundColor: whiteColor,
                    contentPadding: const .symmetric(
                      horizontal: 8,
                      vertical: 5,
                    ),
                    shape: DesignConfig.setRoundedBorder(whiteColor, 10, false),
                    content: StatefulBuilder(
                      builder: (context, StateSetter setState) {
                        return DeliveryBoySelectionDialog(
                          onCustomerSelect: (DeliveryBoy deliveryBoy) {
                            if (selectedDeliveryBoyId !=
                                deliveryBoy.id.toString()) {
                              selectedDeliveryBoyId = deliveryBoy.id.toString();
                              selectedDeliveryBoy =
                                  deliveryBoy; // Store the selected delivery boy object
                              setState(() {
                                controllers[chooseDeliveryBoyKey]!.text =
                                    deliveryBoy.name ?? '';
                              });
                            }
                            Navigator.pop(customerdialogContext);
                          },
                        );
                      },
                    ),
                  );
                },
              );
            },
    );
  }

  updateStatus() {
    String selectedStatus = controllers[orderStatusKey]!.text.trim();

    // Check if update is allowed
    if (_isUpdateButtonDisabled()) {
      if (selectedStatus.isEmpty) {
        Utils.showSnackBar(message: pleaseSelectStatusKey);
      } else {
        Utils.showSnackBar(message: cannotUpdateToSameStatusKey);
      }
      return;
    }

    // Check if selected status is disabled
    if (_isStatusDisabled(selectedStatus)) {
      Utils.showSnackBar(message: cannotChangeToPreviousOrCurrentStatusKey);
      return;
    }

    // Check if delivery boy is required for the selected status
    if ((selectedStatus == shippedStatusType ||
            selectedStatus == deliveredStatusType) &&
        (selectedDeliveryBoyId == null ||
            selectedDeliveryBoyId!.trim().isEmpty)) {
      Utils.showSnackBar(message: selectDeliveryBoyKey);
      return;
    }

    Map<String, String> params = {
      ApiURL.parcelIdApiKey: widget.parcel.id.toString(),
    };

    params[ApiURL.statusApiKey] = selectedStatus;

    if (selectedDeliveryBoyId != null &&
        selectedDeliveryBoyId!.trim().isNotEmpty) {
      params[ApiURL.deliverByApiKey] = selectedDeliveryBoyId ?? "0";
    }

    BlocProvider.of<OrderUpdateCubit>(
      context,
    ).updateOrder(context, params, 1, ApiURL.updateParcelOrderStatus);
  }

  statusUpdateSuccess(OrderUpdateSuccess state) {
    if (selectedDeliveryBoyId != null && selectedDeliveryBoy != null) {
      widget.parcel.deliveryBoyId = selectedDeliveryBoyId;
      widget.parcel.deliveryBoyDetails =
          selectedDeliveryBoy; // Use the stored delivery boy object
    }
    widget.parcel.activeStatus = state.status;

    // Update allowed statuses after successful status update
    setState(() {
      _updateAllowedStatuses();
      controllers[orderStatusKey]!.text = state.status;
    });

    List<int> orderItemIds = widget.parcel.items!
        .map((e) => e.orderItemId!)
        .toList();
    for (int i = 0; i < orderItemIds.length; i++) {
      OrderItems item = widget.order.orderItems!.firstWhere(
        (element) => element.id == orderItemIds[i],
      );

      item.activeStatus = state.status;
    }
    widget.callback(widget.parcel);
  }

  buildParcelItemsContainer() {
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
                  textKey: parcelItemsKey,
                  style: Theme.of(context).textTheme.titleMedium,
                ),
              ),
              const Divider(height: 12, thickness: 0.5),
              ListView.separated(
                separatorBuilder: (context, index) =>
                    DesignConfig.smallHeightSizedBox,
                shrinkWrap: true,
                padding: .zero,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: widget.parcel.items!.length,
                itemBuilder: (context, index) {
                  OrderItems orderItem =
                      widget.parcel.items![index].orderData!.first;

                  return Container(
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
                                      style: Theme.of(
                                        context,
                                      ).textTheme.titleSmall!,
                                    ),
                                    DesignConfig.smallHeightSizedBox,
                                    Utils.buildStatusWidget(
                                      context,
                                      orderItem.activeStatus!,
                                    ),

                                    const SizedBox(height: 4),
                                    Wrap(
                                      spacing: 8,
                                      runSpacing: 8,
                                      children: [
                                        Utils.buildVariantList(
                                          context,
                                          orderItem,
                                        ),
                                        CustomTextContainer(
                                          textKey:
                                              Utils.priceWithCurrencySymbol(
                                                price:
                                                    double.tryParse(
                                                      orderItem.price
                                                          .toString(),
                                                    ) ??
                                                    0,
                                                context: context,
                                              ),
                                          style: Theme.of(context)
                                              .textTheme
                                              .titleMedium
                                              ?.copyWith(
                                                color: Theme.of(
                                                  context,
                                                ).colorScheme.primary,
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
                      ],
                    ),
                  );
                },
              ),
            ],
          ),
        ),
        DesignConfig.smallHeightSizedBox,
      ],
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
