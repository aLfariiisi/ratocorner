<?php

use App\Enums\UserRole;
use App\Models\CafeSetting;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('staff login screen can be rendered using custom staff login view', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Login Staf ERP');
    $response->assertSee('Ratocorner');
    // Public customer registration must NOT exist
    $response->assertDontSee('href="/register"');
});

test('public registration route is disabled and returns 404', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('staff can authenticate using login credentials', function () {
    $response = $this->post('/login', [
        'email' => 'kasir@ratocorner.com',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('staff cannot authenticate with invalid password', function () {
    $response = $this->post('/login', [
        'email' => 'kasir@ratocorner.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('staff can log out successfully', function () {
    $kasir = User::where('email', 'kasir@ratocorner.com')->first();

    $response = $this->actingAs($kasir)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});

test('kasir is forbidden from accessing reports and settings routes', function () {
    $kasir = User::where('email', 'kasir@ratocorner.com')->first();

    // Kasir can access dashboard
    $this->actingAs($kasir)->get('/admin/dashboard')->assertStatus(200);

    // Kasir can access tables management
    $this->actingAs($kasir)->get('/admin/tables')->assertStatus(200);

    // Kasir strictly FORBIDDEN from reports
    $this->actingAs($kasir)->get('/admin/reports/analytics')->assertStatus(403);
    $this->actingAs($kasir)->get('/admin/reports/financials')->assertStatus(403);
    $this->actingAs($kasir)->get('/admin/reports/hpp')->assertStatus(403);

    // Kasir strictly FORBIDDEN from settings
    $this->actingAs($kasir)->get('/admin/settings')->assertStatus(403);
});

test('manager and owner can access reports and settings routes', function () {
    $manager = User::where('email', 'manager@ratocorner.com')->first();
    $owner = User::where('email', 'owner@ratocorner.com')->first();

    // Manager can access reports & settings
    $this->actingAs($manager)->get('/admin/reports/financials')->assertStatus(200);
    $this->actingAs($manager)->get('/admin/settings')->assertStatus(200);

    // Owner can access reports & settings
    $this->actingAs($owner)->get('/admin/reports/financials')->assertStatus(200);
    $this->actingAs($owner)->get('/admin/settings')->assertStatus(200);
});

test('gates enforce role modification and audit log deletion restrictions', function () {
    $owner = User::where('email', 'owner@ratocorner.com')->first();
    $manager = User::where('email', 'manager@ratocorner.com')->first();
    $kasir = User::where('email', 'kasir@ratocorner.com')->first();

    // 1. Manager CANNOT modify Owner's role
    expect(Gate::forUser($manager)->allows('edit-user-role', [$owner, 'kasir']))->toBeFalse();

    // 2. Manager CANNOT promote any user to Owner
    expect(Gate::forUser($manager)->allows('edit-user-role', [$kasir, UserRole::Owner->value]))->toBeFalse();

    // 3. Manager CAN modify Kasir's role (to non-owner)
    expect(Gate::forUser($manager)->allows('edit-user-role', [$kasir, 'manager']))->toBeTrue();

    // 4. Manager and Kasir CANNOT delete audit logs
    expect(Gate::forUser($manager)->allows('delete-audit-log'))->toBeFalse();
    expect(Gate::forUser($kasir)->allows('delete-audit-log'))->toBeFalse();

    // 5. Owner CAN delete audit logs
    expect(Gate::forUser($owner)->allows('delete-audit-log'))->toBeTrue();
});

test('owner and manager can update cafe settings successfully', function () {
    $manager = User::where('email', 'manager@ratocorner.com')->first();

    $response = $this->actingAs($manager)->put('/admin/settings', [
        'name' => 'Ratocorner Coffee Hub',
        'tagline' => 'Best Specialty Coffee in Town',
        'email' => 'contact@ratocorner.com',
        'phone' => '081299998888',
        'address' => 'Jl. Senopati Baru No. 12, Jakarta',
        'wifi_name' => 'Ratocorner-SuperFast',
        'wifi_password' => 'kopi12345',
        'tax_percentage' => 11.00,
        'service_charge_percentage' => 5.00,
        'receipt_header' => '*** RATOCORNER COFFEE ***',
        'receipt_footer' => 'Terima kasih atas kunjungan Anda!',
    ]);

    $response->assertRedirect('/admin/settings');
    $response->assertSessionHas('success');

    $setting = CafeSetting::first();
    expect($setting->name)->toBe('Ratocorner Coffee Hub')
        ->and($setting->wifi_name)->toBe('Ratocorner-SuperFast')
        ->and($setting->wifi_password)->toBe('kopi12345');
});

test('table qr management crud and customer qr order view work completely', function () {
    $manager = User::where('email', 'manager@ratocorner.com')->first();

    // 1. Create table
    $response = $this->actingAs($manager)->post('/admin/tables', [
        'table_number' => '10',
        'name' => 'Meja 10 (Outdoor Garden)',
        'capacity' => 4,
        'location' => 'Outdoor',
        'status' => 'available',
        'is_active' => true,
    ]);

    $response->assertRedirect('/admin/tables');
    $table = Table::where('table_number', '10')->first();
    expect($table)->not->toBeNull()
        ->and($table->qr_code_token)->not->toBeEmpty();

    // 2. View Table Detail & QR Code
    $this->actingAs($manager)->get("/admin/tables/{$table->id}")
        ->assertStatus(200)
        ->assertSee('Meja 10')
        ->assertSee($table->qr_code_token);

    // 3. View Printable QR Stand Card
    $this->actingAs($manager)->get("/admin/tables/{$table->id}/print")
        ->assertStatus(200)
        ->assertSee('SCAN UNTUK PESAN')
        ->assertSee('10');

    // 4. View Batch Printable Stand Cards
    $this->actingAs($manager)->get('/admin/tables/print-all')
        ->assertStatus(200)
        ->assertSee('Cetak Semua Meja QR');

    // 5. Update Table
    $this->actingAs($manager)->put("/admin/tables/{$table->id}", [
        'table_number' => '10-VIP',
        'name' => 'Meja 10 VIP',
        'capacity' => 6,
        'location' => 'VIP Outdoor',
        'status' => 'occupied',
        'is_active' => true,
    ])->assertRedirect('/admin/tables');

    $table->refresh();
    expect($table->table_number)->toBe('10-VIP')
        ->and($table->capacity)->toBe(6)
        ->and($table->status)->toBe('occupied');

    // 6. Customer accesses QR ordering link via token
    $customerResponse = $this->get("/order/{$table->qr_code_token}");
    $customerResponse->assertStatus(200);
    $customerResponse->assertSee('10-VIP');
    $customerResponse->assertSee('Daftar Menu');

    // 7. Delete Table
    $this->actingAs($manager)->delete("/admin/tables/{$table->id}")
        ->assertRedirect('/admin/tables');
    expect(Table::find($table->id))->toBeNull();
});
