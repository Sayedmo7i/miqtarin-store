import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/widgets/safeAreaWithBottomPadding.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/blocs/manageCartCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/removeProductFromCartCubit.dart';
import 'package:eshop_plus/ui/cart/models/cart.dart';
import 'package:eshop_plus/commons/product/models/product.dart';
import 'package:eshop_plus/ui/cart/widgets/quantitySelector.dart';
import 'package:eshop_plus/ui/explore/productDetails/productDetailsScreen.dart';
import 'package:eshop_plus/commons/widgets/addToCartButton.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customRoundedButton.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/commons/widgets/dottedLineRectPainter.dart';

import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import 'package:eshop_plus/core/theme/colors.dart';

import 'package:eshop_plus/commons/widgets/customImageWidget.dart';

import 'package:eshop_plus/utils/utils.dart';

class CartProductList extends StatefulWidget {
  final bool? isFinalCartScreen;
  final Cart cart;
  final RemoveFromCartCubit removeFromCartCubit;

  const CartProductList({
    Key? key,
    required this.cart,
    this.isFinalCartScreen = false,
    required this.removeFromCartCubit,
  }) : super(key: key);

  @override
  _CartProductListState createState() => _CartProductListState();
}

class _CartProductListState extends State<CartProductList> {
  late Cart cart;
  List<CartProduct> savedForLaterProducts = [];
  late TextStyle bodyMedtextStyle;
  bool _isPickingFile = false;
  bool _isRemoveBottomSheetOpen = false;
  @override
  initState() {
    super.initState();
    cart = widget.cart;
  }

