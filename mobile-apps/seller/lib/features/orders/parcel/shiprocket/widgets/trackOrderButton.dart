import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';

class TrackOrderButton extends StatelessWidget {
  final Parcel parcel;
  const TrackOrderButton({Key? key, required this.parcel}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return CustomRoundedButton(
      widthPercentage: 1.0,
      buttonTitle: '',
      showBorder: true,
      backgroundColor: Theme.of(context).colorScheme.primaryContainer,
      borderColor: Theme.of(
        context,
      ).colorScheme.secondary.withValues(alpha: 0.3),
      style: Theme.of(context).textTheme.bodySmall!,
      child: Row(
        mainAxisAlignment: .center,
        spacing: 4,
        children: [
          Icon(Icons.location_on_outlined),
          CustomTextContainer(textKey: trackOrderKey),
        ],
      ),
      onTap: () {
        if (parcel.trackingDetails?.url == null ||
            parcel.trackingDetails!.url!.isEmpty) {
          Utils.showSnackBar(message: trackingUrlNotAddedKey);
          return;
        }
        Utils.launchURL(parcel.trackingDetails!.url ?? "");
      },
    );
  }
}
