import 'package:app_links/app_links.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/product/models/product.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/main.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get/get.dart';

/// Simplified deep link manager that handles all scenarios correctly
class DeepLinkManager {
  static final DeepLinkManager _instance = DeepLinkManager._internal();
  factory DeepLinkManager() => _instance;
  DeepLinkManager._internal();

  late AppLinks _appLinks;
  Uri? _lastHandledUri;
  bool _isInitialized = false;
  bool _isAppReady = false;
  bool _isNavigatorReady = false;

  // Only queue deep links that arrive during cold start
  final List<Uri> _coldStartDeepLinks = [];
  bool _isColdStart = true;

  /// Initialize the deep link manager
  void initialize() {
    if (_isInitialized) return;
    _isInitialized = true;

    _appLinks = AppLinks();

    // Handle initial link when app is launched from terminated state
    _handleInitialLink();

    // Handle links when app is in foreground/background
    _appLinks.uriLinkStream.listen(_onDeepLinkReceived);
  }

  /// Mark app as ready (called after stores and settings are loaded)
  void markAppAsReady() {
    if (_isAppReady) return;

    _isAppReady = true;
    // Process any cold start deep links
    _processColdStartDeepLinks();
  }

  /// Mark navigator as ready (called when main screen is loaded)
  void markNavigatorReady() {
    if (_isNavigatorReady) return;

    _isNavigatorReady = true;

    // Mark cold start as complete
    _isColdStart = false;

    // Process any remaining cold start deep links
    _processColdStartDeepLinks();
  }

  /// Check if app can handle deep links immediately
  bool get canHandleDeepLinks => _isAppReady && _isNavigatorReady;

  /// Check if there are cold start deep links pending
  bool get hasColdStartDeepLinks => _coldStartDeepLinks.isNotEmpty;

  /// Check if there are any deep links pending (including initial link)
  bool get hasPendingDeepLinks =>
      _coldStartDeepLinks.isNotEmpty || _lastHandledUri != null;

  /// Check if there are pending deep links before app is ready
  bool get hasInitialDeepLink => _lastHandledUri != null;

  /// Get the initial deep link URI
  Uri? get initialDeepLink => _lastHandledUri;

  /// Get count of cold start deep links
  int get coldStartDeepLinksCount => _coldStartDeepLinks.length;

  Future<void> _handleInitialLink() async {
    try {
      final Uri? initialUri = await _appLinks.getInitialLink();
      if (initialUri != null) {
        _lastHandledUri = initialUri;
        _handleDeepLink(initialUri);
      }
    } catch (e) {}
  }

  void _onDeepLinkReceived(Uri uri) {
    // if (uri != _lastHandledUri) {
    _lastHandledUri = uri;
    _handleDeepLink(uri);
    // }
  }

  void _handleDeepLink(Uri uri) {
    // If app can handle deep links immediately, process now
    if (canHandleDeepLinks) {
      _processDeepLink(uri);
    } else {
      // App not ready, queue for cold start processing
      // if (!_coldStartDeepLinks.contains(uri)) {
      _coldStartDeepLinks.add(uri);
      // }
    }
  }

  void _processColdStartDeepLinks() {
    if (_coldStartDeepLinks.isEmpty || !canHandleDeepLinks) return;

    // Process the oldest cold start deep link
    final uri = _coldStartDeepLinks.removeAt(0);
    _processDeepLink(uri);
  }

  void _processDeepLink(Uri uri) {
    // Get the current context from navigator key
    final context = navigatorKey.currentContext;
    if (context != null) {
      _executeDeepLink(uri, context);
    } else {
      if (_isColdStart) {
        _coldStartDeepLinks.insert(0, uri);
      }
    }
  }

  void _executeDeepLink(Uri uri, BuildContext context) {
    try {
      // Check if we're still on splash screen
      if (Get.currentRoute == Routes.splashScreen) {
        if (_isColdStart) {
          _coldStartDeepLinks.insert(0, uri);
        }
        return;
      }

      final type = uri.pathSegments.isNotEmpty ? uri.pathSegments[0] : '';
      switch (type) {
        case 'product':
          _handleProductDeepLink(uri, context);
          break;
        case 'combo-product':
          _handleComboProductDeepLink(uri, context);
          break;
        case 'products':
          // affiliate links
          _handleProductDeepLink(uri, context);
          break;
        case 'blog':
          _handleBlogDeepLink(uri, context);
          break;
        case 'seller':
          _handleSellerDeepLink(uri, context);
          break;
      }
    } catch (e) {
      // Re-queue the deep link if there was an error and still in cold start
      if (_isColdStart) {
        _coldStartDeepLinks.insert(0, uri);
      }
    }
  }

