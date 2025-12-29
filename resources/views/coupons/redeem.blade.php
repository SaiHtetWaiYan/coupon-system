<x-admin.layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redeem Coupon</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your coupon code to check its validity and redeem it.</p>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800" x-data="couponSearch()">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Search Coupon</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your 8-character coupon code to check its status.</p>

                <div class="mt-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <x-input-label for="code" :value="__('Coupon Code')" />
                            <x-text-input
                                id="code"
                                type="text"
                                class="mt-1 block w-full font-mono uppercase"
                                x-model="code"
                                maxlength="8"
                                placeholder="XXXXXXXX"
                                x-on:input="code = code.toUpperCase(); result = null"
                            />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>
                        <div class="flex items-end pb-0.5 pt-6">
                            <button
                                type="button"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 disabled:opacity-50 dark:text-indigo-400 dark:hover:text-indigo-300"
                                x-on:click="searchCoupon"
                                x-bind:disabled="loading || code.length !== 8"
                                x-text="loading ? 'Searching...' : 'Search'"
                            ></button>
                        </div>
                    </div>
                </div>

                <!-- Not Found -->
                <div x-show="result && !result.found" x-cloak class="mt-6 rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/30">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200" x-text="result ? result.message : ''"></p>
                        </div>
                    </div>
                </div>

                <!-- Found - Valid -->
                <div x-show="result && result.found && result.status === 'valid'" x-cloak class="mt-6 rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/30">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-800 dark:text-green-200">Valid</span>
                                <span class="font-mono text-lg font-bold text-gray-900 dark:text-white" x-text="result && result.coupon ? result.coupon.code : ''"></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Campaign: <span class="font-medium" x-text="result && result.coupon ? result.coupon.campaign : ''"></span>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Expires: <span class="font-medium" x-text="result && result.coupon ? result.coupon.expires_at : ''"></span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $currencySymbol }}<span x-text="result && result.coupon ? parseFloat(result.coupon.value).toFixed(2) : '0.00'"></span></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Discount Value</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('coupons.redeem') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="code" x-bind:value="code">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Redeem This Coupon
                        </button>
                    </form>
                </div>

                <!-- Found - Invalid (used, expired, disabled, campaign inactive) -->
                <div x-show="result && result.found && result.status !== 'valid'" x-cloak class="mt-6 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-800 dark:bg-red-900/30">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-800 dark:text-red-200"
                                      x-text="result ? (result.status === 'already_used' ? 'Used' : result.status === 'expired' ? 'Expired' : result.status === 'disabled' ? 'Disabled' : 'Inactive') : ''"></span>
                                <span class="font-mono text-lg font-bold text-gray-900 dark:text-white" x-text="result && result.coupon ? result.coupon.code : ''"></span>
                            </div>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="result ? result.message : ''"></p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Campaign: <span class="font-medium" x-text="result && result.coupon ? result.coupon.campaign : ''"></span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-gray-400 line-through">{{ $currencySymbol }}<span x-text="result && result.coupon ? parseFloat(result.coupon.value).toFixed(2) : '0.00'"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function couponSearch() {
            return {
                code: '',
                loading: false,
                result: null,

                async searchCoupon() {
                    if (this.code.length !== 8) return;

                    this.loading = true;
                    this.result = null;

                    try {
                        const response = await fetch('{{ route('coupons.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ code: this.code })
                        });

                        this.result = await response.json();
                    } catch (error) {
                        this.result = {
                            found: false,
                            message: 'An error occurred. Please try again.'
                        };
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-admin.layouts.app>
