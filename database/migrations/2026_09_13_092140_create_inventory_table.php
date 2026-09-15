<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->string('unit')->default('pcs');
            $table->integer('qty_on_hand')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};