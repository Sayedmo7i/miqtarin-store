import 'package:eshopplus_seller/commons/repositories/categoryRepository.dart';
import 'package:eshopplus_seller/features/profile/addProduct/models/category.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

abstract class CategoryListState {}

class CategoryListFetchInitial extends CategoryListState {}

class CategoryListFetchProgress extends CategoryListState {
  CategoryListFetchProgress();
}

class CategoryListFetchSuccess extends CategoryListState {
  List<Category> categoryList;
  List<Category>? requestedCategoryList;
  CategoryListFetchSuccess(this.categoryList,this.requestedCategoryList);
}

class CategoryListFetchFailure extends CategoryListState {
  final String errorMessage;
  CategoryListFetchFailure(this.errorMessage);
}

class CategoryListCubit extends Cubit<CategoryListState> {
  CategoryListCubit() : super(CategoryListFetchInitial());
 

  getCategoryList(BuildContext context, Map<String, String?> parameter,
      {bool getAllCategories = false}) {
    emit(CategoryListFetchProgress());
   CategoryRepository(). getCategoryListProcess( parameter,
            getAllCategories: getAllCategories)
        .then((result) {
      if (!isClosed) emit(CategoryListFetchSuccess(result.data, result.requested_categories));
    }).catchError((e) {
      if (!isClosed) emit(CategoryListFetchFailure(e.toString()));
    });
  }


 List<Category> categoryList() {
    if (state is CategoryListFetchSuccess) {
      return (state as CategoryListFetchSuccess).categoryList;
    }
    return [];
  }

  List<Category> requestedCategoryList() {
    if (state is CategoryListFetchSuccess) {
      return (state as CategoryListFetchSuccess).requestedCategoryList ?? [];
    }
    return [];
  }
addRequestedCategory(Category category) {
    if (state is CategoryListFetchSuccess) {
      final currentState = state as CategoryListFetchSuccess;
      final updatedRequestedList = List<Category>.from(currentState.requestedCategoryList ?? []);
      updatedRequestedList.insert(0, category);
      emit(CategoryListFetchSuccess(currentState.categoryList, updatedRequestedList));
    }
    else{
      emit(CategoryListFetchSuccess([],[category]));
    }
  }
emitSuccessState(List<Category> requestedCategoryList) {
    emit(CategoryListFetchSuccess((state as CategoryListFetchSuccess).categoryList, requestedCategoryList));
  }
}
