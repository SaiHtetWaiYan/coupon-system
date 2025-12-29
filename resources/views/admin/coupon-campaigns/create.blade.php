<x-admin.layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Coupon Campaign</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set up a new coupon campaign. Coupons will be automatically generated based on total amount divided by coupon value.</p>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <form action="{{ route('admin.coupon-campaigns.store') }}" method="POST" class="space-y-6 p-6" x-data="campaignForm()">
                @csrf

                <div>
                    <x-input-label for="title" :value="__('Campaign Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus placeholder="e.g., Holiday Sale 2024" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="total_amount" :value="__('Total Campaign Amount (' . $currencySymbol . ')')" />
                        <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('total_amount')" required x-model="totalAmount" placeholder="e.g., 1000.00" />
                        <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="coupon_value" :value="__('Value Per Coupon (' . $currencySymbol . ')')" />
                        <x-text-input id="coupon_value" name="coupon_value" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('coupon_value')" required x-model="couponValue" placeholder="e.g., 10.00" />
                        <x-input-error :messages="$errors->get('coupon_value')" class="mt-2" />
                    </div>
                </div>

                <div x-show="calculatedCoupons > 0" class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/30">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                This campaign will generate <span class="font-semibold" x-text="calculatedCoupons"></span> coupons worth {{ $currencySymbol }}<span x-text="parseFloat(couponValue).toFixed(2)"></span> each.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="expires_at" :value="__('Expiration Date')" />
                    <x-text-input id="expires_at" name="expires_at" type="date" class="mt-1 block w-full" :value="old('expires_at')" required :min="date('Y-m-d', strtotime('+1 day'))" />
                    <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-x-4">
                    <a href="{{ route('admin.coupon-campaigns.index') }}" class="text-sm font-semibold text-gray-900 dark:text-gray-300">Cancel</a>
                    <x-primary-button>Create Campaign</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function campaignForm() {
            return {
                totalAmount: {{ old('total_amount', 0) }},
                couponValue: {{ old('coupon_value', 0) }},
                get calculatedCoupons() {
                    if (this.totalAmount > 0 && this.couponValue > 0) {
                        return Math.floor(this.totalAmount / this.couponValue);
                    }
                    return 0;
                }
            }
        }
    </script>
</x-admin.layouts.app>
