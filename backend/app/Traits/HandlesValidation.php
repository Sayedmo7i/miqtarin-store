<?php

namespace App\Traits;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HandlesValidation
{
    public function HandlesValidation(Request $request, array $rules, array $messages = [], ?Closure $after = null, bool $fromApp = false)
    {
        if ($uploadErrorResponse = $this->checkFailedUploads($request, $fromApp)) {
            return $uploadErrorResponse;
        }

        // Sanitize inputs
        $sanitized = $this->sanitizeInput($request->all());
        $request->merge($sanitized);

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($after) {
            $validator->after($after);
        }

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();

            // If custom message array exists for that rule, prefer that
            if (!empty($messages)) {
                foreach ($messages as $key => $msg) {
                    $rule = explode('.', $key)[0] ?? null;
                    if ($rule && $validator->errors()->has($rule)) {
                        $firstError = $msg;
                        break;
                    }
                }
            }

            // Extract clean error message (handling potential JSON strings)
            $decoded = json_decode($firstError, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $firstField = array_key_first($decoded);
                if (isset($decoded[$firstField]) && is_array($decoded[$firstField]) && isset($decoded[$firstField][0])) {
                    $firstError = $decoded[$firstField][0];
                } elseif (isset($decoded[$firstField]) && is_string($decoded[$firstField])) {
                    $firstError = $decoded[$firstField];
                }
            }

            $errorResponse = [
                'error'   => true,
                'message' => $firstError,
                'errors'  => $validator->errors(),
                'code'    => 102,
            ];

            if ($fromApp || $request->ajax()) {
                return response()->json($errorResponse);
            }

            throw new \Illuminate\Validation\ValidationException(
                Validator::make([], []),
                response()->json($errorResponse, 422)
            );
        }

        return null;
    }

    /**
     * Strip tags and dangerous patterns from all request inputs
     */
    protected function sanitizeInput(array $inputs): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->sanitizeInput($value);
            }

            if (is_string($value)) {
                $cleanValue = trim($value);

                // Disallowed HTML/JS tags
                if (preg_match('/<\s*(script|meta|object|embed|style|base|form|input|button|textarea|svg|math)\b/i', $cleanValue)) {
                    throw new \Illuminate\Validation\ValidationException(
                        Validator::make([], []),
                        response()->json([
                            'error'   => true,
                            'message' => 'Input contains disallowed HTML/JS.',
                            'code'    => 103,
                        ], 422)
                    );
                }

                // Disallow inline JS or event handlers
                if (preg_match('/javascript:/i', $cleanValue) || preg_match('/on\w+=/i', $cleanValue)) {
                    throw new \Illuminate\Validation\ValidationException(
                        Validator::make([], []),
                        response()->json([
                            'error'   => true,
                            'message' => 'Input contains potentially unsafe content.',
                            'code'    => 104,
                        ], 422)
                    );
                }

                // Allow URLs
                if (filter_var($cleanValue, FILTER_VALIDATE_URL)) {
                    return $cleanValue;
                }

                return strip_tags($cleanValue);
            }

            return $value;
        }, $inputs);
    }

    protected function checkFailedUploads(Request $request, bool $fromApp = false)
    {
        $files = $request->allFiles();
        if (empty($files)) {
            return null;
        }

        $invalid = $this->findFirstInvalidFile($files);
        if (!$invalid) {
            return null;
        }

        [$fieldKey, $file] = $invalid;
        $baseKey = preg_replace('/\..*$/', '', $fieldKey);
        $attrKey = "validation.attributes.{$baseKey}";
        $attributeLabel = __($attrKey);
        if ($attributeLabel === $attrKey) {
            $attributeLabel = str_replace('_', ' ', $baseKey);
        }

        $reasonKey = match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE => 'validation.custom_upload_errors.ini_size',
            UPLOAD_ERR_FORM_SIZE => 'validation.custom_upload_errors.form_size',
            UPLOAD_ERR_PARTIAL => 'validation.custom_upload_errors.partial',
            UPLOAD_ERR_NO_FILE => 'validation.custom_upload_errors.no_file',
            UPLOAD_ERR_NO_TMP_DIR => 'validation.custom_upload_errors.no_tmp_dir',
            UPLOAD_ERR_CANT_WRITE => 'validation.custom_upload_errors.cant_write',
            UPLOAD_ERR_EXTENSION => 'validation.custom_upload_errors.extension',
            default => 'validation.custom_upload_errors.generic',
        };

        $message = __($reasonKey, [
            'attribute' => $attributeLabel,
            'max' => ini_get('upload_max_filesize') ?: '2M',
        ]);

        if ($message === $reasonKey) {
            $message = "Failed to upload {$attributeLabel}.";
        }

        $errorResponse = [
            'error' => true,
            'message' => $message,
            'errors' => [$baseKey => [$message]],
            'code' => 102,
        ];

        if ($fromApp || $request->ajax()) {
            return response()->json($errorResponse);
        }

        throw new \Illuminate\Validation\ValidationException(
            Validator::make([], []),
            response()->json($errorResponse, 422)
        );
    }

    protected function findFirstInvalidFile(array $files, string $prefix = ''): ?array
    {
        foreach ($files as $key => $item) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if ($item instanceof \Illuminate\Http\UploadedFile) {
                if (!$item->isValid()) {
                    return [$fullKey, $item];
                }
            } elseif (is_array($item)) {
                $found = $this->findFirstInvalidFile($item, $fullKey);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }
}
