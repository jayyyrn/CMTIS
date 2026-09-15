<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('reference_no')->unique();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->unsignedBigInteger('assigned_tech_id')->nullable();
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->string('location');
            $table->text('problem_description');
            $table->enum('work_type', [
                'electrical', 'aircon', 'carpentry',
                'fabrication', 'plumbing', 'general', 'other'
            ])->default('general');
            $table->string('photo_evidence')->nullable();
            $table->string('after_repair_photo')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'new', 'reviewed', 'assigned', 'inspecting',
                'diagnosed', 'waiting_for_materials',
                'pending_verification', 'verified',
                'repairing', 'repaired', 'closed', 'rejected'
            ])->default('new');
            $table->date('date_reported');
            $table->timestamp('date_assigned')->nullable();
            $table->timestamp('date_completed')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')
                  ->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('equipment_id')
                  ->references('equipment_id')->on('equipment')->onDelete('set null');
            $table->foreign('assigned_tech_id')
                  ->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('dept_id')
                  ->references('dept_id')->on('departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};