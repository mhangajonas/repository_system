<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoryController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckLibrarian;
use App\Http\Middleware\CheckSupervisor;
use Illuminate\Support\Facades\Route;

// Public Search & Discovery Routes (FR-3.1, FR-3.2, FR-3.3, FR-3.4, FR-3.5)
Route::get('/', [RepositoryController::class, 'search'])->name('public.search');
Route::get('/home', [RepositoryController::class, 'search'])->name('home');
Route::get('/repository/{id}', [RepositoryController::class, 'show'])->name('repositories.show');
Route::get('/repository/{id}/download', [RepositoryController::class, 'download'])->name('repositories.download');

// Role-Based Dashboard Redirect
Route::get('/dashboard', [RepositoryController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 1. STUDENT ROUTES
    Route::get('/repositories/create', [RepositoryController::class, 'create'])->name('repositories.create');
    Route::post('/repositories', [RepositoryController::class, 'store'])->name('repositories.store');
    Route::get('/student/history', [RepositoryController::class, 'studentHistory'])->name('student.history');
    Route::delete('/repositories/{id}', [RepositoryController::class, 'studentDestroy'])->name('repositories.destroy');

    // 2. SUPERVISOR ROUTES (Ulinzi wa CheckSupervisor)
    Route::middleware(CheckSupervisor::class)->group(function () {
        Route::get('/supervisor/review', [RepositoryController::class, 'supervisorIndex'])->name('supervisor.index');
        Route::post('/supervisor/review/{id}', [RepositoryController::class, 'supervisorAction'])->name('supervisor.action');
        Route::get('/supervisor/history', [RepositoryController::class, 'supervisorHistory'])->name('supervisor.history');
        
        // Njia iliyoongezwa kuruhusu Supervisor kufuta kupitia logi/backups bila kukutana na 403 ya admin
        Route::delete('/supervisor/backups/destroy/{id}', [RepositoryController::class, 'destroyFromLog'])->name('supervisor.backups.destroy');
    });

    // 3. LIBRARIAN ROUTES (Ulinzi wa CheckLibrarian)
    Route::middleware(CheckLibrarian::class)->group(function () {
        Route::get('/library/review', [RepositoryController::class, 'libraryIndex'])->name('library.index');
        Route::post('/library/review/{id}', [RepositoryController::class, 'libraryAction'])->name('library.action');

        // Edit Metadata & Set Access Level
        Route::get('/library/repository/{id}/edit', [RepositoryController::class, 'editMetadata'])->name('library.repositories.edit');
        Route::put('/library/repository/{id}', [RepositoryController::class, 'updateMetadata'])->name('library.repositories.update');

        // Manage Catalogues
        Route::get('/library/catalogues', [RepositoryController::class, 'manageCatalogues'])->name('library.catalogues');

        // Analytical Dashboard & Reports
        Route::get('/library/reports', [RepositoryController::class, 'analytics'])->name('library.reports');

        // Alias ya muda kuzuia error ya library.users endapo view yoyote inaiita
        Route::get('/library/users', [RepositoryController::class, 'adminDashboard'])->name('library.users');
    });

    // 4. SYSTEM ADMINISTRATOR / ICT ROUTES (Ulinzi wa CheckAdmin)
    Route::middleware(CheckAdmin::class)->group(function () {
        Route::get('/admin/dashboard', [RepositoryController::class, 'adminDashboard'])->name('admin.dashboard');

        // Manage Users & Roles (Exclusively for Administrator)
        Route::get('/admin/users', [RepositoryController::class, 'usersIndex'])->name('admin.users.index');
        Route::put('/admin/users/{id}/role', [RepositoryController::class, 'updateRoleByAdmin'])->name('admin.users.updateRole');
        Route::delete('/admin/users/{id}', [RepositoryController::class, 'deleteUser'])->name('admin.users.delete');

        // Alias ya kuzuia Error kwenye views za zamani (library.users.updateRole)
        Route::put('/library/users/{id}/role', [RepositoryController::class, 'updateRoleByAdmin'])->name('library.users.updateRole');

        // Configure System Settings
        Route::get('/admin/settings', [RepositoryController::class, 'systemSettings'])->name('admin.settings');
        Route::post('/admin/settings', [RepositoryController::class, 'saveSettings'])->name('admin.settings');
        Route::post('/admin/settings/save', [RepositoryController::class, 'saveSettings'])->name('admin.settings.save');
        Route::post('/admin/settings/update', [RepositoryController::class, 'saveSettings'])->name('admin.save_settings'); // Alias iliyoongezwa kuzuia RouteNotFoundException

        // Manage Backups & Restore Action
        Route::get('/admin/backups', [RepositoryController::class, 'manageBackups'])->name('admin.backups');
        Route::post('/admin/backups/create', [RepositoryController::class, 'createBackup'])->name('admin.create_backup');
        Route::post('/admin/backups/generate', [RepositoryController::class, 'createBackup'])->name('admin.backups.create');
        Route::post('/admin/backups/restore/{id}', [RepositoryController::class, 'restoreFromLog'])->name('admin.backups.restore');
        Route::delete('/admin/backups/destroy/{id}', [RepositoryController::class, 'destroyFromLog'])->name('admin.backups.destroy');
    });
});

require __DIR__.'/auth.php';