import 'package:eshopplus_seller/commons/blocs/zoneListCubit.dart';
import 'package:eshopplus_seller/commons/widgets/safeAreaWithBottomPadding.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/core/constants/appConstants.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import 'package:eshopplus_seller/features/profile/addProduct/widgets/helper/shippingSelectionDialog.dart';
import 'package:eshopplus_seller/features/profile/deliverability/blocs/updateProductDeliverabilityCubit.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/bulkUpdatePickupLocationCubit.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/getPickupLocationCubit.dart';
import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/blocs/userDetailsCubit.dart';
import 'package:eshopplus_seller/features/mainScreen.dart';
import 'package:eshopplus_seller/features/profile/addProduct/widgets/helper/zoneSelectionDialog.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customDropDownContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextFieldContainer.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/models/location.dart';

import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:eshopplus_seller/utils/inputValidators.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class DeliveryModal extends StatefulWidget {
  final List<int> selectedProductIds;
  final Map<String, String> deliverableTypes;
  final Map<String, TextEditingController> controllers;
  final bool isComboProductScreen;
  final bool isPickupLocationMode;
  final Function refreshAPI;
  DeliveryModal({
    required this.selectedProductIds,
    required this.deliverableTypes,
    required this.controllers,
    required this.isComboProductScreen,
    this.isPickupLocationMode = false,
    required this.refreshAPI,
  });

  @override
  _DeliveryModalState createState() => _DeliveryModalState();
}

class _DeliveryModalState extends State<DeliveryModal> {
  String? deliverableType;
  Map<String, dynamic> selectedZones = {};
  Map<String, String?> zoneApiParams = {};
  String? selectedPickupLocationId;
  String? selectedPickupLocationName;
  final Map<String, String> apiFormField = {
    productNameKey: "product_id",
    deliverableTypeKey: "deliverable_type",
    selectZonesKey: "deliverable_zones[]",
  };
  final TextEditingController pickupLocationController =
      TextEditingController();
  @override
  void initState() {
    super.initState();
    Future.delayed(Duration.zero, () {
      
      if (!widget.isPickupLocationMode) {
        // Load pickup locations

        zoneApiParams = {
          ApiURL.storeIdApiKey: context
              .read<UserDetailsCubit>()
              .getDefaultStoreOfUser(context)
              .storeId
              .toString(),
          //if the seller has set deliverable type to all then we'll remove seller id
          ApiURL.sellerIdApiKey:
              context
                      .read<UserDetailsCubit>()
                      .getDefaultStoreOfUser(context)
                      .deliverableType ==
                  1
              ? null
              : context
                    .read<UserDetailsCubit>()
                    .getDefaultStoreOfUser(context)
                    .sellerId
                    .toString(),
        };
        if (widget.controllers[deliverableTypeKey]!.text == '2') {
          callZoneApi();
        }
      }
    });
  }

  Future<void> _updateDeliverability() async {
    if (isDemoApp) {
      Utils.showSnackBar(message: demoModeOnKey);
      return;
    }

    if (widget.isPickupLocationMode) {
      // Pickup location mode
      if (selectedPickupLocationId == null) {
        Utils.showSnackBar(message: "Please select a pickup location");
        return;
      }

      Map<String, String> params = {
        'product_ids': widget.selectedProductIds.join(","),
        ApiURL.pickupLocationIdApiKey: selectedPickupLocationId!,
      };

      context.read<BulkUpdatePickupLocationCubit>().bulkUpdatePickupLocation(
        params: params,
        apiUrl: widget.isComboProductScreen
            ? ApiURL.bulkUpdateComboProductPickupLocations
            : ApiURL.bulkUpdateProductPickupLocations,
      );
    } else {
      // Deliverability mode
      Map<String, String> params = {
        ApiURL.storeIdApiKey: context
            .read<UserDetailsCubit>()
            .getDefaultStoreOfUser(context)
            .storeId!
            .toString(),
      };

      params[ApiURL.productIdApiKey] = widget.selectedProductIds.join(",");
      params[apiFormField[deliverableTypeKey]!] =
          widget.controllers[deliverableTypeKey]!.text;
        
      if ((selectedZones[selectZonesKey] ?? {}).isNotEmpty) {
        params[apiFormField[selectZonesKey]!] = (selectedZones[selectZonesKey])
            .keys
            .join(",");
      }

      context
          .read<UpdateProductDeliverabilityCubit>()
          .updateProductDeliverability(
            params: params,
            apiUrl: widget.isComboProductScreen
                ? ApiURL.updateComboProductDeliverability
                : ApiURL.updateProductDeliverability,
          );
    }
  }

