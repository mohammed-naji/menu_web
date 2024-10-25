<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');

            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_address')->nullable();

            $table->enum('order_type', ['in-restaurant', 'delivery', 'takeaway'])->default('in-restaurant');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total', 8, 2);

            $table->string('payment_method')->default('cash');
            $table->boolean('is_paid')->default(false);
            $table->string('payment_status')->default('pending');
            $table->string('payment_transaction_id')->nullable();

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
