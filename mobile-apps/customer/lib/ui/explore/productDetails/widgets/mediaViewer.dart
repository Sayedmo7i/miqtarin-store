import 'package:eshop_plus/core/constants/themeConstants.dart';
import 'package:eshop_plus/core/routes/routes.dart';
import 'package:eshop_plus/commons/blocs/storesCubit.dart';
import 'package:eshop_plus/commons/widgets/customImageWidget.dart';
import 'package:eshop_plus/commons/widgets/customTextContainer.dart';
import 'package:eshop_plus/utils/cardStyles.dart';
import 'package:eshop_plus/utils/utils.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';

class MediaViewer extends StatefulWidget {
  final List<String> imageUrls;
  final String? videoUrl;
  final String? videoType;


  MediaViewer(
      {required this.imageUrls,
      this.videoUrl,
      this.videoType,
     });

  @override
  _MediaViewerState createState() => _MediaViewerState();
}

class _MediaViewerState extends State<MediaViewer> {
  late bool isProductStyle1 = true;
  int _selectedIndex = 0; // Index of the currently displayed image
  late PageController _pageController;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(initialPage: _selectedIndex);
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }
String? getYoutubeVideoId(String url) {
    return YoutubePlayer.convertUrlToId(url);
  }

  String? getVimeoVideoId(String url) {
    try {
      // Extract Vimeo video ID from URL
      // Supports formats: https://vimeo.com/123456789 or https://player.vimeo.com/video/123456789
      final regex = RegExp(r'vimeo\.com\/(?:video\/)?(\d+)');
      final match = regex.firstMatch(url);
      return match?.group(1);
    } catch (e) {
      return null;
    }
  }

  String getThumbnailUrl() {
    if (widget.videoType == 'youtube') {
      String? videoId = getYoutubeVideoId(widget.videoUrl ?? '');
      if (videoId != null) {
        return "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";
      }
    } else if (widget.videoType == 'vimeo') {
      String? videoId = getVimeoVideoId(widget.videoUrl ?? '');
      if (videoId != null) {
        // Vimeo thumbnail pattern - using a common thumbnail endpoint
        return "https://vumbnail.com/$videoId.jpg";
      }
    }
    // For self-hosted videos, return a placeholder or empty string
    // You may need to add a separate thumbnail URL parameter for self-hosted videos
    return '';
  }

  @override
  Widget build(BuildContext context) {
    isProductStyle1 =    context
                  .read<StoresCubit>()
                  .getDefaultStore()
                  .storeSettings!
                  .productStyle!.toCardStyle() == CardStyle.style1 ;

    String thumbnailUrl = getThumbnailUrl();
    return Column(
      crossAxisAlignment: .center,
      mainAxisAlignment: .start,
      children: <Widget>[
        SizedBox(
          height: 200,
          child: Stack(
            clipBehavior: Clip.none,
            alignment: Alignment.bottomCenter,
            children: [
              PageView.builder(
                controller: _pageController,
                clipBehavior: Clip.none,
                onPageChanged: (index) => onChangeVideo(index),
                itemCount: widget.imageUrls.length,
                itemBuilder: (context, index) {
                  if (widget.imageUrls[_selectedIndex] == 'video' &&
                      widget.videoUrl != '') {
                    // Show video thumbnail with play icon
                    return GestureDetector(
                      onTap: () {
                        // Navigate to video player screen

                        Utils.navigateToScreen(context, Routes.videoPlayerScreen, arguments: {
                          'videoUrl': widget.videoUrl!,
                          'videoType': widget.videoType!,
                        });
                      },
                      child: Container(
                        height: 200,
                        width: MediaQuery.of(context).size.width,
                        color: Colors.black,
                        child: Stack(
                          alignment: Alignment.center,
                          children: [
                            if (thumbnailUrl.isNotEmpty)
                              CustomImageWidget(
                                url: thumbnailUrl,
                                height: 200,
                                width: MediaQuery.of(context).size.width,
                                boxFit: BoxFit.cover,
                              ),
                            Center(
                              child: Icon(
                                Icons.play_circle_outline,
                                size: 80,
                                color: Theme.of(context).colorScheme.onPrimary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  } else {
                    return GestureDetector(
                      onTap: () => Utils.navigateToScreen(
                          context, Routes.fullScreenImageScreen,
                          arguments: {
                            'imageUrl': widget.imageUrls[_selectedIndex],
                            'index': _selectedIndex,
                            'imageUrls': widget.imageUrls
                                .where((element) => element != 'video')
                                .toList(),
                          }),
                      child: Stack(
                        clipBehavior: Clip.none,
                        children: [
                          CustomImageWidget(
                            height: MediaQuery.of(context).size.width,
                            width: MediaQuery.of(context).size.width,
                            url: widget.imageUrls[index],
                            boxFit: BoxFit.contain,
                            borderRadius: borderRadius,
                          ),
                        ],
                      ),
                    );
                  }
                },
              ),
              if (isProductStyle1)
                Positioned(
                  bottom: -13,
                  child: AnimatedContainer(
                    height: 26,
                    alignment: Alignment.center,
                    duration: const Duration(seconds: 2),
                    padding: const EdgeInsetsDirectional.symmetric(
                        horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Theme.of(context).colorScheme.primary,
                      border: Border.all(
                          color: Theme.of(context).colorScheme.onPrimary,
                          width: 2),
                      borderRadius: BorderRadius.circular(100),
                    ),
                    child: CustomTextContainer(
                      textKey:
                          '${_selectedIndex + 1} / ${widget.imageUrls.length}',
                      style: Theme.of(context).textTheme.bodySmall!.copyWith(
                          color: Theme.of(context).colorScheme.onPrimary),
                    ),
                  ),
                ),
            ],
          ),
        ),
        if (!isProductStyle1)
          SizedBox(
            height: 100,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: widget.imageUrls.length,
              itemBuilder: (context, index) {
                return GestureDetector(
                  onTap: () => onChangeVideo(index),
                  child: widget.imageUrls[index] == 'video'
                      ? GestureDetector(
                        onTap: ()=>   Utils.navigateToScreen(context, Routes.videoPlayerScreen, arguments: {
                          'videoUrl': widget.videoUrl!,
                          'videoType': widget.videoType!,
                        }),
                        child: Container(
                            width: 80,
                            height: 80,
                            margin: const .all(8.0),
                            alignment: Alignment.center,
                            decoration: BoxDecoration(
                              border: Border.all(
                                color: _selectedIndex == index
                                    ? Theme.of(context).colorScheme.primary
                                    : Theme.of(context)
                                        .inputDecorationTheme
                                        .iconColor!,
                                width: _selectedIndex == index ? 1.5 : 0.5,
                              ),
                            ),
                            child: Stack(
                              alignment: Alignment.center,
                              children: [
                                if (thumbnailUrl.isNotEmpty)
                                  CustomImageWidget(
                                    url: thumbnailUrl,
                                    width: 80,
                                    height: 80,
                                    boxFit: BoxFit.cover,
                                  ),
                                Icon(Icons.play_arrow_rounded,
                                    size: 50,
                                    color: Theme.of(context).colorScheme.onPrimary),
                              ],
                            ),
                          ),
                      )
                      : Container(
                          margin: const .all(8.0),
                          decoration: BoxDecoration(
                            border: Border.all(
                              color: _selectedIndex == index
                                  ? Theme.of(context).colorScheme.primary
                                  : Theme.of(context)
                                      .inputDecorationTheme
                                      .iconColor!,
                              width: _selectedIndex == index ? 1.5 : 0.5,
                            ),
                          ),
                          child: CustomImageWidget(
                            url: widget.imageUrls[index],
                            width: 80,
                            height: 80,
                            borderRadius: 2,
                            boxFit: BoxFit.contain,
                          ),
                        ),
                );
              },
            ),
          ),
      ],
    );
  }

  onChangeVideo(int index) {
    _selectedIndex = index;
    _pageController.jumpToPage(index);
    setState(() {});
  }
}
