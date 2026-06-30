<?php

use App\Http\Controllers\Dashboard\WhatsAppController;

use App\Http\Controllers\Portal\PortalRentalController;

use App\Http\Controllers\Dashboard\SecurityUserController;

use App\Http\Controllers\Auth\TwoFactorController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\Dashboard\AuditLogController;
use App\Http\Controllers\Dashboard\ContractController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\GeneratorController;
use App\Http\Controllers\Dashboard\QuotationController;
use App\Http\Controllers\Dashboard\RentalController;
use App\Http\Controllers\Dashboard\VisitController;

use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Public\PublicController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
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
| Authentication Routes
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

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
| Admin, Sales, and Support can enter the internal dashboard.
| Specific actions are protected by role groups below.
*/

Route::middleware(['auth', 'role:admin,sales,support'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Main Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/', [DashboardController::class, 'index'])->name('index');

        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');

            Route::get('/customers-pending', [CustomerController::class, 'pending'])->name('customers.pending');
            Route::post('/customers/{customer}/approve', [CustomerController::class, 'approve'])->name('customers.approve');
            Route::post('/customers/{customer}/reject', [CustomerController::class, 'reject'])->name('customers.reject');
            Route::post('/customers/{customer}/suspend', [CustomerController::class, 'suspend'])->name('customers.suspend');
            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

            Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');

            Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
            Route::post('/contracts/{contract}/terminate', [ContractController::class, 'terminate'])->name('contracts.terminate');

            Route::delete('/generators/{generator}', [GeneratorController::class, 'destroy'])->name('generators.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Quote Requests
        |--------------------------------------------------------------------------
        | Admin and Sales can view/process public quote requests.
        */

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::get('/quote-requests', [DashboardController::class, 'quoteRequests'])->name('quote_requests.index');
            Route::post('/quote-requests/{quoteRequest}/process', [DashboardController::class, 'processQuoteRequest'])->name('quote_requests.process');
        });

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        | Admin/Sales can manage customers.
        | Support can view customers read-only.
        */

        Route::middleware(['role:admin,sales,support'])->group(function () {
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        });

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
            Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        });

        /*
        |--------------------------------------------------------------------------
        | Generators
        |--------------------------------------------------------------------------
        | Admin/Sales can manage generators.
        | Support can view generators.
        */

        Route::middleware(['role:admin,sales,support'])->group(function () {
            Route::get('/generators', [GeneratorController::class, 'index'])->name('generators.index');
            Route::get('/generators/{generator}', [GeneratorController::class, 'show'])->name('generators.show');
        });

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::get('/generators/create', [GeneratorController::class, 'create'])->name('generators.create');
            Route::post('/generators', [GeneratorController::class, 'store'])->name('generators.store');
            Route::get('/generators/{generator}/edit', [GeneratorController::class, 'edit'])->name('generators.edit');
            Route::put('/generators/{generator}', [GeneratorController::class, 'update'])->name('generators.update');
        });

        /*
        |--------------------------------------------------------------------------
        | Quotations
        |--------------------------------------------------------------------------
        | Admin/Sales manage quotations.
        */

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
            Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
            Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
            Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
            Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
            Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');

            Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');
            Route::post('/quotations/{quotation}/mark-sent', [QuotationController::class, 'markSent'])->name('quotations.mark_sent');
            Route::post('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
            Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        });

        /*
        |--------------------------------------------------------------------------
        | Maintenance Contracts
        |--------------------------------------------------------------------------
        | Admin/Sales manage contracts.
        | Termination and delete are Admin-only above.
        */

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
            Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
            Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
            Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
            Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
            Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');

            Route::get('/contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])->name('contracts.pdf');
            Route::post('/contracts/{contract}/activate', [ContractController::class, 'activate'])->name('contracts.activate');
        });

        /*
        |--------------------------------------------------------------------------
        | Rentals
        |--------------------------------------------------------------------------
        | Current controller only has index route.
        */

        Route::middleware(['role:admin,sales,support'])->group(function () {
            Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
        });

        /*
        |--------------------------------------------------------------------------
        | Maintenance Visits
        |--------------------------------------------------------------------------
        | Staff can view visits.
        | Admin/Support can perform technician workflow actions.
        */

        Route::middleware(['role:admin,sales,support'])->group(function () {
            Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
            Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
        });

        Route::middleware(['role:admin,sales,support'])->group(function () {
            Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
            Route::put('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
            Route::post('/visits/{visit}/confirm', [VisitController::class, 'confirm'])->name('visits.confirm');
        });

        Route::middleware(['role:admin,support'])->group(function () {
            Route::post('/visits/{visit}/start', [VisitController::class, 'start'])->name('visits.start');
            Route::post('/visits/{visit}/complete', [VisitController::class, 'complete'])->name('visits.complete');
        });

        Route::middleware(['role:admin,sales'])->group(function () {
            Route::post('/visits/{visit}/cancel', [VisitController::class, 'cancel'])->name('visits.cancel');
        });
    });

