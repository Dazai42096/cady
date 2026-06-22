<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Public\PublicController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\GeneratorController;
use App\Http\Controllers\Dashboard\QuotationController;
use App\Http\Controllers\Dashboard\ContractController;
use App\Http\Controllers\Dashboard\VisitController;
use App\Http\Controllers\Dashboard\RentalController;
use App\Http\Controllers\Dashboard\AuditLogController;
use App\Http\Controllers\Portal\PortalController;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest access)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/quote-request', [PublicController::class, 'showQuoteRequestForm'])->name('quote_request.form');
Route::post('/quote-request', [PublicController::class, 'submitQuoteRequest'])->name('quote_request.submit');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Requires Active Status)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user'])->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Staff / Internal Dashboard Area (Admin, Sales, Support)
    |--------------------------------------------------------------------------
    */
    Route::prefix('dashboard')
        ->name('dashboard.')
        ->middleware(['role:admin,sales,support'])
        ->group(function () {
            
            // Stats Dashboard
            Route::get('/', [DashboardController::class, 'index'])->name('index');

            // Customers CRUD (Admin/Sales manage, Support view)
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('role:admin,sales');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('role:admin,sales');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('role:admin,sales');
            Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('role:admin,sales');
            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('role:admin,sales');
            
            // Customer status triggers (Admin only)
            Route::get('/customers-pending', [CustomerController::class, 'pending'])->name('customers.pending')->middleware('role:admin');
            Route::post('/customers/{customer}/approve', [CustomerController::class, 'approve'])->name('customers.approve')->middleware('role:admin');
            Route::post('/customers/{customer}/reject', [CustomerController::class, 'reject'])->name('customers.reject')->middleware('role:admin');
            Route::post('/customers/{customer}/suspend', [CustomerController::class, 'suspend'])->name('customers.suspend')->middleware('role:admin');

            // Generators CRUD (Admin/Sales manage, Support view)
            Route::get('/generators', [GeneratorController::class, 'index'])->name('generators.index');
            Route::get('/generators/create', [GeneratorController::class, 'create'])->name('generators.create')->middleware('role:admin,sales');
            Route::post('/generators', [GeneratorController::class, 'store'])->name('generators.store')->middleware('role:admin,sales');
            Route::get('/generators/{generator}', [GeneratorController::class, 'show'])->name('generators.show');
            Route::get('/generators/{generator}/edit', [GeneratorController::class, 'edit'])->name('generators.edit')->middleware('role:admin,sales');
            Route::put('/generators/{generator}', [GeneratorController::class, 'update'])->name('generators.update')->middleware('role:admin,sales');
            Route::delete('/generators/{generator}', [GeneratorController::class, 'destroy'])->name('generators.destroy')->middleware('role:admin,sales');
            // Rental Control (Admin/Sales view and manage rental offers)
            Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index')->middleware('role:admin,sales');
            // Quotations CRUD (Admin/Sales manage, Support cannot view/manage)
            Route::middleware('role:admin,sales')->group(function () {
                Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
                Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
                Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
                Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show')->withoutMiddleware('role:admin,sales')->middleware('role:admin,sales,support'); // Support can view only
                Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
                Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
                Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
                
                // Quotation Workflow
                Route::post('/quotations/{quotation}/mark-sent', [QuotationController::class, 'markSent'])->name('quotations.mark_sent');
                Route::post('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
                Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
            });
            // Support can view quotation PDF
            Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf')->middleware('role:admin,sales,support');

            // Maintenance Contracts CRUD (Admin/Sales manage, Support view)
            Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
            Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create')->middleware('role:admin,sales');
            Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('role:admin,sales');
            Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
            Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit')->middleware('role:admin,sales');
            Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update')->middleware('role:admin,sales');
            Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy')->middleware('role:admin,sales');
            
            // Contract Actions
            Route::post('/contracts/{contract}/activate', [ContractController::class, 'activate'])->name('contracts.activate')->middleware('role:admin,sales');
            Route::post('/contracts/{contract}/terminate', [ContractController::class, 'terminate'])->name('contracts.terminate')->middleware('role:admin,sales');
            Route::get('/contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])->name('contracts.pdf');

            // Maintenance Visits CRUD (Support can update, admin can manage everything, sales can view)
            Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
            Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
            Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit')->middleware('role:admin,support');
            Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update')->middleware('role:admin,support');
            
            // Visit Actions
            Route::post('/visits/{visit}/confirm', [VisitController::class, 'confirm'])->name('visits.confirm')->middleware('role:admin,support');
            Route::post('/visits/{visit}/start', [VisitController::class, 'start'])->name('visits.start')->middleware('role:admin,support');
            Route::post('/visits/{visit}/complete', [VisitController::class, 'complete'])->name('visits.complete')->middleware('role:admin,support');
            Route::post('/visits/{visit}/cancel', [VisitController::class, 'cancel'])->name('visits.cancel')->middleware('role:admin,support');

            // Audit Logs (Admin only)
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index')->middleware('role:admin');
            
            // Public Quote Requests (from the landing page form)
            Route::get('/quote-requests', [DashboardController::class, 'quoteRequests'])->name('quote_requests.index');
            Route::post('/quote-requests/{quoteRequest}/process', [DashboardController::class, 'processQuoteRequest'])->name('quote_requests.process');
        });

    /*
    |--------------------------------------------------------------------------
    | Customer Portal Area
    |--------------------------------------------------------------------------
    */
    Route::prefix('portal')
        ->name('portal.')
        ->middleware(['customer.portal'])
        ->group(function () {
            
            // Portal index page
            Route::get('/', [PortalController::class, 'index'])->name('index');
            Route::get('/quotations', [PortalController::class, 'quotations'])->name('quotations');
            Route::get('/contracts', [PortalController::class, 'contracts'])->name('contracts');
            
            // Customer Portal document downloads
            Route::get('/quotations/{quotation}/pdf', [PortalController::class, 'downloadQuotationPdf'])->name('quotations.pdf');
            Route::get('/contracts/{contract}/pdf', [PortalController::class, 'downloadContractPdf'])->name('contracts.pdf');
        });
});
