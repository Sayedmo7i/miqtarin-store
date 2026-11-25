import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/commons/product/models/product.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/blocs/manageCartCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/removeProductFromCartCubit.dart';
import 'package:eshop_plus/ui/cart/models/cart.dart';

import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class QuantitySelector extends StatefulWidget {
  final CartProduct product;
  final bool primaryTheme;
  final String? stock;
  const QuantitySelector({
    Key? key,
    required this.product,
    required this.primaryTheme,
    this.stock,
  }) : super(key: key);

  @override
  _QuantitySelectorState createState() => _QuantitySelectorState();
}

class _QuantitySelectorState extends State<QuantitySelector> {
  late int initialQuantity;
  late int minimumOrderQuantity;
  late int maximumAllowedQuantity;
  late String stockType;
  late String stock;
  late int quantityStepSize;
  late final CartProduct product;

  @override
  void initState() {
    super.initState();
    product = widget.product;
  }

  void _increaseQuantity() {
    if (stockType != "" &&
        int.parse(stock) < initialQuantity + quantityStepSize) {
      Utils.showSnackBar(context: context, message: stockLimitReachedKey);
      return;
    }
    if (maximumAllowedQuantity == 0 ||
        initialQuantity + quantityStepSize <= maximumAllowedQuantity) {
      setState(() {
        initialQuantity += quantityStepSize;
      });
      onQuantityChanged(initialQuantity);
    } else {
      Utils.showSnackBar(context: context, message: maxQuantityReachedKey);
      return;
    }
  }

  void _decreaseQuantity(RemoveFromCartCubit removeFromCartCubit) {
    if (initialQuantity - quantityStepSize >= minimumOrderQuantity) {
      setState(() {
        initialQuantity -= quantityStepSize;
      });
      onQuantityChanged(initialQuantity);
    } else {
      removeProductFromCartFunction(removeFromCartCubit);
      return;
    }
  }

  onQuantityChanged(int newQty) {
    context.read<ManageCartCubit>().manageUserCart(
      widget.product.cartProductType == comboType
          ? widget.product.id
          : widget.product.productVariantId,
      reloadCart: false,
      changeQuantity: true,
      params: {
        ApiURL.storeIdApiKey: widget.product.storeId,
        ApiURL.productVariantIdApiKey:
            widget.product.cartProductType == comboType
            ? widget.product.id
            : widget.product.productVariantId,
        ApiURL.productTypeApiKey: widget.product.cartProductType == comboType
            ? comboType
            : regularType,
        ApiURL.isSavedForLaterApiKey: 0,
        ApiURL.qtyApiKey: newQty,
        ApiURL.addressIdApiKey:
            context.read<DeliveryLocationCubit>().currentSelectedAddress != null
            ? context.read<DeliveryLocationCubit>().currentSelectedAddress!.id!
            : '',
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    initialQuantity =
        widget.product.qty ?? widget.product.minimumOrderQuantity ?? 1;
    minimumOrderQuantity = widget.product.minimumOrderQuantity ?? 1;
    maximumAllowedQuantity = widget.product.totalAllowedQuantity ?? 1;
    quantityStepSize = widget.product.quantityStepSize ?? 1;
    stockType = widget.product.productDetails![0].stockType ?? '';
    stock =
        widget.stock ??
        (widget.product.type == variableProductType
            ? widget.product.productVariants![0].stock!
            : widget.product.productDetails![0].stock!);
    Color fontColor = widget.primaryTheme
        ? Theme.of(context).colorScheme.onPrimary
        : Theme.of(context).colorScheme.secondary;
    return BlocProvider(
      create: (context) => RemoveFromCartCubit(),
      child: BlocConsumer<RemoveFromCartCubit, RemoveFromCartState>(
        listener: (context, removestate) {
          if (removestate is RemoveFromCartFetchSuccess) {
            // Use the cart returned by the remove API so prices update
            context.read<GetUserCartCubit>().emitSuccessState(removestate.cart);
          }
        },
        builder: (context, removestate) {
          return Container(
            height: 26,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: widget.primaryTheme
                  ? Theme.of(context).colorScheme.primary
                  : Theme.of(context).scaffoldBackgroundColor,
              border: Border.all(
                width: 0.5,
                color: widget.primaryTheme
                    ? Theme.of(context).colorScheme.onPrimary
                    : Theme.of(context).inputDecorationTheme.iconColor!,
              ),
              borderRadius: BorderRadius.circular(4.0),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: .center,
              crossAxisAlignment: .center,
              children: [
                BlocBuilder<ManageCartCubit, ManageCartState>(
                  builder: (context, state) {
                    return IconButton(
                      visualDensity: VisualDensity(
                        vertical: -4,
                        horizontal: -2,
                      ),
                      padding: .zero,
                      icon: Icon(
                        Icons.remove,
                        color: fontColor,
                        /*  initialQuantity - quantityStepSize >=
                                widget.minimumOrderQuantity
                            ? fontColor
                            : greyColor, */
                      ),
                      onPressed: () {
                        if (state is ManageCartFetchInProgress &&
                            state.cartProductId ==
                                (widget.product.cartProductType == comboType
                                    ? widget.product.id
                                    : widget.product.productVariantId))
                          return;
                        _decreaseQuantity(context.read<RemoveFromCartCubit>());
                      },
                    );
                  },
                ),
                Text(
                  '$initialQuantity',
                  style: Theme.of(
                    context,
                  ).textTheme.bodyMedium!.copyWith(color: fontColor),
                ),
                BlocBuilder<ManageCartCubit, ManageCartState>(
                  builder: (context, state) {
                    return IconButton(
                      visualDensity: VisualDensity(
                        vertical: -4,
                        horizontal: -2,
                      ),
                      padding: .zero,
                      icon: Icon(
                        Icons.add,
                        color:
                            maximumAllowedQuantity == 0 ||
                                initialQuantity + quantityStepSize <=
                                    maximumAllowedQuantity
                            ? fontColor
                            : greyColor,
                      ),
                      onPressed: () {
                        if (state is ManageCartFetchInProgress &&
                            state.cartProductId ==
                                (widget.product.cartProductType == comboType
                                    ? widget.product.id
                                    : widget.product.productVariantId))
                          return;
                        _increaseQuantity();
                      },
                    );
                  },
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  removeProductFromCartFunction(RemoveFromCartCubit removeFromCartCubit) {
    List<CartProduct>? products = context
        .read<GetUserCartCubit>()
        .getCartDetail()
        .cartProducts;
    // if (removeFromCartCubit.state is RemoveFromCartFetchInProgress &&
    //     removeFromCartCubit.state.products!
    //             .firstWhere((element) => element.id == widget.product.id)
    //             .removeProductInProgress ==
    //         true)
    //   return;

    removeFromCartCubit.removeProductFromCart(
      params: {
        ApiURL.storeIdApiKey: widget.product.storeId,
        ApiURL.productVariantIdApiKey:
            widget.product.cartProductType == comboType
            ? widget.product.productId
            : widget.product.productVariantId,
        ApiURL.productTypeApiKey: widget.product.cartProductType == comboType
            ? comboType
            : regularType,
        ApiURL.isSavedForLaterApiKey: 0,
        ApiURL.addressIdApiKey:
            context.read<DeliveryLocationCubit>().currentSelectedAddress != null
            ? context.read<DeliveryLocationCubit>().currentSelectedAddress!.id!
            : 0,
      },
      products: products,
      cartId: widget.product.cartId!,
      isRemoveForSavedForLater: false,
    );
  }
}