/*
|--------------------------------------------------------------------------
| Customer Portal Routes
|--------------------------------------------------------------------------
| Customers can only access the external portal.
*/

Route::middleware(['auth', 'role:customer'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/', [PortalController::class, 'index'])->name('index');

        Route::get('/contracts', [PortalController::class, 'contracts'])->name('contracts');
        Route::get('/contracts/{contract}/pdf', [PortalController::class, 'downloadContractPdf'])->name('contracts.pdf');

        Route::get('/quotations', [PortalController::class, 'quotations'])->name('quotations');
        Route::get('/quotations/{quotation}/pdf', [PortalController::class, 'downloadQuotationPdf'])->name('quotations.pdf');
    });


Route::get('/two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
Route::post('/two-factor/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/two-factor', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/dashboard/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/dashboard/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});


Route::middleware(['auth', 'role:admin'])->prefix('dashboard/security-users')->name('dashboard.security-users.')->group(function () {
    Route::get('/', [SecurityUserController::class, 'index'])->name('index');
    Route::post('/{user}/unlock', [SecurityUserController::class, 'unlock'])->name('unlock');
    Route::post('/{user}/reset-2fa', [SecurityUserController::class, 'resetTwoFactor'])->name('reset-2fa');
    Route::post('/{user}/activate', [SecurityUserController::class, 'activate'])->name('activate');
    Route::post('/{user}/deactivate', [SecurityUserController::class, 'deactivate'])->name('deactivate');
});


Route::middleware(['auth', 'role:admin,sales'])->prefix('dashboard/rentals')->name('dashboard.rentals.')->group(function () {
    Route::get('/create', [RentalController::class, 'create'])->name('create');
    Route::post('/', [RentalController::class, 'store'])->name('store');
    Route::get('/{rental}/edit', [RentalController::class, 'edit'])->name('edit');
    Route::put('/{rental}', [RentalController::class, 'update'])->name('update');
    Route::post('/{rental}/activate', [RentalController::class, 'activate'])->name('activate');
    Route::post('/{rental}/extend', [RentalController::class, 'extend'])->name('extend');
    Route::post('/{rental}/complete', [RentalController::class, 'complete'])->name('complete');
    Route::post('/{rental}/cancel', [RentalController::class, 'cancel'])->name('cancel');
});

Route::middleware(['auth', 'role:admin,sales,support'])->prefix('dashboard/rentals')->name('dashboard.rentals.')->group(function () {
    Route::get('/{rental}', [RentalController::class, 'show'])->name('show');
    Route::post('/{rental}/hour-meter', [RentalController::class, 'updateHourMeter'])->name('hour-meter');
    Route::get('/{rental}/export', [RentalController::class, 'export'])->name('export');
});


Route::middleware(['auth', 'role:customer'])
    ->prefix('portal/rentals')
    ->name('portal.rentals.')
    ->group(function () {
        Route::get('/', [PortalRentalController::class, 'index'])->name('index');
        Route::get('/{rental}', [PortalRentalController::class, 'show'])->name('show');
        Route::get('/{rental}/export', [PortalRentalController::class, 'export'])->name('export');
    });


Route::middleware(['auth', 'role:admin,sales,support'])
    ->prefix('dashboard/whatsapp-messages')
    ->name('dashboard.whatsapp.')
    ->group(function () {
        Route::get('/', [WhatsAppController::class, 'index'])->name('index');
        Route::get('/create', [WhatsAppController::class, 'create'])->name('create');
        Route::post('/', [WhatsAppController::class, 'store'])->name('store');
        Route::get('/{whatsapp}', [WhatsAppController::class, 'show'])->name('show');
        Route::post('/{whatsapp}/open', [WhatsAppController::class, 'open'])->name('open');
        Route::post('/{whatsapp}/mark-sent', [WhatsAppController::class, 'markSent'])->name('mark-sent');
    });
