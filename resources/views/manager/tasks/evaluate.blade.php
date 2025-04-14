<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Evaluate Task Submission') }}
            </h2>
            <a href="{{ route('manager.tasks.show', $task) }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Task Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Task Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Task Name</p>
                                    <p class="mt-1">{{ $task->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Project</p>
                                    <p class="mt-1">{{ $task->project->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Submitted By</p>
                                    <p class="mt-1">{{ $submission->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Submission Date</p>
                                    <p class="mt-1">{{ $submission->submitted_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation Form -->
                    <form method="POST" action="{{ route('manager.tasks.evaluate', $task) }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="submission" value="{{ $submission->id }}">
                        <input type="hidden" name="evaluation_type" value="regular">
                        <input type="hidden" name="status" value="Completed">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quality Score -->
                            <div>
                                <x-input-label for="quality_score" :value="__('Quality Score')" />
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <input type="range" id="quality_score" name="quality_score"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                            min="0" max="100" step="1"
                                            value="70"
                                            oninput="document.getElementById('quality_score_display').textContent = this.value; updateAverage();">
                                        <span id="quality_score_display" class="ml-2 text-gray-700 min-w-[4ch]">70</span>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('quality_score')" class="mt-2" />
                            </div>

                            <!-- Timeliness Score -->
                            <div>
                                <x-input-label for="timeliness_score" :value="__('Timeliness Score')" />
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <input type="range" id="timeliness_score" name="timeliness_score"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                            min="0" max="100" step="1"
                                            value="70"
                                            oninput="document.getElementById('timeliness_score_display').textContent = this.value; updateAverage();">
                                        <span id="timeliness_score_display" class="ml-2 text-gray-700 min-w-[4ch]">70</span>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('timeliness_score')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Average Score -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Average Score:</span>
                                <span id="average_score" class="text-lg font-bold text-indigo-600">70</span>
                            </div>
                        </div>

                        <!-- Feedback -->
                        <div>
                            <x-input-label for="feedback" :value="__('Feedback')" />
                            <textarea id="feedback" name="feedback" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>{{ old('feedback') }}</textarea>
                            <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                        </div>

                        <!-- Evaluation Notes -->
                        <div>
                            <x-input-label for="evaluation_notes" :value="__('Additional Notes (Optional)')" />
                            <textarea id="evaluation_notes" name="evaluation_notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('evaluation_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('evaluation_notes')" class="mt-2" />
                        </div>

                        <!-- Download Submission -->
                        @if($submission->file_path)
                        <div class="mt-4">
                            <a href="{{ route('manager.tasks.submission.download', ['task' => $task->id, 'submission' => $submission->id]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Submission
                            </a>
                        </div>
                        @endif

                        <div class="flex justify-end mt-6">
                            <x-secondary-button type="button" class="mr-3"
                                onclick="window.location.href='{{ route('manager.tasks.show', $task) }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Submit Evaluation') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateAverage() {
            const quality = parseInt(document.getElementById('quality_score').value);
            const timeliness = parseInt(document.getElementById('timeliness_score').value);
            const average = Math.round((quality + timeliness) / 2);
            document.getElementById('average_score').textContent = average;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateAverage);
    </script>
    @endpush
</x-app-layout>
