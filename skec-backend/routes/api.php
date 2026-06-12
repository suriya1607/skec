<?php

use App\Http\Controllers\Admin\AccessLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminNoteController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SubjectController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserActive;
use App\Http\Middleware\EnsureValidSession;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ROUTES ─────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/invitation/{token}', [AuthController::class, 'validateInvitation']);
        Route::post('/register', [AuthController::class, 'registerFromInvitation']);
        Route::post('/register-public', [AuthController::class, 'publicRegister']);
    });

    // Public settings & landing data
    Route::get('/settings/public', [SettingsController::class, 'public']);
    Route::get('/categories', [CategoryController::class, 'indexPublic']);
    Route::get('/subjects', [SubjectController::class, 'indexPublic']);
    Route::get('/free-notes', [CategoryController::class, 'freeNotes']);
    Route::get('/free-notes/{id}/stream', [NoteController::class, 'streamFree'])->name('notes.stream-free');

    // Contact
    Route::post('/contact', [ContactController::class, 'sendMessage']);

    // ─── AUTHENTICATED ROUTES ───────────────────────────────────────────────
    Route::middleware(['auth:sanctum', EnsureValidSession::class, EnsureUserActive::class])->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/profile', [AuthController::class, 'updateProfile']);

        // Notes (student)
        Route::get('/notes', [NoteController::class, 'index']);
        Route::get('/notes/{id}/stream-token', [NoteController::class, 'getStreamToken']);
        Route::post('/notes/{id}/log', [NoteController::class, 'logAccess']);
        Route::get('student/categories', [CategoryController::class, 'StudentCategories']);

        // Notifications (student in-app)
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);

        // ─── ADMIN ROUTES ──────────────────────────────────────────────────
        Route::middleware(EnsureAdmin::class)->prefix('admin')->group(function () {

            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);

            // Students
            Route::get('/students/export', [AdminStudentController::class, 'export']);
            Route::get('/students', [AdminStudentController::class, 'index']);
            Route::get('/students/{id}', [AdminStudentController::class, 'show']);
            Route::get('/students/{id}/photo', [AdminStudentController::class, 'downloadPhoto']);
            Route::patch('/students/{id}', [AdminStudentController::class, 'update']);
            Route::delete('/students/{id}', [AdminStudentController::class, 'destroy']);
            Route::patch('/students/profile/{id}', [AdminStudentController::class, 'profileupdate']);
            Route::post('/students/{id}/logout', [AdminStudentController::class, 'forceLogout']);

            // Invitations
            Route::get('/invitations', [InvitationController::class, 'index']);
            Route::post('/invitations', [InvitationController::class, 'store']);
            Route::delete('/invitations/{id}', [InvitationController::class, 'destroy']);
            Route::post('/invitations/{id}/resend', [InvitationController::class, 'resend']);

            // Notes (admin)
            Route::get('/notes', [AdminNoteController::class, 'index']);
            Route::post('/notes', [AdminNoteController::class, 'store']);
            Route::get('/notes/{id}', [AdminNoteController::class, 'show']);
            Route::patch('/notes/{id}', [AdminNoteController::class, 'update']);
            Route::delete('/notes/{id}', [AdminNoteController::class, 'destroy']);
            Route::patch('/notes/{id}/status', [AdminNoteController::class, 'toggleStatus']);

            // Categories
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::patch('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

            // Subjects
            Route::get('/subjects', [SubjectController::class, 'index']);
            Route::post('/subjects', [SubjectController::class, 'store']);
            Route::patch('/subjects/{id}', [SubjectController::class, 'update']);
            Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);

            // Sessions
            Route::get('/sessions', [SessionController::class, 'index']);
            Route::delete('/sessions/{id}', [SessionController::class, 'destroy']);

            // Settings
            Route::get('/settings', [SettingsController::class, 'index']);
            Route::post('/settings', [SettingsController::class, 'update']);

            // Media / File Upload
            Route::post('/media/upload', [MediaController::class, 'upload']);
            Route::post('/media/upload-multiple', [MediaController::class, 'uploadMultiple']);
            Route::delete('/media', [MediaController::class, 'delete']);
            Route::get('/media', [MediaController::class, 'list']);

            // Access Logs
            Route::get('/logs', [AccessLogController::class, 'index']);

            // Announcements
            Route::get('/announcements', [AnnouncementController::class, 'index']);
            Route::post('/announcements', [AnnouncementController::class, 'store']);
            Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
            Route::post('/announcements/{id}/resend', [AnnouncementController::class, 'resend']);
        });
    });

    // Note stream — uses signed URL
    Route::get('/notes/{id}/stream', [NoteController::class, 'stream'])->name('notes.stream');
});
