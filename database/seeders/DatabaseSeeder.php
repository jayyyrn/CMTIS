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
            ['Dr.', 'Tangon', 'drtangon', 'drtangon@campus.test', 'head', null, null],
            ['Admin', 'User', 'admin', 'admin@campus.test', 'admin', null, null],
            ['Juan', 'Dela Cruz', 'teacher1', 'teacher1@campus.test', 'teacher', 'CCS', null],
            ['Maria', 'Santos', 'teacher2', 'teacher2@campus.test', 'teacher', 'COE', null],
            ['Rico', 'Coordinator', 'coord1', 'coord1@campus.test', 'coordinator', 'CCS', null],
            ['Tom', 'Technician', 'tech1', 'tech1@campus.test', 'technician', null, 'electrical'],
            ['Liza', 'Lead Tech', 'leadtech', 'leadtech@campus.test', 'lead_technician', null, 'general'],
            ['Ivan', 'Inventory', 'inventory', 'inventory@campus.test', 'inventory_officer', null, null],
        ];

        foreach ($users as [$first, $last, $username, $email, $role, $dept, $spec]) {
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
                    'specialization' => $spec ?? 'general',
                ]);
            }
        }

        Equipment::create(['asset_no' => 'AST-0001', 'name' => 'Projector', 'location' => 'Room 201', 'dept_id' => $deptIds['CCS'], 'status' => 'working']);
        Equipment::create(['asset_no' => 'AST-0002', 'name' => 'Aircon Unit', 'location' => 'Faculty Room', 'dept_id' => $deptIds['CCS'], 'status' => 'working']);
        Equipment::create(['asset_no' => 'AST-0003', 'name' => 'Chair', 'location' => 'Room 105', 'dept_id' => $deptIds['COE'], 'status' => 'working']);
        Equipment::create(['asset_no' => 'AST-0004', 'name' => 'Faucet', 'location' => 'Comfort Room 1F', 'dept_id' => $deptIds['CCS'], 'status' => 'working']);

        Inventory::create(['item_name' => 'LED Bulb 9W', 'category' => 'electrical', 'unit' => 'pcs', 'qty_on_hand' => 42, 'low_stock_threshold' => 10]);
        Inventory::create(['item_name' => 'Electrical Wire (10m)', 'category' => 'electrical', 'unit' => 'roll', 'qty_on_hand' => 12, 'low_stock_threshold' => 5]);
        Inventory::create(['item_name' => 'Faucet Washer', 'category' => 'plumbing', 'unit' => 'pcs', 'qty_on_hand' => 6, 'low_stock_threshold' => 10]);
        Inventory::create(['item_name' => 'Paint (White) 1L', 'category' => 'carpentry', 'unit' => 'can', 'qty_on_hand' => 11, 'low_stock_threshold' => 5]);
        Inventory::create(['item_name' => 'PVC Pipe 1"', 'category' => 'plumbing', 'unit' => 'pcs', 'qty_on_hand' => 3, 'low_stock_threshold' => 5]);
        Inventory::create(['item_name' => 'Nails (Assorted)', 'category' => 'carpentry', 'unit' => 'box', 'qty_on_hand' => 20, 'low_stock_threshold' => null]);
        Inventory::create(['item_name' => 'Refrigerant R32', 'category' => 'other', 'unit' => 'kg', 'qty_on_hand' => 8, 'low_stock_threshold' => 3]);
        Inventory::create(['item_name' => 'Hammer', 'category' => 'tools', 'unit' => 'pcs', 'qty_on_hand' => 5, 'low_stock_threshold' => null]);
    }
}