import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/features/orders/parcel/blocs/getParcelCubit.dart';
import 'package:eshopplus_seller/features/orders/parcel/models/parcel.dart';
import 'package:eshopplus_seller/features/orders/parcel/shiprocket/blocs/createShiprocketOrderCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customTextFieldContainer.dart';
import 'package:eshopplus_seller/commons/widgets/filterContainerForBottomSheet.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';

import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:eshopplus_seller/utils/inputValidators.dart';

class CreateShiprocketOrderButton extends StatelessWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  const CreateShiprocketOrderButton({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const .symmetric(vertical: 8),
      color: Theme.of(context).colorScheme.primaryContainer,
      child: BlocProvider(
        create: (context) => CreateShiprocketOrderCubit(),
        child:
            BlocBuilder<CreateShiprocketOrderCubit, CreateShiprocketOrderState>(
              builder: (context, state) {
                return CustomRoundedButton(
                  widthPercentage: 1.0,
                  buttonTitle: createShiprocketOrderKey,
                  showBorder: false,
                  backgroundColor: Theme.of(context).colorScheme.secondary,
                  onTap: () => Utils.openModalBottomSheet(
                    context,
                    isDismissible: state is! CreateShiprocketOrderProgress,
                    ShiprocketOrderContainer(
                      parcel: parcel,
                      parcelCubit: parcelCubit,
                      createShiprocketOrderCubit: context
                          .read<CreateShiprocketOrderCubit>(),
                    ),
                  ),
                );
              },
            ),
      ),
    );
  }
}

class ShiprocketOrderContainer extends StatefulWidget {
  final Parcel parcel;
  final ParcelCubit parcelCubit;
  final CreateShiprocketOrderCubit createShiprocketOrderCubit;
  const ShiprocketOrderContainer({
    Key? key,
    required this.parcel,
    required this.parcelCubit,
    required this.createShiprocketOrderCubit,
  }) : super(key: key);

  @override
  _ShiprocketOrderContainerState createState() =>
      _ShiprocketOrderContainerState();
}

class _ShiprocketOrderContainerState extends State<ShiprocketOrderContainer> {
  Map<String, TextEditingController> controllers = {};

  Map<String, FocusNode> focusNodes = {};
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  final List formFields = [
    pickupLocationKey,
    weightKey,
    heightKey,
    breadthKey,
    lengthKey,
  ];
  @override
  void initState() {
    super.initState();
    formFields.forEach((key) {
      controllers[key] = TextEditingController();
      focusNodes[key] = FocusNode();
    });
    // controllers[pickupLocationKey]!.text = widget.parcel.pickupLocation ?? '';
  }

