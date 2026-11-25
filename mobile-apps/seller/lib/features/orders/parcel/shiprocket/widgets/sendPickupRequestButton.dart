import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/sendPickupRequestCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class SendPickupRequestButton extends StatelessWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  const SendPickupRequestButton({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => SendPickupRequestCubit(),
      child: BlocConsumer<SendPickupRequestCubit, SendPickupRequestState>(
        listener: (context, state) {
          if (state is SendPickupRequestFailure) {
            Utils.showSnackBar(message: state.errorMessage);
          }
          if (state is SendPickupRequestSuccess) {
            parcelCubit.updateParcel(state.parcel);
            Utils.showSnackBar(message: state.successMessage);
          }
        },
        builder: (context, state) {
          return CustomRoundedButton(
            widthPercentage: 1.0,
            buttonTitle: sendPickupRequestKey,
            showBorder: false,
            backgroundColor: greenColor.withValues(alpha: 0.1),
            child: state is SendPickupRequestProgress
                ? CustomCircularProgressIndicator()
                : Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: <Widget>[
                      Icon(Icons.local_shipping_outlined, color: greenColor),
                      const SizedBox(width: 8),
                      CustomTextContainer(
                        textKey: sendPickupRequestKey,
                        style: Theme.of(
                          context,
                        ).textTheme.bodyMedium!.copyWith(color: greenColor),
                      ),
                    ],
                  ),
            onTap: () {
              if (state is SendPickupRequestProgress) return;
              context.read<SendPickupRequestCubit>().sendPickupRequest(
                shipmentId: parcel.trackingDetails!.shipmentId ?? '',
              );
            },
          );
        },
      ),
    );
  }
}
