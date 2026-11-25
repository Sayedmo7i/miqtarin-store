import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/widgets/customDefaultContainer.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/core/localization/appLocalizations.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/cart/models/deliverabilityResponse.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class ShippingOptionsWidget extends StatefulWidget {
  final ShippingOptions? shippingOptions;
  final String? defaultShippingOption;
  final Function(String selectedOption)? onShippingOptionChanged;

  const ShippingOptionsWidget({
    Key? key,
    required this.shippingOptions,
    this.defaultShippingOption,
    this.onShippingOptionChanged,
  }) : super(key: key);

  @override
  State<ShippingOptionsWidget> createState() => _ShippingOptionsWidgetState();
}

class _ShippingOptionsWidgetState extends State<ShippingOptionsWidget> {
  late String selectedOption;

  @override
  void initState() {
    super.initState();
    selectedOption = widget.defaultShippingOption ?? 'recommended';
  }

  @override
  void didUpdateWidget(covariant ShippingOptionsWidget oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.defaultShippingOption != oldWidget.defaultShippingOption &&
        widget.defaultShippingOption != null) {
      selectedOption = widget.defaultShippingOption!;
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.shippingOptions == null) {
      return const SizedBox.shrink();
    }

    final shippingOptions = widget.shippingOptions!;
    final List<ShippingOptionItem> options = [];

    // Add recommended option
    if (shippingOptions.recommended != null) {
      options.add(
        ShippingOptionItem(
          label: 'recommended',
          option: shippingOptions.recommended!,
          value: 'recommended',
        ),
      );
    }

    // Add cheapest option
    if (shippingOptions.cheapest != null) {
      options.add(
        ShippingOptionItem(
          label: 'cheapest',
          option: shippingOptions.cheapest!,
          value: 'cheapest',
        ),
      );
    }

    // Add fastest option
    if (shippingOptions.fastest != null) {
      options.add(
        ShippingOptionItem(
          label: 'fastest',
          option: shippingOptions.fastest!,
          value: 'fastest',
        ),
      );
    }

    if (options.isEmpty) {
      return const SizedBox.shrink();
    }

    return Column(
      children: [
        CustomDefaultContainer(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CustomTextContainer(
                textKey: selectShippingOptionKey,
                style: Theme.of(
                  context,
                ).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w600),
              ),
              DesignConfig.smallHeightSizedBox,
              ...options.map((option) {
                return Container(
                  padding: const EdgeInsetsDirectional.symmetric(vertical: 8.0),
                  decoration: BoxDecoration(
                    border: Border.all(color: transparentColor),
                  ),
                  child: GestureDetector(
                    onTap: () {
                      setState(() {
                        selectedOption = option.value;
                      });
                      widget.onShippingOptionChanged?.call(option.value);
                    },
                    child: Row(
                      crossAxisAlignment: .start,
                      children: [
                        Radio<String>(
                          value: option.value,
                          groupValue: selectedOption,
                          onChanged: (value) {
                            if (value != null) {
                              setState(() {
                                selectedOption = value;
                              });
                              widget.onShippingOptionChanged?.call(value);
                            }
                          },
                        ),
                        SizedBox(width: 8),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: .start,
                            children: [
                              Row(
                                mainAxisAlignment: .spaceBetween,
                                children: [
                                  Expanded(
                                    child: CustomTextContainer(
                                      textKey: option.label,
                                      style: Theme.of(context)
                                          .textTheme
                                          .bodyMedium
                                          ?.copyWith(
                                            fontWeight: FontWeight.w500,
                                          ),
                                    ),
                                  ),
                                  Text(
                                    option.option.formatRate ??
                                        '${option.option.rate}',
                                    style: Theme.of(context)
                                        .textTheme
                                        .bodyMedium
                                        ?.copyWith(
                                          fontWeight: FontWeight.w600,
                                          color: Theme.of(
                                            context,
                                          ).colorScheme.primary,
                                        ),
                                  ),
                                ],
                              ),
                              if (option.option.days != null)
                                Padding(
                                  padding: const EdgeInsetsDirectional.only(
                                    top: 4,
                                  ),
                                  child: Text(
                                    AppLocalizations.get(
                                      context,
                                      deliveryInDaysKey,
                                      placeholders: {
                                        'days': option.option.days.toString(),
                                      },
                                    ),
                                    style: Theme.of(context).textTheme.bodySmall
                                        ?.copyWith(color: greyColor),
                                  ),
                                ),
                              if (option.option.courier != null)
                                Padding(
                                  padding: const EdgeInsetsDirectional.only(
                                    top: 2,
                                  ),
                                  child: Text(
                                    '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: courierAgencyKey)}: ${option.option.courier}',
                                    style: Theme.of(context).textTheme.bodySmall
                                        ?.copyWith(color: greyColor),
                                    maxLines: 2,
                                  ),
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ],
          ),
        ),
        DesignConfig.smallHeightSizedBox,
      ],
    );
  }
}

class ShippingOptionItem {
  final String label;
  final ShippingOption option;
  final String value;

  ShippingOptionItem({
    required this.label,
    required this.option,
    required this.value,
  });
}
