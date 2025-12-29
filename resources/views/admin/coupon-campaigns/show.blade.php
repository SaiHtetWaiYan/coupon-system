<x-admin.layouts.app>
    <div class="space-y-6" x-data="{
        deleteCouponId: null,
        deleteCouponCode: '',
        disableCouponId: null,
        disableCouponCode: '',
        enableCouponId: null,
        enableCouponCode: '',
        redeemedCoupons: {},
        init() {
            if (window.Echo) {
                window.Echo.channel('coupons')
                    .listen('.coupon.redeemed', (e) => {
                        if (e.campaign_id === {{ $couponCampaign->id }}) {
                            this.redeemedCoupons[e.coupon_id] = {
                                status: e.status,
                                used_at: e.used_at,
                                redeemed_by: e.redeemed_by
                            };
                        }
                    });
            }
        }
    }">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $couponCampaign->title }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View campaign details and manage coupons.</p>
            </div>
            <div class="mt-4 flex gap-x-3 sm:mt-0">
                <a href="{{ route('admin.coupon-campaigns.edit', $couponCampaign) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Edit Campaign
                </a>
                <a href="{{ route('admin.coupon-campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $currencySymbol }}{{ number_format($couponCampaign->total_amount, 2) }}</div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Coupon Value</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $currencySymbol }}{{ number_format($couponCampaign->coupon_value, 2) }}</div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Coupons</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $couponCampaign->total_coupons }}</div>
            </div>
            <div class="overflow-hidden rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</div>
                <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $couponCampaign->expires_at->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-700 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Coupons</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.coupon-campaigns.show', $couponCampaign) }}"
                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ !$status ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            All ({{ array_sum($statusCounts) }})
                        </a>
                        <a href="{{ route('admin.coupon-campaigns.show', ['coupon_campaign' => $couponCampaign, 'status' => 'unused']) }}"
                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ $status === 'unused' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50' }}">
                            Unused ({{ $statusCounts['unused'] }})
                        </a>
                        <a href="{{ route('admin.coupon-campaigns.show', ['coupon_campaign' => $couponCampaign, 'status' => 'used']) }}"
                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ $status === 'used' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50' }}">
                            Used ({{ $statusCounts['used'] }})
                        </a>
                        <a href="{{ route('admin.coupon-campaigns.show', ['coupon_campaign' => $couponCampaign, 'status' => 'expired']) }}"
                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ $status === 'expired' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50' }}">
                            Expired ({{ $statusCounts['expired'] }})
                        </a>
                        <a href="{{ route('admin.coupon-campaigns.show', ['coupon_campaign' => $couponCampaign, 'status' => 'disabled']) }}"
                           class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ $status === 'disabled' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                            Disabled ({{ $statusCounts['disabled'] }})
                        </a>
                    </div>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">Code</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Value</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Expires</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Used At</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Redeemed By</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($coupons as $coupon)
                        <tr x-data="{ couponId: {{ $coupon->id }} }">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $coupon->code }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($coupon->value, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <template x-if="redeemedCoupons[couponId]">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">Used</span>
                                </template>
                                <template x-if="!redeemedCoupons[couponId]">
                                    <span>
                                        @if($coupon->status === 'unused')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">Unused</span>
                                        @elseif($coupon->status === 'used')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">Used</span>
                                        @elseif($coupon->status === 'expired')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">Expired</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-300">Disabled</span>
                                        @endif
                                    </span>
                                </template>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $coupon->expires_at->format('M d, Y') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <template x-if="redeemedCoupons[couponId]">
                                    <span x-text="new Date(redeemedCoupons[couponId].used_at).toLocaleString()"></span>
                                </template>
                                <template x-if="!redeemedCoupons[couponId]">
                                    <span>{{ $coupon->used_at ? $coupon->used_at->format('M d, Y H:i') : '-' }}</span>
                                </template>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <template x-if="redeemedCoupons[couponId]">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="redeemedCoupons[couponId].redeemed_by.name"></div>
                                            <div class="text-gray-500 dark:text-gray-400" x-text="redeemedCoupons[couponId].redeemed_by.email"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!redeemedCoupons[couponId]">
                                    <span>
                                        @if($coupon->usedByUser)
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $coupon->usedByUser->name }}</div>
                                                    <div class="text-gray-500 dark:text-gray-400">{{ $coupon->usedByUser->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </template>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a
                                    href="{{ route('admin.coupons.image.download', $coupon) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                                    target="_blank"
                                >Image</a>
                                @if($coupon->status === 'unused')
                                    <template x-if="!redeemedCoupons[couponId]">
                                        <button
                                            type="button"
                                            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 mr-3"
                                            x-on:click="disableCouponId = {{ $coupon->id }}; disableCouponCode = '{{ $coupon->code }}'; $dispatch('open-modal', 'confirm-coupon-disable')"
                                        >Disable</button>
                                    </template>
                                @endif
                                @if($coupon->status === 'disabled')
                                    <button
                                        type="button"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3"
                                        x-on:click="enableCouponId = {{ $coupon->id }}; enableCouponCode = '{{ $coupon->code }}'; $dispatch('open-modal', 'confirm-coupon-enable')"
                                    >Enable</button>
                                @endif
                                <button
                                    type="button"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    x-on:click="deleteCouponId = {{ $coupon->id }}; deleteCouponCode = '{{ $coupon->code }}'; $dispatch('open-modal', 'confirm-coupon-deletion')"
                                >Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links() }}
        </div>

        <x-modal name="confirm-coupon-disable" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Disable Coupon
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to disable coupon <span class="font-mono font-semibold" x-text="disableCouponCode"></span>? This coupon will no longer be redeemable.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600"
                        x-on:click="$dispatch('close-modal', 'confirm-coupon-disable')"
                    >
                        Cancel
                    </button>

                    <form :action="`{{ url('dashboard/coupons') }}/${disableCouponId}/disable`" method="POST">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500"
                        >
                            Disable
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        <x-modal name="confirm-coupon-enable" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Enable Coupon
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to enable coupon <span class="font-mono font-semibold" x-text="enableCouponCode"></span>? This coupon will be redeemable again.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600"
                        x-on:click="$dispatch('close-modal', 'confirm-coupon-enable')"
                    >
                        Cancel
                    </button>

                    <form :action="`{{ url('dashboard/coupons') }}/${enableCouponId}/enable`" method="POST">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                        >
                            Enable
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        <x-modal name="confirm-coupon-deletion" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete Coupon
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete coupon <span class="font-mono font-semibold" x-text="deleteCouponCode"></span>? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600"
                        x-on:click="$dispatch('close-modal', 'confirm-coupon-deletion')"
                    >
                        Cancel
                    </button>

                    <form :action="`{{ url('dashboard/coupons') }}/${deleteCouponId}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
</x-admin.layouts.app>
