<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Evaluation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Task Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Task Information</h3>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Task Name</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $evaluation->task->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Project</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $evaluation->task->project->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Assigned To</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $evaluation->task->assignedTo->name }}</p>
                                <p class="text-sm text-gray-500">{{ $evaluation->task->assignedTo->email }}</p>
                            </div>
                        </div>

                        <!-- Evaluation Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Evaluation Details</h3>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Quality Score</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $evaluation->quality_score >= 70 ? 'green' : ($evaluation->quality_score >= 50 ? 'yellow' : 'red') }}-100 text-{{ $evaluation->quality_score >= 70 ? 'green' : ($evaluation->quality_score >= 50 ? 'yellow' : 'red') }}-800">
                                        {{ $evaluation->quality_score }}%
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Timeliness Score</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $evaluation->timeliness_score >= 70 ? 'green' : ($evaluation->timeliness_score >= 50 ? 'yellow' : 'red') }}-100 text-{{ $evaluation->timeliness_score >= 70 ? 'green' : ($evaluation->timeliness_score >= 50 ? 'yellow' : 'red') }}-800">
                                        {{ $evaluation->timeliness_score }}%
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Evaluation Type</p>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($evaluation->evaluation_type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Evaluated At</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $evaluation->evaluated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback and Notes -->
                    <div class="mt-8 space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Feedback to User</h3>
                            <p class="mt-2 text-sm text-gray-900">{{ $evaluation->feedback }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Private Evaluation Notes</h3>
                            <p class="mt-2 text-sm text-gray-900">{{ $evaluation->evaluation_notes }}</p>
                        </div>

                        @if($evaluation->evaluation_criteria)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Evaluation Criteria</h3>
                                <ul class="mt-2 list-disc list-inside text-sm text-gray-900">
                                    @foreach($evaluation->evaluation_criteria as $criterion)
                                        <li>{{ ucfirst($criterion) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($evaluation->needs_follow_up)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Follow-up Information</h3>
                                <p class="mt-2 text-sm text-gray-900">
                                    Follow-up scheduled for: {{ $evaluation->follow_up_date->format('Y-m-d') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('manager.evaluations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Back to Evaluations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
