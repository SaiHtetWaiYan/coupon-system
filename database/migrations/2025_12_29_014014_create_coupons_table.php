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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_campaign_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('value', 10, 2);
            $table->dateTime('expires_at');
            $table->enum('status', ['unused', 'used', 'expired', 'disabled'])->default('unused');
            $table->dateTime('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
