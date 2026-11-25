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
import 'package:eshopplus_seller/features/profile/brands/blocs/deleteBrandCubit.dart';
import 'package:eshopplus_seller/features/profile/brands/models/brand.dart';
import 'package:eshopplus_seller/features/profile/brands/blocs/brandListCubit.dart';
import 'package:eshopplus_seller/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';

class BrandsListScreen extends StatefulWidget {
  const BrandsListScreen({Key? key}) : super(key: key);

  @override
  State<BrandsListScreen> createState() => _BrandsListScreenState();

  static Widget getRouteInstance() {
    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (context) => BrandListCubit()),
        BlocProvider(create: (context) => DeleteBrandCubit()),
      ],
      child: const BrandsListScreen(),
    );
  }
}

class _BrandsListScreenState extends State<BrandsListScreen> {
  @override
  void initState() {
    super.initState();
    Future.delayed(Duration.zero, () {
      context.read<BrandListCubit>().getBrandList(context, {
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
      appBar: CustomAppbar(titleKey: brandsKey),
      bottomNavigationBar: CustomBottomButtonContainer(
        child: CustomRoundedButton(
          widthPercentage: 1.0,
          buttonTitle: addBrandKey,
          showBorder: false,
          onTap: () => Utils.navigateToScreen(
            context,
            Routes.addBrandScreen,
            arguments: context.read<BrandListCubit>(),
          ),
        ),
      ),
      body: BlocBuilder<BrandListCubit, BrandListState>(
        builder: (context, state) {
          if (state is BrandListFetchProgress) {
            return Center(child: CustomCircularProgressIndicator());
          }

          if (state is BrandListFetchSuccess) {
            return BlocListener<DeleteBrandCubit, DeleteBrandState>(
              listener: (context, deletestate) {
                if (deletestate is DeleteBrandSuccess) {
                  state.requestedBrandList?.removeWhere(
                    (element) => element.id.toString() == deletestate.brandId,
                  );
                  context.read<BrandListCubit>().emitSuccessState(
                    state.requestedBrandList ?? [],
                  );
                  Utils.showSnackBar(message: deletestate.message);
                  Navigator.of(context).pop();
                }
                if (deletestate is DeleteBrandFailure) {
                  Navigator.of(context).pop();
                  Utils.showSnackBar(message: deletestate.errorMessage);
                }
              },
              child: state.requestedBrandList!.isNotEmpty
                  ? _buildBrandsContent(
                      state.brandList,
                      state.requestedBrandList ?? [],
                    )
                  : ErrorScreen(onPressed: () {}, text: noRequestedBrandsKey),
            );
          }

          if (state is BrandListFetchFailure) {
            return ErrorScreen(
              text: state.errorMessage,
              onPressed: () {
                context.read<BrandListCubit>().getBrandList(context, {
                  ApiURL.storeIdApiKey: context
                      .read<StoresCubit>()
                      .getDefaultStore()
                      .id
                      .toString(),
                });
              },
            );
          }

          return const SizedBox.shrink();
        },
      ),
    );
  }

  Widget _buildBrandsContent(
    List<Brand> assignedBrands,
    List<Brand> requestedBrands,
  ) {
    return SingleChildScrollView(
      child: Column(
        children: [
          // if (assignedBrands.isNotEmpty) ...[
          //   _buildSectionContainer(assignedBrandsKey, assignedBrands.length, _buildAssignedBrandsList(assignedBrands)),
          //   const SizedBox(height: 8),
          // ],
          if (requestedBrands.isNotEmpty) ...[
            _buildSectionContainer(
              requestedBrandsKey,
              requestedBrands.length,
              _buildRequestedBrandsList(requestedBrands),
              note: requestedBrandsNoteKey,
            ),
            const SizedBox(height: 8),
          ],
          if (assignedBrands.isEmpty && requestedBrands.isEmpty)
            _buildEmptyState(noBrandsFoundNoteKey),
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

  Widget _buildRequestedBrandsList(List<Brand> brands) {
    return Column(
      children: brands
          .map(
            (brand) => Padding(
              padding: const .only(bottom: 8),
              child: _buildRequestedBrandItem(brand),
            ),
          )
          .toList(),
    );
  }

  Widget _buildRequestedBrandItem(Brand brand) {
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
        leading: _buildBrandImage(brand.image ?? ''),
        title: Text(
          brand.name ?? '',
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
              color: _getStatusColor(brand.status).withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(4),
            ),
            child: Text(
              _getStatusText(brand.status),
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: _getStatusColor(brand.status),
                fontWeight: .w600,
                fontSize: 10,
              ),
            ),
          ),
        ),
        trailing: _buildCompactActionButtons(brand),
      ),
    );
  }

  Widget _buildBrandImage(String imageUrl) {
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

  Widget _buildCompactActionButtons(Brand brand) {
    return Row(
      mainAxisSize: .min,
      children: [
        IconButton(
          onPressed: () =>
              _showDeleteConfirmation(context.read<DeleteBrandCubit>(), brand),
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
              Icon(
                Icons.branding_watermark_outlined,
                size: 40,
                color: greyColor[400],
              ),
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

  void _showDeleteConfirmation(DeleteBrandCubit deleteBrandCubit, Brand brand) {
    Utils.openAlertDialog(
      context,
      message: deleteBrandKey,
      content: deleteBrandConfirmationKey,
      onTapYes: () async {
        try {
          deleteBrandCubit.deleteBrand(brandId: brand.id.toString());
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
