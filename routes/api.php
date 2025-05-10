<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// M-Pesa Routes
Route::prefix('mpesa')->group(function () {
    Route::post('/stk-push', [MpesaController::class, 'initiatePayment']);
    Route::post('/callback', [MpesaController::class, 'callback']);
    Route::post('/simulate-callback', [MpesaController::class, 'simulateCallback']);
    Route::get('/receipt', [MpesaController::class, 'generateReceipt']);
    Route::get('/check-status', [MpesaController::class, 'checkStatus']);
});