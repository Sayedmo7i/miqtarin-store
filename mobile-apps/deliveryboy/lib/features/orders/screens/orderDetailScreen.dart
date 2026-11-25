import 'package:eshopplus_deliveryboy/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/errorScreen.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/safeAreaWithBottomPadding.dart';
import 'package:eshopplus_deliveryboy/core/api/apiEndPoints.dart';
import 'package:eshopplus_deliveryboy/core/constants/themeConstants.dart';
import 'package:eshopplus_deliveryboy/core/theme/colors.dart';
import 'package:eshopplus_deliveryboy/features/orders/blocs/orderCubit.dart';
import 'package:eshopplus_deliveryboy/features/orders/blocs/orderUpdateCubit.dart';
import 'package:eshopplus_deliveryboy/features/orders/models/order.dart';
import 'package:eshopplus_deliveryboy/features/orders/models/parcel.dart';
import 'package:eshopplus_deliveryboy/features/orders/repositories/orderRepository.dart';
import 'package:eshopplus_deliveryboy/features/orders/widgets/orderDetailsContainer.dart';
import 'package:eshopplus_deliveryboy/features/orders/widgets/orderStatusContainer.dart';
import 'package:eshopplus_deliveryboy/utils/designConfig.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../commons/blocs/settingsAndLanguagesCubit.dart';

import '../../../core/localization/labelKeys.dart';
import '../../../utils/utils.dart';
import '../../../commons/widgets/customAppbar.dart';
import '../../../commons/widgets/customDefaultContainer.dart';
import '../../../commons/widgets/customRoundedButton.dart';
import '../../../commons/widgets/customTextContainer.dart';

class OrderDetailScreen extends StatefulWidget {
  final Parcel parcel;
  final int? parcelId;
  final Function? callback;

  const OrderDetailScreen({
    Key? key,
    required this.parcel,
    this.callback,
    this.parcelId,
  }) : super(key: key);
  static Widget getRouteInstance() {
    Map arguments = Get.arguments;
    return BlocProvider(
      create: (context) => OrdersCubit(OrderRepository()),
      child: OrderDetailScreen(
        parcel: arguments['parcel'],
        callback:
            arguments.containsKey('callback') ? arguments['callback'] : null,
        parcelId:
            arguments.containsKey('parcelId') ? arguments['parcelId'] : null,
      ),
    );
  }

  @override
  _OrderDetailScreenState createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  late TextStyle bodyMedtextStyle;

  bool isMapOpened = false; // Flag to prevent multiple taps
  bool hasReturnStatus = false; // Will be true if any status contains 'return'
  late Parcel parcel;
  @override
  void initState() {
    super.initState();
    parcel = widget.parcel;
    if (widget.parcelId != null) {
      context.read<OrdersCubit>().getOrders(params: {
        ApiURL.parcelIdApiKey: widget.parcelId,
      });
    }
    // Check if any status contains 'return'
    if (parcel.status != null) {
      hasReturnStatus = parcel.status!
          .any((entry) => entry.status.toLowerCase().contains('return'));
    }
  }

