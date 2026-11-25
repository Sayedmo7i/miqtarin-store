import 'package:eshop_plus/commons/blocs/deliveryLocationCubit.dart';
import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/profile/address/blocs/addNewAddressCubit.dart';
import 'package:eshop_plus/ui/profile/address/blocs/cityCubit.dart';
import 'package:eshop_plus/ui/profile/address/models/address.dart';
import 'package:eshop_plus/ui/profile/address/models/zipcode.dart';
import 'package:eshop_plus/commons/widgets/customBottomButtonContainer.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customRoundedButton.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';

import '../../../../core/routes/routes.dart';
import '../blocs/getAddressCubit.dart';
import '../blocs/zipcodeCubit.dart';
import '../models/city.dart';
import 'package:eshop_plus/core/api/apiEndPoints.dart';
import '../../../../utils/validator.dart';
import '../../../../commons/widgets/customAppbar.dart';
import '../../../../commons/widgets/customDefaultContainer.dart';
import '../../../../commons/widgets/customTextFieldContainer.dart';

class AddNewAddressScreen extends StatefulWidget {
  final GetAddressCubit getAddressCubit;
  final bool isEditScreen;
  final Address? address;
  const AddNewAddressScreen({
    Key? key,
    required this.getAddressCubit,
    required this.isEditScreen,
    required this.address,
  }) : super(key: key);
  static Widget getRouteInstance() {
    final arguments = Get.arguments as Map<String, dynamic>;
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => CityCubit()),
        BlocProvider(create: (context) => ZipcodeCubit()),
      ],
      child: AddNewAddressScreen(
        getAddressCubit: arguments['bloc'] as GetAddressCubit,
        isEditScreen: arguments['isEditScreen'] as bool,
        address: arguments['address'] as Address?,
      ),
    );
  }

  @override
  _AddNewAddressScreenState createState() => _AddNewAddressScreenState();
}

class _AddNewAddressScreenState extends State<AddNewAddressScreen> {
  bool _isDefaultAddress = false;
  String _selectedAddressType = 'home';
  bool _setCityFirstTime = true, _setZipcodeFirstTime = true;
  final _formKey = GlobalKey<FormState>();
  Map<String, TextEditingController> controllers = {};
  Map<String, FocusNode> focusNodes = {};
  bool serviceEnabled = false;
  City? _selectedCity;
  Zipcode? _selectedZipcode;
  bool _isLoading = false;
  final List formFields = [
    nameKey,
    mobileNumberKey,
    addressKey,
    stateKey,
    selectCityKey,
    selectPincodeKey,
    areaNameKey,
    latitudeKey,
    longitudeKey,
    searchCityKey,
    searchPincodeKey,
  ];
  @override
  void initState() {
    super.initState();
    formFields.forEach((key) {
      controllers[key] = TextEditingController();
      focusNodes[key] = FocusNode();
    });
    Future.delayed(Duration.zero, () {
      context.read<CityCubit>().getCities();
    });
    setFieldValues();
  }

  Future<void> _fetchAndSetCityZipcode(
    String cityName,
    String postalCode,
  ) async {
    try {
      final cityCubit = context.read<CityCubit>();
      final zipcodeCubit = context.read<ZipcodeCubit>();

      // Fetch cities matching the city name from location
      await cityCubit.getCities(search: cityName);

      // Wait for cities to load
      await Future.delayed(Duration(milliseconds: 500));

      // Get the matching city
      final cities = cityCubit.getCityList();

      if (cities.isNotEmpty) {
        // Find the best matching city
        City? matchedCity = cities.firstWhere(
          (city) => city.name?.toLowerCase() == cityName.toLowerCase(),
          orElse: () => cities.first,
        );
        if (mounted) {
          setState(() {
            _selectedCity = matchedCity;
            controllers[selectCityKey]!.text = matchedCity.name ?? '';
          });
        }

        // Fetch zipcodes for the selected city
        if (matchedCity.id != null) {
          await zipcodeCubit.getZipcodes(cityId: matchedCity.id);

          // If postal code is available, try to find matching zipcode
          if (postalCode.isNotEmpty) {
            await Future.delayed(Duration(milliseconds: 500));
            final zipcodes = zipcodeCubit.getZipcodeList();

            if (zipcodes.isNotEmpty) {
              Zipcode? matchedZipcode = zipcodes.firstWhere(
                (z) => z.zipcode?.toLowerCase() == postalCode.toLowerCase(),
                orElse: () => zipcodes.first,
              );

              if (mounted) {
                setState(() {
                  _selectedZipcode = matchedZipcode;
                  controllers[selectPincodeKey]!.text =
                      matchedZipcode.zipcode ?? '';
                });
              }
            }
          }
        }
      }
    } catch (e) {}
  }

