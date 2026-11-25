import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/updateShiprocketOrderStatusCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class RefreshParcelStatusButton extends StatelessWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  const RefreshParcelStatusButton({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => UpdateShiprocketOrderStatusCubit(),
      child:
          BlocConsumer<
            UpdateShiprocketOrderStatusCubit,
            UpdateShiprocketOrderStatusState
          >(
            listener: (context, state) {
              if (state is UpdateShiprocketOrderStatusFailure)
                Utils.showSnackBar(message: state.errorMessage);
              if (state is UpdateShiprocketOrderStatusSuccess) {
                parcelCubit.updateParcel(state.parcel);
                Utils.showSnackBar(message: state.successMessage);
              }
            },
            builder: (context, state) {
              return CustomRoundedButton(
                widthPercentage: 0.3,
                buttonTitle: refreshKey,
                showBorder: false,
                backgroundColor: blueColor.withValues(alpha: 0.1),
                child: state is UpdateShiprocketOrderStatusProgress
                    ? CustomCircularProgressIndicator(
                        indicatorColor: Theme.of(
                          context,
                        ).scaffoldBackgroundColor,
                      )
                    : Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: <Widget>[
                          Icon(Icons.refresh_rounded, color: blueColor),
                          CustomTextContainer(
                            textKey: refreshKey,
                            style: Theme.of(
                              context,
                            ).textTheme.bodyMedium!.copyWith(color: blueColor),
                          ),
                        ],
                      ),

                onTap: () {
                  if (state is UpdateShiprocketOrderStatusProgress) return;
                  context
                      .read<UpdateShiprocketOrderStatusCubit>()
                      .updateShiprocketOrderStatus(
                        trackingId: parcel.trackingDetails!.trackingId ?? '',
                      );
                },
              );
            },
          ),
    );
  }
}
