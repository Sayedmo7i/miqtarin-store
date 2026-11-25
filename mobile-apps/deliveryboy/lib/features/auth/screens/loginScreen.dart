import 'package:country_code_picker/country_code_picker.dart';
import 'package:eshopplus_deliveryboy/core/routes/routes.dart';
import 'package:eshopplus_deliveryboy/features/auth/blocs/authCubit.dart';
import 'package:eshopplus_deliveryboy/features/auth/blocs/signInCubit.dart';
import 'package:eshopplus_deliveryboy/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshopplus_deliveryboy/commons/blocs/userDetailsCubit.dart';
import 'package:eshopplus_deliveryboy/core/api/apiEndPoints.dart';

import 'package:eshopplus_deliveryboy/features/auth/repositories/authRepository.dart';
import 'package:eshopplus_deliveryboy/features/auth/widgets/loginContainer.dart';
import 'package:eshopplus_deliveryboy/features/auth/widgets/countryCodePicker.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/customTextButton.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/customTextFieldContainer.dart';
import 'package:eshopplus_deliveryboy/commons/widgets/showHidePasswordButton.dart';
import 'package:eshopplus_deliveryboy/core/configs/appConfig.dart';
import 'package:eshopplus_deliveryboy/core/localization/labelKeys.dart';
import 'package:eshopplus_deliveryboy/utils/inputValidators.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../utils/utils.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  _LoginScreenState createState() => _LoginScreenState();
  static Widget getRouteInstance() => BlocProvider(
        create: (context) => SignInCubit(),
        child: const LoginScreen(),
      );
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _mobileController =
      TextEditingController(text: isDemoApp ? '9712008893' : null);
  final TextEditingController _passwordController =
      TextEditingController(text: isDemoApp ? '12345678' : null);
  bool _hidePassword = true;
  FocusNode? passwordFocus = FocusNode(), mobileFocus = FocusNode();
  String _selectedCountryCode = '';

  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    _selectedCountryCode =
        context.read<SettingsAndLanguagesCubit>().getCountryCode();
  }

  @override
  Widget build(BuildContext context) {

    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Scaffold(
          backgroundColor: Theme.of(context).colorScheme.primaryContainer,
          body: BlocConsumer<SignInCubit, SignInState>(
            listener: (context, state) {
              if (state is SignInSuccess) {
                if (state.userDetails.active == '0') {
                  Utils.showSnackBar(
                      context: context, message: deactivatedErrorMessageKey);
                  return;
                }
                context.read<AuthCubit>().authenticateUser(
                    userDetails: state.userDetails, token: state.token);
                context.read<UserDetailsCubit>().emitUserSuccessState(
                    state.userDetails.toJson(), state.token);
                FocusScope.of(context).unfocus();
                Utils.navigateToScreen(context, Routes.mainScreen,
                    replaceAll: true);
              }
              if (state is SignInFailure) {
                Utils.showSnackBar(
                    context: context, message: state.errorMessage);
              }
            },
            builder: (context, state) {
              return LoginContainer(
                titleText: welcomeBackKey,
                descriptionText: pleaseEnterLoginDetailsKey,
                buttonText: signInKey,
                onTapButton: state is SignInProgress ? () {} : callSignInApi,
                buttonWidget: state is SignInProgress
                    ? const CustomCircularProgressIndicator()
                    : null,
                content: buildContentWidget(),
                footerWidget: buildFooterWidget(),
              );
            },
          )),
    );
  }

  Widget buildContentWidget() {
    return Form(
      key: _formkey,
      child: Padding(
        padding: const EdgeInsets.only(top: 25),
        child: Column(
          children: <Widget>[
            Row(
              children: [
                Expanded(
                  child: CustomCountryCodePicker(
                    initialCountryCode: _selectedCountryCode,
                    onChanged: (CountryCode countryCode) {
                      setState(() {
                        _selectedCountryCode = countryCode.dialCode!;
                      });
                    },
                  ),
                ),
                Expanded(
                  flex: 2,
                  child: CustomTextFieldContainer(
                    hintTextKey: mobileNumberKey,
                    textEditingController: _mobileController,
                    labelKey: '',
                    keyboardType: TextInputType.number,
                    focusNode: mobileFocus,
                    textInputAction: TextInputAction.next,
                    isSetValidator: true,
                    inputFormatters: [
                      FilteringTextInputFormatter
                          .digitsOnly, // Allow only digits
                      LengthLimitingTextInputFormatter(
                          15), // Limit to 15 digits
                    ],
                    validator: (v) => Validator.validatePhoneNumber(v, context),
                    onFieldSubmitted: (v) {
                      FocusScope.of(context).requestFocus(passwordFocus);
                    },
                  ),
                ),
              ],
            ),
            CustomTextFieldContainer(
              hintTextKey: passwordKey,
              textEditingController: _passwordController,
              labelKey: '',
              prefixWidget: const Icon(Icons.lock_outline),
              hideText: _hidePassword,
              keyboardType: TextInputType.text,
              focusNode: passwordFocus,
              textInputAction: TextInputAction.done,
              maxLines: 1,
              isSetValidator: true,
              inputFormatters: [
                FilteringTextInputFormatter.deny(RegExp('[ ]')),
              ],
              suffixWidget: ShowHidePasswordButton(
                hidePassword: _hidePassword,
                onTapButton: () {
                  setState(() {
                    _hidePassword = !_hidePassword;
                  });
                },
              ),
              onFieldSubmitted: (v) {
                passwordFocus!.unfocus();
              },
            ),
            const SizedBox(
              height: 15,
            ),
            Align(
                alignment: Alignment.bottomRight,
                child: CustomTextButton(
                  buttonTextKey: forgotPasswordKey,
                  onTapButton: () {
                    Utils.navigateToScreen(
                        context, Routes.forgotPasswordScreen);
                  },
                  textStyle: Theme.of(context).textTheme.bodyLarge!.copyWith(
                      color: Theme.of(context).colorScheme.primary,
                      fontWeight: FontWeight.w400),
                ))
          ],
        ),
      ),
    );
  }

  void callSignInApi() async {
    FocusScope.of(context).unfocus();

    if (_formkey.currentState!.validate()) {
      {
        String fcmId = await AuthRepository.getFcmToken();
        context.read<SignInCubit>().login(params: {
          ApiURL.mobileApiKey: _mobileController.text.trim(),
          ApiURL.passwordApiKey: _passwordController.text.trim(),
          ApiURL.fcmIdApiKey: fcmId,
          "country_code": _selectedCountryCode.replaceAll('+', '')
        });
      }
    }
  }

  buildFooterWidget() {
    return Align(
      alignment: Alignment.center,
      child: Text.rich(
        TextSpan(
          children: [
            TextSpan(
              text: context
                  .read<SettingsAndLanguagesCubit>()
                  .getTranslatedValue(labelKey: dontHaveAccountKey),
            ),
            const TextSpan(
              text: ' ',
            ),
            TextSpan(
                text: context
                    .read<SettingsAndLanguagesCubit>()
                    .getTranslatedValue(labelKey: signUpKey),
                style: TextStyle(
                  color: Theme.of(context).colorScheme.primary,
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
                recognizer: TapGestureRecognizer()
                  ..onTap = () => Utils.navigateToScreen(
                      context, Routes.signupScreen,
                      arguments: false)),
          ],
        ),
        textAlign: TextAlign.center,
      ),
    );
  }
}