  @override
  void dispose() {
    // Dispose all text editing controllers
    controllers.forEach((key, controller) {
      controller.dispose();
    });
    focusNodes.forEach((key, focusNode) {
      focusNode.dispose();
    });
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<CreateShiprocketOrderCubit, CreateShiprocketOrderState>(
      bloc: widget.createShiprocketOrderCubit,
      listener: (context, state) {
        if (state is CreateShiprocketOrderSuccess) {
          widget.parcelCubit.updateParcel(state.parcel);
          //this will close the bottom sheet
          Navigator.of(context).pop();
          Utils.showSnackBar(message: state.successMessage);
        }
        if (state is CreateShiprocketOrderFailure) {
          Utils.showSnackBar(message: state.errorMessage);
        }
      },
      builder: (context, state) {
        return FilterContainerForBottomSheet(
          title: createShiprocketOrderParcelKey,
          borderedButtonTitle: backKey,
          primaryButtonTitle: createOrderKey,
          borderedButtonOnTap: () {
            if (state is! CreateShiprocketOrderProgress)
              Utils.popNavigation(context);
          },
          primaryButtonOnTap: () => onTapCreateButton(state),
          primaryChild: state is CreateShiprocketOrderProgress
              ? CustomCircularProgressIndicator(
                  indicatorColor: Theme.of(context).scaffoldBackgroundColor,
                )
              : null,
          content: Form(
            key: _formkey,
            child: Column(
              crossAxisAlignment: .start,
              children: <Widget>[
                Container(
                  padding: const .symmetric(horizontal: 8, vertical: 6),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(borderRadius),
                    color: const Color(0xFFDC3545),
                  ),
                  child: Text.rich(
                    TextSpan(
                      children: [
                        TextSpan(
                          text: '${Utils.getTranslatedValue(noteKey)} : ',
                          style: Theme.of(
                            context,
                          ).textTheme.titleMedium!.copyWith(color: whiteColor),
                        ),
                        TextSpan(
                          text: context
                              .read<SettingsAndLanguagesCubit>()
                              .getTranslatedValue(
                                labelKey: shiprocketOrderLocationNoteKey,
                              ),
                          style: Theme.of(
                            context,
                          ).textTheme.bodyMedium!.copyWith(color: whiteColor),
                        ),
                      ],
                    ),
                  ),
                ),
                DesignConfig.defaultHeightSizedBox,
                CustomTextFieldContainer(
                  hintTextKey: pickupLocationKey,
                  textEditingController: TextEditingController(
                    text: widget.parcel.pickupLocation ?? '',
                  ),
                  labelKey: pickupLocationKey,
                  isFieldValueMandatory: false,
                  readOnly: true,
                ),

                DesignConfig.defaultHeightSizedBox,
                CustomTextContainer(
                  textKey: totalWeightOfBoxKey,
                  style: Theme.of(context).textTheme.titleSmall,
                ),
                Row(
                  children: <Widget>[
                    Expanded(
                      child: CustomTextFieldContainer(
                        hintTextKey: weightKey,
                        textEditingController: controllers[weightKey]!,
                        labelKey: weightKey,
                        textInputAction: TextInputAction.next,
                        focusNode: focusNodes[weightKey],
                        keyboardType: TextInputType.number,
                        validator: (v) =>
                            Validator.emptyValueValidation(v, context),
                        onFieldSubmitted: (v) => FocusScope.of(
                          context,
                        ).requestFocus(focusNodes[heightKey]),
                      ),
                    ),
                    DesignConfig.defaultWidthSizedBox,
                    Expanded(
                      child: CustomTextFieldContainer(
                        hintTextKey: heightKey,
                        textEditingController: controllers[heightKey]!,
                        labelKey: heightKey,
                        textInputAction: TextInputAction.next,
                        focusNode: focusNodes[heightKey],
                        keyboardType: TextInputType.number,
                        validator: (v) =>
                            Validator.emptyValueValidation(v, context),

                        onFieldSubmitted: (v) => FocusScope.of(
                          context,
                        ).requestFocus(focusNodes[breadthKey]),
                      ),
                    ),
                  ],
                ),
                Row(
                  children: <Widget>[
                    Expanded(
                      child: CustomTextFieldContainer(
                        hintTextKey: breadthKey,
                        textEditingController: controllers[breadthKey]!,
                        labelKey: breadthKey,
                        textInputAction: TextInputAction.next,
                        focusNode: focusNodes[breadthKey],
                        keyboardType: TextInputType.number,
                        validator: (v) =>
                            Validator.emptyValueValidation(v, context),
                        onFieldSubmitted: (v) => FocusScope.of(
                          context,
                        ).requestFocus(focusNodes[lengthKey]),
                      ),
                    ),
                    DesignConfig.defaultWidthSizedBox,
                    Expanded(
                      child: CustomTextFieldContainer(
                        hintTextKey: lengthKey,
                        textEditingController: controllers[lengthKey]!,
                        labelKey: lengthKey,
                        textInputAction: TextInputAction.next,
                        focusNode: focusNodes[lengthKey],
                        keyboardType: TextInputType.number,
                        validator: (v) =>
                            Validator.emptyValueValidation(v, context),
                        onFieldSubmitted: (v) => FocusScope.of(
                          context,
                        ).requestFocus(focusNodes[selectZipCodeKey]),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  void onTapCreateButton(CreateShiprocketOrderState state) {
    if (isDemoApp) {
      Utils.showSnackBar(message: demoModeOnKey);
      return;
    }
    if (_formkey.currentState!.validate() &&
        state is! CreateShiprocketOrderProgress) {
      FocusScope.of(context).unfocus();
      Map<String, String> params = {
        ApiURL.orderIdApiKey: widget.parcel.orderId.toString(),
        ApiURL.pickupLocationApiKey: widget.parcel.pickupLocation ?? '',
        ApiURL.parcelWeightApiKey: controllers[weightKey]!.text.trim(),
        ApiURL.parcelHeightApiKey: controllers[heightKey]!.text.trim(),
        ApiURL.parcelBreadthApiKey: controllers[breadthKey]!.text.trim(),
        ApiURL.parcelLengthApiKey: controllers[lengthKey]!.text.trim(),
        ApiURL.parcelIdApiKey: widget.parcel.id.toString(),
        ApiURL.storeIdApiKey: widget.parcel.storeId.toString(),
        ApiURL.pickupLocationIdApiKey: widget.parcel.pickupLocationId ?? '',
      };
      widget.createShiprocketOrderCubit.createShiprocketOrder(params: params);
    }
  }
}
