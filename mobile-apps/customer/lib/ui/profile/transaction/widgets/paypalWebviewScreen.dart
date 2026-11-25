import 'dart:async';

import 'package:eshop_plus/commons/widgets/safeAreaWithBottomPadding.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/ui/profile/orders/blocs/deleteOrderCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/ui/profile/transaction/blocs/addTransactionCubit.dart';

import 'package:eshop_plus/commons/widgets/checkInterconnectiviy.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';

import 'package:eshop_plus/core/api/apiEndPoints.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';
import 'package:webview_flutter/webview_flutter.dart';

import 'package:webview_flutter_android/webview_flutter_android.dart';

// Import for iOS features.
import 'package:webview_flutter_wkwebview/webview_flutter_wkwebview.dart';

class PaypalWebviewScreen extends StatefulWidget {
  final String? url, from, msg, amt, orderId;
  final String? returnUrl, cancelUrl;
  final double? price;

  const PaypalWebviewScreen({
    Key? key,
    this.url,
    this.from,
    this.msg,
    this.amt,
    this.orderId,
    this.price,
    this.returnUrl,
    this.cancelUrl,
  }) : super(key: key);
  static Widget getRouteInstance() {
    final arguments = Get.arguments as Map<String, dynamic>;
    return BlocProvider(
      create: (context) => DeleteOrderCubit(),
      child: PaypalWebviewScreen(
        url: arguments['url'],
        from: arguments['from'],
        msg: arguments['msg'],
        amt: arguments['amt'],
        orderId: arguments['orderId'],
        returnUrl: arguments['returnUrl'],
        cancelUrl: arguments['cancelUrl'],
      ),
    );
  }

  @override
  State<StatefulWidget> createState() {
    return StatePaypalWebviewScreen();
  }
}

class StatePaypalWebviewScreen extends State<PaypalWebviewScreen> {
  String message = '';
  bool isloading = true;
  GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();
  DateTime? currentBackPressTime;
  bool isNetworkAvail = false;
  bool _isNavigating = false;
  late final WebViewController _controller;

  @override
  void initState() {
    webViewInitiliased();
    checkNetwork();
    super.initState();
  }

  checkNetwork() async {
    isNetworkAvail = !await InternetConnectivity.isUserOffline();
    if (isNetworkAvail) {
      setState(() {
        isloading = false;
      });
    }
  }

