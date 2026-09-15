<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// ---- Auth (guest) ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// ---- Authenticated ----
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // ---- Requests (order matters: /create BEFORE /{id}) ----
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');

    Route::middleware('role:teacher')->group(function () {
        Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
        Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    });

    Route::get('/requests/{id}', [RequestController::class, 'show'])
        ->whereNumber('id')->name('requests.show');

    // Assign technicians
    Route::middleware('role:coordinator,lead_technician,admin')->group(function () {
        Route::post('/requests/{id}/assign', [RequestController::class, 'assign'])
            ->whereNumber('id')->name('requests.assign');
    });

    // Technician actions
    Route::middleware('role:technician,lead_technician')->group(function () {
        Route::post('/requests/{id}/status', [RequestController::class, 'updateStatus'])
            ->whereNumber('id')->name('requests.status');

        Route::post('/requests/{id}/diagnosis', [DiagnosisController::class, 'store'])
            ->whereNumber('id')->name('diagnosis.store');

        Route::post('/requests/{id}/material', [MaterialRequestController::class, 'store'])
            ->whereNumber('id')->name('material.store');

        Route::post('/material/{id}/return', [MaterialRequestController::class, 'returnMaterial'])
            ->whereNumber('id')->name('material.return');
    });

    // Supervisor verification
    Route::middleware('role:lead_technician,admin')->group(function () {
        Route::post('/diagnosis/{id}/verify', [DiagnosisController::class, 'verify'])
            ->whereNumber('id')->name('diagnosis.verify');
    });

    // Inventory officer actions
    Route::middleware('role:inventory_officer,admin')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn'])
            ->whereNumber('id')->name('inventory.stockIn');
        Route::post('/material/{id}/release', [InventoryController::class, 'releaseMaterial'])
            ->whereNumber('id')->name('material.release');
    });

    // Equipment
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/{id}/history', [EquipmentController::class, 'history'])
        ->whereNumber('id')->name('equipment.history');

    Route::middleware('role:admin')->group(function () {
        Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    });

    // Reports
    Route::middleware('role:admin,lead_technician,coordinator,inventory_officer')->group(function () {
        Route::get('/reports/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports/audit', [ReportController::class, 'auditTrail'])->name('reports.audit');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])
            ->whereNumber('id')->name('users.toggle');
    });
});