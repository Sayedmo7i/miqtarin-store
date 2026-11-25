import 'package:eshop_plus/commons/widgets/customDefaultContainer.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/profile/orders/models/order.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';

class OrderTrackingContainer extends StatelessWidget {
  final Order order;
  final OrderItems orderItem;
  const OrderTrackingContainer({
    Key? key,
    required this.order,
    required this.orderItem,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Check if any tracking data is available
    if (order.shiprocketOrder &&
        orderItem.shiprocketOrderTrackingUrl != null &&
        orderItem.shiprocketOrderTrackingUrl!.isNotEmpty) {
      return Column(
        crossAxisAlignment: .start,
        children: <Widget>[
          CustomDefaultContainer(
            child: buildTrackingRow(
              context,
              trackingUrlKey,
              orderItem.shiprocketOrderTrackingUrl!,
              isLink: true,
              onTap: () {
                Utils.launchURL(orderItem.shiprocketOrderTrackingUrl!);
              },
            ),
          ),
          DesignConfig.smallHeightSizedBox,
        ],
      );
    } else {
      bool hasTrackingData =
          (orderItem.trackingId != null && orderItem.trackingId!.isNotEmpty) ||
          (orderItem.courierAgency != null &&
              orderItem.courierAgency!.isNotEmpty) ||
          (orderItem.url != null && orderItem.url!.isNotEmpty);

      return hasTrackingData
          ? buildOrderTracking(context, orderItem)
          : const SizedBox.shrink();
    }
  }

  buildOrderTracking(BuildContext context, OrderItems orderItem) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsetsDirectional.symmetric(
            vertical: appContentVerticalSpace,
          ),
          color: Theme.of(context).colorScheme.primaryContainer,
          child: Column(
            crossAxisAlignment: .start,
            children: <Widget>[
              Padding(
                padding: const EdgeInsetsDirectional.symmetric(
                  horizontal: appContentHorizontalPadding,
                ),
                child: CustomTextContainer(
                  textKey: orderTrackingKey,
                  style: Theme.of(context).textTheme.titleMedium,
                ),
              ),
              const Divider(height: 12, thickness: 0.5),
              if (orderItem.trackingId != null &&
                  orderItem.trackingId!.isNotEmpty)
                buildTrackingRow(context, trackingIdKey, orderItem.trackingId!),
              if (orderItem.courierAgency != null &&
                  orderItem.courierAgency!.isNotEmpty)
                buildTrackingRow(
                  context,
                  courierAgencyKey,
                  orderItem.courierAgency!,
                ),
              if (orderItem.url != null && orderItem.url!.isNotEmpty)
                buildTrackingRow(
                  context,
                  trackingUrlKey,
                  orderItem.url!,
                  isLink: true,
                  onTap: () {
                    Utils.launchURL(orderItem.url!);
                  },
                ),
            ],
          ),
        ),
        DesignConfig.smallHeightSizedBox,
      ],
    );
  }

  buildTrackingRow(
    BuildContext context,
    String title,
    String value, {
    VoidCallback? onTap,
    bool isLink = false,
  }) {
    return Padding(
      padding: const EdgeInsetsDirectional.symmetric(
        horizontal: appContentHorizontalPadding,
        vertical: 8,
      ),
      child: Row(
        crossAxisAlignment: .start,
        children: [
          CustomTextContainer(
            textKey: title,
            style: Theme.of(context).textTheme.bodyMedium,
          ),
          const SizedBox(width: 4),
          const Text(':'),
          const SizedBox(width: 4),
          Expanded(
            child: GestureDetector(
              onTap: onTap,
              child: CustomTextContainer(
                textKey: value,
                maxLines: 2,
                style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                  decoration: isLink ? TextDecoration.underline : null,
                  color: isLink
                      ? blueColor
                      : Theme.of(
                          context,
                        ).colorScheme.secondary.withValues(alpha: 0.8),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
