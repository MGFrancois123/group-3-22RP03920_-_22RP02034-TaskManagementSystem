<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\UpdateLastLoginAt;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\ProjectController as ManagerProjectController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route with role-based redirection
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    $route = match($user->role) {
        'admin' => 'admin.dashboard',
        'manager' => 'manager.dashboard',
        'user' => 'user.dashboard',
        default => 'login'
    };

    return redirect()->route($route);
})->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/roles', [AdminController::class, 'manageRoles'])->name('users.roles');
    Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
    Route::get('/tasks/categories', [AdminController::class, 'taskCategories'])->name('tasks.categories');
    Route::post('/tasks/categories', [AdminController::class, 'storeTaskCategory'])->name('tasks.categories.store');
    Route::delete('/tasks/categories/{category}', [AdminController::class, 'destroyTaskCategory'])->name('tasks.categories.destroy');
    Route::get('/tasks/priorities', [AdminController::class, 'taskPriorities'])->name('tasks.priorities');
    Route::post('/tasks/priorities', [AdminController::class, 'storeTaskPriority'])->name('tasks.priorities.store');
    Route::delete('/tasks/priorities/{priority}', [AdminController::class, 'destroyTaskPriority'])->name('tasks.priorities.destroy');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/performance', [AdminController::class, 'systemPerformance'])->name('reports.performance');
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::put('/settings/task', [AdminController::class, 'updateTaskSettings'])->name('settings.task');
    Route::put('/settings/email', [AdminController::class, 'updateEmailSettings'])->name('settings.email');
    Route::put('/settings/notification', [AdminController::class, 'updateNotificationSettings'])->name('settings.notification');

    // Add tasks resource routes
    Route::resource('tasks', TaskController::class);

    // Add projects resource routes
    // Route::resource('projects', ProjectController::class); // Removed for admin
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::resource('projects', ManagerProjectController::class);

    // Tasks
    Route::controller(ManagerTaskController::class)->group(function () {
        Route::get('/tasks', 'index')->name('tasks.index');
        Route::get('/tasks/create', 'create')->name('tasks.create');
        Route::get('/tasks/report', 'report')->name('tasks.report');
        Route::post('/tasks', 'store')->name('tasks.store');
        Route::get('/tasks/{task}', 'show')->name('tasks.show');
        Route::get('/tasks/{task}/edit', 'edit')->name('tasks.edit');
        Route::put('/tasks/{task}', 'update')->name('tasks.update');
        Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');

        // Task Evaluation
        Route::get('/tasks/{task}/evaluate', 'showEvaluationForm')->name('tasks.evaluate.form');
        Route::post('/tasks/{task}/evaluate', 'evaluate')->name('tasks.evaluate');
        Route::get('/tasks/{task}/submissions/{submission}/download', 'downloadSubmission')->name('tasks.submission.download');
    });
});

// User routes
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('role:user');
    Route::get('/tasks/{task}/submissions/{submission}/download', [TaskController::class, 'downloadSubmission'])
        ->name('tasks.submission.download')
        ->middleware('allow.task');
    Route::resource('tasks', TaskController::class)->middleware('allow.task');
});

// Shared authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
