import 'package:eshop_plus/ui/blog/repositories/blogRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../models/blogCategory.dart';

abstract class BlogCategoryState {}

class BlogCategoryInitial extends BlogCategoryState {}

class BlogCategoryFetchInProgress extends BlogCategoryState {}

class BlogCategoryFetchSuccess extends BlogCategoryState {
  final List<BlogCategory> categories;

  BlogCategoryFetchSuccess({required this.categories});
}

class BlogCategoryFetchFailure extends BlogCategoryState {
  final String errorMessage;

  BlogCategoryFetchFailure(this.errorMessage);
}

class BlogCategoryCubit extends Cubit<BlogCategoryState> {
  final BlogRepository _blogRepository = BlogRepository();

  BlogCategoryCubit() : super(BlogCategoryInitial());

  void getBlogCategories({required int storeId}) async {
    emit(BlogCategoryFetchInProgress());
    try {
      final categories = await _blogRepository.getBlogCategories(
        storeId: storeId,
      );
      emit(BlogCategoryFetchSuccess(categories: categories));
    } catch (e) {
      emit(BlogCategoryFetchFailure(e.toString()));
    }
  }

  List<BlogCategory> getCategoryList() {
    if (state is BlogCategoryFetchSuccess) {
      return (state as BlogCategoryFetchSuccess).categories;
    }
    return [];
  }

  resetState() {
    emit(BlogCategoryInitial());
  }
}
