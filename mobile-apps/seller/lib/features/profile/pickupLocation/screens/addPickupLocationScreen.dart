import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customAppbar.dart';
import 'package:eshopplus_seller/commons/widgets/customBottomButtonContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/core/routes/routes.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/addPickupLocationCubit.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/getPickupLocationCubit.dart';
import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:eshopplus_seller/utils/inputValidators.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geocoding/geocoding.dart' as geo;
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import '../models/location.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import '../../../../utils/utils.dart';

import '../../../../commons/widgets/customRoundedButton.dart';
import '../../../../commons/widgets/customTextFieldContainer.dart';

class AddPickupLocationScreen extends StatefulWidget {
  final GetPickupLocationCubit getPickupLocationCubit;
  const AddPickupLocationScreen({
    Key? key,
    required this.getPickupLocationCubit,
  }) : super(key: key);

  static Widget getRouteInstance() {
    Map arguments = Get.arguments;
    return BlocProvider<AddPickupLocationCubit>(
      create: (context) => AddPickupLocationCubit(),
      child: AddPickupLocationScreen(
        getPickupLocationCubit: arguments.containsKey('getPickupLocationCubit')
            ? arguments['getPickupLocationCubit']
            : null,
      ),
    );
  }

  @override
  AddPickupLocationScreenState createState() => AddPickupLocationScreenState();
}

class AddPickupLocationScreenState extends State<AddPickupLocationScreen> {
  Map<String, TextEditingController> controllers = {};
  Map<String, FocusNode> focusNodes = {};
  bool _isLoading = false;
  bool serviceEnabled = false;
  final _formKey = GlobalKey<FormState>();
  Map<String, String>? fieldErrors;
  Map apiField = {
    pickupLocationNameKey: "pickup_location",
    nameKey: "name",
    emailKey: "email",
    phoneNumberKey: "phone",
    countryKey: "country",
    stateKey: "state",
    cityKey: "city",
    pincodeKey: "pincode",
    addressKey: "address",
    additionalAddressKey: "address2",
    latitudeKey: "latitude",
    longitudeKey: "longitude",
  };

  @override
  void initState() {
    super.initState();
    apiField.forEach((key, value) {
      controllers[key] = TextEditingController();
      focusNodes[key] = FocusNode();
    });
  }