  setFieldValues() {
    if (widget.isEditScreen) {
      controllers[nameKey]!.text = widget.address!.name!;
      controllers[mobileNumberKey]!.text = widget.address!.mobile!;
      controllers[addressKey]!.text = widget.address!.address!;
      controllers[stateKey]!.text = widget.address!.state!;
      controllers[selectCityKey]!.text = widget.address!.city!;
      controllers[areaNameKey]!.text = widget.address!.area!;
      controllers[selectPincodeKey]!.text = widget.address!.pincode!;
      controllers[latitudeKey]!.text = widget.address!.latitude.toString();
      controllers[longitudeKey]!.text = widget.address!.longitude.toString();
      _selectedAddressType = widget.address!.type!;
      _isDefaultAddress = widget.address!.isDefault == 1 ? true : false;
      setState(() {});
    }
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
      appBar: const CustomAppbar(titleKey: myAddressDetailsKey),
      bottomNavigationBar: buildSaveButton(),
      body: MultiBlocListener(
        listeners: [
          BlocListener<AddNewAddressCubit, AddNewAddressState>(
            listener: (context, state) {
              if (state is AddNewAddressFetchSuccess) {
                // If this is an edit and the edited address is currently selected in delivery location cubit, update it
                if (widget.isEditScreen && widget.address != null) {
                  final deliveryLocationCubit = context
                      .read<DeliveryLocationCubit>();
                  final currentSelectedAddress =
                      deliveryLocationCubit.currentSelectedAddress;

                  if (currentSelectedAddress != null &&
                      currentSelectedAddress.id == widget.address!.id) {
                    // Create updated address object with new values
                    final updatedAddress = state.address;

                    // Update the delivery location cubit with the edited address
                    deliveryLocationCubit.selectAddress(updatedAddress);
                  }
                }

                Utils.showSnackBar(
                  message: state.successMessage,
                  context: context,
                );
                Navigator.of(context).pop();
              }
            },
          ),
          BlocListener<CityCubit, CityState>(
            listener: (context, state) {
              if (state is CityFetchSuccess &&
                  widget.isEditScreen &&
                  _setCityFirstTime) {
                setState(() {
                  _selectedCity = state.cities.firstWhereOrNull(
                    (element) => element.id == widget.address!.cityId,
                  );
                  context.read<ZipcodeCubit>().getZipcodes(
                    cityId: widget.address!.cityId,
                  );
                  _setCityFirstTime = false;
                });
              }
            },
          ),
          BlocListener<ZipcodeCubit, ZipcodeState>(
            listener: (context, state) {
              if (state is ZipcodeFetchSuccess &&
                  widget.isEditScreen &&
                  _setZipcodeFirstTime) {
                setState(() {
                  _selectedZipcode = state.zipcodes.firstWhereOrNull(
                    (element) => element.zipcode == widget.address!.pincode,
                  );

                  _setZipcodeFirstTime = false;
                });
              }
            },
          ),
        ],
        child: buildForm(),
      ),
    );
  }

  Padding buildForm() {
    return Padding(
      padding: const EdgeInsetsDirectional.symmetric(
        vertical: 12,
        horizontal: appContentHorizontalPadding,
      ),
      child: CustomDefaultContainer(
        borderRadius: 8,
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: .start,
              children: <Widget>[
                CustomTextFieldContainer(
                  hintTextKey: nameKey,
                  textEditingController: controllers[nameKey]!,
                  focusNode: focusNodes[nameKey],
                  textInputAction: TextInputAction.next,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  onFieldSubmitted: (v) {
                    FocusScope.of(
                      context,
                    ).requestFocus(focusNodes[mobileNumberKey]);
                  },
                ),
                CustomTextFieldContainer(
                  hintTextKey: mobileNumberKey,
                  textEditingController: controllers[mobileNumberKey]!,
                  focusNode: focusNodes[mobileNumberKey],
                  textInputAction: TextInputAction.next,
                  keyboardType: TextInputType.phone,
                  inputFormatters: [
                    FilteringTextInputFormatter.digitsOnly, // Allow only digits
                    LengthLimitingTextInputFormatter(15), // Limit to 15 digits
                  ],
                  validator: (v) => Validator.validatePhoneNumber(v, context),
                  isFieldValueMandatory: true,
                  onFieldSubmitted: (v) {
                    FocusScope.of(context).requestFocus(focusNodes[addressKey]);
                  },
                ),
                CustomTextFieldContainer(
                  hintTextKey: addressKey,
                  textEditingController: controllers[addressKey]!,
                  focusNode: focusNodes[addressKey],
                  textInputAction: TextInputAction.next,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  suffixWidget: _isLoading
                      ? SizedBox(
                          height: 24,
                          width: 24,
                          child: CustomCircularProgressIndicator(
                            indicatorColor: Theme.of(
                              context,
                            ).colorScheme.primary,
                          ),
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
                              serviceEnabled =
                                  await Geolocator.isLocationServiceEnabled();
                              if (!serviceEnabled) {
                                // Location services are not enabled don't continue
                                // accessing the position and request users of the
                                // App to enable the location services.
                                Utils.showSnackBar(
                                  message: locationServiceDisabledKey,
                                  context: context,
                                );
                                await Geolocator.openLocationSettings();
                                // return;
                              }
                              LocationPermission permission;

                              permission = await Geolocator.checkPermission();
                              bool canGetLocation = true;
                              if (permission == LocationPermission.denied) {
                                permission =
                                    await Geolocator.requestPermission();
                                if (permission == LocationPermission.denied) {
                                  if (widget.isEditScreen) {
                                    if (mounted)
                                      setState(() => _isLoading = false);
                                    return Utils.showSnackBar(
                                      message: locationServiceDisabledKey,
                                      context: context,
                                    );
                                  }
                                  canGetLocation = false;
                                }
                              }
                              if (permission ==
                                  LocationPermission.deniedForever) {
                                if (widget.isEditScreen) {
                                  if (mounted)
                                    setState(() => _isLoading = false);
                                  // Permissions are denied forever, handle appropriately.
                                  return Utils.showSnackBar(
                                    message: llocationServicePermanentDeniedKey,
                                    context: context,
                                  );
                                }
                                canGetLocation = false;
                              }

                              double? mapLatitude, mapLongitude;

                              if (canGetLocation) {
                                Position position =
                                    await Geolocator.getCurrentPosition(
                                      locationSettings: LocationSettings(
                                        accuracy: LocationAccuracy.high,
                                      ),
                                    );
                                mapLatitude = position.latitude;
                                mapLongitude = position.longitude;
                              }
                              if (widget.isEditScreen) {
                                if (widget.address!.latitude != null &&
                                    widget.address!.latitude != 0.0 &&
                                    widget.address!.longitude != null &&
                                    widget.address!.longitude != 0.0) {
                                  mapLatitude = widget.address!.latitude;
                                  mapLongitude = widget.address!.longitude;
                                } else {
                                  // Build lat/lng from city and state

                                  List<Location>
                                  locations = await locationFromAddress(
                                    "${widget.address!.city}, ${widget.address!.state}",
                                  );
                                  if (locations.isNotEmpty) {
                                    mapLatitude = locations.first.latitude;
                                    mapLongitude = locations.first.longitude;
                                  }
                                }
                              }

                              // If canGetLocation is false (add screen, permission denied),
                              // mapLatitude/mapLongitude remain null → map opens with no pre-selection.

                              var result = await Utils.navigateToScreen(
                                context,
                                Routes.mapScreen,
                                arguments: {
                                  latitudeKey: mapLatitude,
                                  longitudeKey: mapLongitude,
                                },
                              );
                              if (mounted)
                                setState(() {
                                  _isLoading = false;
                                });

                              if (result != null) {
                                List<Placemark> placemark =
                                    await placemarkFromCoordinates(
                                      double.parse(result[latitudeKey]),
                                      double.parse(result[longitudeKey]),
                                    );

                                final Placemark place = placemark.first;

                                final addressParts = [
                                  place.name,
                                  if (place.street != place.name) place.street,
                                  place.subLocality,
                                  place.locality,
                                  place.country,
                                  place.postalCode,
                                ];

                                // Join all non-null and non-empty parts with commas
                                final address = addressParts
                                    .where(
                                      (part) =>
                                          part != null &&
                                          part.trim().isNotEmpty,
                                    )
                                    .join(', ');

                                if (mounted) {
                                  setState(() {
                                    controllers[addressKey]!.text = address;
                                    controllers[stateKey]!.text =
                                        placemark[0].administrativeArea ?? '';
                                    controllers[latitudeKey]!.text =
                                        result[latitudeKey];
                                    controllers[longitudeKey]!.text =
                                        result[longitudeKey];
                                  });
                                  // Auto-fill city and zipcode from location
                                  final cityName = placemark[0].locality ?? '';
                                  final postalCode =
                                      placemark[0].postalCode ?? '';
                                  if (context
                                      .read<SettingsAndLanguagesCubit>()
                                      .isStandardShippingEnabled()) {
                                    controllers[selectPincodeKey]!.text =
                                        postalCode;
                                    controllers[selectCityKey]!.text = cityName;
                                    _selectedCity = null;
                                    _selectedZipcode = null;
                                  } else {
                                    if (cityName.isNotEmpty) {
                                      _fetchAndSetCityZipcode(
                                        cityName,
                                        postalCode,
                                      );
                                    }
                                  }
                                }
                              }
                            }
                          },
                        ),
                  onFieldSubmitted: (v) {
                    FocusScope.of(context).requestFocus(focusNodes[stateKey]);
                  },
                ),
                CustomTextFieldContainer(
                  hintTextKey: stateKey,
                  textEditingController: controllers[stateKey]!,
                  focusNode: focusNodes[stateKey],
                  textInputAction: TextInputAction.next,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  onFieldSubmitted: (v) {
                    FocusScope.of(
                      context,
                    ).requestFocus(focusNodes[selectCityKey]);
                  },
                ),
                CustomTextFieldContainer(
                  readOnly: true,
                  hintTextKey: selectCityKey,
                  textEditingController: controllers[selectCityKey]!,
                  focusNode: focusNodes[selectCityKey],
                  textInputAction: TextInputAction.next,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  onFieldSubmitted: (v) {
                    FocusScope.of(
                      context,
                    ).requestFocus(focusNodes[areaNameKey]);
                  },
                  onTap: () => selectCityBottomsheet(
                    context,
                    context.read<CityCubit>(),
                    context.read<ZipcodeCubit>(),
                  ),
                ),
                // Show manual entry toggle if standard shipping is enabled
                CustomTextFieldContainer(
                  hintTextKey: areaNameKey,
                  textEditingController: controllers[areaNameKey]!,
                  focusNode: focusNodes[areaNameKey],
                  textInputAction: TextInputAction.next,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  onFieldSubmitted: (v) {
                    FocusScope.of(
                      context,
                    ).requestFocus(focusNodes[selectPincodeKey]);
                  },
                ),
                CustomTextFieldContainer(
                  readOnly: true,
                  hintTextKey: selectPincodeKey,
                  textEditingController: controllers[selectPincodeKey]!,
                  focusNode: focusNodes[selectPincodeKey],
                  textInputAction: TextInputAction.done,
                  isFieldValueMandatory: true,
                  validator: (v) => Validator.emptyValueValidation(context, v),
                  onFieldSubmitted: (v) {
                    focusNodes[selectPincodeKey]!.unfocus();
                  },
                  onTap: () {
                    if (!context
                            .read<SettingsAndLanguagesCubit>()
                            .isStandardShippingEnabled() &&
                        _selectedCity == null) {
                      Utils.showSnackBar(
                        message: pleaseSelectCityKey,
                        context: context,
                      );
                      return;
                    }
                    selectZipcodeBottomsheet(
                      context,
                      context.read<ZipcodeCubit>(),
                    );
                  },
                ),
                DesignConfig.smallHeightSizedBox,
                CustomTextContainer(
                  textKey: saveThisAddressAsKey,
                  style: Theme.of(context).textTheme.titleSmall,
                ),
                Row(
                  mainAxisSize: MainAxisSize.max,
                  children: <Widget>[
                    buildRadioTile(homeKey, 'home'),
                    buildRadioTile(officeKey, 'office'),
                    buildRadioTile(otherKey, 'others'),
                  ],
                ),
                DesignConfig.smallHeightSizedBox,
                GestureDetector(
                  onTap: () {
                    setState(() {
                      _isDefaultAddress = !_isDefaultAddress;
                    });
                  },
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Checkbox(
                        visualDensity: const VisualDensity(
                          vertical: -4,
                          horizontal: -4,
                        ),
                        value: _isDefaultAddress,
                        onChanged: (value) {
                          setState(() {
                            _isDefaultAddress = value!;
                          });
                        },
                      ),
                      CustomTextContainer(
                        textKey: saveAddressAsDefaultKey,
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  buildRadioTile(String title, String value) {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            _selectedAddressType = value;
          });
        },
        child: Row(
          children: <Widget>[
            RadioGroup<String?>(
              groupValue: _selectedAddressType,
              onChanged: (value) {
                setState(() {
                  _selectedAddressType = value!;
                });
              },
              child: Column(children: <Widget>[Radio<String?>(value: value)]),
            ),
            CustomTextContainer(
              textKey: title,
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ],
        ),
      ),
    );
  }

  buildSaveButton() {
    return BlocBuilder<AddNewAddressCubit, AddNewAddressState>(
      builder: (context, state) {
        return CustomBottomButtonContainer(
          child: CustomRoundedButton(
            widthPercentage: 1.0,
            buttonTitle: saveAddressKey,
            showBorder: false,
            child: state is AddNewAddressFetchInProgress
                ? const CustomCircularProgressIndicator()
                : null,
            onTap: () {
              if (_formKey.currentState!.validate()) {
                if (state is AddNewAddressFetchInProgress) {
                  return;
                }

                FocusScope.of(context).unfocus();
                Map<String, dynamic> params = {
                  ApiURL.nameApiKey: controllers[nameKey]!.text.trim(),
                  ApiURL.mobileApiKey: controllers[mobileNumberKey]!.text
                      .trim(),
                  ApiURL.addressApiKey: controllers[addressKey]!.text.trim(),
                  ApiURL.stateApiKey: controllers[stateKey]!.text.trim(),
                  ApiURL.cityNameApiKey: _selectedCity != null
                      ? _selectedCity?.name
                      : controllers[selectCityKey]!.text,
                  ApiURL.cityIdApiKey: _selectedCity?.id,
                  ApiURL.areaNameApiKey: controllers[areaNameKey]!.text.trim(),
                  ApiURL.typeApiKey: _selectedAddressType,
                  ApiURL.isDefaultAddressApiKey: _isDefaultAddress ? 1 : 0,
                  ApiURL.latitudeApiKey: controllers[latitudeKey]!.text.trim(),
                  ApiURL.longitudeApiKey: controllers[longitudeKey]!.text
                      .trim(),
                };
                if (_selectedZipcode != null) {
                  params.addAll({
                    ApiURL.pincodeApiKey: _selectedZipcode?.zipcode,
                  });
                } else {
                  params.addAll({
                    ApiURL.pincodeNameApiKey: controllers[selectPincodeKey]!
                        .text
                        .trim(),
                  });
                }
                if (widget.isEditScreen) {
                  params.addAll({ApiURL.idApiKey: widget.address!.id});
                }
                if (controllers[latitudeKey]!.text.trim() != '') {
                  params.addAll({
                    ApiURL.latitudeApiKey: controllers[latitudeKey]!.text
                        .trim(),
                  });
                }
                if (controllers[longitudeKey]!.text.trim() != '') {
                  params.addAll({
                    ApiURL.longitudeApiKey: controllers[longitudeKey]!.text
                        .trim(),
                  });
                }
                context.read<AddNewAddressCubit>().addAddress(params: params);
              }
            },
          ),
        );
      },
    );
  }

  selectCityBottomsheet(
    BuildContext buildContext,
    CityCubit cityCubit,
    ZipcodeCubit zipcodeCubit,
  ) {
    ScrollController scrollController = ScrollController();
    final bool isStandardShippingEnabled = buildContext
        .read<SettingsAndLanguagesCubit>()
        .isStandardShippingEnabled();

    return Utils.openModalBottomSheet(
      buildContext,
      StatefulBuilder(
        builder: (context, StateSetter setState) {
          return BlocConsumer<CityCubit, CityState>(
            bloc: cityCubit,
            listener: (context, state) {},
            builder: (context, state) {
              // Attach scroll listener for pagination
              if (scrollController.hasListeners == false) {
                scrollController.addListener(() {
                  if (scrollController.position.pixels >=
                      scrollController.position.maxScrollExtent - 200) {
                    // call load more when reaching near bottom
                    if (state is CityFetchSuccess &&
                        !state.fetchMoreInProgress) {
                      cityCubit.loadMore();
                    }
                  }
                });
              }

              final String searchText = controllers[searchCityKey]!.text.trim();
              final bool showManualOption =
                  isStandardShippingEnabled && searchText.isNotEmpty;

              return Container(
                height: MediaQuery.of(buildContext).size.height,
                padding: const EdgeInsetsDirectional.symmetric(
                  horizontal: appContentHorizontalPadding,
                ),
                child: Column(
                  children: [
                    CustomTextFieldContainer(
                      hintTextKey: searchCityKey,
                      textEditingController: controllers[searchCityKey]!,
                      onChanged: (value) {
                        cityCubit.getCities(
                          search: controllers[searchCityKey]!.text.trim(),
                        );
                        setState(() {}); // Rebuild to show/hide manual option
                      },
                      suffixWidget: Padding(
                        padding: const EdgeInsetsDirectional.only(end: 10),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            GestureDetector(
                              onTap: () => cityCubit.getCities(
                                search: controllers[searchCityKey]!.text.trim(),
                              ),
                              child: const Icon(Icons.search_outlined),
                            ),
                            if (searchText.isNotEmpty)
                              GestureDetector(
                                onTap: () {
                                  setState(() {
                                    controllers[searchCityKey]!.clear();
                                    cityCubit.getCities(search: '');
                                  });
                                },
                                child: const Icon(Icons.close),
                              ),
                          ],
                        ),
                      ),
                    ),
                    // Hint text for manual entry
                    if (isStandardShippingEnabled)
                      Padding(
                        padding: const EdgeInsetsDirectional.only(
                          start: 4,
                          top: 4,
                          bottom: 8,
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.info_outline,
                              size: 14,
                              color: Theme.of(
                                context,
                              ).colorScheme.primary.withValues(alpha: 0.7),
                            ),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                "Can't find your city? Type it and tap 'Use' below",
                                style: Theme.of(context).textTheme.bodySmall
                                    ?.copyWith(
                                      color: Theme.of(context)
                                          .colorScheme
                                          .primary
                                          .withValues(alpha: 0.7),
                                      fontStyle: FontStyle.italic,
                                    ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    state is CityFetchSuccess
                        ? Expanded(
                            child: ListView.separated(
                              controller: scrollController,
                              shrinkWrap: true,
                              physics: const AlwaysScrollableScrollPhysics(),
                              itemCount: _getCityListCount(
                                state.cities.length,
                                cityCubit.hasMore(),
                                showManualOption,
                              ),
                              itemBuilder: (_, index) {
                                // Show "Use [searchText]" option at the top when searching
                                if (showManualOption && index == 0) {
                                  return _buildManualCityOption(
                                    context,
                                    searchText,
                                    zipcodeCubit,
                                    setState,
                                  );
                                }

                                final cityIndex = showManualOption
                                    ? index - 1
                                    : index;

                                if (cityIndex < state.cities.length) {
                                  City city = state.cities[cityIndex];
                                  return buildTile(
                                    _selectedCity != null
                                        ? _selectedCity!.id == city.id
                                        : false,
                                    city.name!,
                                    () {
                                      setState(() {
                                        _selectedCity = city;
                                        controllers[selectCityKey]!.text =
                                            city.name!;
                                        controllers[selectPincodeKey]!.clear();
                                        _selectedZipcode = null;
                                        zipcodeCubit.getZipcodes(
                                          cityId: city.id,
                                        );
                                        Navigator.of(context).pop();
                                      });
                                    },
                                  );
                                } else {
                                  // Loader at the bottom while loading more
                                  return Padding(
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 10,
                                    ),
                                    child: Center(
                                      child: CustomCircularProgressIndicator(
                                        indicatorColor: Theme.of(
                                          context,
                                        ).colorScheme.primary,
                                      ),
                                    ),
                                  );
                                }
                              },
                              separatorBuilder:
                                  (BuildContext context, int index) {
                                    return const SizedBox(height: 2);
                                  },
                            ),
                          )
                        : state is CityFetchFailure
                        ? Expanded(
                            child: Column(
                              children: [
                                if (showManualOption)
                                  _buildManualCityOption(
                                    context,
                                    searchText,
                                    zipcodeCubit,
                                    setState,
                                  ),
                                const Expanded(
                                  child: Center(
                                    child: CustomTextContainer(
                                      textKey: dataNotAvailableKey,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          )
                        : CustomCircularProgressIndicator(
                            indicatorColor: Theme.of(
                              context,
                            ).colorScheme.primary,
                          ),
                  ],
                ),
              );
            },
          );
        },
      ),
      staticContent: false,
      isScrollControlled: true,
    ).then((value) {});
  }

  int _getCityListCount(int citiesLength, bool hasMore, bool showManualOption) {
    int count = citiesLength;
    if (hasMore) count += 1;
    if (showManualOption) count += 1;
    return count;
  }

  Widget _buildManualCityOption(
    BuildContext context,
    String searchText,
    ZipcodeCubit zipcodeCubit,
    StateSetter setState,
  ) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.3),
        ),
      ),
      child: ListTile(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        leading: Icon(
          Icons.add_circle_outline,
          color: Theme.of(context).colorScheme.primary,
        ),
        title: RichText(
          text: TextSpan(
            style: Theme.of(context).textTheme.titleMedium,
            children: [
              TextSpan(
                text: "Use '",
                style: TextStyle(
                  color: Theme.of(context).colorScheme.onSurface,
                ),
              ),
              TextSpan(
                text: searchText,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Theme.of(context).colorScheme.primary,
                ),
              ),
              TextSpan(
                text: "' as city",
                style: TextStyle(
                  color: Theme.of(context).colorScheme.onSurface,
                ),
              ),
            ],
          ),
        ),
        trailing: Icon(
          Icons.arrow_forward_ios,
          size: 16,
          color: Theme.of(context).colorScheme.primary,
        ),
        onTap: () {
          City manualCity = City(id: null, name: searchText);
          this._selectedCity = null;
          controllers[selectCityKey]!.text = manualCity.name!;
          controllers[selectPincodeKey]!.clear();
          _selectedZipcode = null;
          Navigator.of(context).pop();
          this.setState(() {});
        },
      ),
    );
  }

  selectZipcodeBottomsheet(
    BuildContext buildContext,
    ZipcodeCubit zipcodeCubit,
  ) {
    ScrollController scrollController = ScrollController();
    final bool isStandardShippingEnabled = buildContext
        .read<SettingsAndLanguagesCubit>()
        .isStandardShippingEnabled();

    return Utils.openModalBottomSheet(
      buildContext,
      StatefulBuilder(
        builder: (context, StateSetter setState) {
          return BlocBuilder<ZipcodeCubit, ZipcodeState>(
            bloc: zipcodeCubit,
            builder: (context, state) {
              if (scrollController.hasListeners == false) {
                scrollController.addListener(() {
                  if (scrollController.position.pixels >=
                      scrollController.position.maxScrollExtent - 200) {
                    if (state is ZipcodeFetchSuccess &&
                        !state.fetchMoreInProgress) {
                      zipcodeCubit.loadMore();
                    }
                  }
                });
              }

              final String searchText = controllers[searchPincodeKey]!.text
                  .trim();
              final bool showManualOption =
                  isStandardShippingEnabled && searchText.isNotEmpty;

              return Container(
                padding: const EdgeInsetsDirectional.symmetric(
                  horizontal: appContentHorizontalPadding,
                ),
                child: Column(
                  children: [
                    CustomTextFieldContainer(
                      hintTextKey: searchPincodeKey,
                      textEditingController: controllers[searchPincodeKey]!,
                      onChanged: (value) {
                        zipcodeCubit.getZipcodes(
                          cityId: _selectedCity?.id,
                          search: controllers[searchPincodeKey]!.text.trim(),
                        );
                        setState(() {}); // Rebuild to show/hide manual option
                      },
                      suffixWidget: Padding(
                        padding: const EdgeInsetsDirectional.only(end: 10),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            GestureDetector(
                              onTap: () => zipcodeCubit.getZipcodes(
                                cityId: _selectedCity?.id,
                                search: controllers[searchPincodeKey]!.text
                                    .trim(),
                              ),
                              child: const Icon(Icons.search_outlined),
                            ),
                            if (searchText.isNotEmpty)
                              GestureDetector(
                                onTap: () {
                                  setState(() {
                                    controllers[searchPincodeKey]!.clear();
                                    zipcodeCubit.getZipcodes(
                                      cityId: _selectedCity?.id,
                                      search: '',
                                    );
                                  });
                                },
                                child: const Icon(Icons.close),
                              ),
                          ],
                        ),
                      ),
                    ),
                    // Hint text for manual entry
                    if (isStandardShippingEnabled)
                      Padding(
                        padding: const EdgeInsetsDirectional.only(
                          start: 4,
                          top: 4,
                          bottom: 8,
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.info_outline,
                              size: 14,
                              color: Theme.of(
                                context,
                              ).colorScheme.primary.withValues(alpha: 0.7),
                            ),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                "Can't find your pincode? Type it and tap 'Use' below",
                                style: Theme.of(context).textTheme.bodySmall
                                    ?.copyWith(
                                      color: Theme.of(context)
                                          .colorScheme
                                          .primary
                                          .withValues(alpha: 0.7),
                                      fontStyle: FontStyle.italic,
                                    ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    state is ZipcodeFetchSuccess
                        ? Expanded(
                            child: ListView.separated(
                              controller: scrollController,
                              shrinkWrap: true,
                              physics: const AlwaysScrollableScrollPhysics(),
                              itemCount: _getZipcodeListCount(
                                state.zipcodes.length,
                                zipcodeCubit.hasMore(),
                                showManualOption,
                              ),
                              itemBuilder: (_, index) {
                                // Show "Use [searchText]" option at the top when searching
                                if (showManualOption && index == 0) {
                                  return _buildManualZipcodeOption(
                                    context,
                                    searchText,
                                    setState,
                                  );
                                }

                                final zipcodeIndex = showManualOption
                                    ? index - 1
                                    : index;

                                if (zipcodeIndex < state.zipcodes.length) {
                                  Zipcode zipcode =
                                      state.zipcodes[zipcodeIndex];
                                  return buildTile(
                                    _selectedZipcode != null
                                        ? _selectedZipcode!.id == zipcode.id
                                        : false,
                                    zipcode.zipcode!,
                                    () {
                                      setState(() {
                                        _selectedZipcode = zipcode;
                                        controllers[selectPincodeKey]!.text =
                                            zipcode.zipcode!;
                                        Navigator.of(context).pop();
                                      });
                                    },
                                  );
                                } else {
                                  // loader for pagination
                                  return Padding(
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 10,
                                    ),
                                    child: Center(
                                      child: CustomCircularProgressIndicator(
                                        indicatorColor: Theme.of(
                                          context,
                                        ).colorScheme.primary,
                                      ),
                                    ),
                                  );
                                }
                              },
                              separatorBuilder: (context, index) =>
                                  const SizedBox(height: 2),
                            ),
                          )
                        : state is ZipcodeFetchFailure
                        ? Expanded(
                            child: Column(
                              children: [
                                if (showManualOption)
                                  _buildManualZipcodeOption(
                                    context,
                                    searchText,
                                    setState,
                                  ),
                                const Expanded(
                                  child: Center(
                                    child: CustomTextContainer(
                                      textKey: dataNotAvailableKey,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          )
                        : state is ZipcodeFetchInProgress
                        ? CustomCircularProgressIndicator(
                            indicatorColor: Theme.of(
                              context,
                            ).colorScheme.primary,
                          )
                        : SizedBox.shrink(),
                  ],
                ),
              );
            },
          );
        },
      ),
      staticContent: false,
      isScrollControlled: true,
    );
  }

  int _getZipcodeListCount(
    int zipcodesLength,
    bool hasMore,
    bool showManualOption,
  ) {
    int count = zipcodesLength;
    if (hasMore) count += 1;
    if (showManualOption) count += 1;
    return count;
  }

  Widget _buildManualZipcodeOption(
    BuildContext context,
    String searchText,
    StateSetter setState,
  ) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.3),
        ),
      ),
      child: ListTile(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        leading: Icon(
          Icons.add_circle_outline,
          color: Theme.of(context).colorScheme.primary,
        ),
        title: RichText(
          text: TextSpan(
            style: Theme.of(context).textTheme.titleMedium,
            children: [
              TextSpan(
                text: "Use '",
                style: TextStyle(
                  color: Theme.of(context).colorScheme.onSurface,
                ),
              ),
              TextSpan(
                text: searchText,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Theme.of(context).colorScheme.primary,
                ),
              ),
              TextSpan(
                text: "' as pincode",
                style: TextStyle(
                  color: Theme.of(context).colorScheme.onSurface,
                ),
              ),
            ],
          ),
        ),
        trailing: Icon(
          Icons.arrow_forward_ios,
          size: 16,
          color: Theme.of(context).colorScheme.primary,
        ),
        onTap: () {
          Zipcode manualZipcode = Zipcode(id: null, zipcode: searchText);
          this._selectedZipcode = manualZipcode;
          controllers[selectPincodeKey]!.text = manualZipcode.zipcode!;
          Navigator.of(context).pop();
          this.setState(() {});
        },
      ),
    );
  }

  buildTile(bool isSelected, String title, VoidCallback onTap) {
    return ListTile(
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.all(Radius.circular(15.0)),
      ),
      selectedColor: Theme.of(
        context,
      ).colorScheme.primary.withValues(alpha: 0.1),
      title: CustomTextContainer(
        textKey: title,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
          color: isSelected
              ? Theme.of(context).colorScheme.primary
              : blackColor,
          fontWeight: isSelected ? FontWeight.w500 : FontWeight.w400,
          letterSpacing: 0.5,
        ),
      ),
      tileColor: isSelected
          ? Theme.of(context).colorScheme.primary.withValues(alpha: 0.1)
          : transparentColor,
      onTap: onTap,
      trailing: isSelected
          ? Icon(Icons.check, color: Theme.of(context).colorScheme.primary)
          : null,
    );
  }
}
