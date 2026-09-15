<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'teacher', 'coordinator', 'technician', 'lead_technician',
            'inventory_officer', 'head', 'admin'
        )");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'teacher', 'coordinator', 'technician', 'lead_technician',
            'inventory_officer', 'admin'
        )");
    }
};