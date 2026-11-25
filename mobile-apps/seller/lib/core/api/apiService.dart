import 'dart:convert';
import 'dart:io';

import 'package:curl_logger_dio_interceptor/curl_logger_dio_interceptor.dart';
import 'package:dio/dio.dart';
import 'package:dio/io.dart';
import 'package:eshopplus_seller/core/constants/appConstants.dart';
import 'package:eshopplus_seller/core/routes/routes.dart';
import 'package:eshopplus_seller/commons/repositories/settingsRepository.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/features/auth/repositories/authRepository.dart';
import 'package:eshopplus_seller/main.dart';

import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/foundation.dart';

class ApiException implements Exception {
  String errorMessage;
  final List<Map<String, dynamic>>? errorData;
  final int? errorCode;

  ApiException(this.errorMessage, {this.errorData, this.errorCode});

  @override
  String toString() {
    return errorMessage;
  }
}

class Api {
  static Dio? _dioInstance;

  static Dio get dio {
    if (_dioInstance == null) {
      _dioInstance = Dio(
        BaseOptions(
          connectTimeout: const Duration(seconds: 3),
          receiveTimeout: const Duration(seconds: 30),
          sendTimeout: const Duration(seconds: 30),
          followRedirects: false,
          maxRedirects: 0,
          persistentConnection: true,
          responseType: ResponseType.json,
          contentType: Headers.formUrlEncodedContentType,
          headers: {
            'Connection': 'keep-alive',
            'Keep-Alive': 'timeout=30, max=100',
          },
        ),
      );
      // Configure HTTP adapter for better performance
      if (!kIsWeb) {
        (_dioInstance!.httpClientAdapter as IOHttpClientAdapter)
            .createHttpClient = () {
          final client = HttpClient();
          client.connectionTimeout = const Duration(seconds: 3);
          client.idleTimeout = const Duration(seconds: 30);
          return client;
        };
      }

      // Add timing interceptor for debugging
      if (kDebugMode) {
        _dioInstance!.interceptors.add(
          InterceptorsWrapper(
            onRequest: (options, handler) {
              options.extra['start_time'] =
                  DateTime.now().millisecondsSinceEpoch;
              options.extra['dns_start'] =
                  DateTime.now().millisecondsSinceEpoch;

              handler.next(options);
            },
            onResponse: (response, handler) {
              handler.next(response);
            },
            onError: (error, handler) {
              handler.next(error);
            },
          ),
        );
      }
      if (bool.fromEnvironment('dart.vm.product') == false) {
        dio.interceptors.add(
          CurlLoggerDioInterceptor(printOnSuccess: true, convertFormData: true),
        );
      }
    }
    return _dioInstance!;
  }

  static Map<String, dynamic> headers(bool useAuthToken) {
    String token = AuthRepository.getToken();
    Map<String, dynamic> headers = {
      "Language-Id": SettingsRepository().getCurrentAppLanguage().id,
    };
    if (!useAuthToken || token.isEmpty) {
      return headers;
    }
    headers['Authorization'] = "Bearer $token";
    return headers;
  }

  static printLongString(String text) {
    final RegExp pattern = RegExp('.{1,800}'); // 800 is the size of each chunk
    pattern.allMatches(text).forEach((RegExpMatch match) {
      debugPrint(match.group(0));
    });
  }

  static callOnUnauthorized(String url, {String? message}) {
    if ([
      ApiURL.verifyUser,
      ApiURL.register,
      ApiURL.updateFcm,
      ApiURL.updateUser,
      ApiURL.getSellerStores,
    ].contains(url)) {
      Utils.showSnackBar(message: unauthenticatedWarningKey);
      Utils.navigateToScreen(
        navigatorKey.currentContext!,
        Routes.loginScreen,
        replaceAll: true,
      );
    }
  }

