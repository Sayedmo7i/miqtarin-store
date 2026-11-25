import 'package:country_code_picker/country_code_picker.dart';
import 'package:eshopplus_deliveryboy/core/constants/hiveConstants.dart';
import 'package:eshopplus_deliveryboy/core/routes/routes.dart';
import 'package:eshopplus_deliveryboy/core/theme/appTheme.dart';
import 'package:eshopplus_deliveryboy/core/theme/colors.dart';
import 'package:eshopplus_deliveryboy/features/auth/blocs/authCubit.dart';
import 'package:eshopplus_deliveryboy/features/auth/blocs/availabilityToggleCubit.dart';
import 'package:eshopplus_deliveryboy/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_deliveryboy/features/auth/repositories/authRepository.dart';
import 'package:eshopplus_deliveryboy/features/auth/repositories/availabilityRepository.dart';
import 'package:eshopplus_deliveryboy/commons/repositories/settingsRepository.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';

import 'package:hive_flutter/adapters.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'commons/blocs/userDetailsCubit.dart';
import '../firebaseOptions.dart';
import '../utils/session.dart';

late PackageInfo packageInfo;
late SharedPreferences pref;
late Session session;
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

////////
//Version 1.1.1
///////
void main() async {
  await initializeApp();
}

Future<void> initializeApp() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);

  // Initialize Firebase Crashlytics
  FlutterError.onError = (errorDetails) {
    FirebaseCrashlytics.instance.recordFlutterFatalError(errorDetails);
  };

  // Pass all uncaught asynchronous errors to Crashlytics
  PlatformDispatcher.instance.onError = (error, stack) {
    FirebaseCrashlytics.instance.recordError(error, stack, fatal: true);
    return true;
  };

  pref = await SharedPreferences.getInstance();
  session = Session(pref);
  packageInfo = await PackageInfo.fromPlatform();

  SystemChrome.setSystemUIOverlayStyle(
    SystemUiOverlayStyle(
      statusBarColor: transparentColor,
      statusBarBrightness: Brightness.light,
      statusBarIconBrightness: Brightness.dark,
    ),
  );

  SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);

  await Hive.initFlutter();
  await Future.wait([
    Hive.openBox(authBoxKey),
    Hive.openBox(settingsBoxKey),
    Hive.openBox(languageLabelsBoxKey),
  ]);

  runApp(MyApp());
}

class MyApp extends StatefulWidget {
  const MyApp({super.key});

  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  @override
  Widget build(BuildContext context) {
    return MultiBlocProvider(
      providers: [
        BlocProvider(
          create: (context) => SettingsAndLanguagesCubit(SettingsRepository()),
        ),
        BlocProvider(create: (context) => AuthCubit(AuthRepository())),
        BlocProvider<UserDetailsCubit>(create: (_) => UserDetailsCubit()),
        BlocProvider(
            create: (context) =>
                AvailabilityToggleCubit(AvailabilityRepository())),
      ],
      child: Builder(
        builder: (context) {
          return BlocBuilder<SettingsAndLanguagesCubit,
              SettingsAndLanguagesState>(
            builder: (context, state) {
              final currentLanguage = context
                  .watch<SettingsAndLanguagesCubit>()
                  .getCurrentAppLanguage();
              return GetMaterialApp(
                navigatorKey: navigatorKey,
                textDirection: currentLanguage.isThisRTL()
                    ? TextDirection.rtl
                    : TextDirection.ltr,
                theme: AppTheme.getThemeData(context),
                debugShowCheckedModeBanner: false,
                getPages: Routes.getPages,
                initialRoute: Routes.splashScreen,
                routingCallback: (routing) {},
                supportedLocales: [
                  const Locale('en'),
                ],
                localizationsDelegates: [
                  CountryLocalizations.delegate,
                ],
                builder: (context, child) {
                  return Overlay(
                    initialEntries: [
                      OverlayEntry(builder: (context) => child!),
                    ],
                  );
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
  var previousRoute = ''.obs;

  @override
  void onInit() {
    ever(currentRoute, (route) {});
    super.onInit();
  }

  void updateCurrentRoute(String route, String previousRoute) {
    currentRoute.value = currentRoute.value;
    currentRoute.value = route;
  }
}
