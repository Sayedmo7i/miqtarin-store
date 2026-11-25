import 'package:eshopplus_seller/features/profile/brands/models/brand.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../repositories/addDeleteBrandRepository.dart';

abstract class AddBrandState {}

class AddBrandInitial extends AddBrandState {}

class AddBrandInProgress extends AddBrandState {}

class AddBrandSuccess extends AddBrandState {
  final String message;
  final Brand brand;
  AddBrandSuccess(this.brand, this.message);
}

class AddBrandFailure extends AddBrandState {
  final String error;
  AddBrandFailure(this.error);
}

class AddBrandCubit extends Cubit<AddBrandState> {
  AddBrandCubit() : super(AddBrandInitial());

  Future<void> addBrand({
    required String storeId,
    required Map<String, String> names,
    required String imagePath,
  }) async {
    emit(AddBrandInProgress());
    try {
      final result = await AddDeleteBrandRepository().addBrand(
        storeId: storeId,
        names: names,
        imagePath: imagePath,
      );
      emit(AddBrandSuccess(result.brand, result.message));
    } catch (e) {
      emit(AddBrandFailure(e.toString()));
    }
  }
}
