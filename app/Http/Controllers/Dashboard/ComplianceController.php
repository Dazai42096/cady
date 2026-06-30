<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class ComplianceController extends Controller
{
    public function index()
    {
        $modules = [
            [
                'name' => 'Authentication Security',
                'description' => 'Strong passwords, failed login lockout, optional 2FA, and admin unlock/reset controls.',
                'checks' => [
                    'Users table exists' => Schema::hasTable('users'),
                    '2FA setup route exists' => Route::has('two-factor.setup'),
                    'Security users route exists' => Route::has('dashboard.security-users.index'),
                ],
                'links' => [
                    'Security Users' => Route::has('dashboard.security-users.index') ? route('dashboard.security-users.index') : null,
                    '2FA Setup' => Route::has('two-factor.setup') ? route('two-factor.setup') : null,
                ],
            ],
            [
                'name' => 'Customer Management',
                'description' => 'Customers, pending approval, activation, suspension, and customer-user links.',
                'checks' => [
                    'Customers table exists' => Schema::hasTable('customers'),
                    'Customer users table exists' => Schema::hasTable('customer_users'),
                    'Customers route exists' => Route::has('dashboard.customers.index'),
                    'Pending customers route exists' => Route::has('dashboard.customers.pending'),
                ],
                'links' => [
                    'Customers' => Route::has('dashboard.customers.index') ? route('dashboard.customers.index') : null,
                    'Pending Customers' => Route::has('dashboard.customers.pending') ? route('dashboard.customers.pending') : null,
                ],
            ],
            [
                'name' => 'Generator Management',
                'description' => 'Generator inventory, customer assignment, status tracking, and rental status updates.',
                'checks' => [
                    'Generators table exists' => Schema::hasTable('generators'),
                    'Generators route exists' => Route::has('dashboard.generators.index'),
                ],
                'links' => [
                    'Generators' => Route::has('dashboard.generators.index') ? route('dashboard.generators.index') : null,
                ],
            ],
            [
                'name' => 'Quotations',
                'description' => 'Quotation creation, tax, totals, PDF generation, and status actions.',
                'checks' => [
                    'Quotations table exists' => Schema::hasTable('quotations'),
                    'Quotations route exists' => Route::has('dashboard.quotations.index'),
                ],
                'links' => [
                    'Quotations' => Route::has('dashboard.quotations.index') ? route('dashboard.quotations.index') : null,
                ],
            ],
            [
                'name' => 'Maintenance Contracts',
                'description' => 'Contract creation, generator/customer linking, activation, termination, and PDF generation.',
                'checks' => [
                    'Maintenance contracts table exists' => Schema::hasTable('maintenance_contracts'),
                    'Contracts route exists' => Route::has('dashboard.contracts.index'),
                ],
                'links' => [
                    'Contracts' => Route::has('dashboard.contracts.index') ? route('dashboard.contracts.index') : null,
                ],
            ],
            [
                'name' => 'Rental Control',
                'description' => 'Rental records, references, inclusive day calculation, activation, extension, completion, cancellation, and CSV export.',
                'checks' => [
                    'Rentals table exists' => Schema::hasTable('rentals'),
                    'Rental index route exists' => Route::has('dashboard.rentals.index'),
                    'Rental create route exists' => Route::has('dashboard.rentals.create'),
                    'Rental export route exists' => Route::has('dashboard.rentals.export'),
                ],
                'links' => [
                    'Rentals' => Route::has('dashboard.rentals.index') ? route('dashboard.rentals.index') : null,
                    'Create Rental' => Route::has('dashboard.rentals.create') ? route('dashboard.rentals.create') : null,
                ],
            ],
            [
                'name' => 'Customer Portal',
                'description' => 'Customer portal for contracts, quotations, and rentals scoped to linked customer accounts.',
                'checks' => [
                    'Portal route exists' => Route::has('portal.index'),
                    'Portal contracts route exists' => Route::has('portal.contracts'),
                    'Portal quotations route exists' => Route::has('portal.quotations'),
                    'Portal rentals route exists' => Route::has('portal.rentals.index'),
                ],
                'links' => [
                    'Portal Home' => Route::has('portal.index') ? route('portal.index') : null,
                    'Portal Rentals' => Route::has('portal.rentals.index') ? route('portal.rentals.index') : null,
                ],
            ],
            [
                'name' => 'WhatsApp Tracking',
                'description' => 'WhatsApp message records, customer links, related quotation/rental/contract links, WhatsApp Web open action, and sent tracking.',
                'checks' => [
                    'WhatsApp messages table exists' => Schema::hasTable('whatsapp_messages'),
                    'WhatsApp route exists' => Route::has('dashboard.whatsapp.index'),
                ],
                'links' => [
                    'WhatsApp Messages' => Route::has('dashboard.whatsapp.index') ? route('dashboard.whatsapp.index') : null,
                ],
            ],
            [
                'name' => 'Backups',
                'description' => 'Admin JSON backup generation, download, delete, and audit logging.',
                'checks' => [
                    'Backup route exists' => Route::has('dashboard.backups.index'),
                    'Backup generate route exists' => Route::has('dashboard.backups.generate'),
                ],
                'links' => [
                    'Backups' => Route::has('dashboard.backups.index') ? route('dashboard.backups.index') : null,
                ],
            ],
            [
                'name' => 'Audit Logs',
                'description' => 'Admin-accessible activity log for important business and security actions.',
                'checks' => [
                    'Audit logs table exists' => Schema::hasTable('audit_logs'),
                    'Audit logs route exists' => Route::has('dashboard.audit_logs.index'),
                ],
                'links' => [
                    'Audit Logs' => Route::has('dashboard.audit_logs.index') ? route('dashboard.audit_logs.index') : null,
                ],
            ],
        ];

        $completeCount = collect($modules)
            ->filter(fn ($module) => collect($module['checks'])->every(fn ($value) => $value === true))
            ->count();

        $summary = [
            'total' => count($modules),
            'complete' => $completeCount,
            'partial' => count($modules) - $completeCount,
            'missing' => 0,
        ];

        return view('dashboard.compliance.index', compact('modules', 'summary'));
    }
}