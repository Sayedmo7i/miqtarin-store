import 'dart:async';

import 'package:country_code_picker/country_code_picker.dart';
import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/core/constants/hiveConstants.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/core/theme/appTheme.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/profile/address/blocs/addNewAddressCubit.dart';
import 'package:eshop_plus/ui/profile/address/blocs/getAddressCubit.dart';
import 'package:eshop_plus/ui/auth/blocs/authCubit.dart';
import 'package:eshop_plus/ui/cart/blocs/getUserCart.dart';
import 'package:eshop_plus/ui/cart/blocs/manageCartCubit.dart';
import 'package:eshop_plus/ui/favorites/blocs/addFavoriteCubit.dart';
import 'package:eshop_plus/ui/favorites/blocs/getFavoriteCubit.dart';
import 'package:eshop_plus/ui/favorites/blocs/removeFavoriteCubit.dart';
import 'package:eshop_plus/ui/profile/orders/blocs/orderCubit.dart';
import 'package:eshop_plus/ui/profile/orders/blocs/updateOrderCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/ui/profile/promoCode/blocs/validatePromoCodeCubit.dart';
import 'package:eshop_plus/ui/profile/transaction/blocs/transactionCubit.dart';
import 'package:eshop_plus/ui/cart/models/cartItem.dart';

import 'package:eshop_plus/ui/auth/repositories/authRepository.dart';
import 'package:eshop_plus/commons/repositories/storeRepository.dart';
import 'package:eshop_plus/firebase_options.dart';
import 'package:eshop_plus/ui/profile/customerSupport/widgets/chatController.dart';
import 'package:eshop_plus/core/services/deepLinkManager.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:get/get.dart';

import 'package:hive_flutter/adapters.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'ui/categoty/blocs/categoryCubit.dart';
import 'commons/blocs/userDetailsCubit.dart';

//////
// Version 1.1.1
/////

void main() async {
  await initializeApp();
}

late PackageInfo packageInfo;
final RouteController routeController = Get.put(RouteController());
final ChatController chatController = Get.put(ChatController());
String currentChatUserId = '';
String? lastSnackbarMessage = ''; // this is used to show snackbar only once
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();
late SharedPreferences sharedPreferences;

Future<void> initializeApp() async {
  runZonedGuarded(
    () async {
      WidgetsFlutterBinding.ensureInitialized();

      SystemChrome.setSystemUIOverlayStyle(
        SystemUiOverlayStyle(
          statusBarColor: transparentColor,
          statusBarBrightness: Brightness.light,
          statusBarIconBrightness: Brightness.dark,
          systemNavigationBarDividerColor: transparentColor,
        ),
      );
      SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);

      // Run all independent initializations in parallel
      final results = await Future.wait([
        PackageInfo.fromPlatform(),
        Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform),
        SharedPreferences.getInstance().then((prefs) async {
          await prefs.reload();
          return prefs;
        }),
        Hive.initFlutter(),
      ]);

      packageInfo = results[0] as PackageInfo;
      sharedPreferences = results[2] as SharedPreferences;

      // Initialize Firebase Crashlytics
      FlutterError.onError =
          FirebaseCrashlytics.instance.recordFlutterFatalError;

      // Pass all uncaught asynchronous errors to Crashlytics
      PlatformDispatcher.instance.onError = (error, stack) {
        FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
        return true;
      };

      // Register adapter then open all Hive boxes in parallel
      Hive.registerAdapter(CartItemAdapter());
      await Future.wait([
        Hive.openBox(authBoxKey),
        Hive.openBox(settingsBoxKey),

        Hive.openBox(languageLabelsBoxKey),
      ]);

      runApp(const MyApp());
    },
    (error, stackTrace) {
      FirebaseCrashlytics.instance.recordError(error, stackTrace, fatal: true);
    },
  );
}

class MyApp extends StatefulWidget {
  const MyApp({super.key});

  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  @override
  void initState() {
    super.initState();
    if (mounted) {
      // Initialize deep link manager early
      DeepLinkManager().initialize();
      Future.wait([
        Hive.openBox<CartItem>(cartBoxKey),
        Hive.openBox(productsBoxKey),
        Hive.openBox(favoritesBoxKey),
        Hive.openBox(promocodeBoxKey),
        Hive.openBox(searchBoxKey),
        Hive.openBox(affiliateReferencesBoxKey),
        Hive.openBox(deliveryLocationBoxKey),
      ]);
    }
  }

  @override
  Widget build(BuildContext context) {
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (_) => StoresCubit(StoreRepository())),
        BlocProvider<AuthCubit>(create: (_) => AuthCubit(AuthRepository())),
        BlocProvider<UserDetailsCubit>(create: (_) => UserDetailsCubit()),
        BlocProvider<SettingsAndLanguagesCubit>(
          create: (_) => SettingsAndLanguagesCubit(),
        ),
        BlocProvider<DeliveryLocationCubit>(
          create: (context) => DeliveryLocationCubit(),
        ),

        BlocProvider<GetUserCartCubit>(create: (_) => GetUserCartCubit()),
        BlocProvider(create: (context) => ManageCartCubit()),
        BlocProvider<RemoveFavoriteCubit>(
          create: (context) => RemoveFavoriteCubit(),
        ),
        BlocProvider(create: (context) => AddFavoriteCubit()),
        BlocProvider<CategoryCubit>(create: (context) => CategoryCubit()),
        BlocProvider(create: (context) => FavoritesCubit()),
        BlocProvider(create: (context) => GetAddressCubit()),
        BlocProvider(create: (context) => TransactionCubit()),
        // we have taken this cubit as global so that we can update order status from notification
        BlocProvider(create: (context) => OrdersCubit()),
        BlocProvider(create: (context) => UpdateOrderCubit()),
        BlocProvider<AddNewAddressCubit>(
          create: (context) => AddNewAddressCubit(),
        ),
        BlocProvider(create: (_) => ValidatePromoCodeCubit()),
      ],
      child: BlocBuilder<SettingsAndLanguagesCubit, SettingsAndLanguagesState>(
        builder: (context, state) {
          return BlocBuilder<StoresCubit, StoresState>(
            builder: (context, state) {
              final defaultStore = context
                  .read<StoresCubit>()
                  .getDefaultStore();

              return GetMaterialApp(
                navigatorKey: navigatorKey,
                textDirection:
                    context
                        .read<SettingsAndLanguagesCubit>()
                        .getCurrentAppLanguage()
                        .isThisRTL()
                    ? TextDirection.rtl
                    : TextDirection.ltr,
                theme: AppTheme.getThemeData(context, defaultStore),
                debugShowCheckedModeBanner: false,
                getPages: Routes.getPages,
                initialRoute: Routes.splashScreen,
                supportedLocales: [const Locale('en')],
                localizationsDelegates: [CountryLocalizations.delegate],
                builder: (context, child) {
                  return Overlay(
                    initialEntries: [
                      OverlayEntry(builder: (context) => child!),
                    ],
                  );
                },
                routingCallback: (routing) {
                  if (routing != null) {
                    routeController.updateCurrentRoute(
                      context,
                      routing.current,
                    );
                  }
                },
              );
            },
          );
        },
      ),
    );
  }
}

class RouteController extends GetxController {
  var currentRoute = ''.obs;

  @override
  void onInit() {
    ever(currentRoute, (route) {});
    super.onInit();
  }

  void updateCurrentRoute(BuildContext context, String route) {
    currentRoute.value = route;
  }
}
