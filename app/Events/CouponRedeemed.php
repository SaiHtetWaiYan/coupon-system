<?php

namespace App\Events;

use App\Models\Coupon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CouponRedeemed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Coupon $coupon,
        public string $redeemedByName,
        public string $redeemedByEmail
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('coupons'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'coupon.redeemed';
    }

    public function broadcastWith(): array
    {
        return [
            'coupon_id' => $this->coupon->id,
            'coupon_code' => $this->coupon->code,
            'coupon_value' => (float) $this->coupon->value,
            'campaign_id' => $this->coupon->coupon_campaign_id,
            'status' => $this->coupon->status,
            'used_at' => $this->coupon->used_at?->toISOString(),
            'redeemed_by' => [
                'name' => $this->redeemedByName,
                'email' => $this->redeemedByEmail,
            ],
        ];
    }
}
