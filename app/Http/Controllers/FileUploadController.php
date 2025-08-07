<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FileUploadController extends Controller
{
    public function __invoke(UploadFileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $filename = now()->format('Ymd_His') . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename, 'public');

        return response()->json([
            'message' => 'File uploaded successfully!',
            'path' => Storage::url($path),
        ]);
    }
}



