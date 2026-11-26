import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/generateAWBCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class GenerateAWBButton extends StatelessWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  const GenerateAWBButton({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => GenerateAWBCubit(),
      child: BlocConsumer<GenerateAWBCubit, GenerateAWBState>(
        listener: (context, state) {
          if (state is GenerateAWBSuccess) {
            Utils.showSnackBar(message: state.successMessage);
            parcelCubit.updateParcel(state.parcel);
          }
          if (state is GenerateAWBFailure)
            Utils.showSnackBar(message: state.errorMessage);
        },
        builder: (context, state) {
          return CustomRoundedButton(
            widthPercentage: 0.4,
            buttonTitle: generateAwbKey,
            showBorder: false,
            backgroundColor: Theme.of(
              context,
            ).colorScheme.primary.withValues(alpha: 0.2),
            style: Theme.of(context).textTheme.bodyMedium!.copyWith(
              color: Theme.of(context).colorScheme.primary,
            ),
            child: state is GenerateAWBProgress
                ? CustomCircularProgressIndicator()
                : null,
            onTap: () {
              if (state is! GenerateAWBProgress)
                context.read<GenerateAWBCubit>().generateAWB(
                  shipmentId: parcel.trackingDetails!.shipmentId ?? '',
                );
            },
          );
        },
      ),
    );
  }
}
