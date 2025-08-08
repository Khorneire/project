<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Http\Services\PersonImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;
use Throwable;

class FileUploadController extends Controller
{
    public function __invoke(UploadFileRequest $request, PersonImportService $importService): JsonResponse
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['error' => 'No file uploaded.'], 400);
            }

            $file = $request->file('file');

            if (!$file->isValid()) {
                return response()->json(['error' => 'Uploaded file is not valid.'], 400);
            }

            $filename = now()->format('Ymd_His') . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            try {
                $spreadsheet = IOFactory::load($file->getPathname());
            } catch (SpreadsheetException $e) {
                Log::error('Spreadsheet load error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to read the spreadsheet. Ensure it is a valid Excel file.'],
                    422);
            }

            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                return response()->json(['error' => 'Spreadsheet is empty.'], 422);
            }

            $names = [];

            foreach ($rows as $rowIndex => $row) {
                foreach ($row as $col => $cell) {
                    if (!empty($cell)) {
                        $names[] = trim($cell);
                    }
                }
            }

            if (empty($names)) {
                return response()->json(['error' => 'No names found in the spreadsheet.'], 422);
            }

            try {
                $importService->import($names);
            } catch (Throwable $e) {
                Log::error('Import error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Failed to import names. Check log for details.'], 500);
            }

            return response()->json([
                'message' => 'File uploaded and names imported successfully!',
                'path' => Storage::url($path),
            ]);
        } catch (Throwable $e) {
            Log::error('Unexpected error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Unexpected error occurred during upload.'], 500);
        }
    }
}
