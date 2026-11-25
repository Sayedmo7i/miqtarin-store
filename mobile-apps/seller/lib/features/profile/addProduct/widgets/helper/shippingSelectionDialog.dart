import 'dart:async';
import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../../core/theme/colors.dart';
import '../../../pickupLocation/blocs/getPickupLocationCubit.dart';
import '../../../../../commons/blocs/settingsAndLanguagesCubit.dart';
import '../../../pickupLocation/models/location.dart';
import '../../../../../utils/designConfig.dart';
import '../../../../../core/localization/labelKeys.dart';

class ShippingSelectionDialog extends StatefulWidget {
  final Function onShippingSelect;
  final String? selectedPickupLocation;
  final GetPickupLocationCubit locationListCubit;
  const ShippingSelectionDialog(
      {super.key,
      required this.locationListCubit,
      required this.onShippingSelect,
      this.selectedPickupLocation});

  @override
  State<StatefulWidget> createState() {
    return ShippingSelectionDialogState();
  }
}

class ShippingSelectionDialogState extends State<ShippingSelectionDialog> {
  final TextEditingController _searchQuery = TextEditingController();
  String _searchText = "";
  bool isSearching = false;
  Map<String, dynamic>? apiParameter;

  @override
  void initState() {
    super.initState();
    apiParameter = {};
    isSearching = false;
    Future.delayed(Duration.zero, () {
      loadPage();
    });
  }

  loadPage() {
    Map<String, dynamic> parameter = {
      ApiURL.storeIdApiKey:
          context.read<StoresCubit>().getDefaultStore().id.toString(),
      ApiURL.statusApiKey: '1' // pass 1 to get only verified locations
    };
    if (_searchQuery.text.trim().isNotEmpty) {
      parameter[ApiURL.searchApiKey] = _searchText;
    }
    widget.locationListCubit.getPickupLocations(params: parameter);
  }

  loadMoreLocations() {
    Map<String, dynamic> parameter = {
      ApiURL.storeIdApiKey:
          context.read<StoresCubit>().getDefaultStore().id.toString(),
      ApiURL.statusApiKey: '1'
    };
 if (_searchQuery.text.trim().isNotEmpty) {
      parameter[ApiURL.searchApiKey] = _searchText;
    }
    widget.locationListCubit.loadMore(params: parameter);
  }

  searchLocation() {
    if (_searchQuery.text.trim().isEmpty) {
      setState(() {
        isSearching = false;
        _searchText = "";
      });
    } else {
      setState(() {
        isSearching = true;
        _searchText = _searchQuery.text;
      });
    }

    
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.maxFinite,
      height: MediaQuery.of(context).size.height * 0.8,
      child: Column(mainAxisSize: .min, children: [
        Align(
          alignment: AlignmentDirectional.topEnd,
          child: IconButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              icon: const Icon(Icons.cancel_outlined)),
        ),
        Padding(
          padding: const .symmetric(horizontal: 16.0),
          child: TextField(
            controller: _searchQuery,
            decoration: InputDecoration(
                enabledBorder: DesignConfig.setUnderlineInputBorder(greyColor),
                focusedBorder: DesignConfig.setUnderlineInputBorder(greyColor),
                border: DesignConfig.setUnderlineInputBorder(greyColor),
                prefixIcon: Icon(Icons.search, color: greyColor),
                hintText: context
                    .read<SettingsAndLanguagesCubit>()
                    .getTranslatedValue(labelKey: searchKey),
                hintStyle: TextStyle(color: greyColor)),
            onChanged: (value) {
              searchLocation();
              loadPage();
            },
          ),
        ),
        const SizedBox(height: 8),
        Flexible(
          child: BlocBuilder<GetPickupLocationCubit, GetPickupLocationState>(
            bloc: widget.locationListCubit,
            builder: (context, state) {
              return dropdownListWidget(state);
            },
          ),
        ),
      ]),
    );
  }

  dropdownListWidget(GetPickupLocationState state) {
    if (state is GetPickupLocationFetchSuccess) {
      return NotificationListener<ScrollUpdateNotification>(
        onNotification: (notification) {
          if (notification.metrics.pixels >=
              notification.metrics.maxScrollExtent) {
            if (widget.locationListCubit.hasMore()) {
              loadMoreLocations();
            }
          }
          return true;
        },
        child: ListView.separated(
          shrinkWrap: true,
            itemCount: state.locations.length +
              (widget.locationListCubit.hasMore() ? 1 : 0),
          padding: const .symmetric(vertical: 8, horizontal: 16),
          separatorBuilder: (context, index) {
            return Divider(
              color: greyColor,
              thickness: 1,
            );
          },
          itemBuilder: (context, index) {
            if (widget.locationListCubit.hasMore()) {
              if (index == state.locations.length ) {
                if (widget.locationListCubit.fetchMoreError()) {
                  return Center(
                    child: TextButton(
                      onPressed: () {
                        loadMoreLocations();
                      },
                      child: CustomTextContainer(
                        textKey: retryKey,
                        style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                              color: Theme.of(context).colorScheme.primary,
                            ),
                      ),
                    ),
                  );
                }

                return Padding(
                  padding: const .all(8.0),
                  child: CustomCircularProgressIndicator(
                  ),
                );
              }
            }

            Location location = state.locations[index];

            return GestureDetector(
              onTap: () {
                widget.onShippingSelect(location);
              },
              child: Row(
                mainAxisSize: .max,
                crossAxisAlignment: .center,
                children: [
                  Expanded(
                    flex: 1,
                    child: Text(
                      location.pickupLocation ?? '',
                      style: Theme.of(context)
                          .textTheme
                          .titleMedium!
                          .merge(TextStyle(color: blackColor)),
                    ),
                  ),
                  if (widget.selectedPickupLocation == location.id.toString())
                    Icon(Icons.check, color: primaryColor),
                ],
              ),
            );
          },
        ),
      );
    }
    if (state is GetPickupLocationFetchFailure) {
  return ErrorScreen(
        text: state.errorMessage,
        onPressed: loadPage,
      );
    }
    return Padding(
      padding: const .all(8.0),
      child: CustomCircularProgressIndicator(
        indicatorColor: Theme.of(context).colorScheme.primary,
      ),
    );
  }
}
