import 'package:eshopplus_deliveryboy/commons/models/userDetails.dart';
import 'package:eshopplus_deliveryboy/features/auth/repositories/authRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class AuthState {}

class AuthInitial extends AuthState {}

class Unauthenticated extends AuthState {}

class Authenticated extends AuthState {
  final String userMobile;
  final String userCountryCode;
  final String token;

  Authenticated({required this.userMobile,required this.userCountryCode, required this.token});
}

class AuthCubit extends Cubit<AuthState> {
  final AuthRepository authRepository;

  AuthCubit(this.authRepository) : super(AuthInitial()) {
    _checkIsAuthenticated();
  }

  void _checkIsAuthenticated() {
    if (AuthRepository.getIsLogIn()) {
      emit(
        Authenticated(
            userMobile: AuthRepository.getUserMobile(),
            userCountryCode: AuthRepository.getUserCountryCode(),
            token: AuthRepository.getToken()),
      );
    } else {
      emit(Unauthenticated());
    }
  }

  void authenticateUser(
      {required UserDetails userDetails, required String token}) {
    authRepository.setToken(token);
    authRepository.setUserMobile(userDetails.mobile!);
    authRepository.setUserCountryCode(userDetails.countryCode!);
    authRepository.setUserId(userDetails.id!);
    authRepository.setIsLogIn(true);

    emit(Authenticated(userMobile: userDetails.mobile!, userCountryCode: userDetails.countryCode!, token: token));
  }

  void signOut() {
    if (state is Authenticated) {
      authRepository.signOutUser();
      emit(Unauthenticated());
    }
  }

  String getUserMobile() {
    if (state is Authenticated) {
      return (state as Authenticated).userMobile;
    }

    return '';
  }
  String getUserCountryCode() {
    if (state is Authenticated) {
      return (state as Authenticated).userCountryCode;
    }

    return '';
  }
}
