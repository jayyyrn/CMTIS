<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id('diagnosis_id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('tech_id');
            $table->text('findings');
            $table->text('recommended_action')->nullable();
            $table->boolean('is_major')->default(false);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->foreign('request_id')
                  ->references('request_id')->on('maintenance_requests')->onDelete('cascade');
            $table->foreign('tech_id')
                  ->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')
                  ->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};