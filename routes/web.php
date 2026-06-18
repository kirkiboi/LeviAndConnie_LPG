<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;

// ── Public / Auth routes ──────────────────────────────────────────────────────
Route::get('/',       [LoginController::class, 'showLogin'])->name('login');
Route::get('/login',  [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth.employee')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::get('/pos',           [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Inventory
    Route::get('/inventory',            [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements',  [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::post('/inventory/restock',   [InventoryController::class, 'restock'])->name('inventory.restock');

    // Employee profile (own)
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::post('/session/timeout', [EmployeeController::class, 'timeout'])->name('session.timeout');

    // ── Owner-only routes ─────────────────────────────────────────────────────
    Route::middleware('auth.owner')->group(function () {

        // Products / Menu & Pricing
        Route::get('/products',                  [ProductController::class, 'index'])->name('products.index');
        Route::post('/products',                 [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}',        [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/toggle',[ProductController::class, 'toggleActive'])->name('products.toggle');

        // Employees
        Route::get('/employees',                   [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/sessions',          [EmployeeController::class, 'sessions'])->name('employees.sessions');
        Route::post('/employees',                  [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{employee}',        [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{employee}/toggle',[EmployeeController::class, 'toggleActive'])->name('employees.toggle');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});
