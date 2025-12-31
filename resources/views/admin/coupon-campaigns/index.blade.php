<x-admin.layouts.app>
    <div class="space-y-6" x-data="{ deleteCampaignId: null, deleteCampaignName: '' }">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Coupon Campaigns</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage coupon campaigns and their generated coupons.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.coupon-campaigns.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    New Campaign
                </a>
            </div>
        </div>

        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">Campaign</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Total Amount</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Coupon Value</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Coupons</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Expires</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $campaign->title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Created {{ $campaign->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($campaign->total_amount, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $currencySymbol }}{{ number_format($campaign->coupon_value, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $campaign->coupons_count }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @if($campaign->isExpired())
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">Expired</span>
                                @elseif($campaign->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-300">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $campaign->expires_at->format('M d, Y') }}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <a href="{{ route('admin.coupon-campaigns.show', $campaign) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white mr-3">View</a>
                                <a href="{{ route('admin.coupon-campaigns.edit', $campaign) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                                <button
                                    type="button"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    x-on:click="deleteCampaignId = {{ $campaign->id }}; deleteCampaignName = {{ Js::from($campaign->title) }}; $dispatch('open-modal', 'confirm-campaign-deletion')"
                                >Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $campaigns->links() }}
        </div>

        <x-modal name="confirm-campaign-deletion" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Delete Campaign
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <span class="font-semibold" x-text="deleteCampaignName"></span>? This will also delete all associated coupons. This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600"
                        x-on:click="$dispatch('close-modal', 'confirm-campaign-deletion')"
                    >
                        Cancel
                    </button>

                    <form :action="`{{ url('dashboard/coupon-campaigns') }}/${deleteCampaignId}`" method="POST">
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
