<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
     return $request->user();
});

Route::post('login', [AuthController::class, 'apiLogin']);
Route::middleware('jwt.auth')->post('logout', [AuthController::class, 'apiLogout']);

Route::middleware('jwt.auth')->apiResource('daily-activities', DailyActivityController::class);

// File access routes with JWT authentication
Route::middleware('jwt.auth')->group(function () {
     Route::get('files/employee-photos/{filename}', [FileController::class, 'getEmployeePhoto']);
     Route::get('files/daily-activity-attachments/{filename}', [FileController::class, 'getDailyActivityAttachment']);
     Route::get('files/attendance-photos/{filename}', [FileController::class, 'getAttendancePhoto']);
     Route::get('files/correction-attachments/{filename}', [FileController::class, 'getCorrectionAttachment']);
});
