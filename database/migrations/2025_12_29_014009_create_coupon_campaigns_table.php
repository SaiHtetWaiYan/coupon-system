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
        Schema::create('coupon_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('coupon_value', 10, 2);
            $table->integer('total_coupons');
            $table->dateTime('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_campaigns');
    }
};
