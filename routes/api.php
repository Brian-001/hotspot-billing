<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/mpesa/pay', [MpesaController::class, 'initiatePayment']);
Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback']);