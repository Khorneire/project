<?php

namespace App\Http\Services;

use App\Models\Persons;

class PersonImportService
{
    public function __construct(
        private readonly NameParser $parser = new NameParser()
    ) {}

    public function import(array $names): void
    {
        foreach ($names as $fullName) {
            $split = $this->parser->splitNames($fullName);

            foreach ($split as $singleName) {
                $parsed = $this->parser->parseName($singleName);
                if ($parsed) {
                    Persons::create($parsed);
                }
            }
        }
    }
}
