<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">My Tasks</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $myTasks }}</p>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">Completed Tasks</h3>
            <p class="text-3xl font-bold text-green-600">{{ $myCompletedTasks }}</p>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">Pending Tasks</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $myPendingTasks }}</p>
        </div>
    </div>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h3 class="text-lg font-semibold mb-4">My Tasks</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myTasksList as $task)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $task->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $task->project->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($task->status === 'Completed') bg-green-100 text-green-800
                                    @elseif($task->status === 'In-Progress') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $task->due_date->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 