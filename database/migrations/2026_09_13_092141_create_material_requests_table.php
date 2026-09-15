<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id('mat_req_id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('released_by')->nullable();
            $table->integer('qty_requested');
            $table->integer('qty_released')->default(0);
            $table->integer('qty_used')->default(0);
            $table->integer('qty_returned')->default(0);
            $table->enum('status', [
                'pending', 'approved', 'released', 'rejected', 'returned'
            ])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->foreign('request_id')
                  ->references('request_id')->on('maintenance_requests')->onDelete('cascade');
            $table->foreign('item_id')
                  ->references('item_id')->on('inventory')->onDelete('cascade');
            $table->foreign('requested_by')
                  ->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')
                  ->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('released_by')
                  ->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};