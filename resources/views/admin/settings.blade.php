@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- General Settings -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">General Settings</h2>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name']) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                    <textarea name="site_description" id="site_description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update General Settings
                </button>
            </form>
        </div>

        <!-- Task Settings -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Task Settings</h2>
            <form action="{{ route('admin.settings.task') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="default_task_priority" class="block text-sm font-medium text-gray-700">Default Task Priority</label>
                    <select name="default_task_priority" id="default_task_priority"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="low" {{ old('default_task_priority', $settings['default_task_priority']) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('default_task_priority', $settings['default_task_priority']) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('default_task_priority', $settings['default_task_priority']) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('default_task_priority', $settings['default_task_priority']) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="task_reminder_days" class="block text-sm font-medium text-gray-700">Task Reminder (Days before due date)</label>
                    <input type="number" name="task_reminder_days" id="task_reminder_days" min="0" max="30"
                        value="{{ old('task_reminder_days', $settings['task_reminder_days']) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Task Settings
                </button>
            </form>
        </div>

        <!-- Email Settings -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Email Settings</h2>
            <form action="{{ route('admin.settings.email') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Email Address</label>
                    <input type="email" name="mail_from_address" id="mail_from_address"
                        value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name</label>
                    <input type="text" name="mail_from_name" id="mail_from_name"
                        value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Email Settings
                </button>
            </form>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Notification Settings</h2>
            <form action="{{ route('admin.settings.notification') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Enable Notifications</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="enable_email_notifications" value="1"
                                {{ old('enable_email_notifications', $settings['enable_email_notifications']) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2">Email Notifications</span>
                        </label>
                    </div>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="enable_task_reminders" value="1"
                                {{ old('enable_task_reminders', $settings['enable_task_reminders']) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2">Task Reminders</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Notification Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
