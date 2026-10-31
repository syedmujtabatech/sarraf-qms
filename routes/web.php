<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\CustomerHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerOverrideController;
use App\Http\Controllers\Admin\PriorityCategoryController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\AppointmentController;
use App\Http\Controllers\Customer\FeedbackController;
use App\Http\Controllers\Customer\MobileTrackerController;
use App\Http\Controllers\Display\DisplayController;
use App\Http\Controllers\Kiosk\KioskController;
use App\Http\Controllers\Teller\TellerConsoleController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/login/quick/{userId}', [AuthController::class, 'quickLogin'])->name('login.quick');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Self-Service Kiosk (Touchscreen Ticket Dispenser) - Public / Kiosk Device
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('index');
    Route::post('/generate', [KioskController::class, 'generate'])->name('generate');
    Route::get('/slip/{token}', [KioskController::class, 'printSlip'])->name('printSlip');
});

// Waiting Hall Electronic TV Display & Urdu/English Voice Audio Announcements
Route::prefix('display')->name('display.')->group(function () {
    Route::get('/', [DisplayController::class, 'index'])->name('index');
    Route::get('/poll/{branchId}', [DisplayController::class, 'pollUpdates'])->name('poll');
    Route::get('/pole/{counter}', [DisplayController::class, 'pole'])->name('pole');
    Route::get('/pole-api/{counter}', [DisplayController::class, 'poleApi'])->name('poleApi');
});

// Customer Online / Mobile Appointment Booking (SBI Tender Spec Reference 1)
Route::prefix('appointment')->name('appointment.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::post('/store', [AppointmentController::class, 'store'])->name('store');
    Route::get('/confirmation/{appointment}', [AppointmentController::class, 'confirmation'])->name('confirmation');
    Route::post('/check-in/{appointment}', [AppointmentController::class, 'checkIn'])->name('checkIn');
});

// Customer Feedback Rating Terminal (SBI Tender Spec Reference 1)
Route::prefix('feedback')->name('feedback.')->group(function () {
    Route::get('/', [FeedbackController::class, 'index'])->name('index');
    Route::post('/store', [FeedbackController::class, 'store'])->name('store');
    Route::get('/thanks/{feedback}', [FeedbackController::class, 'thankYou'])->name('thankYou');
});

// Mobile Entrance QR Self-Service Token Generation
Route::get('/join', [MobileTrackerController::class, 'joinForm'])->name('mobile.join');
Route::post('/join', [MobileTrackerController::class, 'processJoin'])->name('mobile.join.post');

// Mobile Live Token Tracking (Scan QR Code from Phone)
Route::get('/m/{tokenNumber}', [MobileTrackerController::class, 'show'])->name('mobile.track');
Route::get('/m/api/status/{tokenNumber}', [MobileTrackerController::class, 'status'])->name('mobile.status');

// Teller / Counter Operator Console (Real-time Workbench)
Route::prefix('teller')->name('teller.')->middleware('auth')->group(function () {
    Route::get('/', [TellerConsoleController::class, 'index'])->name('index');
    Route::post('/call-next', [TellerConsoleController::class, 'callNext'])->name('callNext');
    Route::post('/call-specific', [TellerConsoleController::class, 'callSpecific'])->name('callSpecific');
    Route::post('/recall/{token}', [TellerConsoleController::class, 'recall'])->name('recall');
    Route::post('/start-serving/{token}', [TellerConsoleController::class, 'startServing'])->name('startServing');
    Route::post('/complete/{token}', [TellerConsoleController::class, 'complete'])->name('complete');
    Route::post('/hold/{token}', [TellerConsoleController::class, 'hold'])->name('hold');
    Route::post('/transfer/{token}', [TellerConsoleController::class, 'transfer'])->name('transfer');
    Route::post('/no-show/{token}', [TellerConsoleController::class, 'noShow'])->name('noShow');
    Route::post('/counter-break/{counter}', [TellerConsoleController::class, 'takeBreak'])->name('takeBreak');
    Route::post('/counter-resume/{counter}', [TellerConsoleController::class, 'resumeWork'])->name('resumeWork');
    Route::post('/counter-drawer/{counter}', [TellerConsoleController::class, 'updateCashDrawer'])->name('updateCashDrawer');
    Route::get('/api/queue/{counterId}', [TellerConsoleController::class, 'liveQueueApi'])->name('liveQueueApi');
});

// Centralized Administration & Management System
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/live/{branchId}', [DashboardController::class, 'liveApi'])->name('liveApi');

    // Regions & Clusters (Real Banking Hierarchy)
    Route::resource('regions', RegionController::class)->except(['create', 'edit', 'show']);

    // Branch Management
    Route::resource('branches', BranchController::class)->except(['create', 'edit', 'show']);

    // Counter Management
    Route::resource('counters', CounterController::class)->except(['create', 'edit', 'show']);

    // Service Categories
    Route::resource('services', ServiceCategoryController::class)->except(['create', 'edit', 'show']);

    // Priority Categories (SBP & SBI regulatory compliance)
    Route::resource('priorities', PriorityCategoryController::class)->except(['create', 'edit', 'show']);

    // Staff Users & Counter Binding
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

    // Roles & Fine-grained Permissions (RBAC)
    Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);

    // Manager Override & Emergency Token Injection & Floor PA Broadcast
    Route::post('/manager/override-inject', [ManagerOverrideController::class, 'injectEmergencyToken'])->name('manager.injectEmergencyToken');
    Route::post('/manager/escalate/{token}', [ManagerOverrideController::class, 'escalateToken'])->name('manager.escalateToken');
    Route::post('/manager/broadcast-announcement', [ManagerOverrideController::class, 'broadcastAnnouncement'])->name('manager.broadcastAnnouncement');

    // Compliance & Analytical Reports (Avg wait time, PWD/Pensioner SLA, Teller efficiency, CSV export)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');

    // Customer Service History & Tracking (Audit CNIC, Phone, Service records)
    Route::get('/customers/history', [CustomerHistoryController::class, 'index'])->name('customers.history');
});
