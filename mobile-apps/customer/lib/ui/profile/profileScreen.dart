import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/core/constants/appAssets.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/main.dart';
import 'package:eshop_plus/ui/auth/blocs/authCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/blocs/userDetailsCubit.dart';
import 'package:eshop_plus/commons/widgets/customAppbar.dart';
import 'package:eshop_plus/commons/widgets/customTextButton.dart';
import 'package:flutter/material.dart';

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:in_app_review/in_app_review.dart';
import 'package:share_plus/share_plus.dart';

import '../../core/routes/routes.dart';
import '../../commons/blocs/settingsAndLanguagesCubit.dart';
import '../../commons/models/language.dart';
import '../../utils/designConfig.dart';
import '../../core/localization/labelKeys.dart';
import '../../utils/utils.dart';
import '../../commons/widgets/customCircularProgressIndicator.dart';
import '../../commons/widgets/customLabelContainer.dart';
import '../../commons/widgets/customRoundedButton.dart';
import '../../commons/widgets/customTextContainer.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  late Size size;
  late Color textColor;
  final _inAppReview = InAppReview.instance;
  Language? _selectedLanguage;
  bool _isLoading = false, _languageInProgress = false;
  bool triedInAppReview = false;
  @override
  void initState() {
    super.initState();
    _selectedLanguage = (context
        .read<SettingsAndLanguagesCubit>()
        .getCurrentAppLanguage());
  }

  @override
  Widget build(BuildContext context) {
    size = MediaQuery.of(context).size;
    textColor = Theme.of(context).colorScheme.secondary.withValues(alpha: 0.9);
    return Scaffold(
      appBar: const CustomAppbar(titleKey: myProfileKey, showBackButton: false),
      body: buildBody(),
    );
  }

  buildBody() {
    return Stack(
      clipBehavior: Clip.none,
      children: [
        BlocListener<SettingsAndLanguagesCubit, SettingsAndLanguagesState>(
          listener: (context, state) {
            if (state is SettingsAndLanguagesFetchFailure) {
              // Show error message for language-related failures
              Utils.showSnackBar(message: state.errorMessage, context: context);
            }
          },
          child: BlocBuilder<UserDetailsCubit, UserDetailsState>(
            builder: (context, state) {
              return ListView(
                children: [
                  Container(
                    color: Theme.of(context).colorScheme.primaryContainer,
                    height: context.read<UserDetailsCubit>().isGuestUser()
                        ? 90
                        : 210,
                    child: Stack(
                      clipBehavior: Clip.none,
                      children: [
                        buildProfileContainer(),
                        if (!context.read<UserDetailsCubit>().isGuestUser())
                          buildOrderCouponsAndAddressContainer(),
                      ],
                    ),
                  ),
                  Column(
                    children: [
                      if (!context.read<UserDetailsCubit>().isGuestUser())
                        Container(
                          color: Theme.of(context).colorScheme.primaryContainer,
                          width: size.width,
                          margin: const EdgeInsetsDirectional.only(bottom: 8),
                          padding: EdgeInsetsDirectional.only(
                            start: appContentHorizontalPadding,
                            end: appContentHorizontalPadding,
                          ),
                          child: Column(
                            crossAxisAlignment: .start,
                            children: <Widget>[
                              buildLabel(accountKey),
                              buildListTile(
                                Icons.person_2_outlined,
                                profileKey,
                                (() => Utils.navigateToScreen(
                                  context,
                                  Routes.editProfileScreen,
                                  arguments: true,
                                )),
                              ),
                              buildListTile(
                                Icons.account_balance_wallet_outlined,
                                walletKey,
                                () => Utils.navigateToScreen(
                                  context,
                                  Routes.walletScreen,
                                ),
                              ),
                              buildListTile(
                                Icons.receipt_outlined,
                                transactionKey,
                                () => Utils.navigateToScreen(
                                  context,
                                  Routes.transactionScreen,
                                  arguments: {
                                    'transactionType': defaultTransactionType,
                                  },
                                ),
                              ),
                            ],
                          ),
                        ),
                      //we will only display this if there are more than one language or the user is not guest
                      if (context
                                  .read<SettingsAndLanguagesCubit>()
                                  .getLanguages()
                                  .length >
                              1 ||
                          !context.read<UserDetailsCubit>().isGuestUser())
                        buildListContainer([
                          buildLabel(settingsKey),
                          if (context
                                  .read<SettingsAndLanguagesCubit>()
                                  .getLanguages()
                                  .length >
                              1)
                            buildListTile(
                              Icons.translate,
                              changeLanguageKey,
                              openLanguageBottomSheet,
                            ),
                          if (!context.read<UserDetailsCubit>().isGuestUser())
                            buildListTile(
                              Icons.settings_outlined,
                              settingsKey,
                              () => Utils.navigateToScreen(
                                context,
                                Routes.settingScreen,
                              ),
                            ),
                        ]),
                      buildListContainer([
                        buildLabel(supportAndInfoKey),
                        if (!context.read<UserDetailsCubit>().isGuestUser())
                          buildListTile(
                            Icons.support_agent_outlined,
                            customerSupportKey,
                            () => Utils.navigateToScreen(
                              context,
                              Routes.customerSupportScreen,
                            ),
                          ),
                        buildListTile(
                          Icons.perm_phone_msg_outlined,
                          contactUsKey,
                          () => navigatoToPolicyScreen(contactUsKey),
                        ),
                        buildListTile(
                          Icons.question_mark,
                          faqKey,
                          () =>
                              Utils.navigateToScreen(context, Routes.faqScreen),
                        ),
                        buildListTile(
                          Icons.info_outline,
                          aboutUsKey,
                          () => navigatoToPolicyScreen(aboutUsKey),
                        ),
                        buildListTile(
                          Icons.policy_outlined,
                          termsAndPolicyKey,
                          () => Utils.navigateToScreen(
                            context,
                            Routes.termsAndPolicyScreen,
                          ),
                        ),
                      ]),
                      buildListContainer([
                        buildLabel(moreKey),
                        buildListTile(
                          Icons.feed_outlined,
                          blogsKey,
                          () => Utils.navigateToScreen(
                            context,
                            Routes.blogListScreen,
                          ),
                        ),
                        if (!context.read<UserDetailsCubit>().isGuestUser())
                          buildListTile(
                            Icons.monetization_on_outlined,
                            referAndEarnKey,
                            () => Utils.navigateToScreen(
                              context,
                              Routes.referAndEarnScreen,
                            ),
                          ),
                        buildListTile(
                          Icons.star_outline,
                          rateUsKey,
                          openRateUsDialog,
                        ),
                        buildListTile(Icons.share_outlined, shareAppKey, () {
                          //this box logic is added to support ios device
                          final box = context.findRenderObject() as RenderBox?;
                          SharePlus.instance.share(
                            ShareParams(
                              text:
                                  'Download $appName App from below link : ${Utils.getStoreUrl(context)}',
                              subject: appName,
                              sharePositionOrigin: box != null
                                  ? box.localToGlobal(Offset.zero) & box.size
                                  : null,
                            ),
                          );
                        }),
                        if (!context.read<UserDetailsCubit>().isGuestUser())
                          buildListTile(
                            Icons.logout,
                            logoutKey,
                            openLogoutDialog,
                          ),
                      ], bottomSpace: false),
                    ],
                  ),
                ],
              );
            },
          ),
        ),
        if (_languageInProgress)
          Container(
            color: blackColor.withValues(alpha: 0.4),
            child: CustomCircularProgressIndicator(
              indicatorColor: Theme.of(context).colorScheme.primary,
            ),
          ),
      ],
    );
  }

  Widget buildProfileContainer() {
    return BlocBuilder<UserDetailsCubit, UserDetailsState>(
      builder: (context, state) {
        if (state is UserDetailsFetchSuccess) {
          return Container(
            height: 90,
            padding: const EdgeInsetsDirectional.symmetric(
              horizontal: appContentHorizontalPadding,
            ),
            color: Theme.of(context).colorScheme.primary,
            child: Row(
              children: <Widget>[
                Utils.buildProfilePicture(
                  context,
                  60,
                  state.userDetails.image ?? "",
                  selectedFile: null,
                  assetImage: false,
                  outerBorderColor: transparentColor,
                ),
                DesignConfig.defaultWidthSizedBox,
                !context.read<UserDetailsCubit>().isGuestUser()
                    ? Column(
                        mainAxisAlignment: .center,
                        crossAxisAlignment: .start,
                        children: <Widget>[
                          CustomTextContainer(
                            textKey: state.userDetails.username ?? '',
                            style: Theme.of(context).textTheme.titleMedium!
                                .copyWith(
                                  color: Theme.of(
                                    context,
                                  ).colorScheme.onPrimary,
                                ),
                          ),
                          CustomTextContainer(
                            textKey: state.userDetails.email ?? '',
                            style: Theme.of(context).textTheme.bodyMedium!
                                .copyWith(
                                  color: Theme.of(
                                    context,
                                  ).colorScheme.onPrimary,
                                ),
                          ),
                        ],
                      )
                    : CustomTextButton(
                        buttonTextKey: loginKey,
                        onTapButton: () {
                          Utils.navigateToScreen(context, Routes.loginScreen);
                        },
                        textStyle: Theme.of(context).textTheme.titleMedium!
                            .copyWith(
                              decoration: TextDecoration.underline,
                              decorationThickness: 2.0,
                              decorationColor: Theme.of(
                                context,
                              ).colorScheme.onPrimary,
                              color: Theme.of(context).colorScheme.onPrimary,
                            ),
                      ),
              ],
            ),
          );
        }
        return SizedBox();
      },
    );
  }

  buildOrderCouponsAndAddressContainer() {
    return Positioned(
      top: 80,
      child: Padding(
        padding: const .symmetric(horizontal: appContentHorizontalPadding),
        child: Container(
          height: 120,
          width: size.width - (appContentHorizontalPadding * 2),
          padding: const .all(8),
          decoration: ShapeDecoration(
            color: Theme.of(context).colorScheme.primaryContainer,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
            shadows: [
              BoxShadow(
                color: Color(0x1E000000),
                blurRadius: 4,
                offset: Offset(0, 1),
                spreadRadius: 0,
              ),
            ],
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: .spaceAround,
            crossAxisAlignment: .start,
            children: [
              buildCotentBox(
                AppAssets.myOrder,
                myOrderKey,
                Routes.myOrderScreen,
              ),
              buildCotentBox(
                AppAssets.coupons,
                couponsKey,
                Routes.promoCodeScreen,
                arguments: {'fromCartScreen': false},
              ),
              buildCotentBox(
                AppAssets.address,
                addressKey,
                Routes.myAddressScreen,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget buildCotentBox(
    String image,
    String title,
    String route, {
    dynamic arguments,
  }) {
    return Expanded(
      child: GestureDetector(
        onTap: () =>
            Utils.navigateToScreen(context, route, arguments: arguments),
        child: Container(
          padding: const .all(8),
          margin: const .all(8),
          decoration: ShapeDecoration(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(4),
            ),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: .start,
            crossAxisAlignment: .center,
            children: [
              Container(
                width: 42,
                height: 42,
                padding: const .all(9),
                decoration: ShapeDecoration(
                  color: Theme.of(
                    context,
                  ).colorScheme.primary.withValues(alpha: 0.1),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: SvgPicture.asset(
                  image,
                  colorFilter: ColorFilter.mode(
                    Theme.of(context).colorScheme.primary,
                    BlendMode.srcIn,
                  ),
                ),
              ),
              const SizedBox(height: 8),
              CustomTextContainer(
                textKey: title,
                style: Theme.of(context).textTheme.titleSmall,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Container buildListContainer(
    List<Widget> children, {
    bool bottomSpace = true,
  }) {
    return Container(
      color: Theme.of(context).colorScheme.onPrimary,
      width: size.width,
      padding: const .symmetric(horizontal: appContentHorizontalPadding),
      margin: bottomSpace ? const EdgeInsetsDirectional.only(bottom: 8) : null,
      child: Column(crossAxisAlignment: .start, children: children),
    );
  }

  buildLabel(String title) {
    return Padding(
      padding: const .only(top: 16, bottom: 24),
      child: CustomTextContainer(
        textKey: title,
        style: Theme.of(context).textTheme.titleMedium!,
      ),
    );
  }

  buildListTile(IconData icon, String title, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsetsDirectional.only(
          bottom: appContentVerticalSpace,
        ),
        decoration: BoxDecoration(border: Border.all(color: transparentColor)),
        child: Row(
          children: <Widget>[
            Container(
              width: 36,
              height: 36,
              padding: const .all(7),
              decoration: ShapeDecoration(
                color: Theme.of(
                  context,
                ).colorScheme.primary.withValues(alpha: 0.1),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              child: Icon(
                icon,
                size: 22,
                color: Theme.of(context).colorScheme.primary,
              ),
            ),
            const SizedBox(width: 8),
            CustomTextContainer(
              textKey: title,
              style: Theme.of(
                context,
              ).textTheme.titleMedium!.copyWith(color: textColor),
            ),
            const Spacer(),
            Icon(Icons.arrow_forward_ios, color: textColor),
          ],
        ),
      ),
    );
  }

  void openRateUsDialog() async {
    try {
      if (!triedInAppReview && await _inAppReview.isAvailable()) {
        triedInAppReview = true;
        await _inAppReview.requestReview();
      } else {
        await _inAppReview.openStoreListing(
          // appStoreId: _appStoreId,
        );
      }
    } catch (e) {
      await _inAppReview.openStoreListing();
    }
  }

  void openLogoutDialog() {
    showDialog(
      context: context,
      builder: (BuildContext bldcontext) {
        double width = MediaQuery.of(context).size.width;
        return AlertDialog(
          backgroundColor: transparentColor,
          contentPadding: .zero,
          elevation: 0.0,
          insetPadding: const .symmetric(horizontal: 20),
          content: StatefulBuilder(
            builder: (context, setState) {
              return Column(
                mainAxisAlignment: .center,
                children: [
                  Container(
                    padding: const .symmetric(horizontal: 16, vertical: 16),
                    width: width,
                    decoration: BoxDecoration(
                      color: Theme.of(context).colorScheme.primaryContainer,
                      borderRadius: const BorderRadius.all(Radius.circular(8)),
                    ),
                    child: Column(
                      crossAxisAlignment: .center,
                      children: [
                        Icon(
                          Icons.info_outline,
                          color: Theme.of(context).colorScheme.primary,
                          size: 30,
                        ),
                        DesignConfig.defaultHeightSizedBox,
                        CustomTextContainer(
                          textKey: logoutKey,
                          style: Theme.of(context).textTheme.titleMedium,
                        ),
                        const SizedBox(height: 4.0),
                        CustomTextContainer(
                          textKey: areYouSureYouWantToLogoutKey,
                          style: Theme.of(context).textTheme.bodyMedium,
                        ),
                        DesignConfig.defaultHeightSizedBox,
                        Row(
                          children: <Widget>[
                            Expanded(
                              child: CustomRoundedButton(
                                widthPercentage: 0.2,
                                buttonTitle: noKey,
                                showBorder: true,
                                backgroundColor: Theme.of(
                                  context,
                                ).colorScheme.onPrimary,
                                borderColor: Theme.of(
                                  context,
                                ).inputDecorationTheme.iconColor,
                                style: Theme.of(context).textTheme.bodyMedium!
                                    .copyWith(
                                      color: Theme.of(
                                        context,
                                      ).colorScheme.secondary,
                                    ),
                                onTap: () => Utils.popNavigation(context),
                              ),
                            ),
                            const SizedBox(width: 24),
                            Expanded(
                              child: BlocConsumer<AuthCubit, AuthState>(
                                listener: (context, state) {
                                  if (state is Unauthenticated) {
                                    context
                                        .read<UserDetailsCubit>()
                                        .resetUserDetailsState();
                                    setState(() {
                                      _isLoading = false;
                                    });
                                    Navigator.of(context).pop();
                                    // mainScreenKey?.currentState
                                    //     ?.changeCurrentIndex(0);
                                  }
                                },
                                builder: (context, state) {
                                  if (state is AuthInitial) {
                                    return const CircularProgressIndicator();
                                  }
                                  return CustomRoundedButton(
                                    widthPercentage: 0.2,
                                    buttonTitle: logoutKey,
                                    showBorder: false,
                                    child: _isLoading
                                        ? const CustomCircularProgressIndicator()
                                        : null,
                                    onTap: () {
                                      if (state is Authenticated) {
                                        if (_isLoading) return;
                                        setState(() {
                                          _isLoading = true;
                                        });
                                        context.read<AuthCubit>().signOut(
                                          context,
                                        );
                                      } else {
                                        Utils.errorDialog(
                                          context,
                                          pleaseLoginKey,
                                        );
                                      }
                                    },
                                  );
                                },
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              );
            },
          ),
        );
      },
    );
  }

  void navigatoToPolicyScreen(String policy) {
    final settings = context.read<SettingsAndLanguagesCubit>().getSettings();

    Utils.navigateToScreen(
      context,
      Routes.policyScreen,
      arguments: {
        'title': policy,
        'content': policy == aboutUsKey ? settings.aboutUs : settings.contactUs,
        'isRawText': true,
      },
    );
  }

  void openLanguageBottomSheet() {
    Utils.openModalBottomSheet(
      context,
      staticContent: false,
      StatefulBuilder(
        builder: (BuildContext context, StateSetter setBottomSheetState) {
          return BlocBuilder<
            SettingsAndLanguagesCubit,
            SettingsAndLanguagesState
          >(
            bloc: context.read<SettingsAndLanguagesCubit>(),
            builder: (context, state) {
              if (state is SettingsAndLanguagesFetchSuccess) {
                return Container(
                  padding: const .symmetric(
                    horizontal: appContentHorizontalPadding,
                    vertical: 8,
                  ),
                  child: Column(
                    crossAxisAlignment: .start,
                    children: [
                      CustomTextContainer(
                        textKey: appLanguageKey,
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                      DesignConfig.smallHeightSizedBox,
                      Expanded(
                        child: CustomScrollView(
                          slivers: [
                            SliverList(
                              delegate: SliverChildBuilderDelegate((
                                context,
                                index,
                              ) {
                                Language language = state.languages[index];
                                return RadioGroup<String>(
                                  groupValue: _selectedLanguage!.code!,
                                  onChanged: (value) async {
                                    setBottomSheetState(() {
                                      _selectedLanguage = language;
                                    });
                                    setState(() {
                                      _languageInProgress = true;
                                    });

                                    try {
                                      Navigator.of(context).pop();

                                      // Change language
                                      bool status = await navigatorKey
                                          .currentContext!
                                          .read<SettingsAndLanguagesCubit>()
                                          .changeLanguage(language);
                                      if (!status) {
                                        // Reset selection to previous language
                                        setState(() {
                                          _selectedLanguage = context
                                              .read<SettingsAndLanguagesCubit>()
                                              .getCurrentAppLanguage();
                                        });
                                      } else {
                                        // Language change successful, refresh stores
                                        try {
                                          await navigatorKey.currentContext
                                              ?.read<StoresCubit>()
                                              .fetchStores(
                                                stores: navigatorKey
                                                    .currentContext
                                                    ?.read<StoresCubit>()
                                                    .getAllStores(),
                                              );
                                        } catch (e) {
                                          // Handle store refresh error silently
                                        }
                                      }
                                    } catch (e) {
                                      // Handle any other errors
                                      if (mounted) {
                                        // Reset selection to previous language
                                        setState(() {
                                          _selectedLanguage = context
                                              .read<SettingsAndLanguagesCubit>()
                                              .getCurrentAppLanguage();
                                        });
                                      }
                                    } finally {
                                      if (mounted) {
                                        setState(() {
                                          _languageInProgress = false;
                                        });
                                      }
                                    }
                                  },
                                  child: RadioListTile<String>(
                                    contentPadding: .zero,
                                    visualDensity: const VisualDensity(
                                      horizontal: -4,
                                      vertical: -2,
                                    ),
                                    title: Column(
                                      crossAxisAlignment: .start,
                                      children: [
                                        if (language.nativeLanguage != null)
                                          CustomLabelContainer(
                                            textKey:
                                                language.nativeLanguage
                                                    .toString()
                                                    .capitalizeFirst ??
                                                '',
                                            isFieldValueMandatory: false,
                                          ),
                                        CustomLabelContainer(
                                          textKey:
                                              language.language
                                                  .toString()
                                                  .capitalizeFirst ??
                                              '',
                                          isFieldValueMandatory: false,
                                        ),
                                      ],
                                    ),
                                    value: language.code!,
                                  ),
                                );
                              }, childCount: state.languages.length),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              }
              if (state is SettingsAndLanguagesFetchFailure) {
                return const Center(
                  child: CustomTextContainer(textKey: dataNotAvailableKey),
                );
              }
              return CustomCircularProgressIndicator(
                indicatorColor: Theme.of(context).colorScheme.primary,
              );
            },
          );
        },
      ),
    );
  }
}