  @override
  void dispose() {
    controllers.forEach((key, controller) {
      controller.dispose();
    });
    focusNodes.forEach((key, focusNode) {
      focusNode.dispose();
    });
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.primaryContainer,
      appBar: const CustomAppbar(
        titleKey: addPickupLocKey,
        showBackButton: true,
      ),
      body: GestureDetector(
        onTap: () => FocusScope.of(context).unfocus(),
        child: Form(key: _formKey, child: buildAddForm(context)),
      ),
      bottomNavigationBar: buildBottomBar(),
    );
  }

  Widget buildBottomBar() {
    return BlocConsumer<AddPickupLocationCubit, AddPickupLocationState>(
      listener: (context, state) {
        if (state is AddPickupLocationSuccess) {
          setState(() {
            fieldErrors = null;
          });
          Utils.showSnackBar(message: state.successMsg);
          List<Location> locations = [];
          if (widget.getPickupLocationCubit.state
              is GetPickupLocationFetchSuccess) {
            locations =
                (widget.getPickupLocationCubit.state
                        as GetPickupLocationFetchSuccess)
                    .locations;
            locations.insert(0, state.location);
            widget.getPickupLocationCubit.emisSuccessState(
              locations,
              total:
                  (widget.getPickupLocationCubit.state
                          as GetPickupLocationFetchSuccess)
                      .total +
                  1,
            );
          } else {
            locations.insert(0, state.location);
            widget.getPickupLocationCubit.emisSuccessState(
              locations,
              total: locations.length,
            );
          }

          Navigator.of(context).pop();
        }
        if (state is AddPickupLocationProgress) {
          Utils.showLoader(context);
        } else {
          Utils.hideLoader(context);
        }
        if (state is AddPickupLocationFailure) {
          setState(() {
            fieldErrors = state.fieldErrors;
          });
          if (state.fieldErrors != null && state.fieldErrors!.isNotEmpty) {
            _formKey.currentState?.validate();
          } else {
            Utils.showSnackBar(
              message: state.errorMessage,
              msgDuration: const Duration(seconds: 4),
            );
          }
        }
      },
      builder: (context, state) {
        return CustomBottomButtonContainer(
          child: Row(
            children: [
              Expanded(
                child: CustomRoundedButton(
                  widthPercentage: 1,
                  buttonTitle: backKey,
                  showBorder: true,
                  backgroundColor: Theme.of(context).colorScheme.onPrimary,
                  borderColor: Theme.of(context).hintColor,
                  style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                    color: Theme.of(context).colorScheme.secondary,
                  ),
                  onTap: () => Utils.popNavigation(context),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: CustomRoundedButton(
                  widthPercentage: 1,
                  buttonTitle: addLocationKey,
                  showBorder: false,
                  onTap: () {
                    if (_formKey.currentState!.validate()) {
                      addLocationProcess();
                    }
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget buildAddForm(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsetsDirectional.all(appContentHorizontalPadding),
      child: Column(
        children: <Widget>[
          CustomTextFieldContainer(
            hintTextKey: pickupLocationNameKey,
            textEditingController: controllers[pickupLocationNameKey]!,
            labelKey: pickupLocationNameKey,
            textInputAction: TextInputAction.next,
            keyboardType: .text,
            focusNode: focusNodes[pickupLocationNameKey],
            isSetValidator: true,
            inputFormatters: [LengthLimitingTextInputFormatter(100)],
            validator: (v) {
              String? apiError = getFieldError(pickupLocationNameKey);
              if (apiError != null) return apiError;

              if (v == null || v.isEmpty) {
                return Utils.getTranslatedValue(pickupLocationRequiredKey);
              }
              if (v.length < 3) {
                return Utils.getTranslatedValue(pickupLocationMinCharactersKey);
              }

              return null;
            },
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[nameKey]),
          ),
          CustomTextFieldContainer(
            hintTextKey: shipperNameKey,
            textEditingController: controllers[nameKey]!,
            labelKey: nameKey,
            textInputAction: TextInputAction.next,
            keyboardType: .text,
            isSetValidator: true,
            focusNode: focusNodes[nameKey],
            inputFormatters: [LengthLimitingTextInputFormatter(100)],
            validator: (v) {
              String? apiError = getFieldError(nameKey);
              if (apiError != null) return apiError;

              if (v == null || v.isEmpty) {
                return Utils.getTranslatedValue(nameRequiredKey);
              }
              if (v.length < 2) {
                return Utils.getTranslatedValue(nameMinCharactersKey);
              }

              return null;
            },
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[emailKey]),
          ),
          CustomTextFieldContainer(
            hintTextKey: shipperEmailKey,
            textEditingController: controllers[emailKey]!,
            labelKey: emailKey,
            textInputAction: TextInputAction.next,
            keyboardType: .emailAddress,
            isSetValidator: true,
            focusNode: focusNodes[emailKey],
            inputFormatters: [LengthLimitingTextInputFormatter(100)],
            validator: (v) {
              String? apiError = getFieldError(emailKey);
              if (apiError != null) return apiError;

              return Validator.validateEmail(v, context);
            },
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[phoneNumberKey]),
          ),
          CustomTextFieldContainer(
            hintTextKey: shipperPhoneKey,
            textEditingController: controllers[phoneNumberKey]!,
            labelKey: phoneNumberKey,
            textInputAction: TextInputAction.next,
            keyboardType: .phone,
            isSetValidator: true,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
              LengthLimitingTextInputFormatter(15),
            ],
            validator: (v) {
              String? apiError = getFieldError(phoneNumberKey);
              if (apiError != null) return apiError;

              return Validator.validateIndianPhoneNumber(v, context);
            },
            focusNode: focusNodes[phoneNumberKey],
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[countryKey]),
          ),
          buildAddressField(),
          Row(
            children: [
              Expanded(
                child: CustomTextFieldContainer(
                  hintTextKey: countryKey,
                  textEditingController: controllers[countryKey]!,
                  labelKey: countryKey,
                  textInputAction: TextInputAction.next,
                  keyboardType: .text,
                  isSetValidator: true,
                  focusNode: focusNodes[countryKey],
                  inputFormatters: [
                    FilteringTextInputFormatter.allow(Validator.namePattern),
                    LengthLimitingTextInputFormatter(100),
                  ],
                  validator: (v) {
                    String? apiError = getFieldError(countryKey);
                    if (apiError != null) return apiError;

                    if (v == null || v.isEmpty) {
                      return Utils.getTranslatedValue(countryRequiredKey);
                    }
                    if (v.length < 2) {
                      return Utils.getTranslatedValue(countryMinCharactersKey);
                    }
                    return null;
                  },
                  onFieldSubmitted: (v) =>
                      FocusScope.of(context).requestFocus(focusNodes[stateKey]),
                ),
              ),
              SizedBox(width: appContentHorizontalPadding),
              Expanded(
                child: CustomTextFieldContainer(
                  hintTextKey: stateKey,
                  textEditingController: controllers[stateKey]!,
                  labelKey: stateKey,
                  textInputAction: TextInputAction.next,
                  keyboardType: .text,
                  isSetValidator: true,
                  focusNode: focusNodes[stateKey],
                  inputFormatters: [
                    FilteringTextInputFormatter.allow(Validator.namePattern),
                    LengthLimitingTextInputFormatter(100),
                  ],
                  validator: (v) {
                    String? apiError = getFieldError(stateKey);
                    if (apiError != null) return apiError;

                    if (v == null || v.isEmpty) {
                      return Utils.getTranslatedValue(stateRequiredKey);
                    }
                    if (v.length < 2) {
                      return Utils.getTranslatedValue(stateMinCharactersKey);
                    }
                    return null;
                  },
                  onFieldSubmitted: (v) =>
                      FocusScope.of(context).requestFocus(focusNodes[cityKey]),
                ),
              ),
            ],
          ),
          Row(
            children: [
              Expanded(
                child: CustomTextFieldContainer(
                  hintTextKey: cityKey,
                  textEditingController: controllers[cityKey]!,
                  labelKey: cityKey,
                  textInputAction: TextInputAction.next,
                  keyboardType: .text,
                  isSetValidator: true,
                  focusNode: focusNodes[cityKey],
                  inputFormatters: [
                    FilteringTextInputFormatter.allow(Validator.namePattern),
                    LengthLimitingTextInputFormatter(100),
                  ],
                  validator: (v) {
                    String? apiError = getFieldError(cityKey);
                    if (apiError != null) return apiError;

                    if (v == null || v.isEmpty) {
                      return Utils.getTranslatedValue(cityRequiredKey);
                    }
                    if (v.length < 2) {
                      return Utils.getTranslatedValue(cityMinCharactersKey);
                    }
                    return null;
                  },
                  onFieldSubmitted: (v) => FocusScope.of(
                    context,
                  ).requestFocus(focusNodes[pincodeKey]),
                ),
              ),
              DesignConfig.defaultWidthSizedBox,
              Expanded(
                child: CustomTextFieldContainer(
                  hintTextKey: pincodeKey,
                  textEditingController: controllers[pincodeKey]!,
                  labelKey: pincodeKey,
                  textInputAction: TextInputAction.next,
                  isSetValidator: true,
                  keyboardType: .text,
                  inputFormatters: [LengthLimitingTextInputFormatter(10)],
                  validator: (v) {
                    String? apiError = getFieldError(pincodeKey);
                    if (apiError != null) return apiError;

                    if (v == null || v.isEmpty) {
                      return Utils.getTranslatedValue(pincodeRequiredKey);
                    }
                    if (v.length < 3) {
                      return Utils.getTranslatedValue(pincodeMinCharactersKey);
                    }

                    final pincodeRegex = RegExp(r'^[a-zA-Z0-9\s\-]+$');
                    if (!pincodeRegex.hasMatch(v)) {
                      return Utils.getTranslatedValue(pincodeInvalidFormatKey);
                    }
                    return null;
                  },
                  focusNode: focusNodes[pincodeKey],
                  onFieldSubmitted: (v) => FocusScope.of(
                    context,
                  ).requestFocus(focusNodes[addressKey]),
                ),
              ),
            ],
          ),

          CustomTextFieldContainer(
            hintTextKey: additionalAddressKey,
            textEditingController: controllers[additionalAddressKey]!,
            labelKey: additionalAddressKey,
            maxLines: 3,
            textInputAction: TextInputAction.next,
            isSetValidator: true,
            keyboardType: .text,
            focusNode: focusNodes[additionalAddressKey],
            inputFormatters: [LengthLimitingTextInputFormatter(255)],
            validator: (v) {
              String? apiError = getFieldError(additionalAddressKey);
              if (apiError != null) return apiError;

              if (v == null || v.isEmpty) {
                return Utils.getTranslatedValue(additionalAddressRequiredKey);
              }
              if (v.length < 3) {
                return Utils.getTranslatedValue(
                  additionalAddressMinCharactersKey,
                );
              }

              return null;
            },
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[latitudeKey]),
          ),
          CustomTextFieldContainer(
            hintTextKey: latitudeKey,
            textEditingController: controllers[latitudeKey]!,
            labelKey: latitudeKey,
            textInputAction: TextInputAction.next,
            keyboardType: const .numberWithOptions(decimal: true, signed: true),
            isSetValidator: true,
            focusNode: focusNodes[latitudeKey],
            inputFormatters: [
              FilteringTextInputFormatter.allow(RegExp(r'^-?\d*\.?\d*')),
            ],
            validator: (v) {
              String? apiError = getFieldError(latitudeKey);
              if (apiError != null) return apiError;

              if (v == null || v.isEmpty) {
                return Utils.getTranslatedValue(latitudeRequiredKey);
              }
              final latitude = double.tryParse(v);

              if (latitude != null && (latitude < -90 || latitude > 90)) {
                return Utils.getTranslatedValue(latitudeRangeKey);
              }
              return null;
            },
            onFieldSubmitted: (v) =>
                FocusScope.of(context).requestFocus(focusNodes[longitudeKey]),
          ),
          CustomTextFieldContainer(
            hintTextKey: longitudeKey,
            textEditingController: controllers[longitudeKey]!,
            labelKey: longitudeKey,
            textInputAction: TextInputAction.done,
            keyboardType: const .numberWithOptions(decimal: true, signed: true),
            isSetValidator: true,
            focusNode: focusNodes[longitudeKey],
            inputFormatters: [
              FilteringTextInputFormatter.allow(RegExp(r'^-?\d*\.?\d*')),
            ],
            validator: (v) {
              String? apiError = getFieldError(longitudeKey);
              if (apiError != null) return apiError;

              if (v == null || v.isEmpty) {
                return Utils.getTranslatedValue(longitudeRequiredKey);
              }
              final longitude = double.tryParse(v);

              if (longitude != null && (longitude < -180 || longitude > 180)) {
                return Utils.getTranslatedValue(longitudeRangeKey);
              }
              return null;
            },
            onFieldSubmitted: (v) => focusNodes[longitudeKey]!.unfocus(),
          ),
        ],
      ),
    );
  }

  CustomTextFieldContainer buildAddressField() {
    return CustomTextFieldContainer(
      hintTextKey: addressKey,
      textEditingController: controllers[addressKey]!,
      labelKey: addressKey,
      maxLines: 3,
      textInputAction: TextInputAction.next,
      keyboardType: .text,
      isSetValidator: true,
      focusNode: focusNodes[addressKey],
      inputFormatters: [LengthLimitingTextInputFormatter(255)],
      validator: (value) {
        String? apiError = getFieldError(addressKey);
        if (apiError != null) return apiError;

        if (value == null || value.isEmpty) {
          return Utils.getTranslatedValue(addressRequiredKey);
        }
        if (value.length < 10) {
          return Utils.getTranslatedValue(addressMinCharactersKey);
        }

        final addressRegex = RegExp(r'^(?=.*\d)(?=.*[a-zA-Z]).+$');
        if (!addressRegex.hasMatch(value)) {
          return Utils.getTranslatedValue(
            addressMustContainLettersAndNumbersKey,
          );
        }
        return null;
      },
      suffixWidget: _isLoading
          ? SizedBox(
              height: 24,
              width: 24,
              child: CustomCircularProgressIndicator(),
            )
          : IconButton(
              icon: Icon(
                Icons.my_location_outlined,
                color: Theme.of(context).colorScheme.primary,
              ),
              onPressed: () async {
                if (!_isLoading) {
                  setState(() {
                    _isLoading = true;
                  });
                  serviceEnabled = await Geolocator.isLocationServiceEnabled();
                  if (!serviceEnabled) {
                    // Location services are not enabled don't continue
                    // accessing the position and request users of the
                    // App to enable the location services.
                    Utils.showSnackBar(
                      message: locationServiceDisabledKey,
                      context: context,
                    );
                    await Geolocator.openLocationSettings();
                    return;
                  }
                  LocationPermission permission;

                  permission = await Geolocator.checkPermission();
                  if (permission == LocationPermission.denied) {
                    permission = await Geolocator.requestPermission();
                    if (permission == LocationPermission.denied) {
                      return Utils.showSnackBar(
                        message: locationServiceDisabledKey,
                        context: context,
                      );
                    }
                  }
                  if (permission == LocationPermission.deniedForever) {
                    // Permissions are denied forever, handle appropriately.
                    return Utils.showSnackBar(
                      message: llocationServicePermanentDeniedKey,
                      context: context,
                    );
                  }

                  Position position = await Geolocator.getCurrentPosition(
                    locationSettings: LocationSettings(
                      accuracy: LocationAccuracy.high,
                    ),
                  );

                  var result = await Utils.navigateToScreen(
                    context,
                    Routes.mapScreen,
                    arguments: {
                      latitudeKey: position.latitude,
                      longitudeKey: position.longitude,
                    },
                  );
                  if (mounted)
                    setState(() {
                      _isLoading = false;
                    });

                  if (result != null) {
                    List<geo.Placemark> placemark = await geo
                        .placemarkFromCoordinates(
                          double.parse(result[latitudeKey]),
                          double.parse(result[longitudeKey]),
                        );

                    final geo.Placemark place = placemark.first;

                    final addressParts = [
                      place.name,
                      if (place.street != place.name) place.street,
                      place.subLocality,
                      // place.locality,
                      // place.country,
                      // place.postalCode
                    ];

                    // Join all non-null and non-empty parts with commas
                    final address = addressParts
                        .where((part) => part != null && part.trim().isNotEmpty)
                        .join(', ');

                    if (mounted) {
                      setState(() {
                        controllers[addressKey]!.text = address;
                        controllers[countryKey]!.text =
                            placemark[0].country ?? '';
                        controllers[stateKey]!.text =
                            placemark[0].administrativeArea ?? '';
                        controllers[cityKey]!.text =
                            placemark[0].locality ?? '';
                        controllers[pincodeKey]!.text =
                            placemark[0].postalCode ?? '';

                        controllers[latitudeKey]!.text = result[latitudeKey];
                        controllers[longitudeKey]!.text = result[longitudeKey];
                      });
                    }
                  }
                }
              },
            ),
      onFieldSubmitted: (v) =>
          FocusScope.of(context).requestFocus(focusNodes[additionalAddressKey]),
    );
  }

  String? getFieldError(String fieldKey) {
    if (fieldErrors == null) return null;
    String? apiFieldName = apiField[fieldKey];
    if (apiFieldName == null) return null;
    return fieldErrors![apiFieldName];
  }

  addLocationProcess() {
    if (isDemoApp) {
      Utils.showSnackBar(message: demoModeOnKey);
      return;
    }
    setState(() {
      fieldErrors = null;
    });
    Map<String, String> params = {};
    apiField.forEach((key, value) {
      params[value] = controllers[key]!.text.trim();
    });
    params[ApiURL.storeIdApiKey] = context
        .read<StoresCubit>()
        .getDefaultStore()
        .id
        .toString();
    context.read<AddPickupLocationCubit>().addLocation(context, params);
  }
}
