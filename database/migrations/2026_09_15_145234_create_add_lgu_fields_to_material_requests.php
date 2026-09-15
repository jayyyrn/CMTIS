<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            // Who endorsed to LGU
            $table->unsignedBigInteger('endorsed_to_lgu_by')->nullable()->after('approved_by');
            $table->timestamp('endorsed_to_lgu_at')->nullable()->after('endorsed_to_lgu_by');

            // LGU-side tracking
            $table->string('epr_no')->nullable()->after('endorsed_to_lgu_at');
            $table->string('pr_no')->nullable()->after('epr_no');
            $table->string('po_no')->nullable()->after('pr_no');
            $table->text('lgu_notes')->nullable()->after('po_no');

            $table->foreign('endorsed_to_lgu_by')
                  ->references('user_id')->on('users')->onDelete('set null');
        });

        // Extend status enum to include LGU stages
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM(
            'pending',
            'endorsed_to_head',
            'endorsed_to_lgu',
            'approved',
            'released',
            'rejected',
            'returned'
        ) DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign(['endorsed_to_lgu_by']);
            $table->dropColumn([
                'endorsed_to_lgu_by', 'endorsed_to_lgu_at',
                'epr_no', 'pr_no', 'po_no', 'lgu_notes',
            ]);
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM(
            'pending','approved','released','rejected','returned'
        ) DEFAULT 'pending'");
    }
};