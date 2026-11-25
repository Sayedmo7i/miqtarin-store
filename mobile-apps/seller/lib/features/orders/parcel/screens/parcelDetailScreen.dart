import 'package:eshopplus_seller/commons/widgets/safeAreaWithBottomPadding.dart';
import 'package:eshopplus_seller/core/constants/appConstants.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/features/orders/blocs/getInvoiceCubit.dart';
import 'package:eshopplus_seller/features/orders/blocs/orderUpdateCubit.dart';

import 'package:eshopplus_seller/features/orders/models/order.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/RefreshParcelStatusButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/cancelShiprocketOrderButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/createShiprocketOrderButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/generateInvoiceButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/generateLabelButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/geberateAWBButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/sendPickupRequestButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/widgets/trackOrderButton.dart';
import 'package:eshopplus_seller/features/orders/parcel/widgets/parcelDetailsContainer.dart';
import 'package:eshopplus_seller/features/orders/parcel/widgets/parcelItemsContainer.dart';

import 'package:eshopplus_seller/commons/widgets/customAppbar.dart';
import 'package:eshopplus_seller/commons/widgets/customDefaultContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';

import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';
import '../../blocs/deliveryBoyCubit.dart';

class ParcelDetailScreen extends StatefulWidget {
  final Parcel parcel;
  final Order order;
  final ParcelCubit parcelCubit;
  final Function? callback;
  const ParcelDetailScreen({
    Key? key,
    required this.parcel,
    required this.order,
    required this.parcelCubit,
    this.callback,
  }) : super(key: key);
  static Widget getRouteInstance() {
    Map arguments = Get.arguments;
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => GetInvoiceCubit()),
        BlocProvider(create: (context) => DeliveryBoyCubit()),
      ],
      child: ParcelDetailScreen(
        parcel: arguments['parcel'],
        order: arguments['order'],
        parcelCubit: arguments['parcelCubit'],
        callback: arguments.containsKey('callback')
            ? arguments['callback']
            : null,
      ),
    );
  }

  @override
  _ParcelDetailScreenState createState() => _ParcelDetailScreenState();
}

class _ParcelDetailScreenState extends State<ParcelDetailScreen> {
  late TextStyle bodyMedtextStyle;

  late Parcel parcel;

  List<int> selectedOrders = [];
  List<Parcel> parcels = [];
  bool _shiprocketDetailsExpanded = false;
  @override
  void initState() {
    super.initState();
    parcel = widget.parcel;
  }

