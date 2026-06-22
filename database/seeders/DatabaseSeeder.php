<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Generator;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceVisit;
use App\Models\QuoteRequest;
use App\Enums\Role;
use App\Enums\CustomerStatus;
use App\Enums\GeneratorStatus;
use App\Enums\QuotationStatus;
use App\Enums\QuotationType;
use App\Enums\ContractStatus;
use App\Enums\VisitStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Staff Users
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@cady.com')],
            [
                'name' => 'مدير النظام',
                'phone' => '0790000001',
                'role' => Role::ADMIN,
                'is_active' => true,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@123456')),
            ]
        );

        $sales = User::updateOrCreate(
            ['email' => env('SALES_EMAIL', 'sales@cady.com')],
            [
                'name' => 'مسؤول المبيعات',
                'phone' => '0790000002',
                'role' => Role::SALES,
                'is_active' => true,
                'password' => Hash::make(env('SALES_PASSWORD', 'Sales@123456')),
            ]
        );

        $support = User::updateOrCreate(
            ['email' => env('SUPPORT_EMAIL', 'support@cady.com')],
            [
                'name' => 'المهندس الفني',
                'phone' => '0790000003',
                'role' => Role::SUPPORT,
                'is_active' => true,
                'password' => Hash::make(env('SUPPORT_PASSWORD', 'Support@123456')),
            ]
        );

        // 2. Seed Sample Customers
        // A. Active Customer
        $customerActive = Customer::create([
            'company_name' => 'شركة الفوسفات الأردنية',
            'contact_person' => 'م. أحمد الرواشدة',
            'phone' => '065607080',
            'email' => 'phosphate@example.com',
            'address' => 'عمان - الشميساني - شارع الثقافة',
            'business_activity' => 'صناعات تعدينية',
            'status' => CustomerStatus::ACTIVE,
            'notes' => 'عميل رئيسي للمولدات الكبيرة',
        ]);

        $customerUserActive = User::create([
            'name' => 'م. أحمد الرواشدة',
            'email' => 'phosphate_portal@example.com',
            'phone' => '0795551111',
            'role' => Role::CUSTOMER,
            'is_active' => true,
            'password' => Hash::make('Customer@123'),
        ]);

        CustomerUser::create([
            'customer_id' => $customerActive->id,
            'user_id' => $customerUserActive->id,
            'is_primary' => true,
        ]);

        // B. Pending Customer
        $customerPending = Customer::create([
            'company_name' => 'مستشفى الاستقلال',
            'contact_person' => 'د. سمير الخطيب',
            'phone' => '065657090',
            'email' => 'istqlal@example.com',
            'address' => 'عمان - شارع الاستقلال',
            'business_activity' => 'خدمات طبية',
            'status' => CustomerStatus::PENDING_ADMIN_LINK,
            'notes' => 'يحتاج ربط وتفعيل حساب البوابة الخاص به',
        ]);

        $customerUserPending = User::create([
            'name' => 'د. سمير الخطيب',
            'email' => 'istqlal_portal@example.com',
            'phone' => '0795552222',
            'role' => Role::CUSTOMER,
            'is_active' => false, // Pending admin approval!
            'password' => Hash::make('Customer@123'),
        ]);

        CustomerUser::create([
            'customer_id' => $customerPending->id,
            'user_id' => $customerUserPending->id,
            'is_primary' => true,
        ]);

        // 3. Seed Generators
        $generator1 = Generator::create([
            'customer_id' => $customerActive->id,
            'serial_number' => 'GEN-18290-CUMMINS',
            'model' => 'C250D5',
            'brand' => 'Cummins',
            'capacity_kva' => 250.00,
            'fuel_type' => 'diesel',
            'location' => 'مبنى الإدارة العامة - خلف المصعد الرئيسي',
            'status' => GeneratorStatus::RENTED,
            'notes' => 'تم عمل الصيانة الدورية الـ 250 ساعة في 2026-05-15',
        ]);

        $generator2 = Generator::create([
            'customer_id' => $customerActive->id,
            'serial_number' => 'GEN-45920-PERKINS',
            'model' => '1106A-70TAG2',
            'brand' => 'Perkins',
            'capacity_kva' => 150.00,
            'fuel_type' => 'diesel',
            'location' => 'المستودع الجنوبي - بجوار البوابة رقم 3',
            'status' => GeneratorStatus::MAINTENANCE,
            'notes' => 'يعاني من تسريب زيت بسيط في الفلتر الثاني',
        ]);

        // 4. Seed Quotations
        $quotation = Quotation::create([
            'customer_id' => $customerActive->id,
            'created_by' => $sales->id,
            'ref_number' => 'Q-2026-0001',
            'type' => QuotationType::SPARE_PARTS,
            'status' => QuotationStatus::SENT,
            'quotation_date' => now()->subDays(5),
            'valid_until' => now()->addDays(25),
            'project' => 'صيانة طارئة وفلاتر ديزل',
            'subtotal' => 450.00,
            'discount' => 50.00,
            'tax_rate' => 16.00,
            'tax_amount' => 64.00, // (450 - 50) * 0.16 = 64
            'total' => 464.00, // 400 + 64
            'currency' => 'JOD',
            'notes' => 'الأسعار تشمل التوصيل والتركيب وفحص المولد الفوري.',
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'description' => 'فلتر ديزل Cummins أصلي موديل C250',
            'quantity' => 2,
            'unit_price' => 150.00,
            'total' => 300.00,
            'sort_order' => 1,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'description' => 'زيت محرك مولدات ثقيل 15W-40 (20 لتر)',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total' => 150.00,
            'sort_order' => 2,
        ]);

        // 5. Seed Maintenance Contract
        $contract = MaintenanceContract::create([
            'customer_id' => $customerActive->id,
            'generator_id' => $generator1->id,
            'created_by' => $sales->id,
            'ref_number' => 'MC-2026-0001',
            'to_name' => 'شركة الفوسفات الأردنية / قسم الخدمات المساندة',
            'project' => 'عقد صيانة سنوي - مولد Cummins 250kVA',
            'status' => ContractStatus::ACTIVE,
            'contract_start_date' => now()->subMonths(2)->toDateString(),
            'contract_end_date' => now()->addMonths(10)->toDateString(),
            'visit_count' => 4,
            'payment_method' => 'تحويل بنكي - دفعة واحدة',
            'subtotal' => 1200.00,
            'tax_rate' => 16.00,
            'tax_amount' => 192.00,
            'total_value' => 1392.00,
            'currency' => 'JOD',
            'terms' => '1. الالتزام بالزيارات المجدولة كل 3 أشهر.\n2. لا يشمل العقد ثمن قطع الغيار التالفة.',
            'notes' => 'يشرف عليه المهندس الفني المعين من الإدارة.',
        ]);

        // 6. Seed Maintenance Visits
        // Visit 1: Completed
        MaintenanceVisit::create([
            'maintenance_contract_id' => $contract->id,
            'visit_number' => 1,
            'planned_date' => now()->subMonths(1)->toDateString(),
            'confirmed_date' => now()->subMonths(1)->toDateString(),
            'actual_date' => now()->subMonths(1)->toDateString(),
            'status' => VisitStatus::COMPLETED,
            'assigned_to' => $support->id,
            'technician_notes' => 'تم تغيير الزيت والفلاتر وفحص البطاريات. حالة المولد ممتازة.',
            'customer_notes' => 'المهندس حضر بالموقت والخدمة ممتازة.',
        ]);

        // Visit 2: Confirmed/Scheduled (Upcoming)
        MaintenanceVisit::create([
            'maintenance_contract_id' => $contract->id,
            'visit_number' => 2,
            'planned_date' => now()->addMonths(2)->toDateString(),
            'confirmed_date' => now()->addMonths(2)->toDateString(),
            'status' => VisitStatus::CONFIRMED,
            'assigned_to' => $support->id,
        ]);

        // Visit 3: Scheduled
        MaintenanceVisit::create([
            'maintenance_contract_id' => $contract->id,
            'visit_number' => 3,
            'planned_date' => now()->addMonths(5)->toDateString(),
            'status' => VisitStatus::SCHEDULED,
        ]);

        // Visit 4: Scheduled
        MaintenanceVisit::create([
            'maintenance_contract_id' => $contract->id,
            'visit_number' => 4,
            'planned_date' => now()->addMonths(8)->toDateString(),
            'status' => VisitStatus::SCHEDULED,
        ]);

        // 7. Seed Sample Quote Requests
        QuoteRequest::create([
            'company_name' => 'شركة مصانع البلاستيك الحديثة',
            'contact_person' => 'م. محمود عبيد',
            'phone' => '0788887777',
            'email' => 'obeid@modernplastic.com',
            'service_type' => 'rent_generator',
            'message' => 'نحتاج إلى مولد كهربائي بقدرة 500 ك.ف.أ لمدة 3 أشهر لمصنعنا في سحاب. يرجى تزويدنا بعرض سعر شامل التوصيل والديزل والصيانة الدورية.',
            'status' => 'pending',
        ]);
        
        QuoteRequest::create([
            'company_name' => 'سوبرماركت التميز',
            'contact_person' => 'أنس الضمور',
            'phone' => '0799988888',
            'email' => 'tamyoz@example.com',
            'service_type' => 'maintenance_contract',
            'message' => 'لدينا مولد بيركنز 50 ك.ف.أ ونرغب في الحصول على عقد صيانة دورية سنوي يشمل 6 زيارات فحص.',
            'status' => 'pending',
        ]);
    }
}
