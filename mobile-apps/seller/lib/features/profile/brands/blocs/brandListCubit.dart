import 'package:eshopplus_seller/features/profile/brands/models/brand.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/api/apiService.dart';

abstract class BrandListState {}

class BrandListFetchInitial extends BrandListState {}

class BrandListFetchProgress extends BrandListState {}

class BrandListFetchSuccess extends BrandListState {
  List<Brand> brandList;
  List<Brand>? requestedBrandList;
  BrandListFetchSuccess(this.brandList, this.requestedBrandList);
}

class BrandListFetchFailure extends BrandListState {
  final String errorMessage;
  BrandListFetchFailure(this.errorMessage);
}

class BrandListCubit extends Cubit<BrandListState> {
  BrandListCubit() : super(BrandListFetchInitial());
  
  setInitialState() {
    emit(BrandListFetchInitial());
  }

  getBrandList(BuildContext context, Map<String, String?> parameter) {
    emit(BrandListFetchProgress());
    getBrandListProcess(context, parameter)
        .then((result) {
      if (!isClosed) emit(BrandListFetchSuccess(result.data, result.requestedBrands));
    }).catchError((e) {
      if (!isClosed) emit(BrandListFetchFailure(e.toString()));
    });
  }

  Future<({List<Brand> data, List<Brand> requestedBrands})> getBrandListProcess(
      BuildContext context, Map<String, String?> parameter) async {
    try {
      final result = await Api.get(
          url: ApiURL.getBrandList,
          useAuthToken: true,
          queryParameters: parameter);
      
      List data = result[ApiURL.dataKey] ?? [];
      List requestedData = result['requested_brands'] ?? [];

      return (
        data: data.map((e) => Brand.fromJson(e)).toList(),
        requestedBrands: requestedData.map((e) => Brand.fromJson(e)).toList(),
      );
    } catch (e) {
      throw ApiException(e.toString());
    }
  }

  List<Brand> brandList() {
    if (state is BrandListFetchSuccess) {
      return (state as BrandListFetchSuccess).brandList;
    }
    return [];
  }

  List<Brand> requestedBrandList() {
    if (state is BrandListFetchSuccess) {
      return (state as BrandListFetchSuccess).requestedBrandList ?? [];
    }
    return [];
  }

  addRequestedBrand(Brand brand) {
    if (state is BrandListFetchSuccess) {
      final currentState = state as BrandListFetchSuccess;
      final updatedRequestedList = List<Brand>.from(currentState.requestedBrandList ?? []);
      updatedRequestedList.insert(0, brand);
      emit(BrandListFetchSuccess(currentState.brandList, updatedRequestedList));
    } else {
      emit(BrandListFetchSuccess([], [brand]));
    }
  }

emitSuccessState(List<Brand> requestedBrandList) {
    emit(BrandListFetchSuccess(
        (state as BrandListFetchSuccess).brandList,
        requestedBrandList));
  }
}