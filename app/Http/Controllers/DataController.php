<?php

namespace App\Http\Controllers;

use App\Models\Persons;
use Illuminate\Http\JsonResponse;

class DataController extends Controller
{
    public function getDbData(): JsonResponse
    {
        return response()->json(Persons::all());
    }
}



