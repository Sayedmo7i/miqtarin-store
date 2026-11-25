import 'package:eshopplus_seller/features/profile/addProduct/models/category.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../repositories/addDeleteCategoryRepository.dart';

abstract class AddCategoryState {}

class AddCategoryInitial extends AddCategoryState {}

class AddCategoryInProgress extends AddCategoryState {}

class AddCategorySuccess extends AddCategoryState {
  final String message;
  final Category category;
  AddCategorySuccess(this.category,
    this.message,
);
}

class AddCategoryFailure extends AddCategoryState {
  final String error;
  AddCategoryFailure(this.error);
}

class AddCategoryCubit extends Cubit<AddCategoryState> {
  AddCategoryCubit() : super(AddCategoryInitial());

  Future<void> addCategory({
    required String storeId,
    required Map<String, String> names,
    required String imagePath,
    required String bannerPath,
    String? parentId,
  
  }) async {
    emit(AddCategoryInProgress());
    try {
      final result = await AddDeleteCategoryRepository().addCategory(
        storeId: storeId,
        names: names,
        imagePath: imagePath,
        bannerPath: bannerPath,
        parentId: parentId,
      
      );
      emit(AddCategorySuccess(result.category, result.message));
    } catch (e) {
      emit(AddCategoryFailure(e.toString()));
    }
  }
}