  @override
  Widget build(BuildContext context) {
    return SafeAreaWithBottomPadding(
      child: Padding(
        padding: const .all(16.0),
        child: Column(
          mainAxisSize: .min,
          children: [
            if (widget.isPickupLocationMode)
              CustomTextFieldContainer(
                hintTextKey: selectLocationKey,
                textEditingController: pickupLocationController,
                labelKey: forStandardShippingKey,
                textInputAction: TextInputAction.next,
                focusNode: AlwaysDisabledFocusNode(),
                isSetValidator: false,
                isFieldValueMandatory: false,
                suffixWidget: const Icon(Icons.arrow_drop_down),
                errmsg: forStandardShippingKey,

                onTap: () {
                  showDialog(
                    context: context,
                    barrierDismissible: false,
                    builder: (BuildContext mcontext) {
                      return AlertDialog(
                        insetPadding: const .all(appContentHorizontalPadding),
                        backgroundColor: whiteColor,
                        contentPadding: const .symmetric(
                          horizontal: 8,
                          vertical: 5,
                        ),
                        shape: DesignConfig.setRoundedBorder(
                          whiteColor,
                          10,
                          false,
                        ),
                        content: ShippingSelectionDialog(
                          locationListCubit: context
                              .read<GetPickupLocationCubit>(),
                          selectedPickupLocation: selectedPickupLocationId,
                          onShippingSelect: (Location location) {
                            selectedPickupLocationId = location.id.toString();

                            pickupLocationController.text =
                                location.pickupLocation!;
                            Navigator.pop(context);
                          },
                        ),
                      );
                    },
                  );
                },
              )
            else ...[
              CustomDropDownContainer(
                labelKey: deliverableTypeKey,
                dropDownDisplayLabels: widget.deliverableTypes.values.toList(),
                selectedValue: widget.controllers[deliverableTypeKey]!.text,
                onChanged: (value) {
                  setState(() {
                    widget.controllers[deliverableTypeKey]!.text = value
                        .toString();
                  });
                  if (value == '2') {
                    callZoneApi();
                  }
                },
                values: widget.deliverableTypes.keys.toList(),
              ),
            ],
            if (!widget.isPickupLocationMode &&
                isSelectZipCode(widget.controllers[deliverableTypeKey]!.text))
              BlocBuilder<ZoneListCubit, ZoneListState>(
                builder: (context, state) {
                  if (state is ZoneListFetchSuccess) {
                    if (!selectedZones.containsKey(selectZonesKey)) {
                      selectedZones[selectZonesKey] = {};
                    }
                    return CustomTextFieldContainer(
                      hintTextKey: selectZonesKey,
                      textEditingController:
                          widget.controllers[selectZonesKey]!,
                      labelKey: selectZonesKey,
                      textInputAction: TextInputAction.next,
                      isFieldValueMandatory: false,
                      focusNode: AlwaysDisabledFocusNode(),
                      isSetValidator: true,
                      errmsg: selectZonesKey,
                      maxLines: 3,
                      minLines: 1,
                      suffixWidget: const Icon(Icons.arrow_drop_down),
                      onTap: () {
                        if (state.zones.isEmpty) return;
                        showDialog(
                          context: context,
                          barrierDismissible: false,
                          builder: (BuildContext mcontext) {
                            return AlertDialog(
                              insetPadding: const .all(
                                appContentHorizontalPadding,
                              ),
                              backgroundColor: whiteColor,
                              contentPadding: const .symmetric(
                                horizontal: 8,
                                vertical: 5,
                              ),
                              shape: DesignConfig.setRoundedBorder(
                                whiteColor,
                                10,
                                false,
                              ),
                              content: ZoneSelectionDialog(
                                params: zoneApiParams,
                                selectedId: Map<String, String>.from(
                                  selectedZones[selectZonesKey],
                                ),
                                onZoneSelect: (Map<String, String> ids) {
                                 
                                  selectedZones[selectZonesKey].clear();
                                  selectedZones[selectZonesKey].addAll(ids);
                                  setState(() {
                                    widget.controllers[selectZonesKey]!.text =
                                        ids.values.join(", ");
                                  });
                                  Navigator.pop(context);
                                },
                                zoneListCubit: context.read<ZoneListCubit>(),
                              ),
                            );
                          },
                        );
                      },
                    );
                  }
                  if (state is ZoneListFetchFailure) {
                    return ErrorScreen(
                      text: state.errorMessage,
                      child: state is ZoneListFetchInProgress
                          ? CustomCircularProgressIndicator()
                          : null,
                      onPressed: () {
                        callZoneApi();
                      },
                    );
                  }
                  if (state is ZoneListFetchInProgress) {
                    return CustomCircularProgressIndicator();
                  } else {
                    return const SizedBox.shrink();
                  }
                },
              ),
            SizedBox(height: 16),
            buildUpdateButon(),
          ],
        ),
      ),
    );
  }

