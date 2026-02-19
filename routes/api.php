<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("/transactions")->group(function(){
    Route::post("/handle-webhook",[TransactionController::class,"getTransactions"]);
    Route::post("/send-money",[TransactionController::class,"sendTransaction"]);
});