import 'dart:async';

import 'package:eshopplus_seller/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../blocs/deliveryBoyCubit.dart';
import '../models/deliveryBoy.dart';
import '../../../../utils/designConfig.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import '../../../commons/widgets/customTextContainer.dart';

class DeliveryBoySelectionDialog extends StatefulWidget {

  final Function onCustomerSelect;
  final int? selectedId;

  const DeliveryBoySelectionDialog(
      {super.key,
     
      required this.onCustomerSelect,
      this.selectedId});

  @override
  State<StatefulWidget> createState() {
    return DeliveryBoySelectionDialogState();
  }
}

class DeliveryBoySelectionDialogState
    extends State<DeliveryBoySelectionDialog> {
  final TextEditingController _searchController = TextEditingController();

  final scrollController = ScrollController();
  Map<String, dynamic>? apiParameter;
  @override
  void initState() {
    super.initState();
    apiParameter = {};
    Future.delayed(Duration.zero, () {
        context.read<DeliveryBoyCubit>().getDeliveryBoys(params: {
          ApiURL.storeIdApiKey: context
              .read<StoresCubit>()
              .getDefaultStore()
              .id
              .toString()
        });
    });
  }

  loadPage() {
    Map<String, dynamic> parameter = {
      ApiURL.storeIdApiKey:
          context.read<StoresCubit>().getDefaultStore().id.toString()
    };
    if (apiParameter != null) {
      parameter.addAll(apiParameter!);
    }
    context
        .read<DeliveryBoyCubit>()
        .getDeliveryBoys(params: parameter);
  }

  loadMoreDeliveryBoys() {
    Map<String, dynamic> parameter = {
      ApiURL.storeIdApiKey:
          context.read<StoresCubit>().getDefaultStore().id.toString()
    };
    if (apiParameter != null) {
      parameter.addAll(apiParameter!);
    }
    context.read<DeliveryBoyCubit>().loadMore(params: parameter);
  }

  

 
  searchDeliveryBoy() {
    
      if (_searchController.text.trim().isEmpty) {
        apiParameter!.remove(ApiURL.searchApiKey);
      } else {
        apiParameter![ApiURL.searchApiKey] = _searchController.text;
      }
      loadPage();
    
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.maxFinite,
      height: MediaQuery.of(context).size.height * 0.6, // Set a max height
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
            controller: _searchController,
            decoration: InputDecoration(
                enabledBorder: DesignConfig.setUnderlineInputBorder(greyColor),
                focusedBorder: DesignConfig.setUnderlineInputBorder(greyColor),
                border: DesignConfig.setUnderlineInputBorder(greyColor),
                prefixIcon: Icon(Icons.search, color: greyColor),
                hintText: context
                    .read<SettingsAndLanguagesCubit>()
                    .getTranslatedValue(labelKey: searchKey),
                hintStyle: TextStyle(color: greyColor)),
                onChanged: (value) => searchDeliveryBoy(),
          ),
        ),
        const SizedBox(height: 8),
        Flexible(
          child: BlocBuilder<DeliveryBoyCubit, DeliveryBoyState>(
            builder: (context, state) {
              return dropdownListWidget(state);
            },
          ),
        ),
      ]),
    );
  }

 Widget dropdownListWidget(DeliveryBoyState state) {
    if (state is DeliveryBoyFetchSuccess) {
      return NotificationListener<ScrollUpdateNotification>(
        onNotification: (notification) {
          if (notification.metrics.pixels >=
              notification.metrics.maxScrollExtent) {
            if (context.read<DeliveryBoyCubit>().hasMore()) {
              loadMoreDeliveryBoys();
            }
          }
          return true;
        },
        child: ListView.separated(
          shrinkWrap: true,
          itemCount: state.deliveryBoys.length +
              (context.read<DeliveryBoyCubit>().hasMore() ? 1 : 0),
          padding: const .symmetric(vertical: 8, horizontal: 16),
          separatorBuilder: (context, index) {
            return Divider(
              color: greyColor,
              thickness: 1,
            );
          },
          itemBuilder: (context, index) {
            if (context.read<DeliveryBoyCubit>().hasMore()) {
              if (index == state.deliveryBoys.length) {
                if (context.read<DeliveryBoyCubit>().fetchMoreError()) {
                  return Center(
                    child: TextButton(
                      onPressed: () {
                        loadMoreDeliveryBoys();
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
                    indicatorColor: Theme.of(context).colorScheme.primary,
                  ),
                );
              }
            }

            DeliveryBoy deliveryboy = state.deliveryBoys[index];

            return GestureDetector(
              onTap: () {
                widget.onCustomerSelect(deliveryboy);
              },
              child: Row(
                mainAxisSize: .max,
                crossAxisAlignment: .center,
                children: [
                  Expanded(
                    flex: 1,
                    child: Text(
                      deliveryboy.name ?? '',
                      style: Theme.of(context)
                          .textTheme
                          .titleMedium!
                          .merge(TextStyle(color: blackColor)),
                    ),
                  ),
                  if (widget.selectedId == deliveryboy.id)
                    Icon(Icons.check, color: primaryColor),
                ],
              ),
            );
          },
        ),
      );
    }
    if (state is DeliveryBoyFetchFailure) {
      return ErrorScreen(
        text: state.errorMessage,
        onPressed: loadPage,
      );
     
    }
    return Padding(
      padding: const .all(8.0),
      child: CustomCircularProgressIndicator(
      ),
    );
  }
}
