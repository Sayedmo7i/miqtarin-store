import 'dart:async';
import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customAppbar.dart';
import 'package:eshopplus_seller/commons/widgets/customBottomButtonContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/configs/appConfig.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/deletePickupLocationCubit.dart';
import 'package:eshopplus_seller/features/profile/pickupLocation/blocs/getPickupLocationCubit.dart';
import 'package:eshopplus_seller/utils/designConfig.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../core/routes/routes.dart';

import '../models/location.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/constants/themeConstants.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import '../../../../utils/utils.dart';

import '../../../../commons/widgets/customRoundedButton.dart';

class ManagePickupLocationScreen extends StatefulWidget {
  const ManagePickupLocationScreen({Key? key}) : super(key: key);
  static Widget getRouteInstance() => MultiBlocProvider(
    providers: [
      BlocProvider(create: (context) => GetPickupLocationCubit()),
      BlocProvider(create: (context) => DeletePickupLocationCubit()),
    ],
    child: const ManagePickupLocationScreen(),
  );
  @override
  _ManagePickupLocationScreenState createState() =>
      _ManagePickupLocationScreenState();
}

class _ManagePickupLocationScreenState
    extends State<ManagePickupLocationScreen> {
  Map<String, dynamic>? apiParameter;

  @override
  void initState() {
    super.initState();
    apiParameter = {};
    Future.delayed(Duration.zero, () {
      getPickupLocations();
    });
  }

  getPickupLocations() {
    context.read<GetPickupLocationCubit>().getPickupLocations(
      params: {
        ApiURL.storeIdApiKey: context
            .read<StoresCubit>()
            .getDefaultStore()
            .id
            .toString(),
      },
    );
  }

  loadMoreLocations() {
    Map<String, dynamic> parameter = {
      ApiURL.storeIdApiKey: context
          .read<StoresCubit>()
          .getDefaultStore()
          .id
          .toString(),
    };
    if (apiParameter != null) {
      parameter.addAll(apiParameter!);
    }
    context.read<GetPickupLocationCubit>().loadMore(params: parameter);
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: const CustomAppbar(
        titleKey: pickupLocationKey,
        showBackButton: true,
      ),
      body: BlocBuilder<GetPickupLocationCubit, GetPickupLocationState>(
        builder: (context, state) {
          return listContent(state);
        },
      ),
      bottomNavigationBar:
          BlocBuilder<GetPickupLocationCubit, GetPickupLocationState>(
            builder: (context, state) {
              return CustomBottomButtonContainer(
                child: CustomRoundedButton(
                  widthPercentage: 1,
                  buttonTitle: addPickupLocKey,
                  showBorder: false,
                  onTap: () {
                    Utils.navigateToScreen(
                      context,
                      Routes.addPickupLocScreen,
                      arguments: {
                        "getPickupLocationCubit": context
                            .read<GetPickupLocationCubit>(),
                      },
                    );
                  },
                ),
              );
            },
          ),
    );
  }

  listContent(GetPickupLocationState state) {
    if (state is GetPickupLocationFetchSuccess) {
      return RefreshIndicator(
        onRefresh: () async {
          getPickupLocations();
        },
        child:
            BlocListener<DeletePickupLocationCubit, DeletePickupLocationState>(
              listener: (context, deleteState) {
                if (deleteState is DeletePickupLocationSuccess) {
                  state.locations.removeWhere(
                    (element) =>
                        element.id.toString() == deleteState.pickupLocationId,
                  );
                  context.read<GetPickupLocationCubit>().emisSuccessState(
                    state.locations,
                    total:
                        (context.read<GetPickupLocationCubit>().state
                                as GetPickupLocationFetchSuccess)
                            .total +
                        -1,
                  );
                  Utils.showSnackBar(message: deleteState.successMessage);
                  Navigator.of(context).pop();
                }
                if (deleteState is DeletePickupLocationFailure) {
                  Utils.showSnackBar(message: deleteState.errorMessage);
                }
              },
              child: NotificationListener<ScrollUpdateNotification>(
                onNotification: (notification) {
                  if (notification.metrics.pixels >=
                      notification.metrics.maxScrollExtent) {
                    if (context.read<GetPickupLocationCubit>().hasMore()) {
                      loadMoreLocations();
                    }
                  }
                  return true;
                },
                child: state.locations.isNotEmpty
                    ? ListView.separated(
                        shrinkWrap: true,
                        itemCount:
                            state.locations.length +
                            (context.read<GetPickupLocationCubit>().hasMore()
                                ? 1
                                : 0),
                        padding: .all(appContentHorizontalPadding),
                        separatorBuilder: (context, index) {
                          return const SizedBox(height: 8);
                        },
                        itemBuilder: (context, index) {
                          if (context
                              .read<GetPickupLocationCubit>()
                              .hasMore()) {
                            if (index == state.locations.length) {
                              if (context
                                  .read<GetPickupLocationCubit>()
                                  .fetchMoreError()) {
                                return Center(
                                  child: TextButton(
                                    onPressed: () {
                                      loadMoreLocations();
                                    },
                                    child: CustomTextContainer(
                                      textKey: retryKey,
                                      style: Theme.of(context)
                                          .textTheme
                                          .bodyMedium!
                                          .copyWith(
                                            color: Theme.of(
                                              context,
                                            ).colorScheme.primary,
                                          ),
                                    ),
                                  ),
                                );
                              }

                              return Padding(
                                padding: const .all(8.0),
                                child: CustomCircularProgressIndicator(
                                  indicatorColor: Theme.of(
                                    context,
                                  ).colorScheme.primary,
                                ),
                              );
                            }
                          }

                          Location location = state.locations[index];
                          return locationWidget(location);
                        },
                      )
                    : Center(
                        child: CustomTextContainer(
                          textKey: dataNotAvailableKey,
                        ),
                      ),
              ),
            ),
      );
    }
    if (state is GetPickupLocationFetchFailure) {
      return ErrorScreen(
        text: state.errorMessage,
        onPressed: getPickupLocations,
      );
    }
    return CustomCircularProgressIndicator();
  }

  buildLabel(String title, String value) {
    return Padding(
      padding: EdgeInsetsDirectional.symmetric(
        horizontal: appContentHorizontalPadding,
        vertical: 4,
      ),
      child: Row(
        crossAxisAlignment: .start,
        children: [
          if (title.isNotEmpty) ...[
            CustomTextContainer(
              textKey: title,
              style: Theme.of(context).textTheme.titleMedium,
            ),
            const SizedBox(width: 4),
            CustomTextContainer(
              textKey: ':',
              style: Theme.of(context).textTheme.titleMedium,
            ),
            const SizedBox(width: 4),
          ],
          Expanded(
            child: CustomTextContainer(
              textKey: value,
              style: Theme.of(context).textTheme.bodyLarge!.copyWith(
                color: Theme.of(
                  context,
                ).colorScheme.secondary.withValues(alpha: 0.8),
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }

  locationWidget(Location location) {
    return Container(
      padding: const EdgeInsetsDirectional.symmetric(vertical: 12),
      decoration: ShapeDecoration(
        color: Theme.of(context).colorScheme.primaryContainer,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(borderRadius),
        ),
        shadows: DesignConfig.appShadow,
      ),
      child: Column(
        children: <Widget>[
          Row(
            children: <Widget>[
              Expanded(child: buildLabel('', location.pickupLocation ?? "")),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: location.isVerified
                      ? Colors.green.withValues(alpha: 0.1)
                      : Colors.red.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(borderRadius),
                ),
                child: CustomTextContainer(
                  textKey: location.isVerified ? verifiedKey : unverifiedKey,
                  style: Theme.of(context).textTheme.bodySmall!.copyWith(
                    color: location.isVerified ? Colors.green : Colors.red,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              IconButton(
                visualDensity: VisualDensity(horizontal: -2, vertical: -4),
                icon: Icon(
                  Icons.delete_outline_outlined,
                  color: Theme.of(context).colorScheme.error,
                ),
                onPressed: () {
                  if (isDemoApp) {
                    Utils.showSnackBar(message: demoModeOnKey);
                    return;
                  }
                  _showDeleteConfirmation(
                    context.read<DeletePickupLocationCubit>(),
                    location,
                  );
                },
              ),
            ],
          ),
          const SizedBox(height: 4),
          const Divider(height: 1),
          const SizedBox(height: 4),
          buildLabel(nameKey, location.name ?? ""),
          buildLabel(emailKey, location.email ?? ""),
          buildLabel(phoneNumberKey, location.phone ?? ""),
          buildLabel(cityKey, location.city ?? ""),
          buildLabel(pincodeKey, location.pincode ?? ""),
          buildLabel(addressKey, location.address ?? ""),
          buildLabel(additionalAddressKey, location.address2 ?? ""),
        ],
      ),
    );
  }

  void _showDeleteConfirmation(
    DeletePickupLocationCubit deletePickupLocationCubit,
    Location location,
  ) {
    showDialog(
      context: context,
      barrierDismissible: true,
      builder: (context) => AlertDialog(
        backgroundColor: Theme.of(context).colorScheme.primaryContainer,
        insetPadding: const EdgeInsets.symmetric(horizontal: 16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        title: CustomTextContainer(
          textKey: deletePickupLocationKey,
          overflow: TextOverflow.visible,
          style: Theme.of(context).textTheme.titleMedium,
          textAlign: TextAlign.center,
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            CustomTextContainer(
              textKey: deletePickupLocationConfirmationKey,
              style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                color: Theme.of(
                  context,
                ).colorScheme.secondary.withValues(alpha: 0.46),
              ),
              overflow: TextOverflow.visible,
              textAlign: TextAlign.center,
            ),
            if (location.isVerified) ...[
              const SizedBox(height: 16),
              CustomTextContainer(
                textKey: pickuplocationDeleteMessageKey,
                style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                  color: Colors.red,
                  fontWeight: FontWeight.w600,
                ),
                overflow: TextOverflow.visible,
                textAlign: TextAlign.center,
              ),
            ],
          ],
        ),
        actions: <Widget>[
          Row(
            mainAxisSize: MainAxisSize.min,
            children: <Widget>[
              Expanded(
                child: CustomRoundedButton(
                  widthPercentage: 0.5,
                  buttonTitle: cancelKey,
                  showBorder: true,
                  backgroundColor: Theme.of(context).colorScheme.onPrimary,
                  borderColor: Theme.of(context).hintColor,
                  style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                    color: Theme.of(context).colorScheme.secondary,
                  ),
                  onTap: () {
                    Navigator.of(context).pop();
                  },
                ),
              ),
              DesignConfig.defaultWidthSizedBox,
              Expanded(
                child: CustomRoundedButton(
                  widthPercentage: 0.5,
                  buttonTitle: deleteKey,
                  showBorder: false,
                  backgroundColor: Theme.of(context).colorScheme.error,
                  style: Theme.of(context).textTheme.bodyMedium!.copyWith(
                    color: Theme.of(context).colorScheme.onError,
                  ),
                  onTap: () async {
                    deletePickupLocationCubit.deletePickupLocation(
                      pickupLocationId: location.id.toString(),
                    );
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