  /// eshop://product/{slug}?store={store_slug}
  void _handleProductDeepLink(Uri uri, BuildContext context) {
    try {
      final String slug = uri.pathSegments.length > 1
          ? uri.pathSegments[1]
          : '';
      final String storeSlug = uri.queryParameters['store'] ?? '';
      final String reference = uri.queryParameters['ref'] ?? '';
      if (storeSlug.isNotEmpty) {
        context.read<StoresCubit>().changeDefaultStoreBySlug(
          storeSlug: storeSlug,
        );
      }
      Utils.navigateToScreen(
        context,
        Routes.productDetailsScreen,
        preventDuplicates: false,
        replacePrevious: Get.currentRoute == Routes.productDetailsScreen,
        arguments: {
          'slug': slug,
          'storeSlug': storeSlug,
          'product': Product(slug: slug),
          'isComboProduct': false,
          'affiliateReference': reference,
        },
      );
    } catch (e) {
      rethrow;
    }
  }

  /// eshop://combo-product/{slug}?store={store_slug}
  void _handleComboProductDeepLink(Uri uri, BuildContext context) {
    try {
      final String slug = uri.pathSegments.length > 1
          ? uri.pathSegments[1]
          : '';
      final String storeSlug = uri.queryParameters['store'] ?? '';
      final String reference = uri.queryParameters['ref'] ?? '';
      if (storeSlug.isNotEmpty) {
        context.read<StoresCubit>().changeDefaultStoreBySlug(
          storeSlug: storeSlug,
        );
      }
      Utils.navigateToScreen(
        context,
        Routes.productDetailsScreen,
        preventDuplicates: false,
        replacePrevious: Get.currentRoute == Routes.productDetailsScreen,
        arguments: {
          'slug': slug,
          'storeSlug': storeSlug,
          'product': Product(slug: slug, type: comboProductType),
          'isComboProduct': true,
          'affiliateReference': reference,
        },
      );
    } catch (e) {
      rethrow;
    }
  }

  /// eshop://blog/{slug}?store={store_slug}
  void _handleBlogDeepLink(Uri uri, BuildContext context) {
    try {
      final String blogSlug = uri.pathSegments.length > 1
          ? uri.pathSegments[1]
          : '';
      final String storeSlug = uri.queryParameters['store'] ?? '';
      if (storeSlug.isNotEmpty) {
        context.read<StoresCubit>().changeDefaultStoreBySlug(
          storeSlug: storeSlug,
        );
      }
      Utils.navigateToScreen(
        context,
        Routes.blogDetailScreen,
        preventDuplicates: false,
        replacePrevious: Get.currentRoute == Routes.blogDetailScreen,
        arguments: {'blogSlug': blogSlug, 'storeSlug': storeSlug},
      );
    } catch (e) {
      rethrow;
    }
  }

  /// eshop://seller/{sellerId}?store={store_id}
  void _handleSellerDeepLink(Uri uri, BuildContext context) {
    try {
      final int sellerId = int.parse(uri.pathSegments[1]);
      final String storeParam = uri.queryParameters['store'] ?? '';
      final int? storeId = int.tryParse(storeParam);
      if (storeId != null) {
        context.read<StoresCubit>().changeDefaultStore(storeId: storeId);
      }
      Utils.navigateToScreen(
        context,
        Routes.sellerDetailScreen,
        preventDuplicates: false,
        replacePrevious: Get.currentRoute == Routes.sellerDetailScreen,
        arguments: {'sellerId': sellerId, 'storeSlug': storeParam},
      );
    } catch (e) {
      rethrow;
    }
  }

  /// Clear all cold start deep links (useful for testing or reset)
  void clearColdStartDeepLinks() {
    _coldStartDeepLinks.clear();
    _lastHandledUri = null;
  }

  /// Force process cold start deep links (useful for debugging)
  void forceProcessColdStartDeepLinks() {
    if (canHandleDeepLinks) {
      _processColdStartDeepLinks();
    }
  }

  /// Get detailed status for debugging
  Map<String, dynamic> get debugStatus => {
    'isInitialized': _isInitialized,
    'isAppReady': _isAppReady,
    'isNavigatorReady': _isNavigatorReady,
    'isColdStart': _isColdStart,
    'canHandleDeepLinks': canHandleDeepLinks,
    'coldStartDeepLinksCount': _coldStartDeepLinks.length,
    'coldStartDeepLinks': _coldStartDeepLinks
        .map((uri) => uri.toString())
        .toList(),
    'lastHandledUri': _lastHandledUri?.toString(),
  };
}
