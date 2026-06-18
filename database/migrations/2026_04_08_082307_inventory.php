<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('gcash_amount', 10, 2)->default(0);
            $table->string('gcash_reference')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->enum('status', ['completed', 'cancelled', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
