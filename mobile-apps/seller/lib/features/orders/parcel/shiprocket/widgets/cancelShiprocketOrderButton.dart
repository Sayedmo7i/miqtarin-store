import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/cancelShiprocketOrderCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class CancelShiprocketOrderButton extends StatelessWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  const CancelShiprocketOrderButton({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => CancelShiprocketOrderCubit(),
      child:
          BlocConsumer<CancelShiprocketOrderCubit, CancelShiprocketOrderState>(
            listener: (context, state) {
              if (state is CancelShiprocketOrderSuccess) {
                Utils.showSnackBar(message: state.successMessage);
                parcelCubit.updateParcel(state.parcel);
              }
              if (state is CancelShiprocketOrderFailure)
                Utils.showSnackBar(message: state.errorMessage);
            },
            builder: (context, state) {
              return CustomRoundedButton(
                widthPercentage: 1.0,
                buttonTitle: cancelShiprocketOrderKey,
                showBorder: false,
                backgroundColor: Theme.of(context).colorScheme.error,
                child: state is CancelShiprocketOrderProgress
                    ? CustomCircularProgressIndicator(
                        indicatorColor: Theme.of(
                          context,
                        ).scaffoldBackgroundColor,
                      )
                    : null,
                onTap: () => Utils.openAlertDialog(
                  context,
                  content: areYouSureYouWantToCancelOrderKey,
                  onTapYes: () {
                    Navigator.of(context).pop();
                    context
                        .read<CancelShiprocketOrderCubit>()
                        .cancelShiprocketOrder(
                          shiprocketId:
                              parcel.trackingDetails!.shiprocketOrderId ?? '',
                        );
                  },
                  message: cancelShiprocketOrderKey,
                ),
              );
            },
          ),
    );
  }
}
