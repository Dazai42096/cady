<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Generator;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceVisit;
use App\Models\Quotation;
use App\Policies\AuditLogPolicy;
use App\Policies\ContractPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\GeneratorPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\VisitPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! app()->runningInConsole() && request()->hasSession()) {
            App::setLocale(session('locale', 'ar'));
        }
    }
}