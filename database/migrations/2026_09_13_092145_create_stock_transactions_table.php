<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->enum('type', ['in', 'out', 'return', 'adjustment']);
            $table->integer('quantity');
            $table->string('supplier')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('handled_by');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('item_id')
                  ->references('item_id')->on('inventory')->onDelete('cascade');
            $table->foreign('handled_by')
                  ->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};