<x-admin.layouts.app>
    <div class="space-y-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View user information.</p>
            </div>
            <div class="mt-4 flex gap-x-3 sm:mt-0">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                    Back to List
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="px-4 py-6 sm:px-6">
                <div class="flex items-center gap-x-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                        <span class="text-xl font-medium text-gray-600 dark:text-gray-300">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">
                            @if($user->is_admin)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">Admin</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-300">User</span>
                            @endif
                        </dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">Verified on {{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Not Verified</span>
                            @endif
                        </dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">
                            {{ $user->created_at->format('F d, Y H:i') }}
                        </dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:col-span-2 sm:mt-0">
                            {{ $user->updated_at->format('F d, Y H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin.layouts.app>
