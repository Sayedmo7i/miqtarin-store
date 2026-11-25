import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/core/constants/hiveConstants.dart';
import 'package:eshop_plus/ui/cart/blocs/checkCartProductDelCubit.dart';
import 'package:eshop_plus/ui/profile/promoCode/blocs/localPromoCodeCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/blocs/userDetailsCubit.dart';
import 'package:eshop_plus/ui/cart/models/cart.dart';
import 'package:eshop_plus/ui/profile/transaction/models/paymentMethod.dart';
import 'package:eshop_plus/ui/profile/promoCode/models/promoCode.dart';
import 'package:eshop_plus/ui/cart/repositories/cartRepository.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:flutter/material.dart';

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hive/hive.dart';

abstract class GetUserCartState {}

class GetUserCartInitial extends GetUserCartState {}

class GetUserCartFetchInProgress extends GetUserCartState {}

class GetUserCartFetchInProgressForAddress extends GetUserCartState {}

class GetUserCartFetchSuccess extends GetUserCartState {
  Cart cart;

  GetUserCartFetchSuccess({required this.cart});
}

class GetUserCartFetchFailure extends GetUserCartState {
  final String errorMessage;

  GetUserCartFetchFailure(this.errorMessage);
}

class GetUserCartCubit extends Cubit<GetUserCartState> {
  final CartRepository _cartRepository = CartRepository();
  CartCheckoutState _checkoutState = CartCheckoutState();

  GetUserCartCubit() : super(GetUserCartInitial());

  void fetchUserCart({
    required bool refreshCartInBackground,
    required Map<String, dynamic> params,
  }) {
    if (!refreshCartInBackground) emit(GetUserCartFetchInProgress());

    _cartRepository
        .fetchUserCart(params: params)
        .then((result) async {
          if (!isClosed) {
            // Update saved-for-later in checkout state from fresh API data

            _checkoutState.saveForLaterProducts = result.savedForLaterProducts;
            emit(GetUserCartFetchSuccess(cart: result.cart));
          } else {
            emit(GetUserCartFetchFailure(defaultErrorMessageKey));
          }
        })
        .catchError((e) {
          if (!isClosed) emit(GetUserCartFetchFailure(e.toString()));
        });
  }

  void fetchUserCartSilently({required Map<String, dynamic> params}) {
    // Don't emit loading state - fetch silently in background
    _cartRepository
        .fetchUserCart(params: params)
        .then((result) async {
          if (!isClosed) {
            // Update saved-for-later in checkout state from fresh API data
            // if (cart.saveForLaterProducts != null) {
            //   _checkoutState.saveForLaterProducts = cart.saveForLaterProducts;
            // }
            _checkoutState.saveForLaterProducts = result.savedForLaterProducts;
            emit(GetUserCartFetchSuccess(cart: result.cart));
          }
        })
        .catchError((e) {
          // Silent fetch - don't emit error state to avoid disrupting UI
          // Only emit error if there's no existing success state
          if (!isClosed && state is! GetUserCartFetchSuccess) {
            emit(GetUserCartFetchFailure(e.toString()));
          }
        });
  }

  Cart getCartDetail() {
    if (state is GetUserCartFetchSuccess) {
      return (state as GetUserCartFetchSuccess).cart;
    }
    return Cart.fromJson({});
  }

  CartCheckoutState getCheckoutState() => _checkoutState;

  emitSuccessState(Cart cart) {
    if (!isClosed) emit(GetUserCartFetchSuccess(cart: cart));
  }

  getCartProductLength() {
    if (state is GetUserCartFetchSuccess) {
      Cart cart = (state as GetUserCartFetchSuccess).cart;
      return cart.cartProducts != null && cart.cartProducts!.isNotEmpty
          ? cart.cartProducts!.length
          : 0;
    }
    return 0;
  }

  addDeliveryInstruction(String instruction) {
    _checkoutState.deliveryInstruction = instruction;
  }

  addEmailAddress(String emailAddress) {
    _checkoutState.emailAddress = emailAddress;
  }

  changePaymantMethod(PaymentModel? paymentModel) {
    _checkoutState.selectedPaymentMethod = paymentModel;
  }

  useWalletBalance(
    bool useWalletBalance,
    double walletBalance, {
    Cart? newCart,
  }) {
    late Cart cart;
    if (newCart != null) {
      cart = newCart;
    } else if (state is GetUserCartFetchSuccess) {
      cart = (state as GetUserCartFetchSuccess).cart;
    }
    _checkoutState.useWalletBalance = useWalletBalance;
    if (useWalletBalance) {
      if (walletBalance >= cart.overallAmount!) {
        // Case 1: Wallet has enough balance to cover the total amount
        _checkoutState.walletAmount = cart.overallAmount;
        cart.overallAmount = 0.0;
        _checkoutState.selectedPaymentMethod = null;
      } else {
        // Case 2: Wallet has less balance than the total amount
        double remainingAmount = cart.overallAmount! - walletBalance;
        _checkoutState.walletAmount = walletBalance;
        cart.overallAmount = remainingAmount;
      }
    } else {
      // Wallet deselected
      _checkoutState.useWalletBalance = false;
      cart.overallAmount = cart.originalOverallAmount;
      _checkoutState.walletAmount = 0.0;
    }
    emit(GetUserCartFetchSuccess(cart: cart));
  }

