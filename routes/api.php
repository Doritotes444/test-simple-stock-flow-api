<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controller\AuthController;
use App\Presentation\Http\Controller\ProductController;
use App\Presentation\Http\Controller\SaleController;
use App\Presentation\Http\Controller\ReportController;
use App\Presentation\Http\Middleware\JwtMiddleware;

// Rutas Públicas
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas Protegidas (o de uso general del sistema)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
});

Route::prefix('sales')->group(function () {
    Route::post('/', [SaleController::class, 'store']);
});

Route::prefix('reports')->group(function () {
    Route::get('/sales', [ReportController::class, 'sales']);
});
