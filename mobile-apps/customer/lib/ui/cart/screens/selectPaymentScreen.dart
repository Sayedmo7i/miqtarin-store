import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/models/cart.dart';
import 'package:eshop_plus/ui/cart/models/deliverabilityResponse.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/ui/profile/transaction/blocs/getPaymentMethodCubit.dart';
import 'package:eshop_plus/commons/blocs/userDetailsCubit.dart';
import 'package:eshop_plus/ui/profile/transaction/models/paymentMethod.dart';
import 'package:eshop_plus/ui/profile/transaction/widgets/paymentMethodList.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/commons/widgets/error_screen.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:razorpay_flutter/razorpay_flutter.dart';

class SelectPaymentScreen extends StatefulWidget {
  final PaymentModel? selectedPaymentMethod;
  final PaymentMethodCubit paymentMethodCubit;
  final UserDetailsCubit userDetailsCubit;
  final DeliverabilityResponse? deliverabilityData;
  const SelectPaymentScreen({
    Key? key,
    required this.selectedPaymentMethod,
    required this.paymentMethodCubit,
    required this.userDetailsCubit,
    this.deliverabilityData,
  }) : super(key: key);

  @override
  _SelectPaymentScreenState createState() => _SelectPaymentScreenState();
}

class _SelectPaymentScreenState extends State<SelectPaymentScreen> {
  late Razorpay _razorpay;
  bool _isWalletPayment = false;
  bool _isCodSelected = false;

  @override
  void initState() {
    super.initState();

    Future.delayed(Duration.zero, () {
      if (mounted) {
        _isWalletPayment =
            context.read<GetUserCartCubit>().getCheckoutState().useWalletBalance;

        // Check if COD is currently selected
        final selectedMethod = context
            .read<GetUserCartCubit>()
            .getCheckoutState()
            .selectedPaymentMethod;
        _isCodSelected = selectedMethod?.name == cashOnDeliveryKey;

        setState(() {});
      }
    });
    _razorpay = Razorpay();
    _razorpay.on(Razorpay.EVENT_PAYMENT_SUCCESS, _handlePaymentSuccess);
    _razorpay.on(Razorpay.EVENT_PAYMENT_ERROR, _handlePaymentError);
    _razorpay.on(Razorpay.EVENT_EXTERNAL_WALLET, _handleExternalWallet);
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        context.read<UserDetailsCubit>().fetchUserDetails(
          params: Utils.getParamsForVerifyUser(context),
        );
        context.read<PaymentMethodCubit>().fetchPaymentMethods();
      },
      child: BlocConsumer<UserDetailsCubit, UserDetailsState>(
        bloc: widget.userDetailsCubit,
        listener: (context, state) {},
        builder: (context, state) {
          return ListView(
            children: [
              if (state is UserDetailsFetchInProgress)
                CustomCircularProgressIndicator(
                  indicatorColor: Theme.of(context).colorScheme.primary,
                )
              else
                Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(8),
                    color: Theme.of(context).colorScheme.primaryContainer,
                  ),
                  margin: const EdgeInsetsDirectional.symmetric(
                    vertical: 12,
                    horizontal: appContentHorizontalPadding,
                  ),
                  padding: const EdgeInsetsDirectional.symmetric(
                    vertical: appContentHorizontalPadding,
                    horizontal: 8,
                  ),
                  child: Column(
                    crossAxisAlignment: .start,
                    children: <Widget>[
                      CustomTextContainer(
                        textKey: walletBalanceKey,
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                      DesignConfig.defaultHeightSizedBox,
                      BlocListener<GetUserCartCubit, GetUserCartState>(
                        listener: (context, state) {
                          if (state is GetUserCartFetchSuccess) {
                            _isWalletPayment =
                                context
                                    .read<GetUserCartCubit>()
                                    .getCheckoutState()
                                    .useWalletBalance;
                          }
                        },
                        child: Material(
                          child: ListTile(
                            visualDensity: const VisualDensity(
                              horizontal: -4,
                              vertical: -2,
                            ),
                            tileColor: Theme.of(
                              context,
                            ).colorScheme.primaryContainer,
                            contentPadding:
                                const EdgeInsetsDirectional.symmetric(
                                  horizontal: 16,
                                ),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(borderRadius),
                              side: BorderSide(
                                color: Theme.of(
                                  context,
                                ).inputDecorationTheme.iconColor!,
                              ),
                            ),
                            title: CustomTextContainer(
                              textKey:
                                  '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: availableBalanceKey)} : ${Utils.priceWithCurrencySymbol(price: context.read<UserDetailsCubit>().getuserDetails().balance ?? 0, context: context)}',
                            ),
                            leading: Container(
                              width: 18.0,
                              height: 18.0,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: _isWalletPayment
                                      ? Theme.of(context).colorScheme.primary
                                      : Theme.of(context).colorScheme.secondary,
                                  width: 2,
                                ),
                              ),
                              padding: const EdgeInsetsDirectional.all(2.0),
                              child: _isWalletPayment
                                  ? Container(
                                      decoration: BoxDecoration(
                                        color: Theme.of(
                                          context,
                                        ).colorScheme.primary,
                                        shape: BoxShape.circle,
                                      ),
                                    )
                                  : const SizedBox(),
                            ),
                            onTap: changeSelection,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              BlocBuilder<PaymentMethodCubit, PaymentMethodState>(
                bloc: context.read<PaymentMethodCubit>(),
                builder: (context, state) {
                  if (state is PaymentMethodFetchSuccess) {
                    if (state.paymentMethods.isEmpty ||
                        widget.selectedPaymentMethod == null) {
                      return Center(
                        child: CustomTextContainer(
                          textKey: paymentMethodsAreNotAvailableKey,
                        ),
                      );
                    }
                    return IgnorePointer(
                      ignoring: false,
                      child: Padding(
                        padding: const EdgeInsetsDirectional.symmetric(
                          vertical: 8,
                          horizontal: 8,
                        ),
                        child: PaymentMethodList(
                          paymentMethods: state.paymentMethods,
                          paymentMethodCubit: context
                              .read<PaymentMethodCubit>(),
                          onPaymentMethodChanged:
                              context
                                  .read<SettingsAndLanguagesCubit>()
                                  .isStandardShippingEnabled()
                              ? _onPaymentMethodChanged
                              : null,
                        ),
                      ),
                    );
                  }
                  if (state is PaymentMethodFetchFailure) {
                    return ErrorScreen(
                      text: state.errorMessage,
                      onPressed: () {
                        context
                            .read<PaymentMethodCubit>()
                            .fetchPaymentMethods();
                      },
                    );
                  }

                  return const SizedBox.shrink();
                },
              ),
              // COD Pricing Summary Widget
              if (widget.deliverabilityData != null &&
                  context
                      .read<SettingsAndLanguagesCubit>()
                      .isStandardShippingEnabled())
                _buildPricingSummary(),
            ],
          );
        },
      ),
    );
  }

