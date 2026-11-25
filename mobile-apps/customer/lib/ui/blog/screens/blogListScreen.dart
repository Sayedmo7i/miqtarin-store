import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/widgets/customAppbar.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customImageWidget.dart';
import 'package:eshop_plus/commons/widgets/customTextButton.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/commons/widgets/error_screen.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/core/theme/colors.dart';
import 'package:eshop_plus/ui/blog/blocs/blogCategoryCubit.dart';
import 'package:eshop_plus/ui/blog/blocs/blogCubit.dart';
import 'package:eshop_plus/ui/blog/models/blog.dart';
import 'package:eshop_plus/ui/blog/models/blogCategory.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

class BlogListScreen extends StatefulWidget {
  const BlogListScreen({Key? key}) : super(key: key);

  static Widget getRouteInstance() {
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => BlogCubit()),
        BlocProvider(create: (context) => BlogCategoryCubit()),
      ],
      child: const BlogListScreen(),
    );
  }

  @override
  BlogListScreenState createState() => BlogListScreenState();
}

class BlogListScreenState extends State<BlogListScreen> {
  final ScrollController _scrollController = ScrollController();
  int? selectedCategoryId;

  @override
  void initState() {
    super.initState();
    Future.delayed(Duration.zero, () {
      getBlogs();
      getBlogCategories();
    });

    _scrollController.addListener(() {
      if (_scrollController.position.pixels >=
          _scrollController.position.maxScrollExtent) {
        if (context.read<BlogCubit>().hasMore()) {
          loadMoreBlogs();
        }
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void getBlogs() {
    context.read<BlogCubit>().getBlogs(
      storeId: context.read<StoresCubit>().getDefaultStore().id!,
      categoryId: selectedCategoryId,
    );
  }

  void getBlogCategories() {
    context.read<BlogCategoryCubit>().getBlogCategories(
      storeId: context.read<StoresCubit>().getDefaultStore().id!,
    );
  }

  void loadMoreBlogs() {
    context.read<BlogCubit>().loadMore(
      storeId: context.read<StoresCubit>().getDefaultStore().id!,
      categoryId: selectedCategoryId,
    );
  }

  void onCategorySelected(int? categoryId) {
    setState(() {
      selectedCategoryId = categoryId;
    });
    context.read<BlogCubit>().changeCategoryFilter(
      storeId: context.read<StoresCubit>().getDefaultStore().id!,
      categoryId: categoryId,
    );
  }

  Widget buildCategoryChip(BlogCategory? category, bool isSelected) {
    return GestureDetector(
      onTap: () => onCategorySelected(category?.id),
      child: Container(
        alignment: .center,
        padding: const .symmetric(horizontal: 12, vertical: 12),
        decoration: BoxDecoration(
          color: isSelected
              ? Theme.of(context).colorScheme.primary
              : Theme.of(context).colorScheme.primaryContainer,
          borderRadius: .circular(40),
          border: Border.all(
            color: isSelected
                ? transparentColor
                : Theme.of(context).colorScheme.secondary,
            width: 1,
          ),
        ),
        child: CustomTextContainer(
          textKey: category?.name ?? allKey,
          style: Theme.of(context).textTheme.bodyMedium?.copyWith(
            fontWeight: .w400,
            height: 1.43,
            color: isSelected
                ? Theme.of(context).colorScheme.primaryContainer
                : Theme.of(context).colorScheme.secondary,
          ),
        ),
      ),
    );
  }

  Widget buildCategoriesRow() {
    return BlocBuilder<BlogCategoryCubit, BlogCategoryState>(
      builder: (context, state) {
        if (state is BlogCategoryFetchSuccess) {
          List<BlogCategory?> categories = [null, ...state.categories];

          return SizedBox(
            height: 48,
            child: ListView.separated(
              scrollDirection: .horizontal,
              padding: const .symmetric(
                horizontal: appContentHorizontalPadding,
              ),
              itemCount: categories.length,
              separatorBuilder: (context, index) => const SizedBox(width: 12),
              itemBuilder: (context, index) {
                final category = categories[index];
                final isSelected = selectedCategoryId == category?.id;
                return buildCategoryChip(category, isSelected);
              },
            ),
          );
        }
        return const SizedBox.shrink();
      },
    );
  }

  Widget buildBlogCard(Blog blog) {
    return GestureDetector(
      onTap: () {
        Utils.navigateToScreen(
          context,
          Routes.blogDetailScreen,
          arguments: {'blog': blog},
        );
      },
      child: Container(
        color: Theme.of(context).colorScheme.primaryContainer,
        margin: EdgeInsets.only(bottom: 10),
        child: Row(
          crossAxisAlignment: .start,
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.all(
                Radius.circular(borderRadius),
              ),

              child: CustomImageWidget(
                url: blog.image ?? '',
                width: 140,
                height: 80,
                boxFit: .fill,
              ),
            ),
            SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: .start,
                mainAxisAlignment: .start,
                children: [
                  if (blog.categoryName != null &&
                      blog.categoryName!.isNotEmpty)
                    Text(
                      blog.categoryName!,
                      maxLines: 1,
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(
                        color: Theme.of(
                          context,
                        ).colorScheme.secondary.withValues(alpha: 0.67),
                      ),
                    ),
                  const SizedBox(height: 4),
                  Text(
                    blog.title ?? '',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.bodyMedium,
                  ),
                  if (blog.shortDescription != null &&
                      blog.shortDescription!.isNotEmpty)
                    Text(
                      blog.shortDescription!,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(
                        color: Theme.of(
                          context,
                        ).colorScheme.secondary.withValues(alpha: 0.9),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget buildBlogsList() {
    return BlocBuilder<BlogCubit, BlogState>(
      builder: (context, state) {
        if (state is BlogFetchSuccess) {
          if (state.blogs.isEmpty) {
            return Center(
              child: Padding(
                padding: const .all(appContentHorizontalPadding),
                child: CustomTextContainer(
                  textKey: noBlogsAvailableKey,
                  style: Theme.of(context).textTheme.bodyLarge,
                ),
              ),
            );
          }

          return ListView.separated(
            controller: _scrollController,
            padding: const .all(appContentHorizontalPadding),
            itemCount:
                state.blogs.length +
                (context.read<BlogCubit>().hasMore() ? 1 : 0),
            separatorBuilder: (context, index) =>
                Divider(color: Theme.of(context).dividerColor, height: 20),
            itemBuilder: (context, index) {
              if (context.read<BlogCubit>().hasMore()) {
                if (index == state.blogs.length) {
                  if (state.fetchMoreError) {
                    return Center(
                      child: CustomTextButton(
                        buttonTextKey: retryKey,
                        onTapButton: loadMoreBlogs,
                      ),
                    );
                  }
                  return const Center(
                    child: CustomCircularProgressIndicator(
                      strokeWidth: 2,
                      widthAndHeight: 30,
                    ),
                  );
                }
              }

              return buildBlogCard(state.blogs[index]);
            },
          );
        }

        if (state is BlogFetchFailure) {
          return ErrorScreen(onPressed: getBlogs, text: state.errorMessage);
        }

        return CustomCircularProgressIndicator(
          indicatorColor: Theme.of(context).colorScheme.primary,
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.primaryContainer,
      appBar: const CustomAppbar(titleKey: blogsKey),
      body: Column(
        children: [
          const SizedBox(height: 12),
          buildCategoriesRow(),
          const SizedBox(height: 16),
          Expanded(child: buildBlogsList()),
        ],
      ),
    );
  }
}
