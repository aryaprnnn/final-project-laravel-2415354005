<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin ERP',
            'email' => 'admin@erp.com',
            'password' => bcrypt('password123'),
        ]);

        $customer1 = Customer::create([
            'customer_id' => 'CUST-001',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta, Indonesia',
            'status' => true,
        ]);

        $customer2 = Customer::create([
            'customer_id' => 'CUST-002',
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'phone' => '08776655443',
            'address' => 'Bandung, Indonesia',
            'status' => true,
        ]);

        $service1 = Service::create([
            'name' => 'Internet Fiber 50Mbps',
            'price' => 350000,
            'description' => 'Layanan internet cepat untuk rumah tangga',
            'status' => true,
        ]);

        $service2 = Service::create([
            'name' => 'IPTV Premium',
            'price' => 150000,
            'description' => 'Layanan TV kabel dengan channel HD',
            'status' => true,
        ]);

        $service3 = Service::create([
            'name' => 'Hosting Bisnis',
            'price' => 500000,
            'description' => 'Layanan hosting untuk kebutuhan enterprise',
            'status' => true,
        ]);

        Subscription::create([
            'customer_id' => $customer1->id,
            'service_id' => $service1->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        Subscription::create([
            'customer_id' => $customer1->id,
            'service_id' => $service2->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'trial',
        ]);

        Subscription::create([
            'customer_id' => $customer2->id,
            'service_id' => $service3->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);
    }
}
