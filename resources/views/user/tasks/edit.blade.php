@extends('layouts.user')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('user.tasks.show', $task) }}" class="text-indigo-600 hover:text-indigo-900">
            ← Back to Task Details
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Submit Work for: {{ $task->name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Project: {{ $task->project->name }}
            </p>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <form action="{{ route('user.tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were errors with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Task Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Task Status
                        </label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In-Progress" {{ $task->status === 'In-Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <!-- Notes Section -->
                    <div>
                        <label for="submission_notes" class="block text-sm font-medium text-gray-700">
                            Notes (Optional)
                        </label>
                        <div class="mt-1">
                            <textarea id="submission_notes" name="submission_notes" rows="4"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Describe your work or add any notes for the reviewer">{{ old('submission_notes') }}</textarea>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div>
                        <label for="submission_file" class="block text-sm font-medium text-gray-700">
                            Upload File (Optional)
                        </label>
                        <div class="mt-1">
                            <input type="file"
                                id="submission_file"
                                name="submission_file"
                                accept=".pdf,.doc,.docx,.zip"
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            />
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Accepted formats: PDF, DOC, DOCX, ZIP (max 10MB)
                        </p>
                        <div id="file_name" class="mt-2 text-sm text-indigo-600"></div>
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('user.tasks.show', $task) }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Work
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('submission_file');
    const fileNameDisplay = document.getElementById('file_name');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = `Selected file: ${this.files[0].name}`;
        } else {
            fileNameDisplay.textContent = '';
        }
    });
});
</script>
@endpush

@endsection