  void _onPaymentMethodChanged(PaymentModel paymentMethod) {
    final isCod = paymentMethod.name == cashOnDeliveryKey;
    setState(() {
      _isCodSelected = isCod;
    });

    // Update cart with COD or non-COD values based on selection
    if (widget.deliverabilityData != null) {
      final cart = context.read<GetUserCartCubit>().getCartDetail();

      if (isCod) {
        // COD selected - use COD values
        if (widget.deliverabilityData!.deliveryChargeWithCod != null) {
          cart.deliveryCharge =
              widget.deliverabilityData!.deliveryChargeWithCod;
        }
        if (widget.deliverabilityData!.finalTotalWithCod != null) {
          cart.overallAmount = widget.deliverabilityData!.finalTotalWithCod;
          cart.originalOverallAmount =
              widget.deliverabilityData!.finalTotalWithCod;
        }
      } else {
        // Non-COD selected - use non-COD values
        if (widget.deliverabilityData!.deliveryChargeWithoutCod != null) {
          cart.deliveryCharge =
              widget.deliverabilityData!.deliveryChargeWithoutCod;
        }
        if (widget.deliverabilityData!.finalTotalWithoutCod != null) {
          cart.overallAmount = widget.deliverabilityData!.finalTotalWithoutCod;
          cart.originalOverallAmount =
              widget.deliverabilityData!.finalTotalWithoutCod;
        }
      }

      // context.read<GetUserCartCubit>().emitSuccessState(cart);
    }

    // Update selected payment method in cart
    context.read<GetUserCartCubit>().changePaymantMethod(paymentMethod);
  }

