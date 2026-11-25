import 'dart:convert';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../models/location.dart';

abstract class AddPickupLocationState {}

class AddPickupLocationInitial extends AddPickupLocationState {}

class AddPickupLocationProgress extends AddPickupLocationState {
  AddPickupLocationProgress();
}

class AddPickupLocationSuccess extends AddPickupLocationState {
  String successMsg;
  Location location;
  AddPickupLocationSuccess(this.successMsg, this.location);
}

class AddPickupLocationFailure extends AddPickupLocationState {
  final String errorMessage;
  final Map<String, String>? fieldErrors;
  AddPickupLocationFailure(this.errorMessage, {this.fieldErrors});
}

class AddPickupLocationCubit extends Cubit<AddPickupLocationState> {
  AddPickupLocationCubit() : super(AddPickupLocationInitial());

  addLocation(
    BuildContext context,
    Map<String, String?> parameter,
  ) {
    emit(AddPickupLocationProgress());
    addLocationProcess(context, parameter).then((value) {
      parameter['id'] = value[ApiURL.dataKey]['address']['id'].toString();
      Location location = Location.fromJson(parameter);
      emit(AddPickupLocationSuccess(value["message"], location));
    }).catchError((e) {
      final parsedErrors = _parseFieldErrors(e.toString());
      emit(AddPickupLocationFailure(e.toString(), fieldErrors: parsedErrors));
    });
  }

  Map<String, String>? _parseFieldErrors(String errorMessage) {
    try {
      // Parse the outer JSON
      final decoded = jsonDecode(errorMessage);

      // Navigate to the nested message
      String? nestedMessage;
      if (decoded is Map) {
        if (decoded['message'] is Map && decoded['message']['message'] != null) {
          nestedMessage = decoded['message']['message'];
        } else if (decoded['language_message_key'] is Map &&
                   decoded['language_message_key']['message'] != null) {
          nestedMessage = decoded['language_message_key']['message'];
        }
      }

      if (nestedMessage == null) return null;

      // Parse the nested JSON that contains field errors
      final fieldErrorsJson = jsonDecode(nestedMessage);

      if (fieldErrorsJson is! Map) return null;

      // Convert to Map<String, String> - taking first error for each field
      Map<String, String> fieldErrors = {};
      fieldErrorsJson.forEach((key, value) {
        if (value is List && value.isNotEmpty) {
          fieldErrors[key] = value[0].toString();
        } else if (value is String) {
          fieldErrors[key] = value;
        }
      });

      return fieldErrors.isEmpty ? null : fieldErrors;
    } catch (e) {
      return null;
    }
  }

  Future addLocationProcess(
      BuildContext context, Map<String, String?> parameter) async {
    try {
      final result = await Api.post(
          url: ApiURL.addPickupLocation, useAuthToken: true, body: parameter);
      return result;
    } catch (e) {
      throw ApiException(e.toString());
    }
  }
}
