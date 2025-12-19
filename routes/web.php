<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PurokController;

// Public Landing Pages
Route::get('/', function () {
    return redirect()->route('landing');
});

Route::get('/landing', function () {
    return view('landing');
})->name('landing');

Route::get('/aboutus', function () {
    return view('aboutus');
})->name('aboutus');

// Admin Login Routes
Route::get('/adminlogin', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/adminlogin', [AdminController::class, 'adminlogin'])->name('adminlogin.submit');

// Admin Dashboard Route
Route::get('/admin/admindashboard', [AdminController::class, 'admindashboard'])->name('admin.admindashboard');
Route::get('/admin/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');

// Admin Logs
Route::get('/adminlogs', [AdminController::class, 'adminlogs'])->name('adminlogs');

// Admin Audit
Route::get('/adminaudit', [AdminController::class, 'adminaudit'])->name('adminaudit');

// Collector Registration (Admin only)
Route::post('/collector/signup', [AdminController::class, 'registerCollector'])->name('collectorsignup.store');

// User Fines
Route::get('/userfines', [AdminController::class, 'userfines'])->name('userfines');
Route::post('/delete-user-fine', [AdminController::class, 'deleteUserFine'])->name('delete.userfine');
Route::get('/fine-details/{key}', [AdminController::class, 'getFineDetails'])->name('get.fine.details');
Route::post('/mark-fine-paid', [AdminController::class, 'markFineAsPaid'])->name('mark.fine.paid');

// Scheduling
Route::get('/scheduling', [ScheduleController::class, 'index'])->name('scheduling');
Route::post('/save-purok', [ScheduleController::class, 'store']);
Route::get('/schedules', [ScheduleController::class, 'getSchedules']);
Route::get('/get-purok-data', [PurokController::class, 'getPurokData']);
Route::post('/delete-purok', [ScheduleController::class, 'destroy']);
Route::post('/start-collection', [ScheduleController::class, 'startCollection'])->name('start.collection');

// Collector Logs and Audit
Route::get('/collectorlogs', [AdminController::class, 'collectorlogs'])->name('collectorlogs');
Route::get('/collectoraudit', [AdminController::class, 'collectoraudit'])->name('collectoraudit');

// User and Collector Details
Route::get('/userdetails', [AdminController::class, 'userdetails'])->name('userdetails');
Route::get('/collectordetails', [AdminController::class, 'collectordetails'])->name('collectordetails');

// Delete User and Collector
Route::delete('/user/{key}', [AdminController::class, 'deleteUser'])->name('delete.user');
Route::put('/user/{key}', [AdminController::class, 'updateUser'])->name('update.user');
Route::delete('/collector/{key}', [AdminController::class, 'deleteCollector'])->name('delete.collector');
Route::put('/collector/{key}', [AdminController::class, 'updateCollector'])->name('update.collector');

// Change Credential Requests
Route::get('/change-credential-requests', [AdminController::class, 'changeCredentialRequests'])->name('change.credential.requests');
Route::post('/accept-credential-request', [AdminController::class, 'acceptCredentialRequest'])->name('accept.credential.request');
Route::post('/reject-credential-request', [AdminController::class, 'rejectCredentialRequest'])->name('reject.credential.request');

// Admin Logout
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Notifications
Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('get.notifications');
Route::post('/notifications/mark-read', [AdminController::class, 'markNotificationAsRead'])->name('mark.notification.read');
