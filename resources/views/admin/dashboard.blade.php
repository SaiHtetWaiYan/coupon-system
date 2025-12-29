<x-admin.layouts.app>
    <div class="space-y-8" x-data="{
        stats: {
            unusedCoupons: {{ $stats['unused_coupons'] ?? 0 }},
            usedCoupons: {{ $stats['used_coupons'] ?? 0 }},
            redeemedValue: {{ (float) ($stats['redeemed_value'] ?? 0) }}
        },
        campaignUsedCounts: {},
        currencySymbol: '{{ $currencySymbol }}',
        init() {
            if (window.Echo) {
                window.Echo.channel('coupons')
                    .listen('.coupon.redeemed', (e) => {
                        this.stats.unusedCoupons--;
                        this.stats.usedCoupons++;
                        this.stats.redeemedValue += parseFloat(e.coupon_value) || 0;
                        if (!this.campaignUsedCounts[e.campaign_id]) {
                            this.campaignUsedCounts[e.campaign_id] = 0;
                        }
                        this.campaignUsedCounts[e.campaign_id]++;
                    });
            }
        },
        formatCurrency(value) {
            return this.currencySymbol + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coupon campaign overview and statistics.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-admin.components.stat-card title="Total Campaigns" :value="$stats['total_campaigns']" />
            <x-admin.components.stat-card title="Active Campaigns" :value="$stats['active_campaigns']" />
            <x-admin.components.stat-card title="Total Coupons" :value="$stats['total_coupons']" />
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Unused Coupons</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white" x-text="stats.unusedCoupons"></dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Used Coupons</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white" x-text="stats.usedCoupons"></dd>
            </div>
            <x-admin.components.stat-card title="Expired Coupons" :value="$stats['expired_coupons']" />
            <x-admin.components.stat-card title="Total Campaign Value" :value="$currencySymbol . number_format($stats['total_campaign_value'], 2)" />
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Redeemed Value</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white" x-text="formatCurrency(stats.redeemedValue)"></dd>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Recent Campaigns</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentCampaigns as $campaign)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-x-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                    <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <a href="{{ route('admin.coupon-campaigns.show', $campaign) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">{{ $campaign->title }}</a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $currencySymbol }}{{ number_format($campaign->coupon_value, 2) }} per coupon</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            <span x-text="({{ $campaign->used_coupons_count }} + (campaignUsedCounts[{{ $campaign->id }}] || 0))"></span>/{{ $campaign->coupons_count }} used
                                        </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($campaign->isExpired())
                                            <span class="text-red-600 dark:text-red-400">Expired</span>
                                        @elseif($campaign->is_active)
                                            Expires {{ $campaign->expires_at->diffForHumans() }}
                                        @else
                                            <span class="text-gray-500">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                                @if($campaign->isExpired())
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">Expired</span>
                                @elseif($campaign->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-300">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No campaigns found. <a href="{{ route('admin.coupon-campaigns.create') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Create your first campaign</a>.</li>
                @endforelse
            </ul>
            <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-700 sm:px-6">
                <a href="{{ route('admin.coupon-campaigns.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    View all campaigns &rarr;
                </a>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