  setErrorMessage(List<Map<String, dynamic>> errors) {
    if (state is! GetUserCartFetchSuccess) return;

    final cartState = state as GetUserCartFetchSuccess;
    final products = cartState.cart.cartProducts;

    if (products == null || products.isEmpty) return;

    for (final error in errors) {
      final productId = error['product_id'];
      final variantId = error['variant_id'];
      final message = error['message'];

      final index = products.indexWhere((product) {
        if (variantId != null) {
          return product.productVariantId == variantId;
        }
        return product.productId == productId;
      });

      if (index != -1) {
        products[index].errorMessage = message != null && message != ''
            ? message
            : notDelierableErrorMessageKey;
      }
    }
  }

  resetErrorMessages() {
    if (state is GetUserCartFetchSuccess) {
      for (var product
          in (state as GetUserCartFetchSuccess).cart.cartProducts!) {
        product.errorMessage = '';
      }
    }
  }

  setPromoCode(BuildContext context, PromoCode promoCode) {
    if (state is GetUserCartFetchSuccess) {
      LocalPromocodeCubit().applyPromocode(
        storeId: context.read<StoresCubit>().getDefaultStore().id!,
        promoCode: promoCode,
      );
      Cart cart = (state as GetUserCartFetchSuccess).cart;
      if (cart.deliveryCharge != 0) {
        cart.overallAmount = promoCode.finalTotal! + cart.deliveryCharge!;
      } else {
        cart.overallAmount = promoCode.finalTotal!;
      }
      _checkoutState.promoCode = promoCode;
      _checkoutState.couponDiscount = promoCode.finalDiscount!;
      emitSuccessState(cart);
      if (_checkoutState.useWalletBalance == true) {
        useWalletBalance(
          true,
          context.read<UserDetailsCubit>().getuserDetails().balance ?? 0,
        );
      }
    }
  }

  removePromoCode() {
    if (state is GetUserCartFetchSuccess) {
      Cart cart = (state as GetUserCartFetchSuccess).cart;
      cart.overallAmount = cart.originalOverallAmount;
      LocalPromocodeCubit().removePromocode(cart.cartProducts!.first.storeId!);
      _checkoutState.promoCode = null;
      _checkoutState.couponDiscount = 0;
      emitSuccessState(cart);
    }
  }

  double calculateItemTotalForProduct(Cart cart, int productId) {
    double total = 0.0;

    // Iterate over each cart product
    for (var cartProduct in cart.cartProducts!) {
      // Check if the product ID matches the specific product ID
      if (cartProduct.productDetails![0].id == productId) {
        // Add the total of this variant to the running total
        total += cartProduct.specialPrice! * cartProduct.qty!;
      }
    }

    return total;
  }

  updateCart({
    required BuildContext context,
    required Cart oldCart,
    required bool checkDeliverability,
    required Map<String, dynamic> params,
  }) {
    _cartRepository
        .fetchUserCart(params: params, isCallForSavedForLater: false)
        .then((result) {
          emit(GetUserCartFetchSuccess(cart: result.cart));
          if (_checkoutState.useWalletBalance == true) {
            useWalletBalance(
              true,
              _checkoutState.walletAmount!,
              newCart: result.cart,
            );
          } else {
            emit(GetUserCartFetchSuccess(cart: result.cart));
          }
          if (checkDeliverability)
            context
                .read<CheckCartProductDeliverabilityCubit>()
                .checkDeliverability(
                  storeId: context.read<StoresCubit>().getDefaultStore().id!,
                  addressId: context
                      .read<DeliveryLocationCubit>()
                      .currentSelectedAddress!
                      .id!,
                  shippingOption: _checkoutState.shippingOption,
                  promocode: _checkoutState.promoCode?.promoCode,
                  isStandardShippingEnabled: context
                      .read<SettingsAndLanguagesCubit>()
                      .isStandardShippingEnabled(),
                );
        })
        .catchError((e) {
          emit(GetUserCartFetchFailure(e.toString()));
        });
  }

  resetCart(BuildContext context) {
    LocalPromocodeCubit().removePromocode(
      context.read<StoresCubit>().getDefaultStore().id!,
    );
    Hive.box(affiliateReferencesBoxKey).clear();
    _checkoutState = CartCheckoutState();
    if (state is GetUserCartFetchSuccess) {
      // Create a new empty Cart object
      Cart cart = Cart(
        cartProducts: [], // Explicitly setting cartProducts to an empty list
      );

      // Emit loading state before emitting the updated state
      emit(GetUserCartFetchInProgress());

      // Emit the updated state with the reset cart
      emit(GetUserCartFetchSuccess(cart: cart));
    }
  }

  void setStripePayId(String stripePayId) {
    _checkoutState.stripePayId = stripePayId;
  }

  void updateShippingOption(String? option) {
    _checkoutState.shippingOption = option;
  }

  void updateAttachment(int id, String path) {
    _checkoutState.attachments[id] = path;
  }

  void removeAttachment(int id) {
    _checkoutState.attachments.remove(id);
  }

  void syncPromoCodeState(PromoCode? promoCode, double? couponDiscount) {
    _checkoutState.promoCode = promoCode;
    _checkoutState.couponDiscount = couponDiscount;
  }
}
