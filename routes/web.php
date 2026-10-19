<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterData\ClientSmsController;
use App\Http\Controllers\MasterData\CompanyDetailController;
use App\Http\Controllers\MasterData\ServiceController;
use App\Http\Controllers\MasterData\ServicePaymentController;
use App\Http\Controllers\MasterData\VehicleTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AutoCare System
|--------------------------------------------------------------------------
*/

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Authenticated Application Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data Module
    Route::prefix('master-data')->name('master-data.')->group(function () {
        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        // Vehicle Types
        Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicle-types.index');
        Route::get('/vehicle-types/create', [VehicleTypeController::class, 'create'])->name('vehicle-types.create');
        Route::post('/vehicle-types', [VehicleTypeController::class, 'store'])->name('vehicle-types.store');
        Route::get('/vehicle-types/{vehicleType}/edit', [VehicleTypeController::class, 'edit'])->name('vehicle-types.edit');
        Route::put('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'update'])->name('vehicle-types.update');
        Route::patch('/vehicle-types/{vehicleType}/toggle-status', [VehicleTypeController::class, 'toggleStatus'])->name('vehicle-types.toggle-status');

        // Manage Service Payment
        Route::get('/service-payments', [ServicePaymentController::class, 'index'])->name('service-payments.index');

        // Company Details
        Route::get('/company-details', [CompanyDetailController::class, 'edit'])->name('company-details.edit');
        Route::put('/company-details', [CompanyDetailController::class, 'update'])->name('company-details.update');

        // Search Client SMS
        Route::get('/client-sms', [ClientSmsController::class, 'index'])->name('client-sms.index');
    });

    // Admin-Only Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Staff Management
        Route::get('/staff', [StaffManagementController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffManagementController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffManagementController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffManagementController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffManagementController::class, 'update'])->name('staff.update');
        Route::patch('/staff/{staff}/toggle-status', [StaffManagementController::class, 'toggleStatus'])->name('staff.toggle-status');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
