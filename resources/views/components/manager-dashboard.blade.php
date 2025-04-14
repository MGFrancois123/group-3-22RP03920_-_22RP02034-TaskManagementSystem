<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">Assigned Projects</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $assignedProjects }}</p>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">Team Tasks</h3>
            <p class="text-3xl font-bold text-green-600">{{ $teamTasks }}</p>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="text-gray-900">
            <h3 class="text-lg font-semibold">Completed Tasks</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $completedTeamTasks }}</p>
        </div>
    </div>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h3 class="text-lg font-semibold mb-4">Team Projects</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($teamProjects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $project->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $project->tasks_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $project->team_members_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full w-[{{ $project->progress }}%]"></div>
                                </div>
                                <span class="text-sm text-gray-500">{{ $project->progress }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 