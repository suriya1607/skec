<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    use ApiResponse;

    /** 
     * Upload a single image (logo, hero image, etc.)
     * Returns public URL stored under storage/app/public/media/
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'    => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'],
            'folder'  => ['nullable', 'string', 'in:logo,slider,gallery,general'],
        ]);

        $folder   = $request->input('folder', 'general');
        $file     = $request->file('file');
        $ext      = $file->getClientOriginalExtension();
        $filename = Str::uuid()->toString() . '.' . $ext;
        $path     = "media/{$folder}/{$filename}";

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $url = Storage::disk('public')->url($path);

        return $this->success([
            'url'      => $url,
            'path'     => $path,
            'filename' => $filename,
            'size'     => $file->getSize(),
            'mime'     => $file->getMimeType(),
        ], 'File uploaded successfully');
    }

    /**
     * Upload multiple slider images at once
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'files'   => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $results = [];
        foreach ($request->file('files') as $file) {
            $ext      = $file->getClientOriginalExtension();
            $filename = Str::uuid()->toString() . '.' . $ext;
            $path     = "media/slider/{$filename}";
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            $results[] = [
                'url'      => Storage::disk('public')->url($path),
                'path'     => $path,
                'filename' => $filename,
            ];
        }

        return $this->success($results, 'Files uploaded successfully');
    }

    /**
     * Delete a media file by path
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = $request->input('path');

        // Security: only allow deleting from media/ folder
        if (!str_starts_with($path, 'media/')) {
            return $this->error('Invalid file path.', 'invalid_path', 400);
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return $this->noContent();
    }

    /**
     * List media files in a folder
     */
    public function list(Request $request): JsonResponse
    {
        $folder = $request->input('folder', 'general');

        // Validate folder name
        if (!in_array($folder, ['logo', 'slider', 'gallery', 'general'])) {
            return $this->error('Invalid folder.', 'invalid_folder', 400);
        }

        $files = Storage::disk('public')->files("media/{$folder}");

        $result = array_map(function ($path) {
            return [
                'url'  => Storage::disk('public')->url($path),
                'path' => $path,
                'size' => Storage::disk('public')->size($path),
            ];
        }, $files);

        return $this->success($result);
    }
}