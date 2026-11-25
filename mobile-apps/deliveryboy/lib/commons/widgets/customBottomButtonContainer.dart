import 'dart:io';

import 'package:flutter/material.dart';

import 'package:eshopplus_deliveryboy/core/constants/themeConstants.dart';

class CustomBottomButtonContainer extends StatelessWidget {
  final Widget child;
  final double? bottomPadding;
  const CustomBottomButtonContainer({
    super.key,
    required this.child,
    this.bottomPadding,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsetsDirectional.only(
        start: appContentHorizontalPadding,
        end: appContentHorizontalPadding,
        top: 8,
        bottom: Platform.isIOS
            ? MediaQuery.of(context).padding.bottom
            : bottomPadding ?? MediaQuery.of(context).padding.bottom + 8,
      ),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primaryContainer,
        boxShadow: [
          BoxShadow(
            color: Theme.of(context).shadowColor,
            blurRadius: 4,
            offset: const Offset(0, -2),
            spreadRadius: 0,
          ),
        ],
      ),
      child: child,
    );
  }
}
