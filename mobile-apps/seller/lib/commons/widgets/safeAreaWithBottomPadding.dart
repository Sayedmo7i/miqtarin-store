import 'dart:io';

import 'package:flutter/material.dart';

/// A reusable widget that provides SafeArea with proper bottom padding
/// to handle Android 15's transparent navigation bar
class SafeAreaWithBottomPadding extends StatelessWidget {
  final Widget child;
  final bool maintainBottomViewPadding;
  final EdgeInsets minimum;

  const SafeAreaWithBottomPadding({
    super.key,
    required this.child,
    this.maintainBottomViewPadding = true,
    this.minimum = EdgeInsets.zero,
  });

  @override
  Widget build(BuildContext context) {
    final bottomInset = Platform.isAndroid
        ? MediaQuery.of(context).padding.bottom
        : 0.0;
    return Padding(
      padding: EdgeInsets.only(bottom: bottomInset),
      child: child,
    );
  }
}
