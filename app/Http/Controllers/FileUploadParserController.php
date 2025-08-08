<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;
use Illuminate\Support\Facades\Log;
use Throwable;

class FileUploadParserController extends Controller
{
    public function parse(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['error' => 'No file uploaded.'], 400);
            }

            $file = $request->file('file');
            if (!$file->isValid()) {
                return response()->json(['error' => 'Invalid file uploaded.'], 400);
            }

            try {
                $spreadsheet = IOFactory::load($file->getPathname());
            } catch (SpreadsheetException $e) {
                Log::error('Spreadsheet load error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to read spreadsheet.'], 422);
            }

            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                return response()->json(['error' => 'Spreadsheet is empty.'], 422);
            }

            $parsedNames = [];

            foreach ($rows as $row) {
                foreach ($row as $cell) {
                    $cell = trim($cell ?? '');
                    if ($cell === '') {
                        continue;
                    }

                    $people = $this->splitMultiplePeople($cell);

                    // If last word of original cell is a last name and missing in some, fill it
                    $lastWord = $this->extractLastWord($cell);
                    foreach ($people as &$p) {
                        if (empty($p['last_name']) && $lastWord) {
                            $p['last_name'] = $lastWord;
                        }
                    }
                    unset($p);

                    $parsedNames = array_merge($parsedNames, $people);
                }
            }

            return response()->json(['parsedNames' => $parsedNames]);
        } catch (Throwable $e) {
            Log::error('Unexpected parse error: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error occurred.'], 500);
        }
    }

    private function splitMultiplePeople(string $cell): array
    {
        // Split by & or 'and' but keep surname context
        $parts = preg_split('/\s*(?:&|and|,)\s*/i', $cell);
        $people = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $people[] = $this->parsePersonName($part);
            }
        }

        return $people;
    }

    private function extractLastWord(string $cell): ?string
    {
        $words = preg_split('/\s+/', trim($cell));
        if (empty($words)) {
            return null;
        }
        $last = $words[count($words) - 1];
        return preg_match('/^[A-Za-z\-]+$/', $last) ? $last : null;
    }

    private function parsePersonName(string $fullName): array
    {
        $titles = ['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof'];

        $title = null;
        $firstName = null;
        $initial = null;
        $lastName = null;

        $parts = preg_split('/\s+/', trim($fullName));

        // Title detection
        if (!empty($parts) && in_array(preg_replace('/\./', '', $parts[0]), $titles, true)) {
            $title = preg_replace('/\./', '', array_shift($parts));
        }

        if (count($parts) === 1) {
            // Either last name only or initial
            if (preg_match('/^[A-Z]\.?$/', $parts[0])) {
                $initial = rtrim($parts[0], '.');
            } else {
                $lastName = $parts[0];
            }
        } elseif (count($parts) >= 2) {
            // First could be initial or first name
            if (preg_match('/^[A-Z]\.?$/', $parts[0])) {
                $initial = rtrim($parts[0], '.');
            } else {
                $firstName = $parts[0];
            }

            $lastName = $parts[count($parts) - 1];
        }

        return [
            'title'      => $title,
            'first_name' => $firstName,
            'initial'    => $initial,
            'last_name'  => $lastName,
        ];
    }
}
