// import 'package:flutter/material.dart';
// import 'package:flutter/services.dart';
// import 'package:get/get.dart';
// import 'package:video_player/video_player.dart';
// import 'package:vimeo_player_flutter/vimeo_player_flutter.dart';
// import 'package:youtube_player_flutter/youtube_player_flutter.dart';
// import 'package:eshop_plus/core/constants/appConstants.dart';

// class VideoPlayerScreen extends StatefulWidget {
//   final String videoUrl;
//   final String videoType;

//   const VideoPlayerScreen({
//     Key? key,
//     required this.videoUrl,
//     required this.videoType,
//   }) : super(key: key);
//   static Widget getRouteInstance() {
//     final arguments = Get.arguments as Map<String, dynamic>;
//     return VideoPlayerScreen(
//       videoUrl: arguments['videoUrl'] ?? '',
//       videoType: arguments['videoType'] ?? '',
//     );    
    
//   }
//   @override
//   State<VideoPlayerScreen> createState() => _VideoPlayerScreenState();

// }

// class _VideoPlayerScreenState extends State<VideoPlayerScreen> {
//   VideoPlayerController? _videoController;
//   YoutubePlayerController? _youtubeController;
//   bool _isVideoPlaying = false;

//   @override
//   void initState() {
//     super.initState();

  

//     if (widget.videoType == selfHostedType) {
//       // Self-hosted video
//       _videoController = VideoPlayerController.networkUrl(
//         Uri.parse(widget.videoUrl),
//       )..initialize().then((_) {
//           setState(() {});
//           _videoController!.play();
//           _isVideoPlaying = true;
//         });
//     } else if (widget.videoType == youtubeVideoType) {
//       // YouTube video
//       _youtubeController = YoutubePlayerController(
//         initialVideoId: YoutubePlayer.convertUrlToId(widget.videoUrl) ?? '',
//         flags: const YoutubePlayerFlags(
//           autoPlay: true,
//           mute: false,
//           hideControls: false,
//           controlsVisibleAtStart: true,
//           enableCaption: true,
//           loop: false,
//         ),
//       );
//     }
//   }

//   @override
//   void dispose() {
//     _videoController?.dispose();
//     _youtubeController?.dispose();

//     // Restore portrait orientation
//     SystemChrome.setPreferredOrientations([
//       DeviceOrientation.portraitUp,
//     ]);
//     // SystemChrome.setEnabledSystemUIMode(
//     //   SystemUiMode.manual,
//     //   overlays: SystemUiOverlay.values,
//     // );
//     super.dispose();
//   }

//   @override
//   Widget build(BuildContext context) {
//     return PopScope(
//       onPopInvokedWithResult: (didPop, result) async {
//         if (didPop) {
//           await SystemChrome.setPreferredOrientations([
//             DeviceOrientation.portraitUp,
//           ]);
//           // await SystemChrome.setEnabledSystemUIMode(
//           //   SystemUiMode.manual,
//           //   overlays: SystemUiOverlay.values,
//           // );
//         }
//       },
//       child: YoutubePlayerBuilder(
//         onExitFullScreen: () {
//           // The player forces portraitUp after exiting fullscreen. This overrides the behaviour.
//           SystemChrome.setPreferredOrientations(DeviceOrientation.values);
//         },
//         onEnterFullScreen: () => SystemChrome.setPreferredOrientations([
//           DeviceOrientation.landscapeLeft,
//           DeviceOrientation.landscapeRight,
//         ]),
//         player: buildYoutubuePlayer(),
//         builder: (context, player)  {
//           return Scaffold(
//             backgroundColor: Colors.black,
//             body: Stack(
//               children: [
//                 Center(
//                   child: _buildVideoPlayer(),
//                 ),
//                 // Close button
//                 Positioned(
//                   top: 40,
//                   right: 16,
//                   child: IconButton(
//                     icon: const Icon(
//                       Icons.close,
//                       color: Colors.white,
//                       size: 30,
//                     ),
//                     onPressed: () {
//                       Navigator.of(context).pop();
//                     },
//                   ),
//                 ),
//               ],
//             ),
//           );
//         }
//       ),
//     );
//   }

