import 'package:eshop_plus/ui/blog/repositories/blogRepository.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../models/blog.dart';

abstract class BlogState {}

class BlogInitial extends BlogState {}

class BlogFetchInProgress extends BlogState {}

class BlogFetchSuccess extends BlogState {
  final int total;
  final List<Blog> blogs;
  final bool fetchMoreError;
  final bool fetchMoreInProgress;
  final int? selectedCategoryId;

  BlogFetchSuccess({
    required this.blogs,
    required this.fetchMoreError,
    required this.fetchMoreInProgress,
    required this.total,
    this.selectedCategoryId,
  });

  BlogFetchSuccess copyWith({
    bool? fetchMoreError,
    bool? fetchMoreInProgress,
    int? total,
    List<Blog>? blogs,
    int? selectedCategoryId,
  }) {
    return BlogFetchSuccess(
      blogs: blogs ?? this.blogs,
      fetchMoreError: fetchMoreError ?? this.fetchMoreError,
      fetchMoreInProgress: fetchMoreInProgress ?? this.fetchMoreInProgress,
      total: total ?? this.total,
      selectedCategoryId: selectedCategoryId ?? this.selectedCategoryId,
    );
  }
}

class BlogFetchFailure extends BlogState {
  final String errorMessage;

  BlogFetchFailure(this.errorMessage);
}

class BlogCubit extends Cubit<BlogState> {
  final BlogRepository _blogRepository = BlogRepository();

  BlogCubit() : super(BlogInitial());

  void getBlogs({
    int? storeId,
    int? categoryId,
    String? storeSlug,
    String? blogSlug,
  }) async {
    emit(BlogFetchInProgress());
    try {
      final result = await _blogRepository.getBlogs(
        storeId: storeId,
        categoryId: categoryId,
        storeSlug: storeSlug,
        blogSlug: blogSlug,
      );
      emit(
        BlogFetchSuccess(
          blogs: result.blogs,
          fetchMoreError: false,
          fetchMoreInProgress: false,
          total: result.total,
          selectedCategoryId: categoryId,
        ),
      );
    } catch (e) {
      emit(BlogFetchFailure(e.toString()));
    }
  }

  List<Blog> getBlogList() {
    if (state is BlogFetchSuccess) {
      return (state as BlogFetchSuccess).blogs;
    }
    return [];
  }

  bool fetchMoreError() {
    if (state is BlogFetchSuccess) {
      return (state as BlogFetchSuccess).fetchMoreError;
    }
    return false;
  }

  bool hasMore() {
    if (state is BlogFetchSuccess) {
      return (state as BlogFetchSuccess).blogs.length <
          (state as BlogFetchSuccess).total;
    }
    return false;
  }

  void loadMore({
    int? storeId,
    int? categoryId,
    String? storeSlug,
    String? blogSlug,
  }) async {
    if (state is BlogFetchSuccess) {
      if ((state as BlogFetchSuccess).fetchMoreInProgress) {
        return;
      }
      try {
        emit((state as BlogFetchSuccess).copyWith(fetchMoreInProgress: true));

        final moreBlogs = await _blogRepository.getBlogs(
          storeId: storeId,
          categoryId: categoryId,
          storeSlug: storeSlug,
          blogSlug: blogSlug,
          offset: (state as BlogFetchSuccess).blogs.length,
        );

        final currentState = (state as BlogFetchSuccess);

        List<Blog> blogs = currentState.blogs;

        blogs.addAll(moreBlogs.blogs);

        emit(
          BlogFetchSuccess(
            fetchMoreError: false,
            fetchMoreInProgress: false,
            total: moreBlogs.total,
            blogs: blogs,
            selectedCategoryId: categoryId,
          ),
        );
      } catch (e) {
        emit(
          (state as BlogFetchSuccess).copyWith(
            fetchMoreInProgress: false,
            fetchMoreError: true,
          ),
        );
      }
    }
  }

  void changeCategoryFilter({required int storeId, int? categoryId}) {
    getBlogs(storeId: storeId, categoryId: categoryId);
  }

  resetState() {
    emit(BlogInitial());
  }
}