  @override
  Widget build(BuildContext context) {
    bodyMedtextStyle = Theme.of(context).textTheme.bodyMedium!.copyWith(
        color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.8));
    return BlocListener<OrdersCubit, OrdersState>(
        listener: (context, state) {
          if (state is OrdersFetchSuccess) {
            parcel = state.parcelList.firstWhere((o) => o.id == parcel.id);
          }
        },
        child: Scaffold(
          appBar: const CustomAppbar(titleKey: orderDetailsKey),
          body: BlocBuilder<OrdersCubit, OrdersState>(
            builder: (context, state) {
              if (state is OrdersFetchSuccess || widget.parcelId == null) {
                return SafeAreaWithBottomPadding(
                  child: SingleChildScrollView(
                    child: Column(
                      children: <Widget>[
                        const SizedBox(height: 12),
                        OrderDetailsContainer(
                          parcel: parcel,
                        ),
                        const SizedBox(height: 8),
                        BlocProvider<OrderUpdateCubit>(
                          create: (context) => OrderUpdateCubit(),
                          child: OrderStatusContainer(
                            parcel: parcel,
                            callback: (Parcel parcel) {
                              setState(() {});
                              if (widget.callback != null) {
                                widget.callback!(parcel);
                              }
                            },
                          ),
                        ),
                        const SizedBox(height: 8),
                        buildShippingDetailsContainer(),
                        if ((parcel.notes ?? "").trim().isNotEmpty)
                          const SizedBox(height: 8),
                        if ((parcel.notes ?? "").trim().isNotEmpty)
                          buildDeliveryPreferenceContainer(),
                        if ((parcel.returnRequestRemark ?? "")
                            .trim()
                            .isNotEmpty)
                          const SizedBox(height: 8),
                        if ((parcel.returnRequestRemark ?? "")
                            .trim()
                            .isNotEmpty)
                          buildReturnRemarksContainer(),
                        const SizedBox(height: 8),
                        buildPriceDetailsContainer()
                      ],
                    ),
                  ),
                );
              }
              if (state is OrdersFetchFailure) {
                return ErrorScreen(
                    text: state.errorMessage,
                    onPressed: () {
                      context.read<OrdersCubit>().getOrders(
                        params: {
                          ApiURL.idApiKey: widget.parcelId,
                        },
                      );
                    });
              }
              return Center(
                child: CustomCircularProgressIndicator(
                  indicatorColor: Theme.of(context).colorScheme.primary,
                ),
              );
            },
          ),
        ));
  }

  buildShippingDetailsContainer() {
    String address = parcel.userAddress ?? "";

    return commonContainer(
      shippingDetailsKey,
      Padding(
        padding:
            const EdgeInsets.symmetric(horizontal: appContentHorizontalPadding),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if ((parcel.username ?? "").trim().isNotEmpty)
              Text(
                parcel.username ?? '',
                style: Theme.of(context).textTheme.titleSmall,
              ),
            Text(
              address,
              style: bodyMedtextStyle,
            ),
            DesignConfig.smallHeightSizedBox,
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              mainAxisSize: MainAxisSize.min,
              children: <Widget>[
                if ((parcel.mobile ?? "").trim().isNotEmpty) ...[
                  Text(
                    '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: phoneNumberKey)}: ${parcel.mobile}',
                    style: bodyMedtextStyle,
                  ),
                  DesignConfig.defaultWidthSizedBox,
                ],
                Flexible(
                  child: CustomRoundedButton(
                    height: 30,
                    widthPercentage: 0.4,
                    buttonTitle: '',
                    showBorder: false,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: <Widget>[
                        CustomTextContainer(
                          textKey: directionKey,
                          style: Theme.of(context)
                              .textTheme
                              .bodyMedium!
                              .copyWith(color: whiteColor),
                        ),
                        const SizedBox(width: 8),
                        Icon(
                          Icons.my_location,
                          color: whiteColor,
                        )
                      ],
                    ),
                    onTap: () async {
                      if (isMapOpened) return;
                      try {
                        Position? position = await Utils.getCurrentLocation();
                        try {
                          if (position != null)
                            openMapDirections(
                              currentLat:
                                  double.tryParse(position.latitude.toString()),
                              currentLong: double.tryParse(
                                  position.longitude.toString()),
                              destLat: double.tryParse(parcel.latitude ?? ''),
                              destLong: double.tryParse(parcel.longitude ?? ''),
                              currentAddress: parcel.userAddress,
                              destinationAddress: parcel.userAddress,
                              context: context,
                            );
                        } catch (e) {}
                      } catch (e) {
                        // Handle errors (e.g., permission denied, service not available)
                        Utils.showSnackBar(
                            context: context, message: e.toString());
                      }
                    },
                  ),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }

  buildDeliveryPreferenceContainer() {
    return commonContainer(
        deliveryPreferenceKey,
        Padding(
            padding: const EdgeInsets.symmetric(
                horizontal: appContentHorizontalPadding),
            child: Text(
              '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: deliveryInstructionKey)} : ${parcel.notes}',
              style: bodyMedtextStyle,
            )));
  }

  buildReturnRemarksContainer() {
    return commonContainer(
        returnRemarksKey,
        Padding(
            padding: const EdgeInsets.symmetric(
                horizontal: appContentHorizontalPadding),
            child: Text(
              parcel.returnRequestRemark ?? '',
              style: bodyMedtextStyle,
            )));
  }

  buildPriceDetailsContainer() {
    return commonContainer(
        priceDetailsKey,
        Column(
          children: <Widget>[
            buildPriceDetailRow(
                subtotalKey,
                Utils.priceWithCurrencySymbol(
                    price: parcel.totalUnitPrice ?? 0, context: context)),
            buildPriceDetailRow(
                shippingChargesKey,
                parcel.deliveryCharge != 0
                    ? '+${Utils.priceWithCurrencySymbol(price: parcel.deliveryCharge ?? 0, context: context)}'
                    : Utils.priceWithCurrencySymbol(
                        price: parcel.deliveryCharge ?? 0, context: context)),
            const Divider(
              height: 8,
              thickness: 0.5,
            ),
            buildPriceDetailRow(
                totalAmountKey,
                Utils.priceWithCurrencySymbol(
                    price:
                        (parcel.totalUnitPrice ?? 0) + parcel.deliveryCharge!,
                    context: context),
                textStye: Theme.of(context).textTheme.titleSmall),
          ],
        ));
  }

  buildPriceDetailRow(String title, String value, {TextStyle? textStye}) {
    return Padding(
      padding: const EdgeInsets.only(
          left: appContentHorizontalPadding,
          right: appContentHorizontalPadding,
          bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: <Widget>[
          CustomTextContainer(
            textKey: title,
            style: textStye ?? bodyMedtextStyle,
          ),
          Text(
            value,
            style: textStye ?? bodyMedtextStyle,
          )
        ],
      ),
    );
  }

  commonContainer(String title, Widget content) {
    return Container(
      padding: EdgeInsets.symmetric(vertical: appContentVerticalSpace),
      color: Theme.of(context).colorScheme.primaryContainer,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Padding(
            padding: const EdgeInsets.symmetric(
                horizontal: appContentHorizontalPadding),
            child: CustomTextContainer(
              textKey: title,
              style: Theme.of(context).textTheme.titleMedium,
            ),
          ),
          const Divider(
            height: 8,
            thickness: 0.5,
          ),
          content
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
            showBorder: false),
        buildIcons(Icons.add_location_alt_outlined, () {}),
        buildIcons(Icons.label_outline, () {}),
        buildIcons(Icons.cancel_outlined, () {}),
        buildIcons(Icons.receipt_long_outlined, () {}),
      ],
    ));
  }

  buildIcons(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(left: 12),
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(borderRadius),
            color: Theme.of(context).scaffoldBackgroundColor),
        child: Icon(
          icon,
          color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.8),
        ),
      ),
    );
  }

  void openMapDirections({
    required double? currentLat,
    required double? currentLong,
    required double? destLat,
    required double? destLong,
    required BuildContext context,
    String? currentAddress,
    String? destinationAddress,
  }) async {
    if (isMapOpened) return; // Prevent multiple taps

    setState(() {
      isMapOpened = true; // Set the flag to true
    });
    String googleMapsUrl;
    String? crntAddress = currentLat != null || currentLong != null
        ? '$currentLat,$currentLong'
        : currentAddress;
    String? dstAddress = destLat != null || destLong != null
        ? '$destLat,$destLong'
        : destinationAddress;

// Check if latitude and longitude are available

// Use lat-long to open map
//This will work on both iOS and Android. The key part is that Google Maps uses the same URL scheme across platforms.
    googleMapsUrl =
        'https://www.google.com/maps/dir/?api=1&origin=$crntAddress&destination=$dstAddress';

// Check if Google Maps is available
    if (await canLaunchUrl(Uri.parse(googleMapsUrl))) {
      await launchUrl(Uri.parse(googleMapsUrl),
          mode: LaunchMode.externalApplication);
    } else {
      Utils.showSnackBar(
          message: 'Could not launch Google maps', context: context);
    }
    setState(() {
      isMapOpened = false; // Reset flag after closing the map
    });
  }
}
