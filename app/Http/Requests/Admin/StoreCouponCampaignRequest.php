<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'coupon_value' => ['required', 'numeric', 'min:0.01', 'lte:total_amount'],
            'expires_at' => ['required', 'date', 'after:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'coupon_value.lte' => 'The coupon value must be less than or equal to the total amount.',
            'expires_at.after' => 'The expiration date must be in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('total_amount') && $this->has('coupon_value')) {
            $totalAmount = (float) $this->total_amount;
            $couponValue = (float) $this->coupon_value;

            if ($couponValue > 0) {
                $this->merge([
                    'total_coupons' => (int) floor($totalAmount / $couponValue),
                ]);
            }
        }
    }
}
