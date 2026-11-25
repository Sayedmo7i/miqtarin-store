import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/commons/product/models/product.dart';
import 'package:eshop_plus/ui/profile/address/models/address.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/blocs/userDetailsCubit.dart';
import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/ui/cart/blocs/checkCartProductDelCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/profile/promoCode/blocs/validatePromoCodeCubit.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

/// Orchestrates all cart-screen logic so [CartScreen] stays UI-only.
/// The [context] must be the widget's current [BuildContext]; every method
/// is called synchronously from the widget lifecycle (listeners, button taps)
/// so the context is always valid.
class CartController {
  final BuildContext context;

  CartController(this.context);

  // ─── Convenience getters ──────────────────────────────────────────────────

  bool get isGuest => context.read<UserDetailsCubit>().isGuestUser();

  bool get isStandardShippingEnabled =>
      context.read<SettingsAndLanguagesCubit>().isStandardShippingEnabled();

  Address? get currentSelectedAddress =>
      context.read<DeliveryLocationCubit>().currentSelectedAddress;

  // ─── Cart loading ─────────────────────────────────────────────────────────

  void fetchCart(int storeId, {bool background = false}) {
    context.read<GetUserCartCubit>().fetchUserCart(
      refreshCartInBackground: background,
      params: {
        ApiURL.storeIdApiKey: storeId,
        ApiURL.addressIdApiKey: currentSelectedAddress?.id ?? '',
      },
    );
  }

  // ─── Deliverability ───────────────────────────────────────────────────────

  /// Calls the deliverability API.
  /// Returns early (silently) if no address is selected — the caller decides
  /// whether to navigate directly in that case.
  void checkDeliverability(int storeId, {bool emitProgress = false}) {
    final address = currentSelectedAddress;
    if (address == null) return;

    final checkout = context.read<GetUserCartCubit>().getCheckoutState();
    final state = context.read<GetUserCartCubit>().state;
    if (state is GetUserCartFetchSuccess &&
        state.cart.cartProducts!.isNotEmpty &&
        state.cart.cartProducts![0].type != digitalProductType)
      context.read<CheckCartProductDeliverabilityCubit>().checkDeliverability(
        storeId: storeId,
        addressId: address.id!,
        shippingOption: checkout.shippingOption,
        promocode: checkout.promoCode?.promoCode,
        emitProgressState: emitProgress,
        isStandardShippingEnabled: isStandardShippingEnabled,
      );
  }

  // ─── Promo validation ─────────────────────────────────────────────────────

  /// Re-validates the currently applied promo code against [total].
  /// Does nothing if no promo is applied.
  void validatePromo(double total) {
    final promo = context.read<GetUserCartCubit>().getCheckoutState().promoCode;
    if (promo == null) return;

    context.read<ValidatePromoCodeCubit>().validatePromoCode(
      params: {
        ApiURL.finalTotalApiKey: total,
        ApiURL.promoCodeApiKey: promo.promoCode,
      },
    );
  }

  // ─── Navigation ───────────────────────────────────────────────────────────

  void navigateToCheckout(int storeId, {bool showAddressSelection = false}) {
    Utils.navigateToScreen(
      context,
      Routes.placeOrderScreen,
      arguments: {
        'showAddressSelection': showAddressSelection,
        'storeId': storeId,
        'checkCartProductDeliverabilityCubit': context
            .read<CheckCartProductDeliverabilityCubit>(),
      },
    );
  }
}
