<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TodoController;

Route::post('/todos', [TodoController::class, 'store']);
Route::get('/todos/report/excel', [TodoController::class, 'generateExcelReport']);
Route::get('/todos/chart', [TodoController::class, 'getChartData']);
