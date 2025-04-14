<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $task->name }}
            </h2>
            @if(auth()->user()->role === 'admin' ||
                (auth()->user()->role === 'manager' && optional($task->project)->created_by === auth()->id()))
                <div class="flex space-x-4">
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Edit Task') }}
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                onclick="return confirm('Are you sure you want to delete this task?')">
                            {{ __('Delete Task') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Description</h3>
                            <p class="text-gray-600">{{ $task->description ?: 'No description provided.' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Task Details</h3>
                            <div class="space-y-2">
                                <p>
                                    <span class="font-medium">Project:</span>
                                    @if($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">Project not found</span>
                                    @endif
                                </p>
                                <p>
                                    <span class="font-medium">Assigned to:</span>
                                    {{ optional($task->assignedTo)->name ?? 'Unassigned' }}
                                </p>
                                <p>
                                    <span class="font-medium">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($task->status === 'Completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'In-Progress') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $task->status }}
                                    </span>
                                </p>
                                <p>
                                    <span class="font-medium">Due date:</span>
                                    {{ optional($task->due_date)->format('M d, Y') ?? 'No due date set' }}
                                </p>
                                <p>
                                    <span class="font-medium">Created by:</span>
                                    {{ optional($task->creator)->name ?? 'Unknown' }}
                                </p>
                                <p>
                                    <span class="font-medium">Created at:</span>
                                    {{ optional($task->created_at)->format('M d, Y') ?? 'Unknown' }}
                                </p>
                                <p>
                                    <span class="font-medium">Last updated:</span>
                                    {{ optional($task->updated_at)->format('M d, Y') ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submission History - Visible to both users and managers -->
                    @if($task->submissions->count() > 0)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Submission History</h3>
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempt</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality Score</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                                            @if(auth()->user()->role === 'manager')
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($task->submissions()->orderBy('submitted_at', 'desc')->get() as $submission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $submission->submitted_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($submission->file_path)
                                                        <a href="{{ route('tasks.submission.download', [$task, $submission]) }}"
                                                           class="text-indigo-600 hover:text-indigo-900">
                                                            {{ $submission->original_filename }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500">No file</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($submission->evaluated_at)
                                                        {{ $submission->quality_score }}/100
                                                    @else
                                                        <span class="text-gray-500">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if($submission->evaluated_at)
                                                        {{ $submission->feedback ?: 'No feedback provided' }}
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            Evaluated by {{ $submission->evaluator->name }} on {{ $submission->evaluated_at->format('M d, Y H:i') }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-500">Pending evaluation</span>
                                                    @endif
                                                </td>
                                                @if(auth()->user()->role === 'manager')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        @if(!$submission->evaluated_at)
                                                            <a href="{{ route('tasks.evaluate.form', ['task' => $task, 'submission' => $submission]) }}"
                                                               class="text-indigo-600 hover:text-indigo-900">
                                                                Evaluate
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ auth()->user()->role === 'manager' ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-500">
                                                    No submissions yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Task Form - Only visible to assigned user -->
                    @if(auth()->user()->role === 'user' && $task->assigned_to === auth()->id())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Submit Task</h3>
                            <form method="POST" action="{{ route('tasks.submit', $task) }}" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="notes" :value="__('Submission Notes')" />
                                        <textarea id="notes" name="notes" rows="3"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            placeholder="Add any notes about your submission..."></textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                                    </div>

                                    <div>
                                        <x-input-label for="file" :value="__('Attachment')" />
                                        <input type="file" id="file" name="file"
                                            class="mt-1 block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100" />
                                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                                    </div>

                                    <div class="flex justify-end">
                                        <x-primary-button>
                                            {{ __('Submit Task') }}
                                        </x-primary-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Task Evaluation</h3>
                            <form action="{{ route('tasks.evaluate', $task) }}" method="POST" class="space-y-4">
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
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
