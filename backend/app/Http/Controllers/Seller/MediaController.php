<?php

namespace App\Http\Controllers\Seller;


use Imagick;
use App\Models\Media;
use App\Models\Seller;
use App\Models\StorageType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Services\StoreService;
use App\Services\MediaService;
use SimpleXMLElement;
use Illuminate\Support\Facades\Config;

class MediaController extends Controller
{

    public function index(Request $request)
    {
        $store_id = !empty(request('store_id')) ? request('store_id') : app(StoreService::class)->getStoreId();
        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');

        $search = trim($request->input('search'));
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $type = $request->input('type');

        // Create a base query to filter by search term (if provided)
        $query = Media::query();
        $query->where('store_id', $store_id);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }
        if (isset($type) and $type != '') {
            $type = explode(",", $request->input('type'));
            $where_in = $type;
        }
        if (isset($where_in) && !empty($where_in)) {
            $query->whereIn("type", $where_in);
        }

        // Get the total count before applying pagination
        $total = $query->count();

        // Apply sorting and pagination to the query
        $media = $query->orderBy($sort, $order)
            ->paginate($limit);


        if (request()->ajax()) {
            return view('seller.pages.forms.media', compact('media'));
        }
        return view('seller.pages.forms.media', compact('media'));
    }

    public function upload(Request $request)
    {
        try {
            $mediaIds = [];
            $store_id = !empty($request->input('store_id')) ? $request->input('store_id') : app(StoreService::class)->getStoreId();
            $user_id = Auth::user()->id;
            $seller_id = !empty($request->input('seller_id')) ? $request->input('seller_id') : Seller::where('user_id', $user_id)->value('id');

            $media_storage_settings = fetchDetails(StorageType::class, ['is_default' => 1], '*');
            $disk = !$media_storage_settings->isEmpty() ? $media_storage_settings[0]->name : 'public';
            $sub_directory = '/media';

            $mediaPaths = [];

            if ($request->hasFile('documents')) {
                $media = StorageType::find($media_storage_settings[0]->id ?? 1);
                $mediaFiles = $request->file('documents');

                foreach ($mediaFiles as $mediaFile) {
                    $file_extension = $mediaFile->getClientOriginalExtension();
                    $file_mime = $mediaFile->getClientMimeType();
                    $type = 'document';

                    // Determine the type based on the file MIME type
                    if (str_contains($file_mime, 'image')) {
                        $type = 'image';
                    } elseif (str_contains($file_mime, 'video')) {
                        $type = 'video';
                    }
                    // Generate and save the media file
                    $mediaItem = $media->addMedia($mediaFile)
                        ->sanitizingFileName(function ($fileName) {
                            $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                            $uniqueId = time() . '_' . mt_rand(1000, 9999);
                            $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                            $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                            return "{$baseName}-{$uniqueId}.{$extension}";
                        })
                        ->withAttributes(['seller_id' => $seller_id, 'store_id' => $store_id, 'extension' => $file_extension, 'sub_directory' => $sub_directory, 'type' => $type])
                        ->toMediaCollection('media', $disk);
                    // Get the full and relative path
                    $fullPath = (asset('storage/media/' . $mediaItem['file_name']));
                    $relativePath = rtrim($sub_directory, '/') . '/' . ltrim($mediaItem->file_name, '/');

                    // Store both paths
                    $mediaPaths[] = [
                        'full_path' => $fullPath,
                        'relative_path' => $relativePath
                    ];

                    $mediaIds[] = $mediaItem->id;
                }

                // If using S3, update with object URL
                if ($disk == 's3') {
                    $media_list = $media->getMedia('media');
                    for ($i = 0; $i < count($mediaIds); $i++) {
                        $media_url = $media_list[($media_list->count()) - (count($mediaIds) - $i)]->getUrl();
                        updateDetails(['object_url' => $media_url], ['id' => $mediaIds[$i]], Media::class);
                    }
                }

                return response()->json([
                    'error' => false,
                    'message' => labels('admin_labels.files_uploaded_successfully', 'Files Uploaded Successfully..!'),
                    'media_paths' => $mediaPaths,
                    'type' => $type,
                    'file_mime' => $mediaFile->getClientMimeType(),
                    'location' => route('seller.media')
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => labels('admin_labels.files_not_found', 'Files not found !'),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function list(Request $request)
    {
        $store_id = !empty(request('store_id')) ? request('store_id') : app(StoreService::class)->getStoreId();
        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');
        // dd($seller_id);
        $search = trim($request->input('search'));
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $limit = $request->input('limit', 10);
        // $offset = $request->input('offset', 0);
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $type = $request->input('type');

        // Create a base query to filter by search term (if provided)
        $query = Media::query();
        $query->where('store_id', $store_id);
        $query->where('seller_id', $seller_id);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }
        if (isset($type) and $type != '') {
            $type = explode(",", $request->input('type'));
            $where_in = $type;
        }
        if (isset($where_in) && !empty($where_in)) {
            $query->whereIn("type", $where_in);
        }

        // Get the total count before applying pagination
        $total = $query->count();

        // Apply sorting and pagination to the query
        $media = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();



        // Transform the data for response
        $mediaData = $media->map(function ($m) {

            $isPublicDisk = $m->disk == 'public' ? 1 : 0;

            // Generate file URL based on disk visibility
            $fileUrl = $isPublicDisk
                ? asset('storage' . '/' . $m->sub_directory . '/' . $m->file_name)
                : $m->object_url;

            $delete_url = route('seller.media.destroy', $m->id);
            $action = '<div class="dropdown bootstrap-table-dropdown">
            <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu table_dropdown" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item copy-to-clipboard">Copy To Clipboard</a>
                <a class="dropdown-item copy-relative-path">Copy Image Path</a>
                <a class="dropdown-item delete-data" data-url="' . $delete_url . '">Delete</a>
            </div>
        </div>';
            return [
                'id' => $m->id,
                'name' => $m->file_name,
                'media_image' => asset('storage/media/' . $m->file_name),
                'image' => '<div class="image-container table-image"><a href="' . $fileUrl . '" data-lightbox="image-' . $m->id . '"><img src="' . $fileUrl . '" alt="Avatar" class="rounded"/>' .
                    "<span class='path d-none'>" . $fileUrl . "</span>" .
                    "<span class='relative-path d-none'>" . '/' . $m->file_name . "</span></a></div>",
                'size' => app(MediaService::class)->formatSize($m->size),
                'extension' => $m->extension,
                'type' => $m->type,
                'sub_directory' => $m->sub_directory,
                'disk' => $m->disk,
                'object_url' => $m->object_url,
                'action' => $action,
            ];
        });

        return response()->json([
            'rows' => $mediaData,
            'total' => $total,
        ]);
    }
    public function destroy($id)
    {
        $media = Media::find($id);
        $disk = $media->disk;

        if ($media->delete()) {

            $path = 'media/' . $media->file_name; // Example path to the file you want to delete

            // Call the removeFile method to delete the file
            app(MediaService::class)->removeMediaFile($path, $disk);

            return response()->json(['error' => false, 'message' => labels('admin_labels.media_deleted_successfully', 'Media deleted successfully!')]);
        } else {
            return response()->json(['error' => true, 'message' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
        }
    }


    public function dynamic_image(Request $request)
    {
        $url = (string) $request->input('url', '');
        $width = $request->input('width', 100);
        $quality = $request->input('quality', 90);

        // Validate width and quality
        $width = intval($width) <= 0 ? 100 : intval($width);
        $quality = (intval($quality) <= 0 || intval($quality) > 100) ? 90 : intval($quality);

        try {
            if (trim($url) === '') {
                throw new Exception('Image URL is required');
            }

            $parsed = parse_url($url);
            if ($parsed === false) {
                throw new Exception('Invalid image URL');
            }

            $appHost = parse_url((string) Config::get('app.url'), PHP_URL_HOST);
            $requestHost = $request->getHost();
            $forwardedHost = $request->header('X-Forwarded-Host');
            $allowedLocalHosts = array_values(array_unique(array_filter(array_map('strtolower', [
                (string) $appHost,
                (string) $requestHost,
                (string) (is_string($forwardedHost) ? explode(':', $forwardedHost)[0] : ''),
                '127.0.0.1',
                'localhost',
                '::1',
            ]))));

            $urlHost = isset($parsed['host']) ? strtolower($parsed['host']) : '';
            if ($urlHost === '' || in_array($urlHost, $allowedLocalHosts, true)) {
                $path = rawurldecode((string) ($parsed['path'] ?? ''));
                $relativePath = ltrim($path, '/');
                if ($relativePath === '' || str_contains($relativePath, '..') || str_contains($relativePath, "\0")) {
                    throw new Exception('Local file not found');
                }

                $localPath = realpath(public_path($relativePath));
                $allowedRoots = array_filter([
                    realpath(public_path()),
                    realpath(public_path('storage')),
                    realpath(storage_path('app/public')),
                ]);

                $isInsideAllowedRoot = false;
                if ($localPath !== false && is_file($localPath)) {
                    foreach ($allowedRoots as $root) {
                        if (str_starts_with($localPath, $root . DIRECTORY_SEPARATOR)) {
                            $isInsideAllowedRoot = true;
                            break;
                        }
                    }
                }

                if (!$isInsideAllowedRoot) {
                    throw new Exception('Local file not found');
                }

                $imageData = @file_get_contents($localPath);
                if ($imageData === false) {
                    throw new Exception('Failed to read local image');
                }
            } else {
                $awsBucket = (string) (config('filesystems.disks.s3.bucket') ?: env('AWS_BUCKET', ''));
                if ($awsBucket === '' || !str_contains($url, $awsBucket)) {
                    throw new Exception('Domain is restricted');
                }

                $context = stream_context_create([
                    'http' => ['timeout' => 5],
                    'https' => ['timeout' => 5],
                ]);
                $imageData = @file_get_contents($url, false, $context);
                if ($imageData === false) {
                    throw new Exception('Failed to download image');
                }
            }

            // Determine MIME type based on file content
            $mimeType = $this->getMimeTypeFromContent($imageData);
            if (!$mimeType) {
                throw new Exception('Failed to determine MIME type');
            }

            // Handle SVG differently
            if ($mimeType === 'image/svg+xml') {
                $resizedSvgData = $this->resizeSvg($imageData, $width);
                return response()->make($resizedSvgData, 200)->header('Content-Type', 'image/svg+xml');
            }

            // Resize animated GIFs using Imagick (to preserve animation)
            if ($mimeType === 'image/gif') {
                $gif = new Imagick();
                $gif->readImageBlob($imageData);
                foreach ($gif as $frame) {
                    // Resize each frame while preserving animation
                    $frame->resizeImage($width, 0, Imagick::FILTER_LANCZOS, 1);
                }
                $gif = $gif->deconstructImages();
                $encodedImage = $gif->getImagesBlob();
            } else {
                // Resize other image types using Intervention\Image
                $image = Image::make($imageData);
                $image->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $encodedImage = $image->encode(null, $quality);
            }

            // Set the response content type based on the detected MIME type
            return response()->make($encodedImage, 200)->header('Content-Type', $mimeType);
        } catch (Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getMimeTypeFromContent($data)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $data);
        finfo_close($finfo);
        return $mimeType;
    }

    private function resizeSvg($svgData, $targetWidth = null)
    {
        $svg = new SimpleXMLElement($svgData);
        $originalWidth = (float) $svg['width'];
        $originalHeight = (float) $svg['height'];
        if (($originalWidth <= 0 || $originalHeight <= 0 || str_contains((string) $svg['width'], '%')) && !empty($svg['viewBox'])) {
            $parts = preg_split('/[\s,]+/', trim((string) $svg['viewBox']));
            if (count($parts) === 4 && (float) $parts[2] > 0 && (float) $parts[3] > 0) {
                $originalWidth = (float) $parts[2];
                $originalHeight = (float) $parts[3];
            }
        }
        if ($originalWidth <= 0 || $originalHeight <= 0) {
            return $svgData;
        }
        $targetWidth = (float) ($targetWidth ?: $originalWidth);
        $aspectRatio = $originalWidth / $originalHeight;
        $targetHeight = $targetWidth / $aspectRatio;
        $svg['width'] = $targetWidth;
        $svg['height'] = $targetHeight;
        return $svg->asXML();
    }
}
