<?php

namespace Database\Seeders\Admin;

use App\Constants\Constant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 1️⃣ CREATE ROLES
         */
        $roles = [
            'Admin'            => 'admin',
            'User'             => 'web',
            'Store Manager'    => 'admin',
            'Product Manager'  => 'admin',
            'Order Manager'    => 'admin',
            'Staff'            => 'admin',
            'Customer Support' => 'admin',
        ];

        $roleObjects = [];

        foreach ($roles as $label => $guard) {
            $slug = Str::slug($label);

            $roleObjects[$slug] = Role::updateOrCreate(
                [
                    'name'       => $slug,
                    'guard_name' => $guard,
                ],
                [
                    'display_name' => Str::title($label),
                ]
            );
        }

        /**
         * 2️⃣ CREATE USERS
         */
        $usersData = [
            ['name' => 'Super Admin',        'email' => 'admin@gmail.com',           'phone' => '9999999999', 'role' => 'admin',            'password' => 'Admin@123'],
            ['name' => 'Store Manager',      'email' => 'store_manager@gmail.com',  'phone' => '8888888888', 'role' => 'store-manager',    'password' => 'Admin@123'],
            ['name' => 'Product Manager',    'email' => 'product_manager@gmail.com', 'phone' => '7777777777', 'role' => 'product-manager',  'password' => 'Admin@123'],
            ['name' => 'Order Manager',      'email' => 'order_manager@gmail.com',  'phone' => '6666666666', 'role' => 'order-manager',    'password' => 'Admin@123'],
            ['name' => 'Staff Manager',      'email' => 'staff_manager@gmail.com',  'phone' => '5555555555', 'role' => 'staff',            'password' => 'Admin@123'],
            ['name' => 'Customer Manager',   'email' => 'support_manager@gmail.com', 'phone' => '4444444444', 'role' => 'customer-support', 'password' => 'Admin@123'],
            ['name' => 'User One',           'email' => 'user1@gmail.com',          'phone' => '9000000001', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Two',           'email' => 'user2@gmail.com',          'phone' => '9000000002', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Three',         'email' => 'user3@gmail.com',          'phone' => '9000000003', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Four',          'email' => 'user4@gmail.com',          'phone' => '9000000004', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Five',          'email' => 'user5@gmail.com',          'phone' => '9000000005', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Six',           'email' => 'user6@gmail.com',          'phone' => '9000000006', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Seven',         'email' => 'user7@gmail.com',          'phone' => '9000000007', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Eight',         'email' => 'user8@gmail.com',          'phone' => '9000000008', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Nine',          'email' => 'user9@gmail.com',          'phone' => '9000000009', 'role' => 'user',             'password' => 'User@123'],
            ['name' => 'User Ten',           'email' => 'user10@gmail.com',         'phone' => '9000000010', 'role' => 'user',             'password' => 'User@123'],
        ];

        foreach ($usersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'phone'             => $data['phone'],
                    'phonecode'         => '91',
                    'email_verified_at' => now(),
                    'password'          => Hash::make($data['password']),
                    'timezone'          => 'Asia/Kolkata',
                    'currency'          => 'USD',
                    'status'            => Constant::ACTIVE,
                    'profile_complete'  => true,
                    'last_login_at'     => now(),
                ]
            );

            // Assign role using slug
            $slug = Str::slug($data['role']);
            if (isset($roleObjects[$slug])) {
                $user->assignRole($roleObjects[$slug]);
            }
        }
    }
}
