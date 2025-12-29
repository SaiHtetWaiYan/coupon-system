<x-admin.layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage application settings.</p>
        </div>

        <!-- Logo Upload Section -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="px-4 py-6 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Logo</h3>
                <div class="flex items-start gap-6">
                    <!-- Logo Preview -->
                    <div class="shrink-0">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Current Logo" class="h-20 w-auto rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-600 dark:bg-gray-700">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <div class="flex-1 space-y-4">
                        <form action="{{ route('admin.settings.logo.upload') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <x-input-label for="logo" value="Upload New Logo" />
                                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, JPEG, or SVG. Max 2MB.</p>
                            </div>
                            <x-primary-button>Upload</x-primary-button>
                        </form>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />

                        @if($logoUrl)
                            <form action="{{ route('admin.settings.logo.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the logo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                    Remove logo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="divide-y divide-gray-200 dark:divide-gray-700">
                @csrf
                @method('PUT')

                @forelse($settings as $group => $groupSettings)
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white capitalize mb-4">{{ $group }}</h3>
                        <div class="space-y-4">
                            @foreach($groupSettings as $setting)
                                <div>
                                    <x-input-label :for="'setting_' . $setting->key" :value="ucwords(str_replace('_', ' ', $setting->key))" />
                                    @if($setting->type === 'boolean')
                                        <div class="mt-1">
                                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                            <input type="checkbox" id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700" {{ $setting->value ? 'checked' : '' }}>
                                        </div>
                                    @else
                                        <x-text-input :id="'setting_' . $setting->key" :name="'settings[' . $setting->key . ']'" type="text" class="mt-1 block w-full" :value="$setting->value" />
                                    @endif
                                    <x-input-error :messages="$errors->get('settings.' . $setting->key)" class="mt-2" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No settings</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by seeding some default settings.</p>
                    </div>
                @endforelse

                @if($settings->isNotEmpty())
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex justify-end">
                            <x-primary-button>Save Settings</x-primary-button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-admin.layouts.app>
