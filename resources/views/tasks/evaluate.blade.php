<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Evaluate Task') }}: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Task Details
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Assigned to: {{ $task->assignedTo->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Project: {{ $task->project->name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Due Date: {{ $task->due_date->format('M d, Y') }}
                            </p>
                        </header>

                        <form method="POST" action="{{ route('tasks.evaluate', $task) }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="quality_score" :value="__('Quality Score (0-100)')" />
                                <x-text-input id="quality_score" name="quality_score" type="number" min="0" max="100"
                                    class="mt-1 block w-full" :value="old('quality_score', $task->quality_score)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('quality_score')" />
                            </div>

                            <div>
                                <x-input-label for="feedback" :value="__('Feedback')" />
                                <textarea id="feedback" name="feedback" rows="3" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('feedback', $task->feedback) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('feedback')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Evaluation') }}</x-primary-button>

                                <a href="{{ route('tasks.show', $task) }}" class="text-gray-600 hover:text-gray-900">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
