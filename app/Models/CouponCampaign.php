<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CouponCampaign extends Model
{
    /** @use HasFactory<\Database\Factories\CouponCampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'total_amount',
        'coupon_value',
        'total_coupons',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'coupon_value' => 'decimal:2',
            'total_coupons' => 'integer',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Coupon, $this>
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function generateCoupons(): void
    {
        $coupons = [];
        $now = now();

        for ($i = 0; $i < $this->total_coupons; $i++) {
            $coupons[] = [
                'coupon_campaign_id' => $this->id,
                'code' => $this->generateUniqueCode(),
                'value' => $this->coupon_value,
                'expires_at' => $this->expires_at,
                'status' => 'unused',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->coupons()->insert($coupons);
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    public function getUsedCouponsCountAttribute(): int
    {
        return $this->coupons()->where('status', 'used')->count();
    }

    public function getUnusedCouponsCountAttribute(): int
    {
        return $this->coupons()->where('status', 'unused')->count();
    }

    public function getExpiredCouponsCountAttribute(): int
    {
        return $this->coupons()->where('status', 'expired')->count();
    }
}