//   Widget _buildVideoPlayer() {
//     if (widget.videoType == selfHostedType) {
//       // Self-hosted video player
//       if (_videoController != null && _videoController!.value.isInitialized) {
//         return GestureDetector(
//           onTap: () {
//             setState(() {
//               _isVideoPlaying
//                   ? _videoController!.pause()
//                   : _videoController!.play();
//               _isVideoPlaying = !_isVideoPlaying;
//             });
//           },
//           child: Stack(
//             alignment: Alignment.center,
//             children: [
//               AspectRatio(
//                 aspectRatio: _videoController!.value.aspectRatio,
//                 child: VideoPlayer(_videoController!),
//               ),
//               // Play/Pause overlay
//               if (!_isVideoPlaying)
//                 const Icon(
//                   Icons.play_circle_outline,
//                   size: 80,
//                   color: Colors.white,
//                 ),
//               // Progress indicator
//               Positioned(
//                 bottom: 0,
//                 left: 0,
//                 right: 0,
//                 child: VideoProgressIndicator(
//                   _videoController!,
//                   allowScrubbing: true,
//                   colors: VideoProgressColors(
//                     playedColor: Theme.of(context).colorScheme.primary,
//                     bufferedColor: Colors.grey,
//                     backgroundColor: Colors.white24,
//                   ),
//                 ),
//               ),
//             ],
//           ),
//         );
//       } else {
//         return const CircularProgressIndicator(color: Colors.white);
//       }
//     } else if (widget.videoType == youtubeVideoType) {
//       // YouTube video player
//       return buildYoutubuePlayer();
//     } else if (widget.videoType == 'vimeo') {
//       // Vimeo video player
//       final videoId = widget.videoUrl.split('https://vimeo.com/')[1];
//       return VimeoPlayer(
//         videoId: videoId,
//       );
//     }

//     return const SizedBox.shrink();
//   }

//   YoutubePlayer buildYoutubuePlayer() {
//     return YoutubePlayer(
//       controller: _youtubeController!,
//       showVideoProgressIndicator: true,
//       progressIndicatorColor: Theme.of(context).colorScheme.primary,
//       progressColors: ProgressBarColors(
//         playedColor: Theme.of(context).colorScheme.primary,
//         handleColor: Theme.of(context).colorScheme.primary,
//         backgroundColor: Colors.white24,
//       ),
//     );
//   }
// }
// video_player_screen.dart
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:vimeo_player_flutter/vimeo_player_flutter.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
// controller/app_video_controller.dart
import 'package:flutter/services.dart';
// players/app_self_hosted_player.dart
import 'package:video_player/video_player.dart';

class VideoPlayerScreen extends StatefulWidget {
  final String videoUrl;
  final String videoType;

  const VideoPlayerScreen({
    super.key,
    required this.videoUrl,
    required this.videoType,
  });

  static Widget getRouteInstance() {
    final args = Get.arguments as Map<String, dynamic>;
    return VideoPlayerScreen(
      videoUrl: args['videoUrl'],
      videoType: args['videoType'],
    );
  }

  @override
  State<VideoPlayerScreen> createState() => _VideoPlayerScreenState();
}

class _VideoPlayerScreenState extends State<VideoPlayerScreen> {


  @override
  void initState() {
    super.initState();
    if (widget.videoType == VideoType.youtube) {
     
    }
  }

  @override
  void dispose() {

    AppVideoController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // if (widget.videoType == VideoType.youtube) {
    //   return Scaffold(
    //     backgroundColor: Colors.black,
    //     body: AppYoutubePlayer(videoUrl: widget.videoUrl,),
    //   );
    // }

    // Non-YouTube videos
    return SafeArea(
      child: Scaffold(
        backgroundColor: Colors.black,
        body: Stack(
          children: [
            Center(child: _buildPlayer()),
             Positioned(
              top: 40,
              right: 16,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 30),
                onPressed: () => Get.back(),
              ),
            ),
          ],
        ),
      ),
    );
  }
   
  

  Widget _buildPlayer() {
    switch (widget.videoType) {
      case VideoType.youtube:
        return AppYoutubePlayer(videoUrl: widget.videoUrl);
      case VideoType.vimeo:
        return AppVimeoPlayer(url: widget.videoUrl);
      default:
        return AppSelfHostedPlayer(url: widget.videoUrl);
    }
  }
}


class AppVimeoPlayer extends StatelessWidget {
  final String url;

  const AppVimeoPlayer({super.key, required this.url});

  @override
  Widget build(BuildContext context) {
    final id = url.split('vimeo.com/').last;
    return VimeoPlayer(videoId: id);
  }
}



class AppSelfHostedPlayer extends StatefulWidget {
  final String url;

  const AppSelfHostedPlayer({super.key, required this.url});

  @override
  State<AppSelfHostedPlayer> createState() => _AppSelfHostedPlayerState();
}

class _AppSelfHostedPlayerState extends State<AppSelfHostedPlayer> {
  late final VideoPlayerController _controller;
  bool _isPlaying = true;
  bool _showControls = true;
  bool _isFullscreen = false;

