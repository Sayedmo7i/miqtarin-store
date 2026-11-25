import 'package:eshopplus_deliveryboy/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_deliveryboy/core/constants/themeConstants.dart';
import 'package:eshopplus_deliveryboy/core/theme/colors.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/localization/labelKeys.dart';
import '../../../../utils/utils.dart';
import '../../../core/routes/routes.dart';
import '../../../commons/blocs/userDetailsCubit.dart';
import '../../../commons/widgets/customTextContainer.dart';
import '../../../commons/widgets/customRoundedButton.dart';
import '../../../commons/blocs/settingsAndLanguagesCubit.dart';
import '../../../features/auth/blocs/availabilityToggleCubit.dart';

class HomeAppBar extends StatelessWidget implements PreferredSizeWidget {
  const HomeAppBar({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return BlocListener<AvailabilityToggleCubit, AvailabilityToggleState>(
      listener: (context, state) {
        if (state is AvailabilityToggleSuccess) {
          context
              .read<UserDetailsCubit>()
              .updateAvailabilityStatus(state.isAvailable ? 1 : 0);
          Utils.showSnackBar(
            context: context,
            message: state.message,
          );
        } else if (state is AvailabilityToggleFailure) {
          Utils.showSnackBar(
            context: context,
            message: state.errorMessage,
          );
        }
      },
      child: BlocBuilder<UserDetailsCubit, UserDetailsState>(
        builder: (context, state) {
          return PreferredSize(
            preferredSize: const Size.fromHeight(80),
            child: Container(
              decoration: BoxDecoration(color: whiteColor, boxShadow: [
                BoxShadow(
                    color: Color.fromRGBO(0, 0, 0, 0.08),
                    offset: Offset(0, 5.0),
                    blurRadius: 9.0,
                    spreadRadius: 0)
              ]),
              padding: const EdgeInsetsDirectional.symmetric(
                  horizontal: appContentHorizontalPadding, vertical: 8),
              child: AppBar(
                leadingWidth: 40,
                automaticallyImplyLeading: false,
                titleSpacing: 10,
                elevation: 0,
                backgroundColor: whiteColor,
                surfaceTintColor: whiteColor,
                leading: Align(
                  alignment: Alignment.centerLeft,
                  child: Utils.buildProfilePicture(
                      context,
                      44,
                      (context.read<UserDetailsCubit>().state
                              as UserDetailsFetchSuccess)
                          .userDetails
                          .image!,
                      selectedFile: null,
                      assetImage: false,
                      outerBorderColor: greyColor),
                ),
                title: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    CustomTextContainer(
                      textKey: welcomeKey,
                      style: Theme.of(context).textTheme.bodyLarge,
                    ),
                    CustomTextContainer(
                      textKey: (context.read<UserDetailsCubit>().state
                                  as UserDetailsFetchSuccess)
                              .userDetails
                              .username ??
                          "",
                      style: Theme.of(context).textTheme.bodySmall,
                    ),
                  ],
                ),
                actions: [
                  _buildAvailabilityToggle(context),
                  const SizedBox(width: 12),
                  GestureDetector(
                    onTap: () => Utils.navigateToScreen(
                        context, Routes.notificationScreen),
                    child: const Icon(
                      Icons.notifications_outlined,
                    ),
                  ),
                  const SizedBox(width: 8),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildAvailabilityToggle(BuildContext context) {
    final isAvailable = context.read<UserDetailsCubit>().isUserAvailable();
    Color availabilityColor = isAvailable ? greenColor : redColor;
    return GestureDetector(
      onTap: () => _onAvailabilityToggled(context, isAvailable),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: availabilityColor.withValues(alpha: 0.1),
          border: Border.all(
            color: availabilityColor,
            width: 1.5,
          ),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 10,
              height: 10,
              decoration: BoxDecoration(
                color: availabilityColor,
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 6),
            Text(
              context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
                    labelKey: isAvailable ? availableKey : notAvailableKey,
                  ),
              style: TextStyle(
                color: availabilityColor,
                fontWeight: FontWeight.w600,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _onAvailabilityToggled(BuildContext context, bool isAvailable) {
    if (isAvailable) {
      _showOfflineConfirmationDialog(context);
    } else {
      _updateAvailabilityStatus(context, true);
    }
  }

  void _showOfflineConfirmationDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (BuildContext dialogContext) {
        return AlertDialog(
          backgroundColor: Theme.of(context).colorScheme.primaryContainer,
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          elevation: 0.0,
          insetPadding: const EdgeInsets.symmetric(
            horizontal: 20,
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          title: Row(
            children: [
              Icon(
                Icons.warning_amber_rounded,
                color: orangeColor,
                size: 28,
              ),
              const SizedBox(width: 8),
              Text(
                context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
                      labelKey: changeStatusKey,
                    ),
                style: Theme.of(dialogContext).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
              ),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
                      labelKey: confirmGoOfflineKey,
                    ),
                style: Theme.of(dialogContext).textTheme.bodyLarge?.copyWith(
                      fontWeight: FontWeight.w500,
                    ),
              ),
              const SizedBox(height: 12),
              Text(
                context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
                      labelKey: offlineWarningMessageKey,
                    ),
                style: Theme.of(dialogContext).textTheme.bodyMedium?.copyWith(
                      color: Theme.of(dialogContext)
                          .textTheme
                          .bodyMedium
                          ?.color
                          ?.withValues(alpha: 0.7),
                    ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(dialogContext).pop();
              },
              child: Text(
                context.read<SettingsAndLanguagesCubit>().getTranslatedValue(
                      labelKey: cancelKey,
                    ),
                style: TextStyle(
                  color: Theme.of(dialogContext).colorScheme.secondary,
                ),
              ),
            ),
            CustomRoundedButton(
              height: 36,
              widthPercentage: 0.25,
              buttonTitle: goOfflineKey,
              onTap: () {
                Navigator.of(dialogContext).pop();
                _updateAvailabilityStatus(context, false);
              },
              child: context.read<AvailabilityToggleCubit>().state
                      is AvailabilityToggleInProgress
                  ? CustomCircularProgressIndicator(
                      indicatorColor: Theme.of(context).colorScheme.primary,
                    )
                  : null,
              backgroundColor: redColor,
              showBorder: false,
            ),
          ],
        );
      },
    );
  }

  void _updateAvailabilityStatus(BuildContext context, bool available) {
    if (context.read<AvailabilityToggleCubit>().state
        is! AvailabilityToggleInProgress)
      context
          .read<AvailabilityToggleCubit>()
          .toggleAvailability(isAvailable: available);
  }

  @override
  Size get preferredSize => const Size.fromHeight(80);
}