  Widget _buildPricingSummary() {
    final deliverabilityData = widget.deliverabilityData!;
    final deliveryCharge = _isCodSelected
        ? deliverabilityData.deliveryChargeWithCod
        : deliverabilityData.deliveryChargeWithoutCod;
    final finalTotal = _isCodSelected
        ? deliverabilityData.finalTotalWithCod
        : deliverabilityData.finalTotalWithoutCod;
    final codExtraCharge = deliverabilityData.codExtraCharge;

    return Container(
      margin: const EdgeInsetsDirectional.symmetric(
        vertical: 12,
        horizontal: appContentHorizontalPadding,
      ),
      padding: const EdgeInsetsDirectional.all(12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(8),
        color: Theme.of(context).colorScheme.primaryContainer,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          CustomTextContainer(
            textKey: orderSummaryKey,
            style: Theme.of(context).textTheme.titleMedium,
          ),
          const Divider(height: 16, thickness: 0.5),

          // Cart Subtotal
          _buildPriceRow(
            itemTotalKey,
            Utils.priceWithCurrencySymbol(
              price: deliverabilityData.cartSubtotal ?? 0,
              context: context,
            ),
          ),

          // Delivery Charge
          _buildPriceRow(
            deliveryChargesKey,
            '+${Utils.priceWithCurrencySymbol(price: deliveryCharge ?? 0, context: context)}',
          ),

          // // COD Extra Charge (only show when COD is selected)
          // if (_isCodSelected &&
          //     codExtraCharge != null &&
          //     codExtraCharge > 0) ...[
          //   _buildPriceRow(
          //     context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
          //       labelKey: codExtraChargeKey,
          //     ),
          //     '+${Utils.priceWithCurrencySymbol(price: codExtraCharge, context: context)}',
          //     valueColor: warningColor,
          //   ),
          // ],
          const Divider(height: 16, thickness: 0.5),

          // Final Total
          _buildPriceRow(
            totalAmountKey,
            Utils.priceWithCurrencySymbol(
              price: finalTotal ?? 0,
              context: context,
            ),
            isBold: true,
          ),

          // COD Extra Charge Note
          if (_isCodSelected && codExtraCharge != null && codExtraCharge > 0)
            Container(
              margin: const EdgeInsetsDirectional.only(top: 12),
              padding: const EdgeInsetsDirectional.all(10),
              decoration: BoxDecoration(
                color: blueColor.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(6),
                border: Border.all(color: blueColor.withValues(alpha: 0.5)),
              ),
              child: Row(
                children: [
                  Icon(Icons.info_outline, color: blueColor, size: 18),
                  const SizedBox(width: 8),
                  Expanded(
                    child: CustomTextContainer(
                      textKey:
                          '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: codExtraChargeKey)}: ${Utils.priceWithCurrencySymbol(price: codExtraCharge, context: context)}',
                      style: Theme.of(
                        context,
                      ).textTheme.bodySmall?.copyWith(color: blueColor),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildPriceRow(
    String label,
    String value, {
    bool isBold = false,
    Color? valueColor,
  }) {
    return Padding(
      padding: const EdgeInsetsDirectional.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          CustomTextContainer(
            textKey: label,
            style: isBold
                ? Theme.of(context).textTheme.titleSmall
                : Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Theme.of(
                      context,
                    ).colorScheme.secondary.withValues(alpha: 0.8),
                  ),
          ),
          Text(
            value,
            style: isBold
                ? Theme.of(context).textTheme.titleSmall
                : Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color:
                        valueColor ??
                        Theme.of(
                          context,
                        ).colorScheme.secondary.withValues(alpha: 0.8),
                  ),
          ),
        ],
      ),
    );
  }

  changeSelection() {
    if (context.read<UserDetailsCubit>().getuserDetails().balance! >=
        context.read<GetUserCartCubit>().getCartDetail().overallAmount!) {
      setState(() {
        _isWalletPayment = true;
        _isCodSelected = false;
        Cart cart = context.read<GetUserCartCubit>().getCartDetail();
        if (widget.deliverabilityData != null) {
          cart.deliveryCharge =
              widget.deliverabilityData?.deliveryChargeWithoutCod;
        }
        if (widget.deliverabilityData != null) {
          cart.overallAmount = widget.deliverabilityData?.finalTotalWithoutCod;
          cart.originalOverallAmount =
              widget.deliverabilityData?.finalTotalWithoutCod;
        }
        context.read<GetUserCartCubit>().useWalletBalance(
          _isWalletPayment,
          context.read<UserDetailsCubit>().getuserDetails().balance ?? 0,
        );
        if (_isWalletPayment) {
          if (context.read<GetUserCartCubit>().state
                  is GetUserCartFetchSuccess &&
              context.read<GetUserCartCubit>().getCheckoutState().selectedPaymentMethod == null) {
            if (context.read<PaymentMethodCubit>().state
                is PaymentMethodFetchSuccess) {
              (context.read<PaymentMethodCubit>().state
                      as PaymentMethodFetchSuccess)
                  .paymentMethods
                  .forEach((element) => element.isSelected = false);
            }
          }
        } else {
          // if user deselected wallet payment method then select first payment method
          if (context.read<PaymentMethodCubit>().state
              is PaymentMethodFetchSuccess) {
            context.read<GetUserCartCubit>().changePaymantMethod(
              (context.read<PaymentMethodCubit>().state
                      as PaymentMethodFetchSuccess)
                  .paymentMethods
                  .first,
            );
            (context.read<PaymentMethodCubit>().state
                        as PaymentMethodFetchSuccess)
                    .paymentMethods
                    .first
                    .isSelected =
                true;
          }
        }
        setState(() {});
      });
    } else {
      Utils.showSnackBar(
        message: insufficientWalletBalanceKey,
        context: context,
      );
    }
  }

  void _handlePaymentSuccess(PaymentSuccessResponse response) async {
    Utils.showSnackBar(message: transactionSuccessfulKey, context: context);
    Navigator.pop(context, response);
  }

  void _handlePaymentError(PaymentFailureResponse response) {
    Utils.showSnackBar(message: defaultErrorMessageKey, context: context);
    Navigator.pop(context, response);
  }

  void _handleExternalWallet(ExternalWalletResponse response) {}
}
