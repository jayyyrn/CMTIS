<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->string('asset_no')->unique();
            $table->string('name');
            $table->string('location');
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->enum('status', ['working', 'under_repair', 'broken', 'retired'])->default('working');
            $table->date('acquired_date')->nullable();
            $table->timestamps();

            $table->foreign('dept_id')
                  ->references('dept_id')->on('departments')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};