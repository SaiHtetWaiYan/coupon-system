<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponCampaign>
 */
class CouponCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $couponValue = fake()->randomElement([5, 10, 25, 50, 100]);
        $totalCoupons = fake()->numberBetween(10, 100);

        return [
            'title' => fake()->words(3, true).' Campaign',
            'total_amount' => $couponValue * $totalCoupons,
            'coupon_value' => $couponValue,
            'total_coupons' => $totalCoupons,
            'expires_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
