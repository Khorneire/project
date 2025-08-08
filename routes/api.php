<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\FileUploadParserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

Route::post('/upload', FileUploadController::class);
Route::get('/persons', [DataController::class, 'getDbData']);
Route::post('/parse', [FileUploadParserController::class, 'parse']);
