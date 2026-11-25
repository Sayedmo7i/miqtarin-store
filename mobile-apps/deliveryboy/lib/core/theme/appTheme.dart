import 'package:eshopplus_deliveryboy/core/constants/themeConstants.dart';
import 'package:eshopplus_deliveryboy/core/theme/colors.dart';
import 'package:flutter/material.dart';

class AppTheme {
  static ThemeData getThemeData(BuildContext context) {
    final baseTextTheme = Theme.of(
      context,
    ).textTheme.apply(fontFamily: 'Rubik');
    final themeBackgroundColor = const Color(0xFFF5F8F9);
    final borderColorAlpha = borderColor.withValues(alpha: 0.4);
    final secondaryColorAlpha = secondaryColor.withValues(alpha: 0.67);
    final shadowColorConst = const Color(0x3F000000);

    return Theme.of(context).copyWith(
      textTheme: baseTextTheme.copyWith(
        // Apply secondary color to all text styles by default
        displayLarge:
            baseTextTheme.displayLarge?.copyWith(color: secondaryColor),
        displayMedium:
            baseTextTheme.displayMedium?.copyWith(color: secondaryColor),
        displaySmall:
            baseTextTheme.displaySmall?.copyWith(color: secondaryColor),
        headlineLarge:
            baseTextTheme.headlineLarge?.copyWith(color: secondaryColor),
        headlineMedium:
            baseTextTheme.headlineMedium?.copyWith(color: secondaryColor),
        headlineSmall:
            baseTextTheme.headlineSmall?.copyWith(color: secondaryColor),
        titleLarge: baseTextTheme.titleLarge?.copyWith(color: secondaryColor),
        titleMedium: baseTextTheme.titleMedium?.copyWith(color: secondaryColor),
        titleSmall: baseTextTheme.titleSmall?.copyWith(color: secondaryColor),
        bodyLarge: baseTextTheme.bodyLarge?.copyWith(color: secondaryColor),
        bodyMedium: baseTextTheme.bodyMedium?.copyWith(color: secondaryColor),
        bodySmall: baseTextTheme.bodySmall?.copyWith(color: secondaryColor),
        labelLarge: baseTextTheme.labelLarge?.copyWith(color: secondaryColor),
        labelMedium: baseTextTheme.labelMedium?.copyWith(color: secondaryColor),
        labelSmall: baseTextTheme.labelSmall?.copyWith(color: secondaryColor),
      ),
      secondaryHeaderColor: secondaryColor,
      scaffoldBackgroundColor: themeBackgroundColor,
      shadowColor: shadowColorConst,
      hintColor: secondaryColor.withValues(alpha: 0.67),
      appBarTheme: AppBarTheme(
        backgroundColor: Theme.of(context).colorScheme.onPrimary,
      ),
      iconTheme: IconThemeData(color: secondaryColor),
      dividerColor: borderColorAlpha,
      inputDecorationTheme: InputDecorationTheme(
        iconColor: borderColorAlpha,
        border: OutlineInputBorder(
          borderSide: BorderSide(color: borderColorAlpha),
          borderRadius: const BorderRadius.all(Radius.circular(borderRadius)),
        ),
        enabledBorder: OutlineInputBorder(
            borderSide: BorderSide(color: borderColorAlpha),
            borderRadius: BorderRadius.circular(borderRadius)),
        errorBorder: OutlineInputBorder(
            borderSide: BorderSide(color: errorColor),
            borderRadius: BorderRadius.circular(borderRadius)),
        focusedErrorBorder: OutlineInputBorder(
            borderSide: BorderSide(color: borderColorAlpha),
            borderRadius: BorderRadius.circular(borderRadius)),
        focusedBorder: OutlineInputBorder(
            borderSide: BorderSide(color: borderColorAlpha),
            borderRadius: BorderRadius.circular(borderRadius)),
        disabledBorder: OutlineInputBorder(
            borderSide: BorderSide(color: secondaryColorAlpha),
            borderRadius: BorderRadius.circular(borderRadius)),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
          backgroundColor: Theme.of(context).colorScheme.onPrimary,
          elevation: 1),
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryColor,
        primary: primaryColor,
        primaryContainer: whiteColor,
        surface: themeBackgroundColor,
        secondary: secondaryColor,
        shadow: shadowColorConst,
        error: errorColor,
        onPrimary: whiteColor,
      ),
    );
  }
}
