<?php

namespace Database\Factories;

use App\Models\CouponCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coupon_campaign_id' => CouponCampaign::factory(),
            'code' => strtoupper(Str::random(8)),
            'value' => fake()->randomElement([5, 10, 25, 50, 100]),
            'expires_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'status' => 'unused',
            'used_at' => null,
            'used_by' => null,
        ];
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'used',
            'used_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disabled',
        ]);
    }
}
