<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\GoodsReceivedNoteController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\StockReportController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\SupplierPaymentController;
use App\Http\Controllers\MasterData\ClientSmsController;
use App\Http\Controllers\MasterData\CompanyDetailController;
use App\Http\Controllers\MasterData\ServiceController;
use App\Http\Controllers\MasterData\ServicePaymentController;
use App\Http\Controllers\MasterData\VehicleTypeController;
use App\Http\Controllers\Operations\DirectSaleController;
use App\Http\Controllers\Operations\InvoiceController;
use App\Http\Controllers\Operations\JobCardController;
use App\Http\Controllers\Operations\VehicleRegistrationController;
use App\Http\Controllers\Expenses\ExpenseCategoryController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Payroll\AttendanceController;
use App\Http\Controllers\Payroll\EmployeeController;
use App\Http\Controllers\Payroll\SalaryController;
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
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        // Vehicle Types
        Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicle-types.index');
        Route::get('/vehicle-types/create', [VehicleTypeController::class, 'create'])->name('vehicle-types.create');
        Route::post('/vehicle-types', [VehicleTypeController::class, 'store'])->name('vehicle-types.store');
        Route::get('/vehicle-types/{vehicleType}/edit', [VehicleTypeController::class, 'edit'])->name('vehicle-types.edit');
        Route::put('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'update'])->name('vehicle-types.update');
        Route::patch('/vehicle-types/{vehicleType}/toggle-status', [VehicleTypeController::class, 'toggleStatus'])->name('vehicle-types.toggle-status');
        Route::delete('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'destroy'])->name('vehicle-types.destroy');

        // Manage Service Payment
        Route::get('/service-payments', [ServicePaymentController::class, 'index'])->name('service-payments.index');
        Route::get('/service-payments/{service}/prices', [ServicePaymentController::class, 'edit'])->name('service-payments.edit');
        Route::put('/service-payments/{service}/prices', [ServicePaymentController::class, 'update'])->name('service-payments.update');

        // Company Details
        Route::get('/company-details', [CompanyDetailController::class, 'edit'])->name('company-details.edit');
        Route::put('/company-details', [CompanyDetailController::class, 'update'])->name('company-details.update');

        // Search Client SMS
        Route::get('/client-sms', [ClientSmsController::class, 'index'])->name('client-sms.index');
    });

    // Suppliers & Inventory Module
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Suppliers
        Route::get('/suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::patch('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Purchasing (GRN)
        Route::get('/grn', [GoodsReceivedNoteController::class, 'index'])->name('grn.index');
        Route::get('/grn/create', [GoodsReceivedNoteController::class, 'create'])->name('grn.create');
        Route::post('/grn', [GoodsReceivedNoteController::class, 'store'])->name('grn.store');
        Route::get('/grn/{grn}', [GoodsReceivedNoteController::class, 'show'])->name('grn.show');
        Route::patch('/grn/{grn}/confirm', [GoodsReceivedNoteController::class, 'confirm'])->name('grn.confirm');
        Route::get('/grn/{grn}/print', [GoodsReceivedNoteController::class, 'print'])->name('grn.print');
        Route::delete('/grn/{grn}', [GoodsReceivedNoteController::class, 'destroy'])->name('grn.destroy');

        // Supplier Payments on GRN
        Route::post('/grn/{grn}/payments', [SupplierPaymentController::class, 'store'])->name('grn.payments.store');
        Route::delete('/supplier-payments/{payment}', [SupplierPaymentController::class, 'destroy'])->name('supplier-payments.destroy');

        // Stock Report
        Route::get('/stock-report/export', [StockReportController::class, 'export'])->name('stock-report.export');
        Route::get('/stock-report', [StockReportController::class, 'index'])->name('stock-report.index');
        Route::get('/stock-report/{product}/batches', [StockReportController::class, 'batches'])->name('stock-report.batches');
        Route::get('/stock-report/{product}/price-history', [StockReportController::class, 'priceHistory'])->name('stock-report.price-history');
    });

    // Operations Module (Stage 3)
    Route::prefix('operations')->name('operations.')->group(function () {
        // Vehicle Registration
        Route::get('/vehicles', [VehicleRegistrationController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/search', [VehicleRegistrationController::class, 'search'])->name('vehicles.search');
        Route::post('/vehicles', [VehicleRegistrationController::class, 'store'])->name('vehicles.store');

        // Job Cards
        Route::get('/job-cards', [JobCardController::class, 'index'])->name('job-cards.index');
        Route::get('/job-cards/{jobCard}', [JobCardController::class, 'show'])->name('job-cards.show');
        Route::put('/job-cards/{jobCard}', [JobCardController::class, 'update'])->name('job-cards.update');
        Route::patch('/job-cards/{jobCard}/status', [JobCardController::class, 'updateStatus'])->name('job-cards.status');
        Route::post('/job-cards/{jobCard}/cancel', [JobCardController::class, 'cancel'])->name('job-cards.cancel');
        Route::post('/job-cards/{jobCard}/generate-invoice', [JobCardController::class, 'generateInvoice'])->name('job-cards.generate-invoice');
        
        // Job Card Services
        Route::get('/job-cards/{jobCard}/services/{service}/price', [JobCardController::class, 'getServicePrice'])->name('job-cards.service-price');
        Route::post('/job-cards/{jobCard}/services', [JobCardController::class, 'addService'])->name('job-cards.services.add');
        Route::delete('/job-cards/{jobCard}/services/{serviceItem}', [JobCardController::class, 'removeService'])->name('job-cards.services.remove');

        // Job Card Products
        Route::post('/job-cards/{jobCard}/products', [JobCardController::class, 'addProduct'])->name('job-cards.products.add');
        Route::delete('/job-cards/{jobCard}/products/{productItem}', [JobCardController::class, 'removeProduct'])->name('job-cards.products.remove');

        // Direct Sale (POS)
        Route::get('/direct-sales', [DirectSaleController::class, 'index'])->name('direct-sales.index');
        Route::get('/direct-sales/search-products', [DirectSaleController::class, 'searchProducts'])->name('direct-sales.search-products');
        Route::post('/direct-sales', [DirectSaleController::class, 'store'])->name('direct-sales.store');

        // Invoices & Billing
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
    });

    // Employees & Payroll Module (Stage 4)
    Route::prefix('payroll')->name('payroll.')->group(function () {
        // Employee Registration & Profiles
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

        // Employee Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        // Employee Advances
        Route::post('/advances', [SalaryController::class, 'storeAdvance'])->name('advances.store');

        // Employee Salary Management
        Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::get('/salary/{payrollItem}/sheet', [SalaryController::class, 'sheet'])->name('salary.sheet');
        Route::get('/salary/{payrollItem}/print', [SalaryController::class, 'print'])->name('salary.print');
        Route::put('/salary/{payrollItem}/adjustments', [SalaryController::class, 'updateItem'])->name('salary.update-item');
        Route::post('/salary/{payrollItem}/payments', [SalaryController::class, 'recordPayment'])->name('salary.payments.store');
        Route::post('/salary/periods/{period}/approve', [SalaryController::class, 'approve'])->name('salary.approve');
    });

    // Expense Management Module (Stage 5)
    Route::prefix('expenses')->name('expenses.')->group(function () {
        // Expense Categories
        Route::get('/categories', [ExpenseCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [ExpenseCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [ExpenseCategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}/toggle-status', [ExpenseCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        // Expense Records
        Route::get('/records', [ExpenseController::class, 'index'])->name('records.index');
        Route::get('/export', [ExpenseController::class, 'export'])->name('records.export');
        Route::get('/staff/create', [ExpenseController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [ExpenseController::class, 'storeStaff'])->name('staff.store');
        Route::get('/shop/create', [ExpenseController::class, 'createShop'])->name('shop.create');
        Route::post('/shop', [ExpenseController::class, 'storeShop'])->name('shop.store');
        Route::get('/records/{expense}', [ExpenseController::class, 'show'])->name('records.show');
        Route::get('/records/{expense}/edit', [ExpenseController::class, 'edit'])->name('records.edit');
        Route::put('/records/{expense}', [ExpenseController::class, 'update'])->name('records.update');
        Route::post('/records/{expense}/void', [ExpenseController::class, 'void'])->name('records.void');
        Route::get('/records/{expense}/receipt', [ExpenseController::class, 'downloadReceipt'])->name('records.receipt');
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
