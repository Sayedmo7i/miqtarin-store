import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/models/userDetails.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';
import 'package:eshopplus_seller/features/auth/repositories/authRepository.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

@immutable
abstract class UserDetailsState {}

class UserDetailsInitial extends UserDetailsState {}

class UserDetailsFetchInProgress extends UserDetailsState {}

class UserDetailsFetchSuccess extends UserDetailsState {
  final UserDetails userDetails;
  final String token;
  UserDetailsFetchSuccess({required this.userDetails, required this.token});
}

class UserDetailsFetchFailure extends UserDetailsState {
  final String errorMessage;
  final int? errorCode;
  UserDetailsFetchFailure(this.errorMessage, this.errorCode);
}

class UserDetailsCubit extends Cubit<UserDetailsState> {
  final AuthRepository authRepository = AuthRepository();
  UserDetailsCubit() : super(UserDetailsInitial());

  //to fetch user details form remote
  void fetchUserDetails({required BuildContext context,bool emitProgress=true}) async {
    if(emitProgress)
    emit(UserDetailsFetchInProgress());
    try {
      ({String token, UserDetails userDetails}) value =
          await authRepository.verifyUser(context: context);
      emit(UserDetailsFetchSuccess(
          userDetails: value.userDetails, token: value.token));
    } catch (e) {
      emit(
          UserDetailsFetchFailure(e.toString(), (e as ApiException).errorCode));
    }
  }

  getUserData(String returnValue, {dynamic defaultValue}) {
    if (state is UserDetailsFetchSuccess) {
      switch (returnValue) {
        case 'name':
          return (state as UserDetailsFetchSuccess).userDetails.username;
        case 'userId':
          return (state as UserDetailsFetchSuccess).userDetails.id ?? 0;

        case 'status':
          return (state as UserDetailsFetchSuccess).userDetails.active;

        case 'mobileNumber':
          return (state as UserDetailsFetchSuccess).userDetails.mobile;
        case 'email':
          return (state as UserDetailsFetchSuccess).userDetails.email;
        case 'profileUrl':
          return (state as UserDetailsFetchSuccess).userDetails.image;

        case 'type':
          return (state as UserDetailsFetchSuccess).userDetails.type;

        case 'referralCode':
          return (state as UserDetailsFetchSuccess).userDetails.referralCode;
        case 'fcm':
          return (state as UserDetailsFetchSuccess).userDetails.fcmId;
        case 'userDetails':
          return (state as UserDetailsFetchSuccess).userDetails;
      }
    }

    return defaultValue ?? "";
  }

  String? getUserName() {
    return getUserData('name');
  }

  int getUserId() {
    return getUserData('userId', defaultValue: 0);
  }

  String getUserStatus() {
    return getUserData('status', defaultValue: '0');
  }

  String getUserMobile() {
    return getUserData('mobileNumber') ?? '';
  }

  String getUserEmail() {
    return getUserData('email');
  }

  String getUserFcm() {
    return getUserData('fcm');
  }

  String getuserDetailsPicture() {
    return getUserData('profileUrl');
  }

  UserDetails getuserDetails() {
    return getUserData('userDetails', defaultValue: UserDetails());
  }

  bool isNotificationOn() {
    if (state is UserDetailsFetchSuccess) {
      return (state as UserDetailsFetchSuccess).userDetails.isNotificationOn ==
          1;
    }
    return false;
  }
  List<StoreData> getAllStores() {
  UserDetails user =
        (state as UserDetailsFetchSuccess)
            .userDetails;
return user.storeData ?? [];
  }
  StoreData getDefaultStoreOfUser(BuildContext context) {
    UserDetails user =
        (state as UserDetailsFetchSuccess)
            .userDetails;
    StoreData currentStore = user.storeData!.firstWhere((element) {
      return element.storeId ==
          context.read<StoresCubit>().getDefaultStore().id;
    });
    return currentStore;
  }

  void updateuserDetailsUrl(String profileUrl) {
    if (state is UserDetailsFetchSuccess) {
      final oldUserDetails = (state as UserDetailsFetchSuccess).userDetails;

      emitUserSuccessState(oldUserDetails.copyWith(image: profileUrl).toJson(),
          ((state as UserDetailsFetchSuccess).token));
    }
  }

  void updateuserDetails({String? name, String? mobile, String? email}) {
    if (state is UserDetailsFetchSuccess) {
      UserDetails oldUserDetails =
          (state as UserDetailsFetchSuccess).userDetails;
      UserDetails userDetails = oldUserDetails.copyWith(
        email: email,
        mobile: mobile,
        username: name,
      );

      emitUserSuccessState(
          userDetails.toJson(), ((state as UserDetailsFetchSuccess).token));
    }
  }

  void updateStoreStatus({required int storeId, required int newStatus}) {
    if (state is UserDetailsFetchSuccess) {
      final oldUserDetails = (state as UserDetailsFetchSuccess).userDetails;
      int index = oldUserDetails.storeData!.indexWhere((element) => element.storeId == storeId);
      if (index != -1) {
        oldUserDetails.storeData![index].storeStatus = newStatus;
        emitUserSuccessState(
            oldUserDetails.toJson(), ((state as UserDetailsFetchSuccess).token));
      }
      // final updatedStoreData = oldUserDetails.storeData?.map((store) {
      //   if (store.storeId == storeId) {
      //     return StoreData(
      //       id: store.id,
      //       city: store.city,
      //       zipcode: store.zipcode,
      //       cityId: store.cityId,
      //       zipcodeId: store.zipcodeId,
      //       sellerId: store.sellerId,
      //       userId: store.userId,
      //       storeId: store.storeId,
      //       slug: store.slug,
      //       categoryIds: store.categoryIds,
      //       storeName: store.storeName,
      //       storeDescription: store.storeDescription,
      //       logo: store.logo,
      //       storeThumbnail: store.storeThumbnail,
      //       disk: store.disk,
      //       storeUrl: store.storeUrl,
      //       noOfRatings: store.noOfRatings,
      //       rating: store.rating,
      //       bankName: store.bankName,
      //       bankCode: store.bankCode,
      //       accountName: store.accountName,
      //       accountNumber: store.accountNumber,
      //       addressProof: store.addressProof,
      //       taxName: store.taxName,
      //       taxNumber: store.taxNumber,
      //       permissions: store.permissions,
      //       commission: store.commission,
      //       latitude: store.latitude,
      //       longitude: store.longitude,
      //       status: store.status,
      //       otherDocuments: store.otherDocuments,
      //       deliverableType: store.deliverableType,
      //       deliverableZones: store.deliverableZones,
      //       zoneIds: store.zoneIds,
      //       zones: store.zones,
      //       storeStatus: newStatus,
      //     );
      //   }
      //   return store;
      // }).toList();

      

      emitUserSuccessState(
          oldUserDetails.toJson(), (state as UserDetailsFetchSuccess).token);
    }
  }

  emitUserSuccessState(Map userDetails, String token) {
    UserDetails currentuserDetails =
        UserDetails.fromJson(userDetails as Map<String, dynamic>);
    emit(
        UserDetailsFetchSuccess(userDetails: currentuserDetails, token: token));
  }
}




