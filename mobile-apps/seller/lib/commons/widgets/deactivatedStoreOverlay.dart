import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../core/constants/themeConstants.dart';
import '../blocs/settingsAndLanguagesCubit.dart';

class DeactivatedStoreOverlay extends StatelessWidget {
  const DeactivatedStoreOverlay({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.black.withValues(alpha: 0.7),
      child: Center(
        child: Container(
          margin: const .all(appContentHorizontalPadding * 2),
          padding: const .all(appContentHorizontalPadding * 2),
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.surface,
            borderRadius: BorderRadius.circular(borderRadius),
          ),
          child: Column(
            mainAxisSize: .min,
            children: [
              Icon(
                Icons.store_mall_directory_outlined,
                size: 64,
                color: Theme.of(context).colorScheme.error,
              ),
              const SizedBox(height: 16),
              Text(
                context
                    .read<SettingsAndLanguagesCubit>()
                    .getTranslatedValue(labelKey: storeDeactivatedKey),
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: .bold,
                  color: Theme.of(context).colorScheme.onSurface,
                ),
                textAlign: .center,
              ),
              const SizedBox(height: 12),
              Text(
                context
                    .read<SettingsAndLanguagesCubit>()
                    .getTranslatedValue(labelKey: storeDeactivatedMessageKey),
                style: TextStyle(
                  fontSize: 14,
                  color: Theme.of(context)
                      .colorScheme
                      .onSurface
                      .withValues(alpha: 0.7),
                ),
                textAlign: .center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}