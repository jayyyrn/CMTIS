<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id('tech_id');
            $table->unsignedBigInteger('user_id');
            $table->string('specialization')->nullable();
            $table->integer('active_tasks')->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};