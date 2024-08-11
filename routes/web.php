<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/api/logout', [AuthController::class, 'logout']);
    Route::get('/api/divisions', [DivisionController::class, 'index']);
    Route::get('/api/employees', [EmployeeController::class, 'index']);
    Route::put('/api/employees', [EmployeeController::class, 'store']);
    Route::put('/api/employees/{uuid}', [EmployeeController::class, 'update']);
    Route::delete('/api/employees/{uuid}', [EmployeeController::class, 'destroy']);
});