  @override
  Widget build(BuildContext context) {
    bodyMedtextStyle = Theme.of(context).textTheme.bodyMedium!.copyWith(
      color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.8),
    );
    CartCheckoutState checkoutState = context
        .read<GetUserCartCubit>()
        .getCheckoutState();
    return BlocBuilder<GetUserCartCubit, GetUserCartState>(
      builder: (context, state) {
        if (state is GetUserCartFetchSuccess) {
          cart = state.cart;

          return BlocListener<RemoveFromCartCubit, RemoveFromCartState>(
            bloc: widget.removeFromCartCubit,
            listener: (context, remvovestate) {
              if (remvovestate is RemoveFromCartFetchSuccess) {
                if (!remvovestate.isRemoveForSavedForLater) {
                  // Only pop if THIS instance opened the bottom sheet
                  if (_isRemoveBottomSheetOpen) {
                    _isRemoveBottomSheetOpen = false;
                    Navigator.of(context).pop();
                  }
                }

                Utils.showSnackBar(
                  context: context,
                  message: remvovestate.successMessage,
                );

                if (remvovestate.isRemoveForSavedForLater &&
                    checkoutState.saveForLaterProducts != null) {
                  // Remove item from saved-for-later list and keep existing cart
                  checkoutState.saveForLaterProducts!.removeWhere(
                    (element) => element.cartId == remvovestate.id,
                  );
                  context.read<GetUserCartCubit>().emitSuccessState(state.cart);
                } else {
                  // Refresh entire cart from API response, preserving transient fields
                  final Cart newCart = remvovestate.cart;

                  // Checkout state (promoCode, payment, wallet, etc.) is preserved in GetUserCartCubit._checkoutState
                  context.read<GetUserCartCubit>().emitSuccessState(newCart);
                }
              }
              if (remvovestate is RemoveFromCartFetchFailure) {
                if (_isRemoveBottomSheetOpen) {
                  _isRemoveBottomSheetOpen = false;
                  Navigator.of(context).pop();
                }
                Utils.showSnackBar(
                  context: context,
                  message: remvovestate.errorMessage,
                );
              }
            },
            child: BlocBuilder<ManageCartCubit, ManageCartState>(
              builder: (context, manageState) {
                return Column(
                  children: [
                    if (cart.cartProducts != null &&
                        cart.cartProducts!.isNotEmpty)
                      buildCartList(
                        false,
                        cart.cartProducts!,
                        widget.isFinalCartScreen ?? false,
                      ),
                    if (checkoutState.saveForLaterProducts != null &&
                        checkoutState.saveForLaterProducts!.isNotEmpty &&
                        !widget.isFinalCartScreen!)
                      buildSavedForLaterContainer(),
                  ],
                );
              },
            ),
          );
        }
        return const SizedBox.shrink();
      },
    );
  }

  Widget buildCartList(
    bool isSavedForLaterList,
    List<CartProduct> products,
    bool isFinalCartScreen,
  ) {
    return Column(
      spacing: 8,
      children: List.generate(products.length, (index) {
        CartProduct product = products[index];
        return GestureDetector(
          onTap: () {
            if (product.cartProductType == comboType) {
              Utils.navigateToScreen(
                context,
                Routes.productDetailsScreen,
                arguments: ProductDetailsScreen.buildArguments(
                  storeId: product.storeId,
                  product: product.productDetails![0],
                  isComboProduct: true,
                ),
              );
            } else {
              Utils.navigateToScreen(
                context,
                Routes.productDetailsScreen,
                arguments: ProductDetailsScreen.buildArguments(
                  storeId: product.storeId,
                  product: product.productDetails![0],
                ),
              );
            }
          },
          child: Container(
            padding: const EdgeInsetsDirectional.all(8),
            decoration: BoxDecoration(
              color: Theme.of(context).colorScheme.primaryContainer,
              borderRadius: BorderRadius.circular(8),
            ),
            width: MediaQuery.of(context).size.width,
            child: Column(
              children: [
                Row(
                  crossAxisAlignment: .start,
                  children: [
                    buildProductImage(product),
                    const SizedBox(width: 10.0),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: .start,
                        children: [
                          Row(
                            crossAxisAlignment: .center,
                            children: [
                              Expanded(
                                child: CustomTextContainer(
                                  textKey:
                                      product.productDetails![0].name ?? "",
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: Theme.of(context).textTheme.titleSmall,
                                ),
                              ),
                              if (isSavedForLaterList)
                                buildRemoveProductButton(
                                  product,
                                  products,
                                  context,
                                )
                              else
                                buildRemoveOrSaveForLaterButton(
                                  product,
                                  products,
                                  context,
                                ),
                            ],
                          ),
                          Padding(
                            padding: const EdgeInsetsDirectional.symmetric(
                              vertical: 8.0,
                            ),
                            child: CustomTextContainer(
                              textKey:
                                  product.productDetails![0].shortDescription ??
                                  "",
                              maxLines: 2,
                              overflow: TextOverflow.visible,
                              style: Theme.of(context).textTheme.bodySmall!
                                  .copyWith(
                                    height: 1.0,
                                    color: Theme.of(context)
                                        .colorScheme
                                        .secondary
                                        .withValues(alpha: 0.67),
                                  ),
                            ),
                          ),
                          if (isFinalCartScreen ||
                              product.type == variableProductType)
                            Padding(
                              padding: const EdgeInsetsDirectional.symmetric(
                                vertical: 4,
                              ),
                              child: Wrap(
                                // mainAxisSize: .min,
                                spacing: 4,
                                runSpacing: 4,
                                children: <Widget>[
                                  if (isFinalCartScreen)
                                    Utils.buildVariantContainer(
                                      context,
                                      'Qty',
                                      product.qty.toString(),
                                    ),
                                  if (product.type == variableProductType)
                                    Utils.buildVariantContainer(
                                      context,
                                      product.productVariants![0].attrName!,
                                      product
                                          .productVariants![0]
                                          .variantValues!,
                                    ),
                                ],
                              ),
                            ),
                          Row(
                            spacing: 4,

                            children: [
                              product.specialPrice != null &&
                                      product.specialPrice! > 0 &&
                                      product.specialPrice != product.price
                                  ? Expanded(
                                      child: Text.rich(
                                        TextSpan(
                                          children: [
                                            TextSpan(
                                              text:
                                                  Utils.priceWithCurrencySymbol(
                                                    context: context,
                                                    price:
                                                        product.specialPrice!,
                                                  ),
                                              style: Theme.of(context)
                                                  .textTheme
                                                  .titleMedium
                                                  ?.copyWith(
                                                    color: Theme.of(
                                                      context,
                                                    ).colorScheme.primary,
                                                  ),
                                            ),
                                            const TextSpan(text: "  "),
                                            TextSpan(
                                              text:
                                                  Utils.priceWithCurrencySymbol(
                                                    price: product.price!,
                                                    context: context,
                                                  ),
                                              style: Theme.of(context)
                                                  .textTheme
                                                  .bodyMedium
                                                  ?.copyWith(
                                                    decoration: TextDecoration
                                                        .lineThrough,
                                                    color: Theme.of(context)
                                                        .colorScheme
                                                        .secondary
                                                        .withValues(
                                                          alpha: 0.67,
                                                        ),
                                                  ),
                                            ),
                                            const TextSpan(text: "  "),
                                            if (getDiscoutPercentage(
                                                  product.price!,
                                                  product.specialPrice!,
                                                ) >
                                                0)
                                              TextSpan(
                                                text:
                                                    "${getDiscoutPercentage(product.price!, product.specialPrice!)}% ${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: offKey)}",
                                                style: Theme.of(context)
                                                    .textTheme
                                                    .labelSmall
                                                    ?.copyWith(
                                                      color: successStatusColor,
                                                    ),
                                              ),
                                          ],
                                        ),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    )
                                  : Expanded(
                                      child: CustomTextContainer(
                                        textKey: Utils.priceWithCurrencySymbol(
                                          price: product.price!,
                                          context: context,
                                        ),
                                        style: Theme.of(context)
                                            .textTheme
                                            .titleMedium
                                            ?.copyWith(
                                              color: Theme.of(
                                                context,
                                              ).colorScheme.primary,
                                            ),
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                              if (!widget.isFinalCartScreen!) ...[
                                if (isSavedForLaterList &&
                                    ((product.type != variableProductType &&
                                            !product.productDetails![0]
                                                .isProductOutOfStock()) ||
                                        (product.type == variableProductType &&
                                            !product.productDetails![0]
                                                .isVariantOutOfStock(
                                                  product
                                                      .productDetails![0]
                                                      .variants![0],
                                                ))))
                                  AddToCartButton(
                                    storeId: product.storeId!,
                                    reloadCart: true,
                                    widthPercentage: 0.25,
                                    productId:
                                        product.cartProductType == comboType
                                        ? product.id!
                                        : product.productVariantId!,
                                    type: product.cartProductType == comboType
                                        ? comboType
                                        : regularType,
                                    productType: product.type!,
                                    qty: product.qty,
                                    sellerId: product.sellerId!,
                                    stockType:
                                        product.productDetails![0].stockType!,
                                    stock: product.type == variableProductType
                                        ? product.productVariants![0].stock!
                                        : product.productDetails![0].stock!,
                                  )
                                else
                                  FittedBox(
                                    child: QuantitySelector(
                                      product: product,
                                      primaryTheme: false,
                                    ),
                                  ),
                              ],
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                if (widget.isFinalCartScreen == true &&
                    product.productDetails![0].isAttachmentRequired == 1)
                  Padding(
                    padding: const .only(top: 8),
                    child: Column(
                      crossAxisAlignment: .start,
                      children: [
                        GestureDetector(
                          onTap: () async {
                            PlatformFile? file = await pickFile();
                            if (file != null) {
                              final int attachmentId =
                                  product.cartProductType == comboType
                                  ? product.id!
                                  : product.productVariantId!;
                              context.read<GetUserCartCubit>().updateAttachment(
                                attachmentId,
                                file.path!,
                              );
                            }
                            setState(() {});
                          },
                          child:
                              context
                                      .read<GetUserCartCubit>()
                                      .getCheckoutState()
                                      .attachments[product.cartProductType ==
                                          comboType
                                      ? product.id!
                                      : product.productVariantId!] !=
                                  null
                              ? Container(
                                  padding: .symmetric(
                                    horizontal: 4,
                                    vertical: 2,
                                  ),
                                  decoration: BoxDecoration(
                                    color: Theme.of(context).colorScheme.primary
                                        .withValues(alpha: 0.10),
                                    borderRadius: BorderRadius.circular(3),
                                  ),
                                  child: Row(
                                    children: <Widget>[
                                      Expanded(
                                        child: CustomTextContainer(
                                          textKey: context
                                              .read<GetUserCartCubit>()
                                              .getCheckoutState()
                                              .attachments[product
                                                          .cartProductType ==
                                                      comboType
                                                  ? product.id!
                                                  : product.productVariantId!]!
                                              .split('/')
                                              .last,
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: Theme.of(context)
                                              .textTheme
                                              .titleSmall!
                                              .copyWith(
                                                color: Theme.of(
                                                  context,
                                                ).colorScheme.primary,
                                              ),
                                        ),
                                      ),
                                      IconButton(
                                        visualDensity: VisualDensity(
                                          horizontal: -4,
                                          vertical: -4,
                                        ),
                                        onPressed: () {
                                          final int attachmentId =
                                              product.cartProductType ==
                                                  comboType
                                              ? product.id!
                                              : product.productVariantId!;
                                          context
                                              .read<GetUserCartCubit>()
                                              .removeAttachment(attachmentId);
                                          setState(() {});
                                        },
                                        icon: Icon(
                                          Icons.delete,
                                          color: Theme.of(
                                            context,
                                          ).colorScheme.error,
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              : CustomPaint(
                                  painter: DottedLineRectPainter(
                                    strokeWidth: 1.0,
                                    radius: 3.0,
                                    dashWidth: 4.0,
                                    dashSpace: 2.0,
                                    color: Theme.of(
                                      context,
                                    ).colorScheme.primary,
                                  ),
                                  child: Container(
                                    alignment: Alignment.center,
                                    width: double.maxFinite,
                                    padding: .symmetric(
                                      horizontal: 4,
                                      vertical: 4,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Theme.of(context)
                                          .colorScheme
                                          .primary
                                          .withValues(alpha: 0.10),
                                      borderRadius: BorderRadius.circular(3),
                                    ),
                                    child: CustomTextContainer(
                                      textKey: uploadFileKey,
                                      style: Theme.of(context)
                                          .textTheme
                                          .titleSmall!
                                          .copyWith(
                                            color: Theme.of(
                                              context,
                                            ).colorScheme.primary,
                                          ),
                                    ),
                                  ),
                                ),
                        ),
                        SizedBox(height: 4),
                        CustomTextContainer(
                          textKey: productAttachmentNoteKey,
                          style: Theme.of(context).textTheme.bodyMedium!
                              .copyWith(
                                color: Theme.of(context).colorScheme.error,
                              ),
                        ),
                      ],
                    ),
                  ),
                if (product.errorMessage != null &&
                    product.errorMessage!.isNotEmpty)
                  Padding(
                    padding: const .symmetric(vertical: 8.0),
                    child: CustomTextContainer(
                      textKey: product.errorMessage ?? "",
                      style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                        height: 1.0,
                        color: Theme.of(context).colorScheme.error,
                      ),
                    ),
                  ),
              ],
            ),
          ),
        );
      }),
    );
  }

  CustomImageWidget buildProductImage(CartProduct product) {
    return CustomImageWidget(
      url:
          (product.type == variableProductType &&
              product.productVariants != null &&
              product.productVariants!.isNotEmpty &&
              product.productVariants![0].images != null &&
              product.productVariants![0].images!.isNotEmpty)
          ? product.productVariants![0].images!.first
          : product.image ?? '',
      width: 86,
      height: 100,
      borderRadius: 8,
    );
  }

  Future<PlatformFile?> pickFile() async {
    if (_isPickingFile) {
      return null; // Prevent opening if already active
    }
    setState(() {
      _isPickingFile = true;
    });
    try {
      FilePickerResult? result = await FilePicker.platform.pickFiles(
        type: FileType.any, // Allow any type of file
      );

      if (result != null) {
        if (Utils.isValidFile(result.files.first.path!)) {
          return result.files.first;
        } else {
          Utils.showSnackBar(
            message: productAttachmentNoteKey,
            context: context,
          );
        }
      }
    } catch (e) {
    } finally {
      setState(() {
        _isPickingFile = false;
      });
    }
    return null;
  }

  int getDiscoutPercentage(double price, double specialPrice) {
    if (specialPrice != 0.0) {
      return (((price - specialPrice) * 100) / price).round();
    }
    return 0;
  }

  buildPriceDetailRow(String title, String value, {TextStyle? textStyle}) {
    return Padding(
      padding: const EdgeInsetsDirectional.only(
        start: appContentHorizontalPadding,
        end: appContentHorizontalPadding,
        bottom: 8,
      ),
      child: Row(
        mainAxisAlignment: .spaceBetween,
        children: <Widget>[
          CustomTextContainer(textKey: title, style: bodyMedtextStyle),
          CustomTextContainer(
            textKey: Utils.priceWithCurrencySymbol(
              price: double.tryParse(value) ?? 0,
              context: context,
            ),
            style: textStyle ?? bodyMedtextStyle,
          ),
        ],
      ),
    );
  }

  buildRemoveProductButton(
    CartProduct product,
    List<CartProduct> products,
    BuildContext context,
  ) {
    return BlocBuilder<RemoveFromCartCubit, RemoveFromCartState>(
      bloc: widget.removeFromCartCubit,
      builder: (context, state) {
        return IconButton(
          visualDensity: VisualDensity(horizontal: -4, vertical: -4),
          icon:
              state is RemoveFromCartFetchInProgress &&
                  state.isRemoveForSavedForLater &&
                  state.products!
                          .firstWhere((element) => element.id == product.id)
                          .removeProductInProgress ==
                      true
              ? CustomCircularProgressIndicator(
                  indicatorColor: Theme.of(context).colorScheme.secondary,
                )
              : Icon(
                  Icons.close_outlined,
                  size: 24,
                  color: Theme.of(
                    context,
                  ).colorScheme.secondary.withValues(alpha: 0.67),
                ),
          onPressed: () =>
              removeProductFromCartFunction(state, product, products, true),
        );
      },
    );
  }

  buildRemoveOrSaveForLaterButton(
    CartProduct product,
    List<CartProduct> products,
    BuildContext context,
  ) {
    return IconButton(
      visualDensity: VisualDensity(horizontal: -4, vertical: -4),
      icon: Icon(
        Icons.close_outlined,
        size: 24,
        color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.67),
      ),
      onPressed: () {
        _isRemoveBottomSheetOpen = true;
        Utils.openModalBottomSheet(
          context,
          SafeAreaWithBottomPadding(
            child: Padding(
              padding: .symmetric(
                vertical: 12,
                horizontal: appContentHorizontalPadding,
              ),
              child: Column(
                crossAxisAlignment: .start,
                children: <Widget>[
                  Row(
                    children: <Widget>[
                      buildProductImage(product),
                      DesignConfig.smallWidthSizedBox,
                      Expanded(
                        child: Column(
                          crossAxisAlignment: .start,
                          children: <Widget>[
                            CustomTextContainer(
                              textKey: removeFromCartTitlekey,
                              style: Theme.of(context).textTheme.titleMedium,
                            ),
                            CustomTextContainer(
                              textKey: removeFromCartDesckey,
                              style: Theme.of(context).textTheme.bodyMedium!
                                  .copyWith(
                                    color: Theme.of(context)
                                        .colorScheme
                                        .secondary
                                        .withValues(alpha: 0.67),
                                  ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  DesignConfig.smallHeightSizedBox,
                  buildRemoveAndSaveLaterButtons(product, products),
                ],
              ),
            ),
          ),
          staticContent: true,
        );
      },
    );
  }

  buildRemoveAndSaveLaterButtons(
    CartProduct product,
    List<CartProduct> products,
  ) {
    return Row(
      children: [
        Expanded(
          child: BlocBuilder<RemoveFromCartCubit, RemoveFromCartState>(
            bloc: widget.removeFromCartCubit,
            builder: (context, state) {
              return CustomRoundedButton(
                widthPercentage: 0.4,
                buttonTitle: removeKey,
                showBorder: true,
                backgroundColor: Theme.of(context).colorScheme.onPrimary,
                borderColor: Theme.of(context).hintColor,
                style: Theme.of(context).textTheme.bodySmall!.copyWith(
                  color: Theme.of(context).colorScheme.secondary,
                ),
                child:
                    state is RemoveFromCartFetchInProgress &&
                        !state.isRemoveForSavedForLater &&
                        state.products!
                                .firstWhere(
                                  (element) => element.id == product.id,
                                )
                                .removeProductInProgress ==
                            true
                    ? CustomCircularProgressIndicator(
                        indicatorColor: Theme.of(context).colorScheme.secondary,
                      )
                    : null,
                onTap: () => removeProductFromCartFunction(
                  state,
                  product,
                  products,
                  false,
                ),
              );
            },
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: BlocConsumer<ManageCartCubit, ManageCartState>(
            listener: (bcontext, state) {},
            builder: (context, state) {
              return CustomRoundedButton(
                widthPercentage: 0.4,
                buttonTitle: saveForLaterkey,
                showBorder: false,
                child: state is ManageCartFetchInProgress
                    ? const CustomCircularProgressIndicator()
                    : null,
                onTap: () {
                  if (state is ManageCartFetchInProgress) return;
                  context.read<ManageCartCubit>().manageUserCart(
                    product.id,
                    isAddedToSaveLater: true,
                    reloadCart: true,
                    params: {
                      ApiURL.storeIdApiKey: product.storeId,
                      ApiURL.productVariantIdApiKey:
                          product.cartProductType == comboType
                          ? product.id
                          : product.productVariantId,
                      ApiURL.productTypeApiKey:
                          product.cartProductType == comboType
                          ? comboType
                          : regularType,
                      ApiURL.isSavedForLaterApiKey: 1,
                      ApiURL.qtyApiKey: product.qty,
                      ApiURL.addressIdApiKey:
                          context
                                  .read<DeliveryLocationCubit>()
                                  .currentSelectedAddress !=
                              null
                          ? context
                                .read<DeliveryLocationCubit>()
                                .currentSelectedAddress!
                                .id!
                          : '',
                    },
                  );
                  Future.delayed(const Duration(milliseconds: 500), () {
                    Navigator.pop(context);
                  });
                },
              );
            },
          ),
        ),
      ],
    );
  }

  removeProductFromCartFunction(
    RemoveFromCartState state,
    CartProduct product,
    List<CartProduct> products,
    bool isRemoveForSavedForLater,
  ) {
    if (state is RemoveFromCartFetchInProgress &&
        state.products!
                .firstWhere((element) => element.id == product.id)
                .removeProductInProgress ==
            true)
      return;

    widget.removeFromCartCubit.removeProductFromCart(
      params: {
        ApiURL.storeIdApiKey: product.storeId,
        ApiURL.productVariantIdApiKey: product.cartProductType == comboType
            ? product.productId
            : product.productVariantId,
        ApiURL.productTypeApiKey: product.cartProductType == comboType
            ? comboType
            : regularType,
        ApiURL.isSavedForLaterApiKey: isRemoveForSavedForLater ? 1 : 0,
        ApiURL.addressIdApiKey:
            context.read<DeliveryLocationCubit>().currentSelectedAddress != null
            ? context.read<DeliveryLocationCubit>().currentSelectedAddress!.id!
            : 0,
      },
      products: products,
      cartId: product.cartId!,
      isRemoveForSavedForLater: isRemoveForSavedForLater,
    );
  }

  buildSavedForLaterContainer() {
    return Column(
      crossAxisAlignment: .start,
      children: <Widget>[
        Padding(
          padding: const .all(appContentHorizontalPadding),
          child: CustomTextContainer(
            textKey: saveForLaterkey,
            style: Theme.of(context).textTheme.titleMedium,
          ),
        ),
        buildCartList(
          true,
          context
                  .read<GetUserCartCubit>()
                  .getCheckoutState()
                  .saveForLaterProducts ??
              [],
          widget.isFinalCartScreen ?? false,
        ),
      ],
    );
  }
}
