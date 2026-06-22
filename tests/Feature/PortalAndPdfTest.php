<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Quotation;
use App\Models\MaintenanceContract;
use App\Models\Generator;
use App\Enums\Role;
use App\Enums\CustomerStatus;
use App\Enums\QuotationStatus;
use App\Enums\ContractStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAndPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $customerUser;
    private Customer $customer;
    private Quotation $quotation;
    private MaintenanceContract $contract;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create a customer
        $this->customer = Customer::create([
            'company_name' => 'Test Company',
            'contact_person' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@testcompany.com',
            'status' => CustomerStatus::ACTIVE,
        ]);

        // 2. Create customer portal user and link
        $this->customerUser = User::factory()->create([
            'role' => Role::CUSTOMER,
            'is_active' => true,
        ]);

        CustomerUser::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->customerUser->id,
            'is_primary' => true,
        ]);

        // 3. Create a generator
        $generator = Generator::create([
            'customer_id' => $this->customer->id,
            'serial_number' => 'SN-TEST-123',
            'model' => 'Model-X',
            'brand' => 'Brand-Y',
            'capacity_kva' => 100,
            'fuel_type' => 'diesel',
            'status' => 'active',
        ]);

        // 4. Create internal sales user for documents creator
        $sales = User::factory()->create([
            'role' => Role::SALES,
            'is_active' => true,
        ]);

        // 5. Create quotation for this customer
        $this->quotation = Quotation::create([
            'customer_id' => $this->customer->id,
            'created_by' => $sales->id,
            'ref_number' => 'Q-TEST-001',
            'type' => 'spare_parts',
            'status' => QuotationStatus::SENT,
            'quotation_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'project' => 'Test Project',
            'subtotal' => 100,
            'tax_rate' => 15,
            'tax_amount' => 15,
            'total' => 115,
            'currency' => 'SAR',
        ]);

        // 6. Create contract for this customer and generator
        $this->contract = MaintenanceContract::create([
            'customer_id' => $this->customer->id,
            'generator_id' => $generator->id,
            'created_by' => $sales->id,
            'ref_number' => 'MC-TEST-001',
            'to_name' => 'Test Company',
            'project' => 'Maintenance SLA',
            'status' => ContractStatus::ACTIVE,
            'contract_start_date' => now()->toDateString(),
            'contract_end_date' => now()->addYear()->toDateString(),
            'visit_count' => 4,
            'payment_method' => 'Bank Transfer',
            'subtotal' => 1000,
            'tax_rate' => 15,
            'tax_amount' => 150,
            'total_value' => 1150,
            'currency' => 'SAR',
        ]);
    }

    public function test_customer_user_can_access_portal_dashboard(): void
    {
        $this->actingAs($this->customerUser)
            ->get('/portal')
            ->assertStatus(200)
            ->assertSee('Test Company');
    }

    public function test_customer_user_can_access_portal_quotations_list(): void
    {
        $this->actingAs($this->customerUser)
            ->get('/portal/quotations')
            ->assertStatus(200)
            ->assertSee('Q-TEST-001');
    }

    public function test_customer_user_can_access_portal_contracts_list(): void
    {
        $this->actingAs($this->customerUser)
            ->get('/portal/contracts')
            ->assertStatus(200)
            ->assertSee('MC-TEST-001');
    }

    public function test_customer_user_can_download_own_quotation_pdf(): void
    {
        $this->actingAs($this->customerUser)
            ->get("/portal/quotations/{$this->quotation->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_customer_user_can_download_own_contract_pdf(): void
    {
        $this->actingAs($this->customerUser)
            ->get("/portal/contracts/{$this->contract->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_customer_cannot_download_other_customer_documents(): void
    {
        // Create another customer and user
        $otherCustomer = Customer::create([
            'company_name' => 'Other Company',
            'contact_person' => 'Jane Smith',
            'phone' => '0987654321',
            'email' => 'jane@othercompany.com',
            'status' => CustomerStatus::ACTIVE,
        ]);

        $otherUser = User::factory()->create([
            'role' => Role::CUSTOMER,
            'is_active' => true,
        ]);

        CustomerUser::create([
            'customer_id' => $otherCustomer->id,
            'user_id' => $otherUser->id,
            'is_primary' => true,
        ]);

        // Acting as OTHER user, try to access the first customer's documents
        $this->actingAs($otherUser)
            ->get("/portal/quotations/{$this->quotation->id}/pdf")
            ->assertStatus(403);

        $this->actingAs($otherUser)
            ->get("/portal/contracts/{$this->contract->id}/pdf")
            ->assertStatus(403);
    }
}
