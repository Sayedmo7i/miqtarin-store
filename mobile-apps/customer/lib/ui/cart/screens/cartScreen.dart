import 'package:eshop_plus/commons/product/models/product.dart';
import 'package:eshop_plus/core/constants/appAssets.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/cart/controller/cartController.dart';
import 'package:eshop_plus/ui/cart/widgets/deliveryAddressWidget.dart';
import 'package:eshop_plus/ui/cart/blocs/checkCartProductDelCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/blocs/manageCartCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/removeProductFromCartCubit.dart';
import 'package:eshop_plus/ui/cart/widgets/shippingOptionsWidget.dart';
import 'package:eshop_plus/ui/profile/promoCode/blocs/validatePromoCodeCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/ui/profile/promoCode/models/promoCode.dart';
import 'package:eshop_plus/ui/cart/widgets/cartProductList.dart';
import 'package:eshop_plus/ui/cart/widgets/priceDetailContainer.dart';
import 'package:eshop_plus/commons/widgets/customAppbar.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customDefaultContainer.dart';
import 'package:eshop_plus/commons/widgets/customRoundedButton.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/commons/widgets/error_screen.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';

class CartScreen extends StatefulWidget {
  final bool shoulPop;
  final int? storeId;
  const CartScreen({Key? key, this.shoulPop = false, this.storeId})
    : super(key: key);

  static Widget getRouteInstance() => MultiBlocProvider(
    providers: [
      BlocProvider(create: (_) => RemoveFromCartCubit()),
      BlocProvider(create: (_) => CheckCartProductDeliverabilityCubit()),
    ],
    child: CartScreen(
      shoulPop: Get.arguments != null && Get.arguments.containsKey('shoulPop')
          ? Get.arguments['shoulPop'] ?? true
          : true,
      storeId: Get.arguments != null && Get.arguments.containsKey('storeId')
          ? Get.arguments['storeId'] as int?
          : null,
    ),
  );

  @override
  _CartScreenState createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  GlobalKey _cartListKey = GlobalKey();
  GlobalKey _priceDetailKey = GlobalKey();

  double? _lastValidatedTotal;
  String? _lastValidatedPromo;
  late int storeId;
  late CartController _controller;
  bool _isLoading = false;

  /// True only when the user tapped "Proceed to Checkout".
  /// Prevents auto-navigation on the background deliverability check that runs
  /// on initial cart load.
  bool _allowNavigationOnDeliverabilitySuccess = false;

  @override
  void initState() {
    super.initState();
    storeId =
        widget.storeId ?? context.read<StoresCubit>().getDefaultStore().id!;
    _controller = CartController(context);

    Future.delayed(Duration.zero, () {
      if (!mounted) return;
      if (!_controller.isGuest) {
        _controller.fetchCart(storeId);
      }
      if (context.read<CheckCartProductDeliverabilityCubit>().state
              is CheckCartProductDeliverabilityInitial &&
          _controller.currentSelectedAddress != null) {
        _controller.checkDeliverability(storeId);
      }
    });
  }

  // ─── Helpers ───────────────────────────────────────────────────────────────

  /// Reassigns GlobalKeys so child widgets fully remount on the next rebuild.
  /// Must call setState so the reassigned keys actually reach the widget tree.
  void refreshState() {
    if (!mounted) return;
    setState(() {
      _cartListKey = GlobalKey();
      _priceDetailKey = GlobalKey();
    });
  }

  /// Decides whether to call the deliverability API or navigate directly.
  /// Direct navigation is used for digital products or when no address exists.
  void _triggerDeliverabilityOrNavigate(
    GetUserCartFetchSuccess state, {
    bool emitProgress = false,
  }) {
    if (!mounted) return;
    if (!(ModalRoute.of(context)?.isCurrent ?? false)) return;

    final isDigital =
        state.cart.cartProducts!.isNotEmpty &&
        state.cart.cartProducts![0].type == digitalProductType;

    if (!isDigital && _controller.currentSelectedAddress != null) {
      _controller.checkDeliverability(storeId, emitProgress: emitProgress);
    } else if (_allowNavigationOnDeliverabilitySuccess) {
      setState(() {
        _isLoading = false;
        _allowNavigationOnDeliverabilitySuccess = false;
      });
      _controller.navigateToCheckout(storeId, showAddressSelection: true);
    }
  }

