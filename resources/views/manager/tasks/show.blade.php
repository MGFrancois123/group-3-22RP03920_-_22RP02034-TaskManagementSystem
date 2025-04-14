<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.tasks.edit', $task) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Task
                </a>
                <a href="{{ route('manager.tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Tasks
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Task Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Task Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $task->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Project</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $task->project->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $task->assignedTo->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $task->due_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($task->status === 'Completed') bg-green-100 text-green-800
                                            @elseif($task->status === 'In-Progress') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $task->status }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Task Description</h3>
                            <div class="mt-4 prose max-w-none">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    </div>

                    @if($task->submissions->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Submissions</h3>
                            <div class="mt-4 space-y-4">
                                @foreach($task->submissions as $submission)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $submission->user->name }}</p>
                                                <p class="text-sm text-gray-500">Submitted on {{ $submission->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('manager.tasks.submission.download', ['task' => $task->id, 'submission' => $submission->id]) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                    Download
                                                </a>
                                                @if(!$submission->evaluated)
                                                    <a href="{{ route('manager.tasks.evaluate', ['task' => $task->id, 'submission' => $submission->id]) }}"
                                                       class="text-green-600 hover:text-green-900 text-sm">
                                                        Evaluate
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        @if($submission->comments)
                                            <div class="mt-2 text-sm text-gray-600">
                                                {{ $submission->comments }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
