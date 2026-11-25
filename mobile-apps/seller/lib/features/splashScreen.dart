import 'dart:io';

import 'package:eshopplus_seller/commons/blocs/userDetailsCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/core/routes/routes.dart';
import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import 'package:eshopplus_seller/core/constants/appAssets.dart';
import 'package:eshopplus_seller/features/auth/blocs/authCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter/material.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();

  static Widget getRouteInstance() => const SplashScreen();
}

double bottomPadding = 0;

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late final AnimationController _logoController;
  late final Animation<double> _logoAnimation;

  @override
  void initState() {
    super.initState();

    _logoController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );
    _logoAnimation = CurvedAnimation(
      parent: _logoController,
      curve: Curves.easeIn,
    );
    _logoController.forward();
    SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.light);
    Future.delayed(Duration.zero, () {
      bottomPadding = _getBottomPadding(context);
    });
    callApi();
  }

  void navigateToNextScreen() async {
    if (context.read<AuthCubit>().state is Unauthenticated) {
      if (mounted) {
        Utils.navigateToScreen(context, Routes.loginScreen, replaceAll: true);
      }
    } else {
      Future.delayed(Duration.zero, () {
        if (mounted) {
          Utils.navigateToScreen(context, Routes.mainScreen, replaceAll: true);
        }
      });
    }
  }

  double _getBottomPadding(BuildContext context) {
    // Add extra padding for Android 15's transparent navigation bar
    if (Platform.isAndroid) {
      bottomPadding = MediaQuery.paddingOf(context).bottom;
      // Only add padding if there's actual system navigation bar padding
      if (bottomPadding > 0) {
        return bottomPadding;
      }
    }
    return 0.0;
  }

  @override
  void dispose() {
    _logoController.dispose();
    SystemChrome.setSystemUIOverlayStyle(
      SystemUiOverlayStyle(
        statusBarColor: transparentColor,
        systemNavigationBarColor: transparentColor,
        systemNavigationBarDividerColor: transparentColor,
        systemNavigationBarIconBrightness: Brightness.dark,
      ),
    );
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle(
        statusBarColor: primaryColor,
        systemNavigationBarColor: primaryColor,
      ),
      child: Scaffold(
        backgroundColor: primaryColor,
        body:
            BlocConsumer<SettingsAndLanguagesCubit, SettingsAndLanguagesState>(
              listener: (context, state) {
                if (state is SettingsAndLanguagesFetchSuccess) {
                  navigateToNextScreen();
                } else if (state is SettingsAndLanguagesFetchFailure) {
                  Utils.showSnackBar(
                    message: state.errorMessage,
                    context: context,
                  );
                }
              },
              builder: (context, state) {
                if (state is SettingsAndLanguagesFetchFailure) {
                  return ErrorScreen(
                    text: state.errorMessage,
                    onPressed: callApi,
                    child: state is SettingsAndLanguagesFetchInProgress
                        ? CustomCircularProgressIndicator(
                            indicatorColor: Theme.of(
                              context,
                            ).colorScheme.onPrimary,
                          )
                        : null,
                  );
                }

                return Center(
                  child: AnimatedBuilder(
                    animation: _logoAnimation,
                    builder: (context, child) {
                      return Opacity(
                        opacity: _logoAnimation.value,
                        child: Transform.scale(
                          scale: _logoAnimation.value,
                          child: child,
                        ),
                      );
                    },
                    child: Utils.setSvgImage(
                      AppAssets.appLogo,
                      width: 200,
                      height: 200,
                    ),
                  ),
                );
              },
            ),
      ),
    );
  }

  void callApi() {
    Future.delayed(Duration.zero, () {
      context.read<SettingsAndLanguagesCubit>().fetchSettingsAndLanguages();
      if (context.read<AuthCubit>().state is Authenticated) {
        context.read<StoresCubit>().fetchStores();
        context.read<UserDetailsCubit>().fetchUserDetails(context: context);
      }
    });
  }
}