  static Future<Map<String, dynamic>> post({
    required Map<String, dynamic> body,
    required String url,
    required bool useAuthToken,
    Map<String, dynamic>? queryParameters,
    CancelToken? cancelToken,
    Function(int, int)? onSendProgress,
    Function(int, int)? onReceiveProgress,
  }) async {
    try {
      final FormData formData = FormData.fromMap(
        body,
        ListFormat.multiCompatible,
      );
      final response = await dio.post(
        url,
        data: formData,
        queryParameters: queryParameters,
        cancelToken: cancelToken,
        onReceiveProgress: onReceiveProgress,
        onSendProgress: onSendProgress,
        options: Options(headers: headers(useAuthToken)),
      );
      //below APIs have differnet response format
      if ([
        ApiURL.chatifyFetchMessagesApi,
        ApiURL.chatifySendMessageApi,
        ApiURL.chatifyMakeSeenApi,
      ].contains(url)) {
        return Map.from(response.data);
      }
      if (url == ApiURL.chatifyAuthAPI) {
        return jsonDecode(response.data);
      }

      if (response.data[ApiURL.errorKey]) {
        if (response.data[ApiURL.codeKey] == 401) {
          callOnUnauthorized(url);
        }

        String errorMsg =
            SettingsRepository().getCurrentAppLanguage().code != null &&
                SettingsRepository().getCurrentAppLanguage().code !=
                    englishLangCode
            ? response.data[ApiURL.languageMessageKey] ??
                  response.data[ApiURL.messageKey]
            : response.data[ApiURL.messageKey];
        if (url == ApiURL.generateAWB) {
          throw ApiException(errorMsg);
        } else {
          errorMsg = _appendDataErrors(errorMsg, response.data);
          throw ApiException(
            errorMsg,
            errorCode: response.data[ApiURL.codeKey],
          );
        }
      }
      if (SettingsRepository().getCurrentAppLanguage().code != null &&
          SettingsRepository().getCurrentAppLanguage().code !=
              englishLangCode &&
          response.data.containsKey(ApiURL.languageMessageKey)) {
        response.data[ApiURL.messageKey] =
            response.data[ApiURL.languageMessageKey];
      }
      return Map.from(response.data);
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url, message: e.response!.data[ApiURL.messageKey]);
        }
      } else {
        // Something happened in setting up the request or an error occurred before the response
        throw ApiException(
          e.error is SocketException
              ? noInternetKey
              : e.response?.data[ApiURL.messageKey],
        );
      }

      debugPrint("error=${e.message}=${e.response?.data}");

      throw ApiException(
        e.error is SocketException
            ? noInternetKey
            : e.response?.data[ApiURL.messageKey],
      );
      //throw ApiException(e.error is SocketException ? noInternetKey: e.response?.data[ApiURL.messageKey]);
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage, errorCode: e.errorCode);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }

  static String _appendDataErrors(
    String message,
    Map<String, dynamic> responseData,
  ) {
    if (!responseData.containsKey(ApiURL.dataKey) ||
        responseData[ApiURL.dataKey] == null) {
      return message;
    }
    final data = responseData[ApiURL.dataKey];
    String dataErrors = '';
    if (data is Map) {
      dataErrors = getApiMessage(data);
    } else if (data is List) {
      for (var item in data) {
        if (item is Map) {
          dataErrors += getApiMessage(item);
        } else if (item is String) {
          dataErrors += '$item\n';
        }
      }
    } else if (data is String) {
      dataErrors = data;
    }
    if (dataErrors.trim().isNotEmpty) {
      message = '$message\n$dataErrors'.trim();
    }
    return message;
  }

  static String getApiMessage(var message, {bool withkey = false}) {
    String apimsg = '';
    if (message is String) {
      return apimsg = message;
    } else {
      message.forEach((k, v) {
        if (v is List<dynamic>) {
          apimsg = "$apimsg${withkey ? "$k: " : ""}${v.first}\n";
        } else {
          apimsg = "${apimsg + (withkey ? "$k: " : "") + v.toString()}\n";
        }
      });
    }
    return apimsg;
  }

  static Future<Map<String, dynamic>> get({
    required String url,
    required bool useAuthToken,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.get(
        url,
        queryParameters: queryParameters,
        options: Options(headers: headers(useAuthToken)),
      );

      if ([
        ApiURL.getProductRating,
        ApiURL.chatifySearchApi,
        ApiURL.chatifyGetContactsApi,
      ].contains(url)) {
        return Map.from(response.data);
      }
      if (response.data[ApiURL.errorKey]) {
        if (response.data[ApiURL.codeKey] == 401) {
          callOnUnauthorized(url);
        }

        String errorMsg =
            SettingsRepository().getCurrentAppLanguage().code != null &&
                SettingsRepository().getCurrentAppLanguage().code !=
                    englishLangCode
            ? response.data[ApiURL.languageMessageKey] ??
                  response.data[ApiURL.messageKey]
            : response.data[ApiURL.messageKey];
        errorMsg = _appendDataErrors(errorMsg, response.data);
        throw ApiException(errorMsg);
      }
      if (SettingsRepository().getCurrentAppLanguage().code != null &&
          SettingsRepository().getCurrentAppLanguage().code !=
              englishLangCode &&
          response.data.containsKey(ApiURL.languageMessageKey)) {
        response.data[ApiURL.messageKey] =
            response.data[ApiURL.languageMessageKey];
      }
      return Map.from(response.data);
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url, message: e.response!.data[ApiURL.messageKey]);
        }
      }

      throw ApiException(
        e.error is SocketException
            ? noInternetKey
            : e.response?.data[ApiURL.messageKey],
      );
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage, errorCode: e.errorCode);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }

  static Future<Map<String, dynamic>> put({
    Map<String, dynamic>? body,
    required String url,
    required bool useAuthToken,
    Map<String, dynamic>? queryParameters,
    CancelToken? cancelToken,
    Function(int, int)? onSendProgress,
    Function(int, int)? onReceiveProgress,
  }) async {
    try {
      final FormData formData = FormData.fromMap(
        queryParameters ?? {},
        ListFormat.multiCompatible,
      );
      // printLongString('==$url===$queryParameters');
      final response = await dio.put(
        url,
        data: formData,
        queryParameters: queryParameters,
        cancelToken: cancelToken,
        onReceiveProgress: onReceiveProgress,
        onSendProgress: onSendProgress,
        options: Options(headers: headers(useAuthToken)),
      );

      if (response.data[ApiURL.errorKey]) {
        if (response.data[ApiURL.codeKey] == 401) {
          callOnUnauthorized(url);
        }
        String errorMsg =
            SettingsRepository().getCurrentAppLanguage().code != null &&
                SettingsRepository().getCurrentAppLanguage().code !=
                    englishLangCode
            ? response.data[ApiURL.languageMessageKey] ??
                  response.data[ApiURL.messageKey]
            : response.data[ApiURL.messageKey];
        if (url == ApiURL.updateShiprocketOrderStatus) {
          throw ApiException(errorMsg);
        } else {
          errorMsg = _appendDataErrors(errorMsg, response.data);
          throw ApiException(errorMsg);
        }
      }
      if (SettingsRepository().getCurrentAppLanguage().code != null &&
          SettingsRepository().getCurrentAppLanguage().code !=
              englishLangCode &&
          response.data.containsKey(ApiURL.languageMessageKey)) {
        response.data[ApiURL.messageKey] =
            response.data[ApiURL.languageMessageKey];
      }
      return Map.from(response.data);
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url, message: e.response!.data[ApiURL.messageKey]);
        }
      }

      throw ApiException(
        e.error is SocketException
            ? noInternetKey
            : e.response?.data[ApiURL.messageKey],
      );
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }

  static Future<Map<String, dynamic>> delete({
    required String url,
    required bool useAuthToken,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.delete(
        url,
        queryParameters: queryParameters,
        options: Options(headers: headers(useAuthToken)),
      );

      if (response.data[ApiURL.errorKey]) {
        if (response.data[ApiURL.codeKey] == 401) {
          callOnUnauthorized(url);
        }
        String errorMsg =
            SettingsRepository().getCurrentAppLanguage().code != null &&
                SettingsRepository().getCurrentAppLanguage().code !=
                    englishLangCode
            ? response.data[ApiURL.languageMessageKey] ??
                  response.data[ApiURL.messageKey]
            : response.data[ApiURL.messageKey].toString();
        errorMsg = _appendDataErrors(errorMsg, response.data);
        throw ApiException(errorMsg);
      }
      if (SettingsRepository().getCurrentAppLanguage().code != null &&
          SettingsRepository().getCurrentAppLanguage().code !=
              englishLangCode &&
          response.data.containsKey(ApiURL.languageMessageKey)) {
        response.data[ApiURL.messageKey] =
            response.data[ApiURL.languageMessageKey];
      }
      return Map.from(response.data);
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url, message: e.response!.data[ApiURL.messageKey]);
        }
      }

      throw ApiException(
        e.error is SocketException
            ? noInternetKey
            : e.response?.data[ApiURL.messageKey],
      );
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }

  static Future<void> download({
    required String url,
    required CancelToken cancelToken,
    required String savePath,
    required Function updateDownloadedPercentage,
  }) async {
    try {
      await dio.download(
        url,
        savePath,
        cancelToken: cancelToken,
        onReceiveProgress: ((count, total) {
          updateDownloadedPercentage((count / total) * 100);
        }),
      );
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url);
        }
      }
      throw ApiException(
        e.error is SocketException ? noInternetKey : defaultErrorMessageKey,
      );
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }

  static Future<String> getHtmlContent({
    required String url,
    required bool useAuthToken,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.get(
        url,
        queryParameters: queryParameters,
        options: Options(headers: headers(useAuthToken)),
      );

      return response.data;
    } on DioException catch (e) {
      if (e.response != null) {
        // The request was made and the server responded with a status code
        if (e.response!.statusCode == 429) {
          // Handle the 429 error (Too Many Requests)
          throw ApiException(rateLimitErrorMessageKey);
        }
        if (e.response!.statusCode == 500) {
          // Handle the 500 error
          throw ApiException(internalServerErrorMessageKey);
        }
        if (e.response!.statusCode == 401) {
          // Handle the 401 error
          callOnUnauthorized(url, message: e.response!.data[ApiURL.messageKey]);
        }
      }

      throw ApiException(
        e.error is SocketException
            ? noInternetKey
            : e.response?.data[ApiURL.messageKey],
      );
    } on ApiException catch (e) {
      throw ApiException(e.errorMessage);
    } catch (e) {
      throw ApiException(defaultErrorMessageKey);
    }
  }
}
