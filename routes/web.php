<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PrinterSettingController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Billing\AvoidDirectSaleController;
use App\Http\Controllers\Billing\AvoidServiceSaleController;
use App\Http\Controllers\Billing\InvoiceDateChangeController;
use App\Http\Controllers\Billing\MultipleInvoicePaymentController;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\QuotationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Expenses\ExpenseCategoryController;
use App\Http\Controllers\Expenses\ExpenseController;
use App\Http\Controllers\Inventory\GoodsReceivedNoteController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\StockReportController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\SupplierPaymentController;
use App\Http\Controllers\MasterData\BranchController;
use App\Http\Controllers\MasterData\ClientSmsController;
use App\Http\Controllers\MasterData\CompanyDetailController;
use App\Http\Controllers\MasterData\ServiceController;
use App\Http\Controllers\MasterData\ServicePaymentController;
use App\Http\Controllers\MasterData\VehicleTypeController;
use App\Http\Controllers\Operations\AppointmentController;
use App\Http\Controllers\Operations\CompanyVehicleController;
use App\Http\Controllers\Operations\CustomerController;
use App\Http\Controllers\Operations\DirectSaleController;
use App\Http\Controllers\Operations\InvoiceController;
use App\Http\Controllers\Operations\JobCardController;
use App\Http\Controllers\Operations\JobIssueController;
use App\Http\Controllers\Operations\VehicleRegistrationController;
use App\Http\Controllers\Payroll\AttendanceController;
use App\Http\Controllers\Payroll\AttendanceReminderController;
use App\Http\Controllers\Payroll\BonusAllowanceController;
use App\Http\Controllers\Payroll\CompanyHolidayController;
use App\Http\Controllers\Payroll\EmployeeController;
use App\Http\Controllers\Payroll\SalaryAdvanceController;
use App\Http\Controllers\Payroll\SalaryController;
use App\Http\Controllers\Payroll\StatutoryReportController;
use App\Http\Controllers\Reports\CreditManagementController;
use App\Http\Controllers\Reports\MoneyBookController;
use App\Http\Controllers\Reports\ProfitLossController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Reports\StockReportController as Stage7StockReportController;
use App\Http\Controllers\Reports\StockValueReportController;
use App\Http\Controllers\Reports\VehicleServiceReportController;
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

    // Appointments Module
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/events', [AppointmentController::class, 'events'])->name('appointments.events');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{appointment}/arrive', [AppointmentController::class, 'markArrived'])->name('appointments.arrive');
    Route::post('/appointments/{appointment}/create-job-card', [AppointmentController::class, 'createJobCard'])->name('appointments.create-job-card');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

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
        Route::post('/vehicle-types/carpet-categories', [VehicleTypeController::class, 'storeCarpetCategory'])->name('vehicle-types.carpet-categories.store');
        Route::delete('/vehicle-types/carpet-categories/{carpetCategory}', [VehicleTypeController::class, 'destroyCarpetCategory'])->name('vehicle-types.carpet-categories.destroy');

        // Manage Service Payment
        Route::get('/service-payments', [ServicePaymentController::class, 'index'])->name('service-payments.index');
        Route::get('/service-payments/{service}/prices', [ServicePaymentController::class, 'edit'])->name('service-payments.edit');
        Route::put('/service-payments/{service}/prices', [ServicePaymentController::class, 'update'])->name('service-payments.update');

        // Company Details
        Route::get('/company-details', [CompanyDetailController::class, 'edit'])->name('company-details.edit');
        Route::put('/company-details', [CompanyDetailController::class, 'update'])->name('company-details.update');

        // Branches Management
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::patch('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
        Route::patch('/branches/{branch}/set-main', [BranchController::class, 'setMain'])->name('branches.set-main');

        // Search Client SMS
        Route::get('/client-sms', [ClientSmsController::class, 'index'])->name('client-sms.index');
    });

    // Global Branch Selector Switch Route
    Route::post('/branches/switch', [BranchController::class, 'switchBranch'])->name('branches.switch');

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
        Route::post('/supplier-payments/{payment}/reverse', [SupplierPaymentController::class, 'reverse'])->name('supplier-payments.reverse');
        Route::get('/supplier-payments/{payment}/receipt', [SupplierPaymentController::class, 'receipt'])->name('supplier-payments.receipt');
        Route::delete('/supplier-payments/{payment}', [SupplierPaymentController::class, 'destroy'])->name('supplier-payments.destroy');

        // Stock Report
        Route::get('/stock-report/export', [StockReportController::class, 'export'])->name('stock-report.export');
        Route::get('/stock-report', [StockReportController::class, 'index'])->name('stock-report.index');
        Route::get('/stock-report/{product}/batches', [StockReportController::class, 'batches'])->name('stock-report.batches');
        Route::get('/stock-report/{product}/price-history', [StockReportController::class, 'priceHistory'])->name('stock-report.price-history');
    });

    // Operations Module (Stage 3)
    Route::prefix('operations')->name('operations.')->group(function () {
        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

        // Vehicle Registration
        Route::get('/vehicles', [VehicleRegistrationController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/search', [VehicleRegistrationController::class, 'search'])->name('vehicles.search');
        Route::post('/vehicles', [VehicleRegistrationController::class, 'store'])->name('vehicles.store');

        // Job Cards
        Route::get('/job-cards', [JobCardController::class, 'index'])->name('job-cards.index');
        Route::get('/job-cards/{jobCard}', [JobCardController::class, 'show'])->name('job-cards.show');
        Route::get('/job-cards/{jobCard}/print', [JobCardController::class, 'print'])->name('job-cards.print');
        Route::get('/job-cards/{jobCard}/pdf', [JobCardController::class, 'downloadPdf'])->name('job-cards.pdf');
        Route::put('/job-cards/{jobCard}', [JobCardController::class, 'update'])->name('job-cards.update');
        Route::delete('/job-cards/{jobCard}', [JobCardController::class, 'destroy'])->name('job-cards.destroy')->middleware('admin');
        Route::post('/job-cards/{jobCard}/manage', [JobCardController::class, 'manageCompletedJob'])->name('job-cards.manage')->middleware('admin');
        Route::patch('/job-cards/{jobCard}/carpets', [JobCardController::class, 'updateCarpets'])->name('job-cards.carpets.update');
        Route::patch('/job-cards/{jobCard}/status', [JobCardController::class, 'updateStatus'])->name('job-cards.status');
        Route::post('/job-cards/{jobCard}/cancel', [JobCardController::class, 'cancel'])->name('job-cards.cancel');
        Route::post('/job-cards/{jobCard}/generate-invoice', [JobCardController::class, 'generateInvoice'])->name('job-cards.generate-invoice');
        Route::get('/job-cards/{jobCard}/invoice', [JobCardController::class, 'goToInvoice'])->name('job-cards.invoice');
        Route::post('/job-cards/{jobCard}/payments', [JobCardController::class, 'recordPayment'])->name('job-cards.payments.store');
        Route::get('/job-cards/{jobCard}/print-bill', [JobCardController::class, 'printBill'])->name('job-cards.print-bill');
        Route::get('/job-cards/{jobCard}/advance-receipt', [JobCardController::class, 'advanceReceipt'])->name('job-cards.advance-receipt');

        // Vehicle Issues & Customer Approvals
        Route::post('/job-cards/{jobCard}/issues', [JobIssueController::class, 'store'])->name('job-cards.issues.store');
        Route::patch('/job-issues/{issue}/approval', [JobIssueController::class, 'recordApproval'])->name('job-issues.approval');

        // Additional Work & Supplementary Invoices
        Route::post('/job-cards/{jobCard}/additional-work/start', [JobCardController::class, 'startAdditionalWork'])->name('job-cards.additional-work.start');
        Route::post('/job-cards/{jobCard}/additional-work/complete', [JobCardController::class, 'completeAdditionalWork'])->name('job-cards.additional-work.complete');
        Route::post('/job-cards/{jobCard}/supplementary-invoice', [JobCardController::class, 'generateSupplementaryInvoice'])->name('job-cards.supplementary-invoice');
        Route::post('/job-cards/{jobCard}/follow-up', [JobCardController::class, 'createFollowUp'])->name('job-cards.follow-up');

        // Job Card Services
        Route::get('/job-cards/{jobCard}/services/{service}/price', [JobCardController::class, 'getServicePrice'])->name('job-cards.service-price');
        Route::post('/job-cards/{jobCard}/services', [JobCardController::class, 'addService'])->name('job-cards.services.add');
        Route::patch('/job-cards/{jobCard}/services/{serviceItem}/quantity', [JobCardController::class, 'updateServiceQuantity'])->name('job-cards.services.quantity');
        Route::patch('/job-cards/{jobCard}/services/{serviceItem}', [JobCardController::class, 'updateServiceItem'])->name('job-cards.services.update');
        Route::delete('/job-cards/{jobCard}/services/{serviceItem}', [JobCardController::class, 'removeService'])->name('job-cards.services.remove');

        // Job Card Products
        Route::post('/job-cards/{jobCard}/products', [JobCardController::class, 'addProduct'])->name('job-cards.products.add');
        Route::patch('/job-cards/{jobCard}/products/{productItem}/quantity', [JobCardController::class, 'updateProductQuantity'])->name('job-cards.products.quantity');
        Route::patch('/job-cards/{jobCard}/products/{productItem}', [JobCardController::class, 'updateProductItem'])->name('job-cards.products.update');
        Route::delete('/job-cards/{jobCard}/products/{productItem}', [JobCardController::class, 'removeProduct'])->name('job-cards.products.remove');

        // Direct Sale (POS)
        Route::get('/direct-sales', [DirectSaleController::class, 'index'])->name('direct-sales.index');
        Route::get('/direct-sales/search-products', [DirectSaleController::class, 'searchProducts'])->name('direct-sales.search-products');
        Route::post('/direct-sales', [DirectSaleController::class, 'store'])->name('direct-sales.store');
        Route::get('/direct-sales/{directSale}/bill', [DirectSaleController::class, 'printBill'])->name('direct-sales.bill');
        Route::get('/direct-sales/{directSale}/pdf', [DirectSaleController::class, 'downloadPdf'])->name('direct-sales.pdf');
        Route::post('/direct-sales/{directSale}/email', [DirectSaleController::class, 'sendEmail'])->name('direct-sales.email');
        Route::post('/direct-sales/{directSale}/update-contact', [DirectSaleController::class, 'updateContact'])->name('direct-sales.update-contact');
        Route::post('/direct-sales/{directSale}/generate-invoice', [DirectSaleController::class, 'generateInvoice'])->name('direct-sales.generate-invoice');

        // Invoices (Operations legacy shortcuts)
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/{invoice}/thermal', [InvoiceController::class, 'thermalReceipt'])->name('invoices.thermal');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
        Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'sendEmail'])->name('invoices.email');

        // Company & Vehicles
        Route::get('/company-vehicles', [CompanyVehicleController::class, 'index'])->name('company-vehicles.index');
        Route::post('/company-vehicles/companies', [CompanyVehicleController::class, 'storeCompany'])->name('company-vehicles.companies.store');
        Route::put('/company-vehicles/companies/{customer}', [CompanyVehicleController::class, 'updateCompany'])->name('company-vehicles.companies.update');
        Route::post('/company-vehicles/vehicles', [CompanyVehicleController::class, 'storeVehicle'])->name('company-vehicles.vehicles.store');
        Route::put('/company-vehicles/vehicles/{vehicle}', [CompanyVehicleController::class, 'updateVehicle'])->name('company-vehicles.vehicles.update');
        Route::get('/company-vehicles/check-vehicle', [CompanyVehicleController::class, 'checkVehicle'])->name('company-vehicles.check-vehicle');
        Route::get('/company-vehicles/vehicles/{vehicle}/history', [CompanyVehicleController::class, 'vehicleHistory'])->name('company-vehicles.vehicles.history');
        Route::get('/company-vehicles/companies/{customer}/invoices', [CompanyVehicleController::class, 'invoices'])->name('company-vehicles.companies.invoices');
        Route::get('/company-vehicles/companies/{customer}/statement', [CompanyVehicleController::class, 'statement'])->name('company-vehicles.companies.statement');
    });

    // Sales & Billing Module
    Route::prefix('billing')->name('billing.')->group(function () {
        // Quotations
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
        Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');
        Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
        Route::get('/quotations/{quotation}/thermal', [QuotationController::class, 'thermalReceipt'])->name('quotations.thermal');
        Route::post('/quotations/{quotation}/send', [QuotationController::class, 'markAsSent'])->name('quotations.send');
        Route::post('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('/quotations/{quotation}/convert-to-invoice', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert-to-invoice');
        Route::post('/quotations/{quotation}/create-job-card', [QuotationController::class, 'createJobCard'])->name('quotations.create-job-card');
        Route::post('/quotations/{quotation}/clone', [QuotationController::class, 'clone'])->name('quotations.clone');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/{invoice}/thermal', [InvoiceController::class, 'thermalReceipt'])->name('invoices.thermal');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
        Route::post('/invoices/{invoice}/email', [InvoiceController::class, 'sendEmail'])->name('invoices.email');

        // Invoice Payments Management
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('/payments/{payment}/print', [PaymentController::class, 'receipt'])->name('payments.print');
        Route::get('/payments/{payment}/thermal', [PaymentController::class, 'thermalReceipt'])->name('payments.thermal');
        Route::post('/payments/{payment}/email', [PaymentController::class, 'sendEmail'])->name('payments.email');
        Route::post('/payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');

        // Stage 6: Avoid Direct Sale (Admin Only)
        Route::get('/avoid-direct-sale', [AvoidDirectSaleController::class, 'index'])->name('avoid-direct-sale.index');
        Route::post('/avoid-direct-sale/{directSale}', [AvoidDirectSaleController::class, 'cancel'])->name('avoid-direct-sale.cancel');

        // Stage 6: Avoid Service Sale (Admin Only)
        Route::get('/avoid-service-sale', [AvoidServiceSaleController::class, 'index'])->name('avoid-service-sale.index');
        Route::post('/avoid-service-sale/{invoice}', [AvoidServiceSaleController::class, 'cancel'])->name('avoid-service-sale.cancel');

        // Stage 6: Invoice Date Change (Admin Only)
        Route::get('/invoice-date-change', [InvoiceDateChangeController::class, 'index'])->name('invoice-date-change.index');
        Route::post('/invoice-date-change/{invoice}', [InvoiceDateChangeController::class, 'update'])->name('invoice-date-change.update');

        // Stage 6: Multiple Invoice Payment (Admin & Staff)
        Route::get('/multiple-invoice-payment', [MultipleInvoicePaymentController::class, 'index'])->name('multiple-invoice-payment.index');
        Route::get('/multiple-invoice-payment/search-invoice', [MultipleInvoicePaymentController::class, 'searchInvoice'])->name('multiple-invoice-payment.search-invoice');
        Route::get('/multiple-invoice-payment/customers/{customer}/invoices', [MultipleInvoicePaymentController::class, 'customerInvoices'])->name('multiple-invoice-payment.customer-invoices');
        Route::post('/multiple-invoice-payment/split', [MultipleInvoicePaymentController::class, 'storeSplitPayment'])->name('multiple-invoice-payment.split');
        Route::post('/multiple-invoice-payment/bulk', [MultipleInvoicePaymentController::class, 'storeBulkPayment'])->name('multiple-invoice-payment.bulk');
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
        Route::get('/attendance/reminder-status', [AttendanceReminderController::class, 'status'])->name('attendance.reminder.status');
        Route::post('/attendance/reminder-dismiss', [AttendanceReminderController::class, 'dismiss'])->name('attendance.reminder.dismiss');

        // Company Holidays
        Route::get('/holidays', [CompanyHolidayController::class, 'index'])->name('holidays.index');
        Route::post('/holidays', [CompanyHolidayController::class, 'store'])->name('holidays.store');
        Route::put('/holidays/{holiday}', [CompanyHolidayController::class, 'update'])->name('holidays.update');
        Route::patch('/holidays/{holiday}/cancel', [CompanyHolidayController::class, 'cancel'])->name('holidays.cancel');
        Route::delete('/holidays/{holiday}', [CompanyHolidayController::class, 'destroy'])->name('holidays.destroy');

        // Employee Advances (Step 5)
        Route::get('/advances', [SalaryAdvanceController::class, 'index'])->name('advances.index');
        Route::post('/advances', [SalaryAdvanceController::class, 'store'])->name('advances.store');
        Route::get('/advances/{advance}/history', [SalaryAdvanceController::class, 'history'])->name('advances.history');
        Route::post('/advances/{advance}/approve', [SalaryAdvanceController::class, 'approve'])->name('advances.approve');
        Route::post('/advances/{advance}/reject', [SalaryAdvanceController::class, 'reject'])->name('advances.reject');
        Route::post('/advances/{advance}/disburse', [SalaryAdvanceController::class, 'disburse'])->name('advances.disburse');
        Route::post('/advances/{advance}/cancel', [SalaryAdvanceController::class, 'cancel'])->name('advances.cancel');
        Route::post('/advances/{advance}/reverse-disbursement', [SalaryAdvanceController::class, 'reverseDisbursement'])->name('advances.reverse-disbursement');

        // Bonuses, Allowances & Overtime (Step 6)
        Route::get('/bonuses-allowances', [BonusAllowanceController::class, 'index'])->name('bonuses-allowances.index');
        Route::post('/bonuses-allowances/bonuses', [BonusAllowanceController::class, 'storeBonus'])->name('bonuses-allowances.bonuses.store');
        Route::post('/bonuses-allowances/bonuses/{bonus}/approve', [BonusAllowanceController::class, 'approveBonus'])->name('bonuses-allowances.bonuses.approve');
        Route::post('/bonuses-allowances/bonuses/{bonus}/reject', [BonusAllowanceController::class, 'rejectBonus'])->name('bonuses-allowances.bonuses.reject');
        Route::post('/bonuses-allowances/bonuses/{bonus}/cancel', [BonusAllowanceController::class, 'cancelBonus'])->name('bonuses-allowances.bonuses.cancel');

        Route::post('/bonuses-allowances/allowances', [BonusAllowanceController::class, 'storeAllowance'])->name('bonuses-allowances.allowances.store');
        Route::post('/bonuses-allowances/allowances/{allowance}/toggle', [BonusAllowanceController::class, 'toggleAllowanceStatus'])->name('bonuses-allowances.allowances.toggle');
        Route::post('/bonuses-allowances/allowances/{allowance}/cancel', [BonusAllowanceController::class, 'cancelAllowance'])->name('bonuses-allowances.allowances.cancel');

        Route::post('/bonuses-allowances/overtime', [BonusAllowanceController::class, 'storeOvertime'])->name('bonuses-allowances.overtime.store');
        Route::post('/bonuses-allowances/overtime/{overtime}/approve', [BonusAllowanceController::class, 'approveOvertime'])->name('bonuses-allowances.overtime.approve');
        Route::post('/bonuses-allowances/overtime/{overtime}/reject', [BonusAllowanceController::class, 'rejectOvertime'])->name('bonuses-allowances.overtime.reject');
        Route::post('/bonuses-allowances/overtime/{overtime}/cancel', [BonusAllowanceController::class, 'cancelOvertime'])->name('bonuses-allowances.overtime.cancel');
        Route::get('/bonuses-allowances/overtime-rate', [BonusAllowanceController::class, 'calculateOvertimeRate'])->name('bonuses-allowances.overtime-rate');

        // Employee Salary Management
        Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::get('/salary/{payrollItem}/sheet', [SalaryController::class, 'sheet'])->name('salary.sheet');
        Route::get('/salary/{payrollItem}/print', [SalaryController::class, 'print'])->name('salary.print');
        Route::put('/salary/{payrollItem}/adjustments', [SalaryController::class, 'updateItem'])->name('salary.update-item');
        Route::post('/salary/{payrollItem}/payments', [SalaryController::class, 'recordPayment'])->name('salary.payments.store');
        Route::post('/salary/periods/{period}/review', [SalaryController::class, 'markReviewed'])->name('salary.review');
        Route::post('/salary/periods/{period}/approve', [SalaryController::class, 'approve'])->name('salary.approve');
        Route::post('/salary/periods/{period}/rollback', [SalaryController::class, 'rollbackApproval'])->name('salary.rollback');

        // Statutory & Tax Reports (EPF, ETF, APIT) - Step 8
        Route::get('/statutory', [StatutoryReportController::class, 'index'])->name('statutory.index');
        Route::get('/statutory/epf', [StatutoryReportController::class, 'epfReport'])->name('statutory.epf');
        Route::get('/statutory/etf', [StatutoryReportController::class, 'etfReport'])->name('statutory.etf');
        Route::get('/statutory/apit', [StatutoryReportController::class, 'apitReport'])->name('statutory.apit');
        Route::get('/statutory/employer-summary', [StatutoryReportController::class, 'employerSummary'])->name('statutory.employer-summary');
        Route::get('/statutory/settings', [StatutoryReportController::class, 'settings'])->name('statutory.settings');
        Route::post('/statutory/settings', [StatutoryReportController::class, 'updateSettings'])->name('statutory.settings.update');
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
        Route::get('/records/create', [ExpenseController::class, 'createShop'])->name('records.create');
        Route::get('/export', [ExpenseController::class, 'export'])->name('records.export');
        Route::get('/staff/create', [ExpenseController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [ExpenseController::class, 'storeStaff'])->name('staff.store');
        Route::get('/shop/create', [ExpenseController::class, 'createShop'])->name('shop.create');
        Route::post('/shop', [ExpenseController::class, 'storeShop'])->name('shop.store');
        Route::get('/records/{expense}', [ExpenseController::class, 'show'])->name('records.show');
        Route::get('/records/{expense}/edit', [ExpenseController::class, 'edit'])->name('records.edit');
        Route::put('/records/{expense}', [ExpenseController::class, 'update'])->name('records.update');
        Route::post('/records/{expense}/void', [ExpenseController::class, 'void'])->name('records.void');
        Route::post('/records/{expense}/payments', [ExpenseController::class, 'storePayment'])->name('records.payments.store');
        Route::get('/records/{expense}/receipt', [ExpenseController::class, 'downloadReceipt'])->name('records.receipt');
    });

    // Reports & Credit Management Module (Stage 7)
    Route::prefix('reports')->name('reports.')->group(function () {
        // 1. Stock Report
        Route::get('/stock', [Stage7StockReportController::class, 'index'])->name('stock.index');
        Route::get('/stock/print', [Stage7StockReportController::class, 'print'])->name('stock.print');
        Route::get('/stock/export', [Stage7StockReportController::class, 'export'])->name('stock.export');
        Route::get('/stock/{product}/batches', [Stage7StockReportController::class, 'batches'])->name('stock.batches');

        // 2. Stock Value Report (Admin Only)
        Route::middleware('admin')->group(function () {
            Route::get('/stock-value', [StockValueReportController::class, 'index'])->name('stock-value.index');
            Route::get('/stock-value/print', [StockValueReportController::class, 'print'])->name('stock-value.print');
            Route::get('/stock-value/export', [StockValueReportController::class, 'export'])->name('stock-value.export');
        });

        // 3. Sales Report
        Route::get('/sales', [SalesReportController::class, 'index'])->name('sales.index');
        Route::get('/sales/print', [SalesReportController::class, 'print'])->name('sales.print');
        Route::get('/sales/export', [SalesReportController::class, 'export'])->name('sales.export');

        // 4. Profit & Loss (Admin Only)
        Route::middleware('admin')->group(function () {
            Route::get('/profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');
            Route::get('/profit-loss/print', [ProfitLossController::class, 'print'])->name('profit-loss.print');
            Route::get('/profit-loss/export', [ProfitLossController::class, 'export'])->name('profit-loss.export');
        });

        // 5. Vehicle Service Report
        Route::get('/vehicle-service', [VehicleServiceReportController::class, 'index'])->name('vehicle-service.index');
        Route::get('/vehicle-service/search', [VehicleServiceReportController::class, 'search'])->name('vehicle-service.search');
        Route::get('/vehicle-service/{vehicle}/print', [VehicleServiceReportController::class, 'print'])->name('vehicle-service.print');
        Route::get('/vehicle-service/{vehicle}/export', [VehicleServiceReportController::class, 'export'])->name('vehicle-service.export');

        // 6. Money Book
        Route::get('/money-book', [MoneyBookController::class, 'index'])->name('money-book.index');
        Route::get('/money-book/print', [MoneyBookController::class, 'print'])->name('money-book.print');
        Route::get('/money-book/export', [MoneyBookController::class, 'export'])->name('money-book.export');

        // 7. Credit Management
        Route::get('/credit-management', [CreditManagementController::class, 'index'])->name('credit-management.index');
        Route::get('/credit-management/create', [CreditManagementController::class, 'create'])->name('credit-management.create');
        Route::post('/credit-management', [CreditManagementController::class, 'store'])->name('credit-management.store');
        Route::get('/credit-management/check-limit', [CreditManagementController::class, 'checkCreditLimit'])->name('credit-management.check-limit');
        Route::get('/credit-management/{creditAccount}', [CreditManagementController::class, 'show'])->name('credit-management.show');
        Route::put('/credit-management/{creditAccount}', [CreditManagementController::class, 'update'])->name('credit-management.update');
        Route::get('/credit-management/{creditAccount}/statement', [CreditManagementController::class, 'statement'])->name('credit-management.statement');
        Route::get('/credit-management/{creditAccount}/statement/print', [CreditManagementController::class, 'statementPrint'])->name('credit-management.statement.print');
        Route::get('/credit-management/{creditAccount}/statement/export', [CreditManagementController::class, 'statementExport'])->name('credit-management.statement.export');
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

        // Printer Settings (Administration)
        Route::get('/printer-settings', [PrinterSettingController::class, 'index'])->name('printer-settings.index');
        Route::post('/printer-settings', [PrinterSettingController::class, 'store'])->name('printer-settings.store');
        Route::put('/printer-settings/{printerSetting}', [PrinterSettingController::class, 'update'])->name('printer-settings.update');
        Route::delete('/printer-settings/{printerSetting}', [PrinterSettingController::class, 'destroy'])->name('printer-settings.destroy');
        Route::get('/printer-settings/certificate', [PrinterSettingController::class, 'getCertificate'])->name('printer-settings.certificate');
        Route::post('/printer-settings/sign', [PrinterSettingController::class, 'sign'])->name('printer-settings.sign');
        Route::post('/printer-settings/test-print', [PrinterSettingController::class, 'testPrint'])->name('printer-settings.test-print');
    });

    // General Printing API & Workstation Config (Accessible by Admin and Staff for printing)
    Route::get('/print/config', [PrinterSettingController::class, 'getActiveConfig'])->name('print.config');
    Route::post('/print/log', [PrinterSettingController::class, 'logStatus'])->name('print.log');
    Route::get('/print/certificate', [PrinterSettingController::class, 'getCertificate'])->name('print.certificate');
    Route::post('/print/sign', [PrinterSettingController::class, 'sign'])->name('print.sign');
});
