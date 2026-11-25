import 'package:eshopplus_seller/commons/blocs/storesCubit.dart';
import 'package:eshopplus_seller/commons/widgets/customAppbar.dart';
import 'package:eshopplus_seller/commons/widgets/customBottomButtonContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshopplus_seller/commons/widgets/customDefaultContainer.dart';
import 'package:eshopplus_seller/commons/widgets/customRoundedButton.dart';
import 'package:eshopplus_seller/commons/widgets/customTextContainer.dart';
import 'package:eshopplus_seller/commons/widgets/errorScreen.dart';
import 'package:eshopplus_seller/core/api/apiEndPoints.dart';
import 'package:eshopplus_seller/core/localization/labelKeys.dart';
import 'package:eshopplus_seller/core/routes/routes.dart';
import 'package:eshopplus_seller/core/theme/colors.dart';
import 'package:eshopplus_seller/features/profile/addProduct/models/category.dart';
import 'package:eshopplus_seller/features/profile/addProduct/blocs/categoryListCubit.dart';
import 'package:eshopplus_seller/features/profile/categories/blocs/deleteCategoryCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';

class CategoriesListScreen extends StatefulWidget {
  const CategoriesListScreen({Key? key}) : super(key: key);

  @override
  State<CategoriesListScreen> createState() => _CategoriesListScreenState();

  static Widget getRouteInstance() {
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => CategoryListCubit()),
        BlocProvider(create: (context) => DeleteCategoryCubit()),
      ],
      child: const CategoriesListScreen(),
    );
  }
}

class _CategoriesListScreenState extends State<CategoriesListScreen> {
  @override
  void initState() {
    super.initState();
    Future.delayed(Duration.zero, () {
      context.read<CategoryListCubit>().getCategoryList(context, {
        ApiURL.storeIdApiKey: context
            .read<StoresCubit>()
            .getDefaultStore()
            .id
            .toString(),
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: CustomAppbar(titleKey: categoriesKey),
      bottomNavigationBar: CustomBottomButtonContainer(
        child: CustomRoundedButton(
          widthPercentage: 1.0,
          buttonTitle: addCategoryKey,
          showBorder: false,
          onTap: () => Utils.navigateToScreen(
            context,
            Routes.addCategoryScreen,
            arguments: context.read<CategoryListCubit>(),
          ),
        ),
      ),
      body: BlocBuilder<CategoryListCubit, CategoryListState>(
        builder: (context, state) {
          if (state is CategoryListFetchProgress) {
            return Center(child: CustomCircularProgressIndicator());
          }

          if (state is CategoryListFetchSuccess) {
            return BlocListener<DeleteCategoryCubit, DeleteCategoryState>(
              listener: (context, deletestate) {
                if (deletestate is DeleteCategorySuccess) {
                  state.requestedCategoryList?.removeWhere(
                    (element) =>
                        element.id.toString() == deletestate.categoryId,
                  );
                  context.read<CategoryListCubit>().emitSuccessState(
                    state.requestedCategoryList ?? [],
                  );
                  Utils.showSnackBar(message: deletestate.successMessage);
                  Navigator.of(context).pop();
                }
                if (deletestate is DeleteCategoryFailure) {
                  Utils.showSnackBar(message: deletestate.errorMessage);
                }
              },
              child:
                  state.categoryList.isNotEmpty ||
                      state.requestedCategoryList!.isNotEmpty
                  ? _buildCategoriesContent(
                      state.categoryList,
                      state.requestedCategoryList ?? [],
                    )
                  : ErrorScreen(onPressed: () {}, text: noCategoriesFoundKey),
            );
          }

          if (state is CategoryListFetchFailure) {
            return ErrorScreen(
              text: state.errorMessage,
              onPressed: () {
                context.read<CategoryListCubit>().getCategoryList(context, {
                  ApiURL.storeIdApiKey: context
                      .read<StoresCubit>()
                      .getDefaultStore()
                      .id
                      .toString(),
                });
              },
            );
          }
          if (state is CategoryListFetchProgress) {
            return CustomCircularProgressIndicator();
          }
          return const SizedBox.shrink();
        },
      ),
    );
  }

  Widget _buildCategoriesContent(
    List<Category> assignedCategories,
    List<Category> requestedCategories,
  ) {
    return SingleChildScrollView(
      child: Column(
        children: [
          if (assignedCategories.isNotEmpty) ...[
            _buildSectionContainer(
              assignedCategoriesKey,
              assignedCategories.length,
              _buildAssignedCategoriesList(assignedCategories),
            ),
            const SizedBox(height: 8),
          ],
          // if (requestedCategories.isNotEmpty) ...[
          _buildSectionContainer(
            requestedCategoriesKey,
            requestedCategories.length,
            _buildRequestedCategoriesList(requestedCategories),
            note: requestedCategoriesNoteKey,
          ),
          const SizedBox(height: 8),
          // ],
          if (assignedCategories.isEmpty && requestedCategories.isEmpty)
            _buildEmptyState(noCategoriesFoundNoteKey),
        ],
      ),
    );
  }

  Widget _buildSectionContainer(
    String title,
    int count,
    Widget content, {
    String? note,
  }) {
    return CustomDefaultContainer(
      child: Column(
        crossAxisAlignment: .start,
        children: [
          Row(
            mainAxisAlignment: .spaceBetween,
            children: [
              CustomTextContainer(
                textKey: title,
                style: Theme.of(context).textTheme.titleMedium,
              ),
              Container(
                padding: const .symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Theme.of(
                    context,
                  ).colorScheme.primary.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  count.toString(),
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: Theme.of(context).colorScheme.primary,
                    fontWeight: .bold,
                  ),
                ),
              ),
            ],
          ),
          if (note != null)
            Padding(
              padding: const .only(top: 8.0),
              child: CustomTextContainer(
                textKey: note,
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Theme.of(
                    context,
                  ).colorScheme.secondary.withValues(alpha: 0.5),
                ),
              ),
            ),
          const SizedBox(height: 12),
          content,
        ],
      ),
    );
  }