  buildUpdateButon() {
    if (widget.isPickupLocationMode) {
      return BlocConsumer<
        BulkUpdatePickupLocationCubit,
        BulkUpdatePickupLocationState
      >(
        listener: (context, state) {
          if (state is BulkUpdatePickupLocationSuccess) {
            Navigator.of(context).pop();
            Utils.showSnackBar(message: state.successMessage);
            widget.refreshAPI();
            //below API calls will refresh the product list in main screen after updating pickup location
            if (regularProductsCubit != null &&
                widget.isComboProductScreen == false) {
              regularProductsCubit!.getProducts(
                storeId: context.read<StoresCubit>().getDefaultStore().id!,
                showOnlyActiveProducts: 1,
                isComboProduct: false,
              );
            }
            if (comboProductsCubit != null &&
                widget.isComboProductScreen == true) {
              comboProductsCubit!.getProducts(
                storeId: context.read<StoresCubit>().getDefaultStore().id!,
                showOnlyActiveProducts: 1,
                isComboProduct: true,
              );
            }
          }
          if (state is BulkUpdatePickupLocationFailure) {
            Navigator.of(context).pop();
            Utils.showSnackBar(message: state.errorMessage);
          }
        },
        builder: (context, state) {
          return CustomRoundedButton(
            widthPercentage: 0.5,
            buttonTitle: submitKey,
            showBorder: false,
            onTap: _updateDeliverability,
            child: state is BulkUpdatePickupLocationProgress
                ? CustomCircularProgressIndicator(
                    indicatorColor: Theme.of(context).colorScheme.onPrimary,
                  )
                : null,
          );
        },
      );
    } else {
      return BlocConsumer<
        UpdateProductDeliverabilityCubit,
        UpdateProductDeliverabilityState
      >(
        listener: (context, state) {
          if (state is UpdateProductDeliverabilitySuccess) {
            Navigator.of(context).pop();
            Utils.showSnackBar(message: state.successMessage);
            widget.refreshAPI();
            //below API calls will refresh the product list in main screen after updating deliverability
            if (regularProductsCubit != null &&
                widget.isComboProductScreen == false) {
              regularProductsCubit!.getProducts(
                storeId: context.read<StoresCubit>().getDefaultStore().id!,
                showOnlyActiveProducts: 1,
                isComboProduct: false,
              );
            }
            if (comboProductsCubit != null &&
                widget.isComboProductScreen == true) {
              comboProductsCubit!.getProducts(
                storeId: context.read<StoresCubit>().getDefaultStore().id!,
                showOnlyActiveProducts: 1,
                isComboProduct: true,
              );
            }
          }
          if (state is UpdateProductDeliverabilityFailure) {
            Navigator.of(context).pop();
            Utils.showSnackBar(message: state.errorMessage);
          }
        },
        builder: (context, state) {
          return CustomRoundedButton(
            widthPercentage: 0.5,
            buttonTitle: submitKey,
            showBorder: false,
            onTap: _updateDeliverability,
            child: state is UpdateProductDeliverabilityProgress
                ? CustomCircularProgressIndicator(
                    indicatorColor: Theme.of(context).colorScheme.onPrimary,
                  )
                : null,
          );
        },
      );
    }
  }

  void callZoneApi() {
    context.read<ZoneListCubit>().getZoneList(zoneApiParams);
  }
}
