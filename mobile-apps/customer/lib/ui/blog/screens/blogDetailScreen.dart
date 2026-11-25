import 'package:eshop_plus/commons/blocs/settingsAndLanguagesCubit.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/widgets/customAppbar.dart';
import 'package:eshop_plus/commons/widgets/customCircularProgressIndicator.dart';
import 'package:eshop_plus/commons/widgets/customImageWidget.dart';
import 'package:eshop_plus/core/configs/appConfig.dart';
import 'package:eshop_plus/core/constants/appConstants.dart';
import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/localization/labelKeys.dart';
import 'package:eshop_plus/ui/blog/blocs/blogCubit.dart';
import 'package:eshop_plus/ui/blog/models/blog.dart';
import 'package:eshop_plus/utils/designConfig.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_widget_from_html/flutter_widget_from_html.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:share_plus/share_plus.dart';

class BlogDetailScreen extends StatefulWidget {
  final Blog? blog;
  final String? blogSlug;
  final String? storeSlug;

  const BlogDetailScreen({Key? key, this.blog, this.blogSlug, this.storeSlug})
    : super(key: key);

  static Widget getRouteInstance() {
    Map<String, dynamic> arguments = Get.arguments ?? {};
    return BlocProvider(
      create: (context) => BlogCubit(),
      child: BlogDetailScreen(
        blog: arguments['blog'] as Blog?,
        blogSlug: arguments['blogSlug'] as String?,
        storeSlug: arguments['storeSlug'] as String?,
      ),
    );
  }

  @override
  BlogDetailScreenState createState() => BlogDetailScreenState();
}

class BlogDetailScreenState extends State<BlogDetailScreen> {
  Blog? _blog;

  bool _isSharing = false;


  @override
  void initState() {
    super.initState();
    if (widget.blog != null) {
      _blog = widget.blog;
    } else if (widget.blogSlug != null) {
      context.read<BlogCubit>().getBlogs(
        storeSlug:
            widget.storeSlug ??
            context.read<StoresCubit>().getDefaultStore().slug!,
        blogSlug: widget.blogSlug!,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: CustomAppbar(
        titleKey: blogDetailsKey,
        trailingWidget: _shareButton(),
      ),
      backgroundColor: Theme.of(context).colorScheme.primaryContainer,
      body: Padding(
        padding: const .symmetric(
          horizontal: appContentHorizontalPadding,
          vertical: 12,
        ),
        child: _buildContent(),
      ),
    );
  }

  Widget _buildContent() {
    return BlocConsumer<BlogCubit, BlogState>(
      listener: (context, state) {
        if (state is BlogFetchSuccess) {
          _blog = state.blogs.first;
        }
      },
      builder: (context, state) {
        if (state is BlogFetchInProgress) {
          return Center(
            child: CustomCircularProgressIndicator(
              indicatorColor: Theme.of(context).colorScheme.primary,
            ),
          );
        }
        return SingleChildScrollView(
          child: Column(
            crossAxisAlignment: .start,
            children: [
              _buildHeader(),
              DesignConfig.defaultHeightSizedBox,
              _buildMetadata(),
              DesignConfig.defaultHeightSizedBox,
              Text(
                _blog?.title ?? '',
                style: Theme.of(context).textTheme.titleSmall,
              ),
              const SizedBox(height: 12),
              if (_blog?.description != null && _blog!.description!.isNotEmpty)
                HtmlWidget(
                  _blog!.description!,
                  textStyle: Theme.of(
                    context,
                  ).textTheme.bodyMedium?.copyWith(height: 1.6),
                ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildHeader() {
    if (_blog?.image == null || _blog!.image!.isEmpty) {
      return const SizedBox.shrink();
    }

    return CustomImageWidget(
      url: _blog!.image!,
      width: double.infinity,
      height: 200,
      boxFit: BoxFit.fill,
      borderRadius: 8,
    );
  }

  Widget _buildMetadata() {
    if (_blog?.createdAt == null) return const SizedBox.shrink();

    return Text(
      DateFormat('MMMM dd, yyyy').format(DateTime.parse(_blog!.createdAt!)),
      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
        color: Theme.of(context).colorScheme.secondary.withValues(alpha: 0.67),
      ),
    );
  }

  _shareButton() {
    return Builder(
      builder: (buttonContext) {
        return IconButton(
          icon: Icon(
            Icons.share,
            color: Theme.of(context).colorScheme.secondary,
          ),
          onPressed: () async {
            if (_isSharing) return;
            _isSharing = true;
            try {
              final String blogUrl =
                  '$baseUrl/$blogDeeplink/${_blog!.slug}?store=${_blog!.storeSlug}';
              final box = buttonContext.findRenderObject() as RenderBox?;
              await SharePlus.instance.share(
                ShareParams(
                  text:
                      '${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: shareThisBlogKey)} : $blogUrl',
                  subject:
                      "${context.read<SettingsAndLanguagesCubit>().getTranslatedValue(labelKey: '${_blog!.title}')}: $blogUrl",
                  sharePositionOrigin: box != null
                      ? box.localToGlobal(Offset.zero) & box.size
                      : null,
                ),
              );
            } finally {
              _isSharing = false;
            }
          },
        );
      },
    );
  }
}
