<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Enums\Role;
use App\Enums\CustomerStatus;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // 1. Create inactive customer portal user
                $user = User::create([
                    'name' => $request->contact_person,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => Role::CUSTOMER,
                    'is_active' => false, // starts inactive until admin approves
                    'password' => Hash::make($request->password),
                ]);

                // 2. Create customer company record
                $customer = Customer::create([
                    'company_name' => $request->company_name,
                    'contact_person' => $request->contact_person,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'business_activity' => $request->business_activity,
                    'status' => CustomerStatus::PENDING_ADMIN_LINK,
                ]);

                // 3. Create customer user association
                CustomerUser::create([
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'is_primary' => true,
                ]);

                // 4. Log audit log
                $this->auditLogService->log(
                    action: 'auth.customer_register',
                    entityType: Customer::class,
                    entityId: $customer->id,
                    newValues: [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'company_name' => $customer->company_name,
                    ]
                );
            });

            return redirect()->route('login')->with('success', 'تم إنشاء الحساب بنجاح! حسابك حالياً قيد المراجعة والموافقة من قبل الإدارة وسنتواصل معك قريباً.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'email' => 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة مرة أخرى لاحقاً.',
            ]);
        }
    }
}