  @override
  Widget build(BuildContext context) {
    bodyMedtextStyle = Theme.of(context).textTheme.bodyMedium!.copyWith(
      color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.8),
    );
    return BlocListener<DeliveryBoyCubit, DeliveryBoyState>(
      listener: (context, state) {
        // if (state is DeliveryBoyFailure) {
        //   Utils.showSnackBar(message: state.errorMessage);
        // }
      },
      child: Scaffold(
        appBar: const CustomAppbar(titleKey: parcelDetailsKey),
        body: BlocListener<ParcelCubit, ParcelState>(
          bloc: widget.parcelCubit,
          listener: (context, state) {
            if (state is ParcelFetchSuccess) {
              parcel = state.parcels.firstWhere(
                (element) => element.id == widget.parcel.id,
              );
              setState(() {});
            }
          },
          child: SafeAreaWithBottomPadding(
            child: SingleChildScrollView(
              child: Column(
                children: <Widget>[
                  const SizedBox(height: 12),
                  ParcelDetailsContainer(parcel: parcel),
                  DesignConfig.smallHeightSizedBox,
                  BlocProvider<OrderUpdateCubit>(
                    create: (context) => OrderUpdateCubit(),
                    child: ParcelItemsContainer(
                      parcel: parcel,
                      order: widget.order,
                      callback: (var item) {
                        setState(() {});
                        if (widget.callback != null) {
                          List<OrderItems> orderitems = [];
                          orderitems.addAll(widget.order.orderItems!);

                          int index = orderitems.indexWhere(
                            (element) => element.id == item.id,
                          );
                          if (index != -1) {
                            orderitems[index] = item;
                          }
                          widget.order.orderItems!.clear();
                          widget.order.orderItems!.addAll(orderitems);
                          widget.callback!(widget.order);
                        }
                      },
                    ),
                  ),
                  if (widget.order.shiprocketOrder) buildShiprocketSection(),
                  DesignConfig.smallHeightSizedBox,
                  // if (context
                  //         .read<UserDetailsCubit>()
                  //         .getDefaultStoreOfUser(context)
                  //         .permissions!
                  //         .customerPrivacy ==
                  //     1)
                  buildShippingDetailsContainer(),
                  if ((parcel.notes ?? "").trim().isNotEmpty) ...[
                    DesignConfig.smallHeightSizedBox,
                    buildDeliveryPreferenceContainer(),
                    DesignConfig.smallHeightSizedBox,
                  ],
                  buildPriceDetailsContainer(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget buildShiprocketSection() {
    if (parcel.shiprocketOrderCreated) {
      return _buildShiprocketOrderDetails();
    } else {
      return _buildCreateShiprocketOrder();
    }
  }

  Widget _buildCreateShiprocketOrder() {
    return CustomDefaultContainer(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                Icons.local_shipping_outlined,
                color: Theme.of(context).colorScheme.primary,
                size: 20,
              ),
              const SizedBox(width: 8),
              CustomTextContainer(
                textKey: shiprocketShippingKey,
                style: Theme.of(context).textTheme.titleMedium,
              ),
            ],
          ),
          DesignConfig.smallHeightSizedBox,
          Text(
            Utils.getTranslatedValue(createShiprocketOrderDescKey),
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: Theme.of(
                context,
              ).colorScheme.secondary.withValues(alpha: 0.7),
            ),
          ),
          DesignConfig.defaultHeightSizedBox,
          CreateShiprocketOrderButton(
            parcel: parcel,
            parcelCubit: widget.parcelCubit,
          ),
          if (parcel.cancelledTrackingDetails != null &&
              parcel.activeStatus.toString().toLowerCase() ==
                  cancelledStatusType) ...[
            DesignConfig.defaultHeightSizedBox,
            Divider(
              color: Theme.of(
                context,
              ).colorScheme.secondary.withValues(alpha: 0.2),
              height: 1,
            ),
            DesignConfig.defaultHeightSizedBox,
            Row(
              children: [
                Icon(
                  Icons.cancel_outlined,
                  size: 16,
                  color: Theme.of(context).colorScheme.error,
                ),
                const SizedBox(width: 6),
                CustomTextContainer(
                  textKey: previousOrderCancelledKey,
                  style: Theme.of(context).textTheme.titleSmall?.copyWith(
                    color: Theme.of(context).colorScheme.error,
                  ),
                ),
              ],
            ),
            DesignConfig.smallHeightSizedBox,
            Text(
              Utils.getTranslatedValue(previousOrderCancelledDescKey),
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: Theme.of(
                  context,
                ).colorScheme.secondary.withValues(alpha: 0.6),
              ),
            ),
            DesignConfig.smallHeightSizedBox,
            _buildDetailRow(
              cancelledOrderIdKey,
              parcel.cancelledTrackingDetails!.shiprocketOrderId,
            ),
            _buildDetailRow(
              trackingIDKey,
              parcel.cancelledTrackingDetails!.trackingId,
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildShiprocketOrderDetails() {
    final bool isDelivered =
        parcel.activeStatus.toString().toLowerCase() == deliveredStatusType;
    final bool isCancelled =
        parcel.activeStatus.toString().toLowerCase() == cancelledStatusType;
    final bool hasAwbCode =
        parcel.trackingDetails?.awbCode != null &&
        parcel.trackingDetails!.awbCode!.isNotEmpty;
    return CustomDefaultContainer(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header with status badge
          Row(
            children: [
              Icon(
                Icons.local_shipping_outlined,
                color: Theme.of(context).colorScheme.primary,
                size: 20,
              ),
              const SizedBox(width: 8),
              CustomTextContainer(
                textKey: shiprocketShippingKey,
                style: Theme.of(context).textTheme.titleMedium,
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.primary,
                  borderRadius: BorderRadius.circular(borderRadius),
                ),
                child: CustomTextContainer(
                  textKey: lblOrderCreatedKey,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                    color: Theme.of(context).colorScheme.primaryContainer,
                  ),
                ),
              ),
            ],
          ),
          DesignConfig.defaultHeightSizedBox,

          // Shiprocket order details accordion
          _buildShiprocketDetailsAccordion(),
          DesignConfig.smallHeightSizedBox,

          // Action buttons row
          if (!hasAwbCode && !isCancelled) ...[
            Text(
              Utils.getTranslatedValue(pleaseGenerateAwbFirstKey),
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: Theme.of(
                  context,
                ).colorScheme.secondary.withValues(alpha: 0.6),

                fontStyle: FontStyle.italic,
              ),
            ),
            DesignConfig.smallHeightSizedBox,
            Row(
              children: [
                GenerateAWBButton(
                  parcel: parcel,
                  parcelCubit: widget.parcelCubit,
                ),
              ],
            ),
          ] else if (hasAwbCode) ...[
            Wrap(
              spacing: 16,
              runSpacing: 8,
              children: [
                TrackOrderButton(parcel: parcel),

                GenerateInvoiceButton(parcel: parcel),

                GenerateLabelButton(parcel: parcel),
                if (parcel.trackingDetails!.pickupScheduledDate == "" &&
                    (parcel.activeStatus.toString().toLowerCase() !=
                            cancelledStatusType ||
                        parcel.activeStatus.toString().toLowerCase() !=
                            readyToShipStatusType ||
                        parcel.activeStatus.toString().toLowerCase() !=
                            cancellationRequestedStatusType))
                  SendPickupRequestButton(
                    parcel: parcel,
                    parcelCubit: widget.parcelCubit,
                  ),
              ],
            ),
          ],

          // Cancel button at the bottom
          if (!isDelivered && !isCancelled) ...[
            DesignConfig.defaultHeightSizedBox,
            Divider(
              color: Theme.of(
                context,
              ).colorScheme.secondary.withValues(alpha: 0.2),
              height: 1,
            ),
            DesignConfig.smallHeightSizedBox,
            Row(
              children: [
                Expanded(
                  flex: 2,
                  child: CancelShiprocketOrderButton(
                    parcel: parcel,
                    parcelCubit: widget.parcelCubit,
                  ),
                ),
                DesignConfig.defaultWidthSizedBox,
                Expanded(
                  child: RefreshParcelStatusButton(
                    parcel: parcel,
                    parcelCubit: widget.parcelCubit,
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildShiprocketDetailsAccordion() {
    final td = parcel.trackingDetails;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        InkWell(
          onTap: () => setState(
            () => _shiprocketDetailsExpanded = !_shiprocketDetailsExpanded,
          ),
          borderRadius: BorderRadius.circular(8),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 4),
            child: Row(
              children: [
                CustomTextContainer(
                  textKey: shiprocketOrderDetailsKey,
                  style: Theme.of(context).textTheme.titleSmall?.copyWith(
                    color: Theme.of(context).colorScheme.primary,
                  ),
                ),
                const SizedBox(width: 4),
                AnimatedRotation(
                  turns: _shiprocketDetailsExpanded ? 0.5 : 0.0,
                  duration: const Duration(milliseconds: 250),
                  child: Icon(
                    Icons.keyboard_arrow_down,
                    size: 18,
                    color: Theme.of(context).colorScheme.primary,
                  ),
                ),
              ],
            ),
          ),
        ),
        AnimatedCrossFade(
          firstChild: const SizedBox.shrink(),
          secondChild: Padding(
            padding: const EdgeInsets.only(top: 8),
            child: Column(
              children: [
                _buildDetailRow(shiprocketOrderIdKey, td?.shiprocketOrderId),
                _buildDetailRow(shiprocketShipmentIdKey, td?.shipmentId),
                _buildDetailRow(
                  awbNumberKey,
                  (td?.awbCode != null && td!.awbCode!.isNotEmpty)
                      ? td.awbCode
                      : Utils.getTranslatedValue(notGeneratedYetKey),
                ),
                _buildDetailRow(shiprocketTrackingIdKey, td?.trackingId),
                _buildDetailRow(shiprocketTrackingUrlKey, td?.url),
              ],
            ),
          ),
          crossFadeState: _shiprocketDetailsExpanded
              ? CrossFadeState.showSecond
              : CrossFadeState.showFirst,
          duration: const Duration(milliseconds: 250),
        ),
      ],
    );
  }

  Widget _buildDetailRow(String labelKey, String? value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: CustomTextContainer(
              textKey: labelKey,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: Theme.of(
                  context,
                ).colorScheme.secondary.withValues(alpha: 0.6),
              ),
              maxLines: 1,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            flex: 3,
            child: Text(
              (value != null && value.isNotEmpty) ? value : '-',
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: Theme.of(context).colorScheme.secondary,
              ),
              maxLines: 2,
            ),
          ),
        ],
      ),
    );
  }

  buildShippingDetailsContainer() {
    String address = parcel.userAddress ?? "";
    if ((parcel.mobile ?? "").trim().isNotEmpty) {
      address =
          "$address\n${Utils.getTranslatedValue(phoneNumberKey)}: ${parcel.mobile}";
    }
    return commonContainer(
      shippingDetailsKey,
      Padding(
        padding: const .symmetric(horizontal: appContentHorizontalPadding),
        child: Column(
          crossAxisAlignment: .start,
          children: [
            if ((parcel.username ?? "").trim().isNotEmpty)
              Text(
                parcel.username ?? '',
                style: Theme.of(context).textTheme.titleSmall,
              ),
            Text(address, style: bodyMedtextStyle),
          ],
        ),
      ),
    );
  }

  buildDeliveryPreferenceContainer() {
    return commonContainer(
      deliveryPreferenceKey,
      Padding(
        padding: const .symmetric(horizontal: appContentHorizontalPadding),
        child: Text(
          '${Utils.getTranslatedValue(deliveryInstructionKey)} : ${parcel.notes}',
          style: bodyMedtextStyle,
        ),
      ),
    );
  }

  buildPriceDetailsContainer() {
    return commonContainer(
      priceDetailsKey,
      Column(
        children: <Widget>[
          buildPriceDetailRow(
            subtotalKey,
            Utils.priceWithCurrencySymbol(
              price: parcel.totalUnitPrice ?? 0,
              context: context,
            ),
          ),
          buildPriceDetailRow(
            shippingChargesKey,
            parcel.deliveryCharge != 0
                ? '+${Utils.priceWithCurrencySymbol(price: parcel.deliveryCharge ?? 0, context: context)}'
                : Utils.priceWithCurrencySymbol(
                    price: parcel.deliveryCharge ?? 0,
                    context: context,
                  ),
          ),
          // if (parcel.promoDiscount != 0)
          //   buildPriceDetailRow(couponDiscountKey,
          //       '${Utils.priceWithCurrencySymbol(price: parcel.promoDiscount ?? 0, context: context)}'),
          const Divider(height: 8, thickness: 0.5),
          buildPriceDetailRow(
            totalAmountKey,
            Utils.priceWithCurrencySymbol(
              price: (parcel.totalUnitPrice ?? 0) + parcel.deliveryCharge!,
              context: context,
            ),
            textStye: Theme.of(context).textTheme.titleSmall,
          ),
        ],
      ),
    );
  }

  buildPriceDetailRow(String title, String value, {TextStyle? textStye}) {
    return Padding(
      padding: const .only(
        left: appContentHorizontalPadding,
        right: appContentHorizontalPadding,
        bottom: 8,
      ),
      child: Row(
        mainAxisAlignment: .spaceBetween,
        children: <Widget>[
          CustomTextContainer(
            textKey: title,
            style: textStye ?? bodyMedtextStyle,
          ),
          Text(value, style: textStye ?? bodyMedtextStyle),
        ],
      ),
    );
  }

  commonContainer(String title, Widget content) {
    return Container(
      padding: .symmetric(vertical: appContentVerticalSpace),
      color: Theme.of(context).colorScheme.primaryContainer,
      child: Column(
        crossAxisAlignment: .start,
        children: <Widget>[
          Padding(
            padding: .symmetric(horizontal: appContentHorizontalPadding),
            child: CustomTextContainer(
              textKey: title,
              style: Theme.of(context).textTheme.titleMedium,
            ),
          ),
          const Divider(height: 8, thickness: 0.5),
          content,
        ],
      ),
    );
  }

  buildContainer() {
    return CustomDefaultContainer(
      child: Row(
        children: <Widget>[
          const CustomRoundedButton(
            widthPercentage: 0.4,
            buttonTitle: lblOrderCreatedKey,
            showBorder: false,
          ),
          buildIcons(Icons.add_location_alt_outlined, () {}),
          buildIcons(Icons.label_outline, () {}),
          buildIcons(Icons.cancel_outlined, () {}),
          buildIcons(Icons.receipt_long_outlined, () {}),
        ],
      ),
    );
  }

  buildIcons(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const .only(left: 12),
        padding: const .all(4),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(borderRadius),
          color: Theme.of(context).scaffoldBackgroundColor,
        ),
        child: Icon(
          icon,
          color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.8),
        ),
      ),
    );
  }
}
