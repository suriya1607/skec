<?php

use App\Http\Controllers\Admin\AccessLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNoteController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NoteController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserActive;
use App\Http\Middleware\EnsureValidSession;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ─────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/login',              [AuthController::class, 'login']);
        Route::get('/invitation/{token}',  [AuthController::class, 'validateInvitation']);
        Route::post('/register',           [AuthController::class, 'registerFromInvitation']);
    });

    // Public settings & landing data
    Route::get('/settings/public', [SettingsController::class, 'public']);
    Route::get('/categories',       [CategoryController::class, 'indexPublic']);

    // ─── AUTHENTICATED ROUTES ───────────────────────────────────────────────
    Route::middleware(['auth:sanctum', EnsureValidSession::class, EnsureUserActive::class])->group(function () {

        // Auth
        Route::post('/auth/logout',  [AuthController::class, 'logout']);
        Route::get('/auth/me',       [AuthController::class, 'me']);
        Route::post('/auth/profile',  [AuthController::class, 'updateProfile']);

        // Notes (student)
        Route::get('/notes',                   [NoteController::class, 'index']);
        Route::get('/notes/{id}/stream-token', [NoteController::class, 'getStreamToken']);
        Route::post('/notes/{id}/log',         [NoteController::class, 'logAccess']);

        // ─── ADMIN ROUTES ──────────────────────────────────────────────────
        Route::middleware(EnsureAdmin::class)->prefix('admin')->group(function () {

            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);

            // Students
            Route::get('/students',               [AdminStudentController::class, 'index']);
            Route::get('/students/{id}',          [AdminStudentController::class, 'show']);
            Route::get('/students/{id}/photo',    [AdminStudentController::class, 'downloadPhoto']);
            Route::patch('/students/{id}',        [AdminStudentController::class, 'update']);
            Route::delete('/students/{id}',       [AdminStudentController::class, 'destroy']);
            Route::post('/students/{id}/logout',  [AdminStudentController::class, 'forceLogout']);

            // Invitations
            Route::get('/invitations',               [InvitationController::class, 'index']);
            Route::post('/invitations',              [InvitationController::class, 'store']);
            Route::delete('/invitations/{id}',       [InvitationController::class, 'destroy']);
            Route::post('/invitations/{id}/resend',  [InvitationController::class, 'resend']);

            // Notes (admin)
            Route::get('/notes',               [AdminNoteController::class, 'index']);
            Route::post('/notes',              [AdminNoteController::class, 'store']);
            Route::get('/notes/{id}',          [AdminNoteController::class, 'show']);
            Route::patch('/notes/{id}',        [AdminNoteController::class, 'update']);
            Route::delete('/notes/{id}',       [AdminNoteController::class, 'destroy']);
            Route::patch('/notes/{id}/status', [AdminNoteController::class, 'toggleStatus']);

            // Categories
            Route::get('/categories',          [CategoryController::class, 'index']);
            Route::post('/categories',         [CategoryController::class, 'store']);
            Route::patch('/categories/{id}',   [CategoryController::class, 'update']);
            Route::delete('/categories/{id}',  [CategoryController::class, 'destroy']);

            // Sessions
            Route::get('/sessions',            [SessionController::class, 'index']);
            Route::delete('/sessions/{id}',    [SessionController::class, 'destroy']);

            // Settings
            Route::get('/settings',            [SettingsController::class, 'index']);
            Route::post('/settings',           [SettingsController::class, 'update']);

            // Media / File Upload
            Route::post('/media/upload',           [MediaController::class, 'upload']);
            Route::post('/media/upload-multiple',  [MediaController::class, 'uploadMultiple']);
            Route::delete('/media',                [MediaController::class, 'delete']);
            Route::get('/media',                   [MediaController::class, 'list']);

            // Access Logs
            Route::get('/logs', [AccessLogController::class, 'index']);
        });
    });

    // Note stream — uses signed URL
    Route::get('/notes/{id}/stream', [NoteController::class, 'stream'])->name('notes.stream');
});