  webViewInitiliased() {
    late final PlatformWebViewControllerCreationParams params;
    if (WebViewPlatform.instance is WebKitWebViewPlatform) {
      params = WebKitWebViewControllerCreationParams(
        allowsInlineMediaPlayback: true,
        mediaTypesRequiringUserAction: const <PlaybackMediaTypes>{},
      );
    } else {
      params = const PlatformWebViewControllerCreationParams();
    }

    final WebViewController controller =
        WebViewController.fromPlatformCreationParams(params);
    // #enddocregion platform_features

    controller
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            debugPrint('on progress====progress==${progress}');
          },
          onPageStarted: (String url) {
            debugPrint('on page started====url==${url}');
          },
          onUrlChange: (UrlChange url) {
            debugPrint('on url change====url==${url.url}');
          },
          onPageFinished: (String url) {
            debugPrint('on page finished====url==${url}');
            setState(() {
              isloading = false;
            });
            if (Uri.parse(url).queryParameters.containsKey('PayerID')) {
              if (mounted) {
                setState(() {
                  message = transactionSuccessfulKey;
                });
              }
              Navigator.of(context).pop(false);
            }
          },
          onWebResourceError: (WebResourceError error) {
            debugPrint(
              'on web resource error====error==${error.description}===${error.url}',
            );
          },
          onNavigationRequest: (NavigationRequest request) async {
            debugPrint('====url==${request.url}');
            final String responseUrl = request.url;
            final bool isReturnUrl =
                widget.returnUrl != null &&
                responseUrl.startsWith(widget.returnUrl!);
            final bool isCancelUrl =
                widget.cancelUrl != null &&
                responseUrl.startsWith(widget.cancelUrl!);

            if (isReturnUrl || isCancelUrl) {
              if (mounted) {
                setState(() {
                  isloading = true;
                });
              }

              if (isCancelUrl ||
                  responseUrl.contains('Failed') ||
                  responseUrl.contains('failed')) {
                if (mounted) {
                  setState(() {
                    isloading = false;
                    message = transactionFailedKey;
                  });
                }
                if (widget.orderId != null) {
                  deleteOrder();
                } else {
                  Timer(const Duration(seconds: 1), () {
                    Navigator.of(context).pop(true);
                  });
                }
              } else if (responseUrl.contains('Completed') ||
                  responseUrl.contains('completed') ||
                  responseUrl.toLowerCase().contains('success')) {
                if (mounted) {
                  setState(() {
                    message = transactionSuccessfulKey;
                  });
                }
                Navigator.of(context).pop(false);
              } else if (widget.orderId != null &&
                  (responseUrl.contains('Canceled-Reversal') ||
                      responseUrl.contains('Denied'))) {
                deleteOrder();
              }
              return NavigationDecision.prevent;
            }

            return NavigationDecision.navigate;
          },
        ),
      )
      ..addJavaScriptChannel(
        'Toaster',
        onMessageReceived: (JavaScriptMessage message) {
          Utils.showSnackBar(message: message.message, context: context);
        },
      )
      ..loadHtmlString(widget.url!.toString());

    // #docregion platform_features
    if (controller.platform is AndroidWebViewController) {
      AndroidWebViewController.enableDebugging(true);
      (controller.platform as AndroidWebViewController)
          .setMediaPlaybackRequiresUserGesture(false);
    }
    // #enddocregion platform_features

    _controller = controller;
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        DateTime now = DateTime.now();
        if (currentBackPressTime == null ||
            now.difference(currentBackPressTime!) >
                const Duration(seconds: 2)) {
          currentBackPressTime = now;
          Utils.showSnackBar(
            message:
                "${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: paymentBackWarningKey)}\n ${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: paymentExitInstructionKey)}",
            context: context,
          );
        } else {
          deleteOrder();
          if (didPop) {
            return;
          }
        }
      },
      child: BlocListener<DeleteOrderCubit, DeleteOrderState>(
        listener: (context, state) async {
          if (state is DeleteOrderFailure) {
            Utils.showSnackBar(message: state.errorMessage, context: context);
          }
          if (state is DeleteOrderSuccess) {
            Navigator.of(context).pop();
          }
        },
        child: Scaffold(
          key: scaffoldKey,
          appBar: AppBar(
            titleSpacing: 0,
            leading: Builder(
              builder: (BuildContext context) {
                return Container(
                  margin: const .all(10),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(borderRadius),
                    onTap: () {
                      DateTime now = DateTime.now();
                      if (currentBackPressTime == null ||
                          now.difference(currentBackPressTime!) >
                              const Duration(seconds: 2)) {
                        currentBackPressTime = now;
                        Utils.showSnackBar(
                          message:
                              "${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: paymentBackWarningKey)}\n ${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: paymentExitInstructionKey)}",
                          context: context,
                        );
                        return;
                      }

                      if (widget.from == 'order' && widget.orderId != null) {
                        deleteOrder();
                      } else {
                        Navigator.pop(context, true);
                      }
                    },
                    child: Center(
                      child: Icon(
                        Icons.keyboard_arrow_left,
                        size: 40,
                        color: Theme.of(context).colorScheme.secondary,
                      ),
                    ),
                  ),
                );
              },
            ),
            title: CustomTextContainer(
              textKey:
                  context
                      .read<SettingsAndLanguagesCubit>()
                      .getSettings()
                      .systemSettings!
                      .appName ??
                  "",
            ),
          ),
          body: SafeAreaWithBottomPadding(
            child: Stack(
              children: <Widget>[
                isNetworkAvail
                    ? WebViewWidget(controller: _controller)
                    : const SizedBox(),
                isloading
                    ? const Center(child: CircularProgressIndicator())
                    : const SizedBox(),
                message.trim().isEmpty
                    ? const SizedBox()
                    : Center(
                        child: Container(
                          color: Theme.of(context).colorScheme.primary,
                          padding: const .all(5),
                          margin: const .all(5),
                          child: Text(
                            message,
                            style: TextStyle(
                              color: Theme.of(context).colorScheme.onPrimary,
                              fontFamily: 'ubuntu',
                            ),
                          ),
                        ),
                      ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Future<void> deleteOrder() async {
    if (_isNavigating) return;
    _isNavigating = true;
    Navigator.pop(context, true);
  }
}
