<x-admin.layouts.app>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create User</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new user to the system.</p>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6 p-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" id="is_admin" name="is_admin" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700" {{ old('is_admin') ? 'checked' : '' }}>
                    <label for="is_admin" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Admin user</label>
                </div>

                <div class="flex items-center justify-end gap-x-4">
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-gray-900 dark:text-gray-300">Cancel</a>
                    <x-primary-button>Create User</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layouts.app>
