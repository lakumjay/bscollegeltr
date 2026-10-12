<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Profile;
use App\Models\Session;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventoryBillingAndPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('mysql');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_name');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('active_session_id')->nullable();
            $table->text('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('college_name');
            $table->string('contact_number');
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->longText('banner')->nullable();
            $table->timestamps();
        });
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('bill_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('party_name')->nullable();
            $table->string('supplier_name')->nullable();
            $table->text('supplier_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id');
            $table->integer('quantity');
            $table->string('location')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_can_create_inventory_item_with_billing_and_cheque_details(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'name' => 'A4 Paper Reams',
            'unit_price' => 350.00,
            'total_stock' => 50,
            'bill_no' => 'BILL-2026-909',
            'purchase_date' => '2026-09-08',
            'party_name' => 'Aditya Traders',
            'supplier_name' => 'National Paper Mill',
            'supplier_address' => 'Plot 44, GIDC Industrial Estate, Vapi',
            'payment_method' => 'Cheque',
            'cheque_no' => 'CHQ-882211',
            'description' => '50 reams of 75 GSM paper',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'A4 Paper Reams',
            'bill_no' => 'BILL-2026-909',
            'party_name' => 'Aditya Traders',
            'supplier_name' => 'National Paper Mill',
            'supplier_address' => 'Plot 44, GIDC Industrial Estate, Vapi',
            'payment_method' => 'Cheque',
            'cheque_no' => 'CHQ-882211',
            'unit_price' => 350.00,
        ]);
        $this->assertDatabaseHas('stocks', [
            'quantity' => 50,
        ]);
    }

    public function test_can_create_inventory_item_with_online_transaction_details(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'name' => 'Executive Office Chair',
            'unit_price' => 4500.00,
            'total_stock' => 10,
            'bill_no' => 'INV-FUR-101',
            'purchase_date' => '2026-09-05',
            'party_name' => 'Modern Furniture Co',
            'supplier_name' => 'Godrej Dealer',
            'supplier_address' => '12 Ring Road, Surat',
            'payment_method' => 'Online',
            'transaction_id' => 'UPI/2026/89891234',
            'description' => 'Ergonomic mesh chairs',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', [
            'name' => 'Executive Office Chair',
            'bill_no' => 'INV-FUR-101',
            'party_name' => 'Modern Furniture Co',
            'payment_method' => 'Online',
            'transaction_id' => 'UPI/2026/89891234',
        ]);
    }

    public function test_can_update_inventory_item_and_billing_fields(): void
    {
        $admin = $this->createAdmin();
        $item = InventoryItem::create([
            'name' => 'Marker Pens',
            'sku' => 'INV-PEN001',
            'bill_no' => 'BILL-OLD',
            'purchase_date' => '2026-09-01',
            'party_name' => 'Old Party',
            'supplier_name' => 'Old Supplier',
            'supplier_address' => 'Old Address',
            'payment_method' => 'Cash',
            'unit_price' => 50.00,
            'user_id' => $admin->id,
        ]);
        Stock::create([
            'inventory_item_id' => $item->id,
            'quantity' => 100,
            'location' => 'Initial Stock',
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.inventory.update', $item), [
            'name' => 'Whiteboard Marker Pens (Box of 10)',
            'sku' => 'INV-PEN001',
            'bill_no' => 'BILL-NEW-77',
            'purchase_date' => '2026-09-08',
            'party_name' => 'Stationery Mart',
            'supplier_name' => 'Camlin Direct',
            'supplier_address' => 'Station Road, Ahmedabad',
            'payment_method' => 'Cheque',
            'cheque_no' => 'CHQ-123456',
            'unit_price' => 220.00,
            'total_stock' => 120,
            'description' => 'Updated pack size to box',
        ]);

        $response->assertRedirect(route('admin.inventory.index'));
        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'name' => 'Whiteboard Marker Pens (Box of 10)',
            'bill_no' => 'BILL-NEW-77',
            'party_name' => 'Stationery Mart',
            'supplier_name' => 'Camlin Direct',
            'payment_method' => 'Cheque',
            'cheque_no' => 'CHQ-123456',
            'unit_price' => 220.00,
        ]);
    }

    public function test_inventory_csv_export_contains_new_columns_and_formatted_dates(): void
    {
        $admin = $this->createAdmin();
        $item = InventoryItem::create([
            'name' => 'HP Laser Toner',
            'sku' => 'INV-TONER01',
            'bill_no' => 'INV-HP-9988',
            'purchase_date' => '2026-09-08',
            'party_name' => 'Tech Solutions Ltd',
            'supplier_name' => 'HP Distributor',
            'supplier_address' => 'IT Park, Gandhinagar',
            'payment_method' => 'Online',
            'transaction_id' => 'TXN99887766',
            'unit_price' => 2800.00,
            'description' => 'Original toner cartridge',
            'user_id' => $admin->id,
        ]);
        Stock::create([
            'inventory_item_id' => $item->id,
            'quantity' => 5,
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.inventory.export_csv'));
        $response->assertOk();

        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('"Item Name",SKU,"Bill No",Date,"Party Name","Supplier Name","Supplier Address","Unit Price (INR)","Total Stock","Total Amount (INR)","Payment Method","Cheque No","Transaction ID",Description,"Created By"', $csv);
        $this->assertStringContainsString('HP Laser Toner', $csv);
        $this->assertStringContainsString('INV-HP-9988', $csv);
        $this->assertStringContainsString('08-09-2026', $csv);
        $this->assertStringContainsString('Tech Solutions Ltd', $csv);
        $this->assertStringContainsString('HP Distributor', $csv);
        $this->assertStringContainsString('TXN99887766', $csv);
        $this->assertStringContainsString('14000.00', $csv);
    }

    public function test_inventory_index_datewise_filter(): void
    {
        $admin = $this->createAdmin();
        $item1 = InventoryItem::create([
            'name' => 'Item Aug',
            'sku' => 'INV-AUG01',
            'purchase_date' => '2026-08-10',
            'unit_price' => 500.00,
            'user_id' => $admin->id,
        ]);
        Stock::create(['inventory_item_id' => $item1->id, 'quantity' => 2, 'user_id' => $admin->id]);

        $item2 = InventoryItem::create([
            'name' => 'Item Sep',
            'sku' => 'INV-SEP01',
            'purchase_date' => '2026-09-08',
            'unit_price' => 5000.00,
            'user_id' => $admin->id,
        ]);
        Stock::create(['inventory_item_id' => $item2->id, 'quantity' => 2, 'user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('admin.inventory.index', [
            'from_date' => '01-09-2026',
            'to_date' => '30-09-2026',
        ]));

        $response->assertOk();
        $response->assertSee('Item Sep');
        $response->assertDontSee('Item Aug');
        $response->assertSee('₹10,000.00');
    }

    private function createAdmin(): User
    {
        $session = Session::create(['session_name' => '2026-2027']);
        $admin = User::create([
            'name' => 'Inventory Manager',
            'email' => uniqid('inventory-admin-', true) . '@example.test',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'active_session_id' => $session->id,
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'college_name' => 'Test College ERP',
            'contact_number' => '9876543210',
            'email' => 'admin@college.test',
            'address' => 'Campus Address',
        ]);

        return $admin;
    }
}