  Widget _buildAssignedCategoriesList(List<Category> categories) {
    return Column(
      children: categories
          .map(
            (category) => Padding(
              padding: const .only(bottom: 8),
              child: _buildAssignedCategoryItem(category),
            ),
          )
          .toList(),
    );
  }

  Widget _buildRequestedCategoriesList(List<Category> categories) {
    return Column(
      children: categories
          .map(
            (category) => Padding(
              padding: const .only(bottom: 8),
              child: _buildRequestedCategoryItem(category),
            ),
          )
          .toList(),
    );
  }

  Widget _buildAssignedCategoryItem(Category category) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(
          color: Theme.of(context).dividerColor.withValues(alpha: 0.3),
        ),
        borderRadius: BorderRadius.circular(8),
      ),
      child: ListTile(
        dense: true,
        contentPadding: const .symmetric(horizontal: 12, vertical: 4),
        leading: _buildCategoryImage(category.image),
        title: Text(
          category.name,
          style: Theme.of(
            context,
          ).textTheme.bodyMedium?.copyWith(fontWeight: .w600),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        subtitle: Row(
          children: [
            CustomTextContainer(
              textKey: commissionKey,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: greenColor,
                fontWeight: .w500,
              ),
            ),
            Text(
              ": ${category.sellerCommission ?? 0}%",
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: greenColor,
                fontWeight: .w500,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRequestedCategoryItem(Category category) {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(
          color: Theme.of(context).dividerColor.withValues(alpha: 0.3),
        ),
        borderRadius: BorderRadius.circular(8),
      ),
      child: ListTile(
        dense: true,
        contentPadding: const .symmetric(horizontal: 12, vertical: 4),
        leading: _buildCategoryImage(category.image),
        title: Text(
          category.name,
          style: Theme.of(
            context,
          ).textTheme.bodyMedium?.copyWith(fontWeight: .w600),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        subtitle: Align(
          alignment: .centerLeft,
          child: Container(
            padding: const .symmetric(horizontal: 6, vertical: 2),
            decoration: BoxDecoration(
              color: _getStatusColor(category.status).withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(4),
            ),
            child: Text(
              _getStatusText(category.status),
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: _getStatusColor(category.status),
                fontWeight: .w600,
                fontSize: 10,
              ),
            ),
          ),
        ),
        trailing: _buildCompactActionButtons(category),
      ),
    );
  }

  Widget _buildCategoryImage(String imageUrl) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(6),
      child: CachedNetworkImage(
        imageUrl: imageUrl,
        width: 40,
        height: 40,
        fit: .cover,
        placeholder: (context, url) => Container(
          width: 40,
          height: 40,
          color: greyColor[300],
          child: Icon(Icons.image, color: greyColor, size: 20),
        ),
        errorWidget: (context, url, error) => Container(
          width: 40,
          height: 40,
          color: greyColor[300],
          child: Icon(Icons.broken_image, color: greyColor, size: 20),
        ),
      ),
    );
  }

  Widget _buildCompactActionButtons(Category category) {
    return Row(
      mainAxisSize: .min,
      children: [
        IconButton(
          onPressed: () => _showDeleteConfirmation(
            context.read<DeleteCategoryCubit>(),
            category,
          ),
          icon: Icon(Icons.delete, color: redColor, size: 16),
          style: IconButton.styleFrom(
            backgroundColor: redColor.withValues(alpha: 0.1),
            padding: const .all(4),
            minimumSize: const Size(28, 28),
          ),
        ),
      ],
    );
  }

  Widget _buildEmptyState(String message) {
    return CustomDefaultContainer(
      child: Container(
        padding: const .all(24),
        child: Center(
          child: Column(
            children: [
              Icon(Icons.category_outlined, size: 40, color: greyColor[400]),
              const SizedBox(height: 12),
              Text(
                message,
                style: Theme.of(
                  context,
                ).textTheme.bodyMedium?.copyWith(color: greyColor[600]),
                textAlign: .center,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Color _getStatusColor(int? status) {
    switch (status) {
      case 1:
        return greenColor;
      case 2:
        return orangeColor;
      default:
        return redColor;
    }
  }

  String _getStatusText(int? status) {
    switch (status) {
      case 1:
        return approvedKey;
      case 2:
        return pendingKey;
      default:
        return rejectedKey;
    }
  }

  void _showDeleteConfirmation(
    DeleteCategoryCubit deleteCategoryCubit,
    Category category,
  ) {
    Utils.openAlertDialog(
      context,
      message: deleteCategoryKey,
      content: deleteCategoryConfirmationKey,
      onTapYes: () async {
        try {
          deleteCategoryCubit.deleteCategory(
            categoryId: category.id.toString(),
          );
        } catch (e) {
          Utils.showSnackBar(message: "$failedToDeleteCategoryKey: $e");
        }
      },
      onTapNo: () {
        Navigator.of(context).pop();
      },
      noLabel: cancelKey,
      yesLabel: deleteKey,
    );
  }
}
