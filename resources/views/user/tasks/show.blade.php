@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('user.tasks.index') }}"
           class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Tasks
        </a>
    </div>

    <!-- Task Details Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Task Details
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors duration-150">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Task Name
                </dt>
                <dd class="text-sm text-gray-900 col-span-2 flex items-center">{{ $task->name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors duration-150">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Project
                </dt>
                <dd class="text-sm text-gray-900 col-span-2 flex items-center">{{ $task->project->name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors duration-150">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    Description
                </dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ $task->description }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors duration-150">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Due Date
                </dt>
                <dd class="text-sm text-gray-900 col-span-2 flex items-center">
                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors duration-150">
                <dt class="text-sm font-medium text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Status
                </dt>
                <dd class="text-sm col-span-2">
                    <span class="px-3 py-1 inline-flex items-center rounded-full text-sm font-medium
                        {{ $task->status === 'Completed' ? 'bg-green-100 text-green-800' :
                           ($task->status === 'In-Progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($task->status === 'Completed')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @elseif($task->status === 'In-Progress')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                        {{ $task->status }}
                    </span>
                </dd>
            </div>
        </div>
    </div>

    <!-- Quality Score Section -->
    @php
        $taskScore = $task->getUserScore(auth()->id());
    @endphp
    @if($taskScore)
    <div class="mt-8">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Quality Score</h4>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="text-sm font-medium text-gray-500">Quality Score</h5>
                        <div class="mt-2 flex items-center">
                            <div class="text-3xl font-bold text-gray-900">{{ $taskScore->quality_score }}/100</div>
                            <div class="ml-2 flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $taskScore->quality_score >= 80 ? 'bg-green-100 text-green-800' :
                                       ($taskScore->quality_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $taskScore->quality_score >= 80 ? 'Excellent' :
                                       ($taskScore->quality_score >= 60 ? 'Good' : 'Needs Improvement') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5 class="text-sm font-medium text-gray-500">Timeliness Score</h5>
                        <div class="mt-2 flex items-center">
                            <div class="text-3xl font-bold text-gray-900">{{ $taskScore->timeliness_score }}/100</div>
                            <div class="ml-2 flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $taskScore->timeliness_score >= 80 ? 'bg-green-100 text-green-800' :
                                       ($taskScore->timeliness_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $taskScore->timeliness_score >= 80 ? 'On Time' :
                                       ($taskScore->timeliness_score >= 60 ? 'Slightly Delayed' : 'Late') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($taskScore->feedback)
                <div class="mt-6">
                    <h5 class="text-sm font-medium text-gray-500">Feedback</h5>
                    <p class="mt-2 text-sm text-gray-900">{{ $taskScore->feedback }}</p>
                </div>
                @endif
                <div class="mt-4 text-sm text-gray-500">
                    Evaluated by {{ $taskScore->evaluator->name }} on {{ $taskScore->evaluated_at->format('M d, Y H:i') }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Manager's Evaluation Section -->
    @if($task->managerScores->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-600">
                <h4 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Evaluation
                </h4>
            </div>
            <div class="p-6">
                @foreach($task->managerScores->sortByDesc('created_at') as $score)
                <div class="mb-6 last:mb-0">
                    <div class="flex flex-wrap gap-6">
                        <div class="flex-1 min-w-[200px]">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm font-medium text-gray-500 mb-2">Quality Score</p>
                                <div class="flex items-center">
                                    <div class="text-3xl font-bold text-gray-900">{{ $score->quality_score }}</div>
                                    <div class="text-xl text-gray-400 ml-1">/100</div>
                                    <div class="ml-4">
                                        <span class="px-3 py-1 text-sm font-medium rounded-full
                                            {{ $score->quality_score >= 80 ? 'bg-green-100 text-green-800' :
                                               ($score->quality_score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $score->quality_score >= 80 ? 'Excellent' :
                                               ($score->quality_score >= 60 ? 'Good' : 'Needs Improvement') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($score->feedback)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500 mb-2">Feedback</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900">{{ $score->feedback }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="mt-4 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Evaluated by {{ $score->manager->name }} on {{ $score->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($task->submissions->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                <h4 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submission History
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($task->submissions as $submission)
                <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Submitted on {{ $submission->submitted_at->format('M d, Y H:i') }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $submission->submission_notes }}</p>
                            </div>
                        </div>
                        @if($submission->file_path)
                        <a href="{{ route('user.tasks.submission.download', ['task' => $task->id, 'submission' => $submission->id]) }}"
                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Download
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Submit Work Button -->
    @if($task->status !== 'Completed')
    <div class="mt-8 flex justify-end">
        <a href="{{ route('user.tasks.edit', $task) }}"
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Submit Work
        </a>
    </div>
    @endif
</div>
@endsection
