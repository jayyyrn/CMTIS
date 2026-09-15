<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Inventory;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = ['CCS', 'COE', 'CBA', 'CAS', 'Admin'];
        $deptIds = [];
        foreach ($departments as $d) {
            $deptIds[$d] = Department::create(['dept_name' => $d])->dept_id;
        }

        $users = [
            ['Admin',       'User',       'admin',      'admin@campus.test',       'admin',             null],
            ['Juan',        'Dela Cruz',  'teacher1',   'teacher1@campus.test',    'teacher',           'CCS'],
            ['Maria',       'Santos',     'teacher2',   'teacher2@campus.test',    'teacher',           'COE'],
            ['Rico',        'Coordinator','coord1',     'coord1@campus.test',      'coordinator',       'CCS'],
            ['Tom',         'Technician', 'tech1',      'tech1@campus.test',       'technician',        null],
            ['Liza',        'Lead Tech',  'leadtech',   'leadtech@campus.test',    'lead_technician',   null],
            ['Ivan',        'Inventory',  'inventory',  'inventory@campus.test',   'inventory_officer', null],
        ];

        foreach ($users as [$first, $last, $username, $email, $role, $dept]) {
            $user = User::create([
                'first_name' => $first,
                'last_name'  => $last,
                'username'   => $username,
                'email'      => $email,
                'password'   => Hash::make('password'),
                'role'       => $role,
                'dept_id'    => $dept ? $deptIds[$dept] : null,
                'status'     => 'active',
            ]);

            if (in_array($role, ['technician', 'lead_technician'], true)) {
                Technician::create([
                    'user_id'        => $user->user_id,
                    'specialization' => 'General',
                ]);
            }
        }

        Equipment::create(['asset_no' => 'AST-0001', 'name' => 'Projector',    'location' => 'Room 201',     'dept_id' => $deptIds['CCS'], 'status' => 'working']);
        Equipment::create(['asset_no' => 'AST-0002', 'name' => 'Aircon Unit',  'location' => 'Faculty Room', 'dept_id' => $deptIds['CCS'], 'status' => 'working']);
        Equipment::create(['asset_no' => 'AST-0003', 'name' => 'Chair',        'location' => 'Room 105',     'dept_id' => $deptIds['COE'], 'status' => 'working']);

        Inventory::create(['item_name' => 'LED Bulb 9W',      'category' => 'Lighting', 'unit' => 'pcs', 'qty_on_hand' => 42, 'low_stock_threshold' => 10]);
        Inventory::create(['item_name' => 'Faucet Washer',    'category' => 'Plumbing', 'unit' => 'pcs', 'qty_on_hand' => 6,  'low_stock_threshold' => 10]);
        Inventory::create(['item_name' => 'Paint (White) 1L', 'category' => 'Painting', 'unit' => 'can', 'qty_on_hand' => 11, 'low_stock_threshold' => 5]);
        Inventory::create(['item_name' => 'PVC Pipe 1"',      'category' => 'Plumbing', 'unit' => 'pcs', 'qty_on_hand' => 3,  'low_stock_threshold' => 5]);
    }
}