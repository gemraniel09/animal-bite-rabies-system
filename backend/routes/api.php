<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['status' => 'success', 'message' => 'Hello World']);
});
Route::post('/test', function () {
    return response()->json(['status' => 'success', 'message' => 'Hello World']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);


Route::middleware([AdminMiddleware::class])->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/animals', AnimalController::class);
    Route::apiResource('/brands', BrandController::class);
    Route::get('/transaction/exportCsv', [TransactionController::class, 'exportExcel']);
    Route::get('/patient/exportCsv', [PatientController::class, 'exportExcel']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/animals', [AnimalController::class, 'index']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::resource('/patients', PatientController::class);
    Route::get('/patient/{id}', [PatientController::class, 'show']);

    //Transaction Route
    Route::apiResource('/transactions', TransactionController::class);

    Route::apiResource('/transactions/schedules', TransactionScheduleController::class);


    Route::get('/barangays', [BarangayController::class, 'fetch']);
});

Route::get('/predictCase', [TransactionController::class, 'predictCase']);
Route::get('/month6Cases', [TransactionController::class, 'month6Cases']);
Route::get('/getCounts', [TransactionController::class, 'getCounts']);
Route::get('/getOnlyAnimalsCount', [TransactionController::class, 'getOnlyAnimalsCount']);
Route::get('/getTop10BrangayBase6Month', [TransactionController::class, 'getTop10BrangayBase6Month']);
