import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/seller/widgets/sellerInfoContainer.dart';
import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/ui/explore/blocs/comboProductsCubit.dart';
import 'package:eshop_plus/commons/product/blocs/productsCubit.dart';
import 'package:eshop_plus/commons/seller/blocs/sellersCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/seller/models/seller.dart';
import 'package:eshop_plus/ui/explore/screens/exploreScreen.dart';
import 'package:eshop_plus/commons/widgets/customAppbar.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';

import 'package:eshop_plus/commons/widgets/error_screen.dart';

import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';

import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';
import 'package:share_plus/share_plus.dart';

class SellerDetailScreen extends StatefulWidget {
  final Seller? seller;
  final int? sellerId;
  final int? storeId;
  final String? storeSlug;
  const SellerDetailScreen({
    Key? key,
    this.seller,
    this.sellerId,
    this.storeId,
    this.storeSlug,
  }) : super(key: key);
  static Widget getRouteInstance() {
    final arguments = Get.arguments as Map<String, dynamic>;
    return BlocProvider(
      create: (context) => SellersCubit(),
      child: SellerDetailScreen(
        seller: arguments['seller'] as Seller?,
        sellerId: arguments['sellerId'] as int?,
        storeId: arguments['storeId'] as int?,
        storeSlug: arguments['storeSlug'] as String?,
      ),
    );
  }

  @override
  _SellerDetailScreenState createState() => _SellerDetailScreenState();
}

class _SellerDetailScreenState extends State<SellerDetailScreen>
    with SingleTickerProviderStateMixin {
  Seller? seller;
  late AnimationController _sellerInfoAnimController;
  bool _isSellerInfoVisible = true;
  bool _isSharing = false;
  @override
  void initState() {
    super.initState();
    _sellerInfoAnimController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 300),
    );
    if (widget.seller != null) {
      seller = widget.seller!;
    } else {
      Future.delayed(Duration.zero, () => fetchSeller());
    }
  }

  @override
  void dispose() {
    _sellerInfoAnimController.dispose();
    super.dispose();
  }

  void _onScrollNotification(ScrollNotification notification) {
    if (notification is UserScrollNotification) {
      if (notification.direction == ScrollDirection.reverse &&
          _isSellerInfoVisible) {
        _isSellerInfoVisible = false;
        _sellerInfoAnimController.forward();
      } else if (notification.direction == ScrollDirection.forward &&
          !_isSellerInfoVisible) {
        _isSellerInfoVisible = true;
        _sellerInfoAnimController.reverse();
      }
    }
  }

  fetchSeller() {
    context.read<SellersCubit>().getSellers(
      storeId:
          widget.storeId ?? context.read<StoresCubit>().getDefaultStore().id!,
      sellerIds: [widget.sellerId!],
      storeSlug: widget.storeSlug,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: CustomAppbar(
        titleKey: sellerDetailsKey,
        trailingWidget: _shareButton(),
      ),
      body: BlocListener<SellersCubit, SellersState>(
        listener: (context, state) {
          if (state is SellersFetchSuccess) {
            setState(() {
              seller = state.sellers.first;
            });
          }
        },
        child: BlocBuilder<SellersCubit, SellersState>(
          builder: (context, state) {
            if (seller != null || state is SellersFetchSuccess) {
              return NotificationListener<ScrollNotification>(
                onNotification: (notification) {
                  _onScrollNotification(notification);
                  return false;
                },
                child: Column(
                  children: <Widget>[
                    SizeTransition(
                      sizeFactor: ReverseAnimation(_sellerInfoAnimController),
                      axisAlignment: -1.0,
                      child: Column(
                        children: [
                          const SizedBox(height: 12),
                          SellerInfoContainer(
                            seller: seller,
                            sellerId: widget.sellerId,
                          ),
                          DesignConfig.smallHeightSizedBox,
                        ],
                      ),
                    ),
                    Expanded(
                      child: MultiBlocProvider(
                        providers: [
                          BlocProvider(create: (context) => ProductsCubit()),
                          BlocProvider(
                            create: (context) => ComboProductsCubit(),
                          ),
                        ],
                        child: ExploreScreen(
                          sellerId: seller!.sellerId,
                          isExploreScreen: false,
                          forSellerDetailScreen: true,
                          storeSlug: widget.storeSlug,
                          storeId:
                              widget.storeId ??
                              context.read<StoresCubit>().getDefaultStore().id!,
                        ),
                      ),
                    ),
                  ],
                ),
              );
            } else if (state is SellersFetchFailure) {
              return ErrorScreen(
                text: state.errorMessage,
                onPressed: fetchSeller,
              );
            } else {
              return CustomCircularProgressIndicator(
                indicatorColor: Theme.of(context).colorScheme.primary,
              );
            }
          },
        ),
      ),
    );
  }

  _shareButton() {
    return Builder(
      builder: (buttonContext) {
        return IconButton(
          icon: Icon(
            Icons.share,
            color: Theme.of(context).colorScheme.secondary,
          ),
          onPressed: () async {
            if (_isSharing) return;
            _isSharing = true;
            try {
              final String sellerUrl =
                  '$baseUrl/$sellerDeeplink/${seller!.sellerId}?store=${seller!.storeSlug}';
              final box = buttonContext.findRenderObject() as RenderBox?;
              await SharePlus.instance.share(
                ShareParams(
                  text:
                      '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: shareThisSellerKey)} : $sellerUrl',
                  subject:
                      "${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: sellerDetailsKey)}: $sellerUrl",
                  sharePositionOrigin: box != null
                      ? box.localToGlobal(Offset.zero) & box.size
                      : null,
                ),
              );
            } finally {
              _isSharing = false;
            }
          },
        );
      },
    );
  }
}
