<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminCorrectionController;

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/verification/redirect-send', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return redirect('http://localhost:8025');
})->middleware(['auth'])->name('verification.redirect-send');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::middleware('auth','verified')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])->name('attendance.break.start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])->name('attendance.break.end');
    Route::post('/attendance/end', [AttendanceController::class, 'endWork'])->name('attendance.end');

    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])->name('stamp_correction_request.list');

});

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', function () {
    Auth::logout();
    return redirect('/admin/login');
})->name('admin.logout');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'list'])->name('admin.attendance.list');
    Route::get('admin/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');

    Route::get('/admin/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
    Route::get('/admin/attendance/staff/{id}', [AdminStaffController::class, 'show'])
    ->name('admin.staff.attendance');
    Route::get('/admin/attendance/staff/{id}/csv', [AdminStaffController::class, 'exportCsv'])
    ->name('admin.staff.attendance.csv');


    Route::get('/admin/stamp_correction_request/list', [AdminCorrectionController::class, 'index'])
    ->name('admin.stamp_correction_request.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminCorrectionController::class, 'approve'])->name('admin.stamp_correction_request.approve');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [AdminCorrectionController::class, 'approveExecute'])->name('admin.stamp_correction_request.approve.execute');
});