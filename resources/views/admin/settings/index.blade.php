<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Site Settings -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">Site Settings</h3>

                                <div>
                                    <x-input-label for="site_name" :value="__('Site Name')" />
                                    <x-text-input id="site_name" name="site_name" type="text" class="mt-1 block w-full"
                                        :value="$settings['site_name'] ?? ''" required />
                                    <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="site_description" :value="__('Site Description')" />
                                    <textarea id="site_description" name="site_description" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settings['site_description'] ?? '' }}</textarea>
                                    <x-input-error :messages="$errors->get('site_description')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="maintenance_mode" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                        <span class="ml-2">Enable Maintenance Mode</span>
                                    </label>
                                </div>
                            </div>

                            <!-- User Settings -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">User Settings</h3>

                                <div>
                                    <x-input-label for="default_role" :value="__('Default User Role')" />
                                    <select id="default_role" name="default_role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="user" {{ ($settings['default_role'] ?? '') === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="manager" {{ ($settings['default_role'] ?? '') === 'manager' ? 'selected' : '' }}>Manager</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="items_per_page" :value="__('Items Per Page')" />
                                    <x-text-input id="items_per_page" name="items_per_page" type="number" class="mt-1 block w-full"
                                        :value="$settings['items_per_page'] ?? 10" required min="5" max="100" />
                                </div>

                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="enable_notifications" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            {{ ($settings['enable_notifications'] ?? false) ? 'checked' : '' }}>
                                        <span class="ml-2">Enable Email Notifications</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Save Settings') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Information -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">PHP Version: <span class="font-medium">{{ PHP_VERSION }}</span></p>
                            <p class="text-sm text-gray-600">Laravel Version: <span class="font-medium">{{ app()->version() }}</span></p>
                            <p class="text-sm text-gray-600">Environment: <span class="font-medium">{{ config('app.env') }}</span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Server: <span class="font-medium">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span></p>
                            <p class="text-sm text-gray-600">Database: <span class="font-medium">{{ config('database.default') }}</span></p>
                            <p class="text-sm text-gray-600">Timezone: <span class="font-medium">{{ config('app.timezone') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
