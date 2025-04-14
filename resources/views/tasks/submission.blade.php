<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Submission Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $task->name }}
                    </h3>
                    
                    <div class="mt-6 space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Submitted By</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $submission->user->name }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Submitted At</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $submission->submitted_at->format('M d, Y H:i') }}</p>
                        </div>

                        @if($submission->notes)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Notes</h4>
                                <p class="mt-1 text-sm text-gray-600">{{ $submission->notes }}</p>
                            </div>
                        @endif

                        @if($submission->file_path)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Attached File</h4>
                                <a href="{{ route('tasks.submission.download', [$task, $submission]) }}" 
                                   class="mt-1 inline-flex items-center text-sm text-blue-600 hover:text-blue-900">
                                    <x-heroicon-s-download class="w-4 h-4 mr-1" />
                                    {{ $submission->original_filename }}
                                </a>
                            </div>
                        @endif

                        @if($submission->evaluated_at)
                            <div class="border-t pt-6">
                                <h4 class="text-sm font-medium text-gray-900">Evaluation</h4>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Quality Score: {{ $submission->quality_score }}/100</p>
                                    </div>
                                    
                                    @if($submission->feedback)
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-900">Feedback</h5>
                                            <p class="mt-1 text-sm text-gray-600">{{ $submission->feedback }}</p>
                                        </div>
                                    @endif

                                    <div class="text-sm text-gray-500">
                                        Evaluated by {{ $submission->evaluator->name }} on {{ $submission->evaluated_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        @elseif(auth()->user()->can('evaluate', $task))
                            <div class="border-t pt-6">
                                <h4 class="text-sm font-medium text-gray-900">Evaluate Submission</h4>
                                <form method="POST" action="{{ route('tasks.submissions.evaluate', [$task, $submission]) }}" class="mt-4">
                                    @csrf

                                    <div>
                                        <x-input-label for="quality_score" :value="__('Quality Score (0-100)')" />
                                        <x-text-input id="quality_score" name="quality_score" type="number" min="0" max="100"
                                            class="mt-1 block w-full" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('quality_score')" />
                                    </div>

                                    <div class="mt-4">
                                        <x-input-label for="feedback" :value="__('Feedback')" />
                                        <x-textarea-input id="feedback" name="feedback" class="mt-1 block w-full" rows="3" />
                                        <x-input-error class="mt-2" :messages="$errors->get('feedback')" />
                                    </div>

                                    <div class="mt-4">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="update_task_status" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-600">Mark task as completed</span>
                                        </label>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button type="button" onclick="history.back()">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-primary-button class="ml-3">
                                            {{ __('Save Evaluation') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 