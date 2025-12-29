<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    /** @use HasFactory<\Database\Factories\CouponFactory> */
    use HasFactory;

    protected $fillable = [
        'coupon_campaign_id',
        'code',
        'value',
        'expires_at',
        'status',
        'used_at',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<CouponCampaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CouponCampaign::class, 'coupon_campaign_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return $this->status === 'unused' && ! $this->isExpired();
    }

    public function markAsUsed(?int $userId = null): bool
    {
        if (! $this->isUsable()) {
            return false;
        }

        return $this->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by' => $userId,
        ]);
    }

    public function markAsExpired(): bool
    {
        if ($this->status !== 'unused') {
            return false;
        }

        return $this->update(['status' => 'expired']);
    }

    public function disable(): bool
    {
        return $this->update(['status' => 'disabled']);
    }

    public function enable(): bool
    {
        if ($this->status !== 'disabled') {
            return false;
        }

        return $this->update(['status' => 'unused']);
    }

    public function scopeUnused($query)
    {
        return $query->where('status', 'unused');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', 'disabled');
    }
}
