import 'package:eshopplus_seller/features/profile/categories/repositories/addDeleteCategoryRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class DeleteCategoryState {}

class DeleteCategoryInitial extends DeleteCategoryState {}

class DeleteCategoryProgress extends DeleteCategoryState {}

class DeleteCategoryFailure extends DeleteCategoryState {
 
  final String errorMessage;
  DeleteCategoryFailure(this.errorMessage);
}

class DeleteCategorySuccess extends DeleteCategoryState {
  final String successMessage;
   final String categoryId;
  DeleteCategorySuccess({required this.successMessage,required this.categoryId});
}

class DeleteCategoryCubit extends Cubit<DeleteCategoryState> {

  DeleteCategoryCubit() : super(DeleteCategoryInitial());
  void deleteCategory(
      {required String categoryId}) async {
    emit(DeleteCategoryProgress());

    try {
      await AddDeleteCategoryRepository().deleteCategory(categoryId: categoryId).then((value) {
        emit(DeleteCategorySuccess(successMessage: value,categoryId: categoryId));
      });
    } catch (e) {
      emit(DeleteCategoryFailure(e.toString()));
    }
  }
}