  // ─── Build ────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<ManageCartCubit, ManageCartState>(
      listener: (context, manageState) {
        if (manageState is ManageCartFetchSuccess) {
          // Use isActive (not isCurrent) so the cart screen can still process
          // the result when a modal bottom sheet is on top (bottom sheets push
          // a new route, making isCurrent false while the API is in-flight).
          if (!(ModalRoute.of(context)?.isActive ?? false)) return;
          final checkoutState = context
              .read<GetUserCartCubit>()
              .getCheckoutState();

          // Update save-for-later list from API response if provided
          if (manageState.savedForLaterProducts!.isNotEmpty) {
            checkoutState.saveForLaterProducts =
                manageState.savedForLaterProducts;
          }

          // Remove any saved-for-later item that is now in the active cart
          if (checkoutState.saveForLaterProducts != null &&
              checkoutState.saveForLaterProducts!.isNotEmpty &&
              manageState.cart.cartProducts != null &&
              manageState.cart.cartProducts!.isNotEmpty) {
            final cartVariantIds = manageState.cart.cartProducts!
                .map((p) => p.productVariantId)
                .toSet();
            checkoutState.saveForLaterProducts!.removeWhere(
              (p) => cartVariantIds.contains(p.productVariantId),
            );
          }

          context.read<GetUserCartCubit>().emitSuccessState(manageState.cart);
          refreshState();
        }
      },
      builder: (context, mstate) {
        return MultiBlocListener(
          listeners: [
            BlocListener<RemoveFromCartCubit, RemoveFromCartState>(
              listener: (context, removeState) {
                if (removeState is RemoveFromCartFetchSuccess) {
                  // Use the cart returned by the remove API so prices update
                  context.read<GetUserCartCubit>().emitSuccessState(
                    removeState.cart,
                  );
                  refreshState();
                }
              },
            ),
            BlocListener<ValidatePromoCodeCubit, ValidatePromoCodeState>(
              listener: _onPromoStateChanged,
            ),
          ],
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              IgnorePointer(
                ignoring: mstate is ManageCartFetchInProgress,
                child: Scaffold(
                  appBar: CustomAppbar(
                    titleKey: cartKey,
                    showBackButton: widget.shoulPop,
                  ),
                  body: _controller.isGuest
                      ? ErrorScreen(
                          onPressed: () => Utils.navigateToScreen(
                            context,
                            Routes.loginScreen,
                            arguments: Routes.cartScreen,
                          ),
                          text: loginToAddToCartKey,
                          buttonText: loginKey,
                          image: AppAssets.emptyCart,
                        )
                      : BlocConsumer<GetUserCartCubit, GetUserCartState>(
                          listener: _onCartStateChanged,

                          builder: _buildCartBody,
                        ),
                ),
              ),
              if (mstate is ManageCartFetchInProgress) _overlaySpinner(),
            ],
          ),
        );
      },
    );
  }

  // ─── Cart state listener ──────────────────────────────────────────────────

  void _onCartStateChanged(BuildContext context, GetUserCartState state) {
    // if (!(ModalRoute.of(context)?.isCurrent ?? false)) return;
    if (state is GetUserCartFetchSuccess &&
        state.cart.cartProducts != null &&
        state.cart.cartProducts!.isNotEmpty) {
      // Re-validate promo if cart total or promo code changed
      final checkout = context.read<GetUserCartCubit>().getCheckoutState();
      if (checkout.promoCode != null &&
          (state.cart.subTotal != _lastValidatedTotal ||
              checkout.promoCode!.promoCode != _lastValidatedPromo)) {
        setState(() {
          _lastValidatedTotal = state.cart.subTotal;
          _lastValidatedPromo = checkout.promoCode!.promoCode;
        });
        _controller.validatePromo(state.cart.subTotal!);
      }

      // Mark out-of-stock products with an error message
      if (state.cart.outOfStockProducts != null &&
          state.cart.outOfStockProducts!.isNotEmpty) {
        for (var pr in state.cart.outOfStockProducts!) {
          final index = state.cart.cartProducts!.indexWhere(
            (e) => e.id == pr.id,
          );
          if (index != -1) {
            state.cart.cartProducts![index].errorMessage =
                productIsCurrentlyOutOfStockKey;
          }
        }
      }

      if (_controller.isStandardShippingEnabled) {
        _triggerDeliverabilityOrNavigate(state);
      }
    }
  }

  // ─── Cart body builder ────────────────────────────────────────────────────

  Widget _buildCartBody(BuildContext context, GetUserCartState state) {
    if (state is GetUserCartFetchSuccess) {
      final isEmpty =
          (state.cart.cartProducts == null ||
              state.cart.cartProducts!.isEmpty) &&
          (context
                      .read<GetUserCartCubit>()
                      .getCheckoutState()
                      .saveForLaterProducts ==
                  null ||
              context
                  .read<GetUserCartCubit>()
                  .getCheckoutState()
                  .saveForLaterProducts!
                  .isEmpty);

      return Stack(
        clipBehavior: Clip.none,
        children: [
          isEmpty
              ? ErrorScreen(
                  text: emptyCartkey,
                  image: AppAssets.emptyCart,
                  onPressed: () {},
                  child: null,
                )
              : _buildBodyContent(state, context),
          _buildPlaceOrderButton(state),
          if (state is GetUserCartFetchInProgress ||
              context.read<CheckCartProductDeliverabilityCubit>().state
                  is CheckCartProductDeliverabilityInProgress)
            _overlaySpinner(),
        ],
      );
    }

    if (state is GetUserCartFetchFailure) {
      return ErrorScreen(
        onPressed: () => _controller.fetchCart(storeId),
        text: state.errorMessage,
        image: state.errorMessage == noInternetKey
            ? AppAssets.noInternet
            : AppAssets.emptyCart,
        child: state is GetUserCartFetchInProgress
            ? CustomCircularProgressIndicator(
                indicatorColor: Theme.of(context).colorScheme.primary,
              )
            : null,
      );
    }

    return CustomCircularProgressIndicator(
      indicatorColor: Theme.of(context).colorScheme.primary,
    );
  }

  // ─── Promo state listener ─────────────────────────────────────────────────

  void _onPromoStateChanged(
    BuildContext context,
    ValidatePromoCodeState promoState,
  ) {
    if (!(ModalRoute.of(context)?.isActive ?? false)) return;
    final cartCubit = context.read<GetUserCartCubit>();
    if (cartCubit.state is! GetUserCartFetchSuccess) {
      return;
    }
    final cart = (cartCubit.state as GetUserCartFetchSuccess).cart;
    if (promoState is ValidatePromoCodeFetchSuccess) {
      //for shiprocket , we calculate the delivery charge from the check_cart_product_delivarable api
      if (_controller.isStandardShippingEnabled) {
        cart.overallAmount = cart.deliveryCharge != 0
            ? promoState.promoCode.finalTotal! + cart.deliveryCharge!
            : promoState.promoCode.finalTotal!;
      }
      cartCubit.syncPromoCodeState(
        promoState.promoCode,
        promoState.promoCode.finalDiscount!,
      );
      _controller.checkDeliverability(storeId);
    }

    if (promoState is ValidatePromoCodeFetchFailure) {
      Utils.showSnackBar(context: context, message: promoState.errorMessage);
      cart.overallAmount = promoState.finalTotal;
      cartCubit.syncPromoCodeState(null, 0);
    }
  }

  // ─── Body content ─────────────────────────────────────────────────────────

  Widget _buildBodyContent(
    GetUserCartFetchSuccess state,
    BuildContext context,
  ) {
    return ListView(
      padding: EdgeInsets.only(
        top: 12,
        bottom: MediaQuery.of(context).padding.bottom + 58,
      ),
      children: [
        CartProductList(
          key: _cartListKey,
          cart: state.cart,
          removeFromCartCubit: context.read<RemoveFromCartCubit>(),
        ),
        DesignConfig.smallHeightSizedBox,
        if (state.cart.cartProducts != null &&
            state.cart.cartProducts!.isNotEmpty &&
            state.cart.cartProducts![0].type != digitalProductType &&
            _controller.currentSelectedAddress != null)
          DeliveryAddressWidget(storeId: storeId, isFinalCartScreen: false),
        if (state.cart.cartProducts != null &&
            state.cart.cartProducts!.isNotEmpty)
          _offerContainer(
            context.read<GetUserCartCubit>().getCheckoutState().promoCode,
          ),
        BlocBuilder<
          CheckCartProductDeliverabilityCubit,
          CheckCartProductDeliverabilityState
        >(
          builder: (context, delState) {
            if (delState is CheckCartProductDeliverabilitySuccess &&
                delState.deliverabilityData?.shippingOptions != null &&
                state.cart.cartProducts != null &&
                state.cart.cartProducts!.isNotEmpty) {
              return ShippingOptionsWidget(
                shippingOptions: delState.deliverabilityData!.shippingOptions,
                defaultShippingOption:
                    context
                        .read<GetUserCartCubit>()
                        .getCheckoutState()
                        .shippingOption ??
                    delState.deliverabilityData!.defaultShippingOption,
                onShippingOptionChanged: (selectedOption) {
                  context.read<GetUserCartCubit>().updateShippingOption(
                    selectedOption,
                  );
                  setState(() => _isLoading = true);
                  _controller.checkDeliverability(storeId);
                },
              );
            }
            return const SizedBox.shrink();
          },
        ),
        if (state.cart.cartProducts != null &&
            state.cart.cartProducts!.isNotEmpty)
          PriceDetailContainer(key: _priceDetailKey, cart: state.cart),
      ],
    );
  }

  // ─── Offer / promo row ────────────────────────────────────────────────────

  Widget _offerContainer(PromoCode? promoCode) {
    return GestureDetector(
      onTap: () => Utils.navigateToScreen(
        context,
        Routes.promoCodeScreen,
        arguments: {'fromCartScreen': true},
      ),
      child: Padding(
        padding: const EdgeInsets.only(bottom: 8.0),
        child: CustomDefaultContainer(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: CustomTextContainer(
                  textKey: promoCode != null
                      ? promoCode.promoCode!
                      : addCouponCodeKey,
                  style: Theme.of(context).textTheme.titleMedium,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              if (promoCode != null)
                IconButton(
                  visualDensity: const VisualDensity(
                    vertical: -4,
                    horizontal: -4,
                  ),
                  onPressed: () =>
                      context.read<GetUserCartCubit>().removePromoCode(),
                  icon: const Icon(Icons.close, size: 24),
                )
              else
                const Icon(Icons.arrow_forward_ios, size: 24),
            ],
          ),
        ),
      ),
    );
  }

  // ─── Place order button + deliverability listener ─────────────────────────

  Widget _buildPlaceOrderButton(GetUserCartState state) {
    if (state is! GetUserCartFetchSuccess ||
        state.cart.cartProducts == null ||
        state.cart.cartProducts!.isEmpty) {
      return const SizedBox.shrink();
    }

    return BlocListener<
      CheckCartProductDeliverabilityCubit,
      CheckCartProductDeliverabilityState
    >(
      listener: (context, deliveryState) {
        if (!(ModalRoute.of(context)?.isCurrent ?? false)) return;
        if (deliveryState is CheckCartProductDeliverabilitySuccess) {
          setState(() => _isLoading = false);

          if (_controller.isStandardShippingEnabled &&
              deliveryState.deliverabilityData != null) {
            Utils.updateCartWithDeliverabilityData(
              state.cart,
              deliveryState.deliverabilityData!,
            );
          }

          if (_allowNavigationOnDeliverabilitySuccess) {
            setState(() => _allowNavigationOnDeliverabilitySuccess = false);
            context.read<GetUserCartCubit>().resetErrorMessages();
            _controller.navigateToCheckout(storeId);
          }
        }

        if (deliveryState is CheckCartProductDeliverabilityFailure) {
          setState(() => _isLoading = false);

          if (deliveryState.errorData != null) {
            context.read<GetUserCartCubit>().setErrorMessage(
              deliveryState.errorData!,
            );
          }

          // Show snackbar only when the cart screen is the active route
          if (!(ModalRoute.of(context)?.isCurrent ?? false)) return;

          Utils.showSnackBar(
            context: context,
            duration: const Duration(seconds: 5),
            backgroundColor: Theme.of(context).colorScheme.error,
            message: deliveryState.errorMessage,
          );
        }
      },
      child: Align(
        alignment: Alignment.bottomCenter,
        child: Container(
          margin: EdgeInsetsDirectional.only(
            start: appContentHorizontalPadding,
            end: appContentHorizontalPadding,
            top: 8,
            bottom: MediaQuery.of(context).padding.bottom + 8,
          ),
          child: CustomRoundedButton(
            widthPercentage: 1,
            buttonTitle: proceedToCheckoutKey,
            showBorder: false,
            child: _isLoading
                ? CustomCircularProgressIndicator(
                    indicatorColor: Theme.of(context).scaffoldBackgroundColor,
                  )
                : null,
            onTap: () => _onProceedToCheckoutTapped(state),
          ),
        ),
      ),
    );
  }

  void _onProceedToCheckoutTapped(GetUserCartFetchSuccess state) {
    if (state.cart.outOfStockProducts?.isNotEmpty == true) {
      Utils.showSnackBar(
        context: context,
        duration: const Duration(seconds: 7),
        message: removeOutOfStockProductErrorMessageKey,
      );
      return;
    }

    final minAmount = double.parse(
      context
          .read<SettingsAndLanguagesCubit>()
          .getSettings()
          .systemSettings!
          .minimumCartAmount!
          .toString(),
    );
    if (state.cart.subTotal != null && state.cart.subTotal! < minAmount) {
      Utils.showSnackBar(
        context: context,
        duration: const Duration(seconds: 7),
        message:
            '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: minOrderAmountWarning1Key)}'
            '${Utils.priceWithCurrencySymbol(context: context, price: minAmount)}. '
            '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: minOrderAmountWarning2Key)}',
      );
      return;
    }

    setState(() {
      _allowNavigationOnDeliverabilitySuccess = true;
      _isLoading = true;
    });
    _triggerDeliverabilityOrNavigate(state);
  }

  // ─── Shared overlay ───────────────────────────────────────────────────────

  Widget _overlaySpinner() {
    return Container(
      height: double.maxFinite,
      width: double.maxFinite,
      color: blackColor.withValues(alpha: 0.5),
      alignment: Alignment.center,
      child: CustomCircularProgressIndicator(
        indicatorColor: Theme.of(context).colorScheme.primary,
      ),
    );
  }
}
