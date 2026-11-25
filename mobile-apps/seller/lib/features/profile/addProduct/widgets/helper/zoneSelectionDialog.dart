import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../../commons/blocs/zoneListCubit.dart';
import '../../../../../commons/blocs/settingsAndLanguagesCubit.dart';
import '../../../../../commons/models/zone.dart';
import '../../../../../commons/widgets/customCircularProgressIndicator.dart';
import '../../../../../../utils/designConfig.dart';
import '../../../../../core/localization/labelKeys.dart';
import '../../../../../utils/utils.dart';
import '../../../../../core/theme/colors.dart';
import '../../../../../commons/widgets/customTextContainer.dart';

class ZoneSelectionDialog extends StatefulWidget {
  final Function onZoneSelect;
  final Map<String, String> selectedId;
  final Map<String, String?>? params;
  final ZoneListCubit zoneListCubit;

  const ZoneSelectionDialog({
    super.key,
    required this.zoneListCubit,
    this.params,
    required this.onZoneSelect,
    required this.selectedId,
  });

  @override
  ZoneSelectionDialogState createState() => ZoneSelectionDialogState();
}

class ZoneSelectionDialogState extends State<ZoneSelectionDialog> {
  final TextEditingController _searchController = TextEditingController();
  final scrollController = ScrollController();
  Map<String, String?>? apiParameter;
  Map<String, String> selectedIdList = {};

  @override
  void initState() {
    super.initState();
    apiParameter = widget.params ?? {};
    if (widget.selectedId.isNotEmpty) selectedIdList.addAll(widget.selectedId);

    Future.delayed(Duration.zero, () {
      loadPage();
    });
  }

  loadPage() {
    Map<String, String?> parameter = {};
    if (apiParameter != null) {
      parameter.addAll(apiParameter!);
    }
    widget.zoneListCubit.getZoneList(parameter);
  }

  loadMoreZones() {
    Map<String, String?> parameter = {};
    if (apiParameter != null) {
      parameter.addAll(apiParameter!);
    }
    widget.zoneListCubit.loadMore(params: parameter);
  }

  searchZone() {
    if (_searchController.text.trim().isEmpty) {
      apiParameter!.remove("search");
    } else {
      apiParameter!["search"] = _searchController.text;
    }
    loadPage();
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.maxFinite,
      height: MediaQuery.of(context).size.height * 0.6,
      child: Column(
        mainAxisSize: .min,
        children: [
          Align(
            alignment: AlignmentDirectional.topEnd,
            child: IconButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              icon: const Icon(Icons.cancel_outlined),
            ),
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
                hintStyle: TextStyle(color: greyColor),
              ),
              onChanged: (value) => searchZone(),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: BlocBuilder<ZoneListCubit, ZoneListState>(
              bloc: widget.zoneListCubit,
              builder: (context, state) {
                return dropdownListWidget(state);
              },
            ),
          ),
          const SizedBox(height: 5),
          Row(
            mainAxisAlignment: .spaceAround,
            children: [
              TextButton(
                onPressed: () {
                  Navigator.of(context).pop();
                },
                child: const CustomTextContainer(textKey: cancelKey),
              ),
              TextButton(
                onPressed: () {
                  widget.onZoneSelect(selectedIdList);
                },
                child: const CustomTextContainer(textKey: applyKey),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget dropdownListWidget(ZoneListState state) {
    if (state is ZoneListFetchSuccess) {
      return NotificationListener<ScrollUpdateNotification>(
        onNotification: (notification) {
          if (notification.metrics.pixels >=
              notification.metrics.maxScrollExtent) {
            if (widget.zoneListCubit.hasMore()) {
              loadMoreZones();
            }
          }
          return true;
        },
        child: ListView.separated(
          shrinkWrap: true,
          itemCount:
              state.zones.length + (widget.zoneListCubit.hasMore() ? 1 : 0),
          padding: const .symmetric(vertical: 8, horizontal: 16),
          separatorBuilder: (context, index) {
            return Divider(color: greyColor, thickness: 1);
          },
          itemBuilder: (context, index) {
            if (widget.zoneListCubit.hasMore()) {
              if (index == state.zones.length) {
                if (widget.zoneListCubit.fetchMoreError()) {
                  return Center(
                    child: TextButton(
                      onPressed: () {
                        loadMoreZones();
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

            Zone zone = state.zones[index];
            String city = zone.zonename ?? "";

            return GestureDetector(
              onTap: () {
                if (selectedIdList.containsKey(zone.id.toString())) {
                  selectedIdList.remove(zone.id.toString());
                } else {
                  selectedIdList[zone.id.toString()] = city;
                }
                setState(() {});
              },
              child: Row(
                mainAxisSize: .max,
                crossAxisAlignment: .start,
                children: [
                  Expanded(
                    flex: 1,
                    child: Column(
                      crossAxisAlignment: .start,
                      children: [
                        Text(
                          city,
                          style: Theme.of(context).textTheme.titleMedium!.merge(
                            TextStyle(color: primaryColor),
                          ),
                        ),
                        if (zone.zipcodeList!.isNotEmpty)
                          zipcodeCityNames(
                            serviceableZipcodesKey,
                            zone.zipcodeList!
                                .map((e) => e.zipcode!)
                                .toList()
                                .join(","),
                          ),
                        if (zone.cityList!.isNotEmpty)
                          zipcodeCityNames(
                            serviceableCitiesKey,
                            zone.cityList!
                                .map((e) => e.cityName!)
                                .toList()
                                .join(","),
                          ),
                      ],
                    ),
                  ),
                  if (selectedIdList.containsKey(zone.id.toString()))
                    Icon(Icons.check, color: primaryColor),
                ],
              ),
            );
          },
        ),
      );
    }
    if (state is ZoneListFetchFailure) {
      return ErrorScreen(text: state.errorMessage, onPressed: loadPage);
    }
    return Padding(
      padding: const .all(8.0),
      child: CustomCircularProgressIndicator(),
    );
  }

  zipcodeCityNames(String lbl, String namelist) {
    return Row(
      children: [
        Text(
          "${Utils.getTranslatedValue(lbl)} : ",
          style: Theme.of(
            context,
          ).textTheme.bodySmall!.copyWith(fontWeight: .bold),
        ),
        Expanded(
          child: Text(
            namelist,
            style: Theme.of(context).textTheme.bodySmall!,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ),
      ],
    );
  }
}
