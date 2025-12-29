<x-admin.layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Campaign</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update campaign settings. Note: Total amount and coupon value cannot be changed after creation.</p>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <form action="{{ route('admin.coupon-campaigns.update', $couponCampaign) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="title" :value="__('Campaign Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $couponCampaign->title)" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="total_amount" :value="__('Total Campaign Amount ($)')" />
                        <x-text-input id="total_amount" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" :value="number_format($couponCampaign->total_amount, 2)" disabled />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cannot be changed after creation.</p>
                    </div>

                    <div>
                        <x-input-label for="coupon_value" :value="__('Value Per Coupon ($)')" />
                        <x-text-input id="coupon_value" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700" :value="number_format($couponCampaign->coupon_value, 2)" disabled />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cannot be changed after creation.</p>
                    </div>
                </div>

                <div>
                    <x-input-label for="expires_at" :value="__('Expiration Date')" />
                    <x-text-input id="expires_at" name="expires_at" type="date" class="mt-1 block w-full" :value="old('expires_at', $couponCampaign->expires_at->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Updating this will also update the expiration date of all unused coupons.</p>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700" {{ old('is_active', $couponCampaign->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Campaign is active</label>
                </div>

                <div class="flex items-center justify-end gap-x-4">
                    <a href="{{ route('admin.coupon-campaigns.show', $couponCampaign) }}" class="text-sm font-semibold text-gray-900 dark:text-gray-300">Cancel</a>
                    <x-primary-button>Update Campaign</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layouts.app>
