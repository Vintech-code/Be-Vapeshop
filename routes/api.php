<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\DashboardController;

//Dashboard
Route::get('/dashboard/product-stats', [DashboardController::class, 'productStats']);
Route::get('/dashboard/recent-products', [DashboardController::class, 'recentProducts']);
Route::get('/dashboard/category-counts', [DashboardController::class, 'categoryCounts']);
Route::get('/dashboard/stock-history', [DashboardController::class, 'stockHistory']);



//Login
Route::post('/login', [AuthController::class, 'login']);

//Products
Route::apiResource('products', ProductController::class);


//History
Route::apiResource('purchases', PurchaseController::class);


//Test
Route::get('/test-cors', function () {
    return response()->json(['message' => 'CORS is working!']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