  @override
  void initState() {
    super.initState();
    _controller = VideoPlayerController.networkUrl(Uri.parse(widget.url),)
      ..initialize().then((_) {
        setState(() {});
        _controller.play();
      });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _toggleFullscreen() {
    setState(() {
      _isFullscreen = !_isFullscreen;
    });

    if (_isFullscreen) {
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersive);
      SystemChrome.setPreferredOrientations([
        DeviceOrientation.landscapeLeft,
        DeviceOrientation.landscapeRight,
      ]);
    } else {
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
      SystemChrome.setPreferredOrientations([
        DeviceOrientation.portraitUp,
      ]);
    }
  }

  void _togglePlayPause() {
    setState(() {
      _isPlaying ? _controller.pause() : _controller.play();
      _isPlaying = !_isPlaying;
      _showControls = true;
    });
  }

  void _toggleControls() {
    setState(() {
      _showControls = !_showControls;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!_controller.value.isInitialized) {
      return const CircularProgressIndicator(color: Colors.white);
    }

    return GestureDetector(
      onTap: _toggleControls,
      child: Stack(
        alignment: Alignment.center,
        children: [
          AspectRatio(
            aspectRatio: _controller.value.aspectRatio,
            child: VideoPlayer(_controller),
          ),

          // Play/Pause icon in center
          if (_showControls && !_isPlaying)
            GestureDetector(
              onTap: _togglePlayPause,
              child: const Icon(Icons.play_circle, size: 80, color: Colors.white),
            ),

          // Bottom controls
          if (_showControls)
            Positioned(
              bottom: 0,
              left: 0,
              right: 0,
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.bottomCenter,
                    end: Alignment.topCenter,
                    colors: [
                      Colors.black.withValues(alpha: 0.7),
                      Colors.transparent,
                    ],
                  ),
                ),
                padding: const .symmetric(horizontal: 8, vertical: 4),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Progress indicator
                    VideoProgressIndicator(
                      _controller,
                      allowScrubbing: true,
                      colors: VideoProgressColors(
                        playedColor: Colors.red,
                        bufferedColor: Colors.grey,
                        backgroundColor: Colors.white24,
                      ),
                    ),
                    const SizedBox(height: 4),
                    // Controls row
                    Row(
                      children: [
                        // Play/Pause button
                        IconButton(
                          icon: Icon(
                            _isPlaying ? Icons.pause : Icons.play_arrow,
                            color: Colors.white,
                          ),
                          onPressed: _togglePlayPause,
                        ),
                        // Time indicator
                        Text(
                          _formatDuration(_controller.value.position),
                          style: const TextStyle(color: Colors.white, fontSize: 12),
                        ),
                        Text(
                          ' / ${_formatDuration(_controller.value.duration)}',
                          style: const TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                        const Spacer(),
                        // Fullscreen button
                        IconButton(
                          icon: Icon(
                            _isFullscreen ? Icons.fullscreen_exit : Icons.fullscreen,
                            color: Colors.white,
                          ),
                          onPressed: _toggleFullscreen,
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }

  String _formatDuration(Duration duration) {
    String twoDigits(int n) => n.toString().padLeft(2, '0');
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    final seconds = duration.inSeconds.remainder(60);

    if (hours > 0) {
      return '$hours:${twoDigits(minutes)}:${twoDigits(seconds)}';
    }
    return '${twoDigits(minutes)}:${twoDigits(seconds)}';
  }
}


class AppYoutubePlayer extends StatefulWidget {
  final String videoUrl;

  const AppYoutubePlayer({super.key, required this.videoUrl});

  @override
  State<AppYoutubePlayer> createState() => _AppYoutubePlayerState();
}

class _AppYoutubePlayerState extends State<AppYoutubePlayer> {
  late YoutubePlayerController _youtubeController;

  @override
  void initState() {
    super.initState();

    _youtubeController = YoutubePlayerController(
      initialVideoId: YoutubePlayer.convertUrlToId(widget.videoUrl) ?? '',
      flags: const YoutubePlayerFlags(
        autoPlay: true,
        disableDragSeek: false,
        enableCaption: false,
        loop: false,
        mute: false,
        useHybridComposition: true,
      ),
    );

    // _youtubeController.addListener(_youtubeListener);
  }

 

  @override
  void dispose() {
    // _youtubeController.removeListener(_youtubeListener);
    _youtubeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return YoutubePlayerBuilder(
      player: YoutubePlayer(
        controller: _youtubeController,
        showVideoProgressIndicator: true,
      ),
      builder: (context, player) {
        return player; // ❗ no wrapper
      },
    );
  }
}


class AppVideoController {
  static void enterFullscreen() {
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersive);
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.landscapeLeft,
      DeviceOrientation.landscapeRight,
    ]);
  }

  static void exitFullscreen() {
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
    ]);
  }

  static void dispose() {
    exitFullscreen();
  }
}
// utils/video_type.dart
class VideoType {
  static const selfHosted = 'self_hosted';
  static const youtube = 'youtube';
  static const vimeo = 'vimeo';
}