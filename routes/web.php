<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BagController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [AuthenticationController::class, 'loginPage'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'login'])->name('login.attempt')->middleware('guest');

Route::authenticated()->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('/', fn() => redirect()->route('home'));
    Route::get('/dashboard', fn() => Inertia::render('Home', []))->name('home');

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'userManagePage')->name('user.browse')->can('user.browse');
        Route::get('/user/data-processing', 'dataprocessing')->name('user.data_processing')->can('user.browse');
        Route::post('/user', 'create')->name('user.create')->can('user.create');
        Route::put('/user/{user:user_uuid}', 'update')->name('user.update')->can('user.update');
        Route::delete('/user/{user:user_uuid}', 'delete')->name('user.delete')->can('user.delete');
        Route::put('/user/{user:user_uuid}/switch-status', 'switchStatus')->name('user.switch_status')->can('user.update');
        Route::post('/user/{user:user_uuid}/change-password', 'changePassword')->name('user.change_password')->can('user.update');
        Route::post('/user/sync-leader', 'syncLeader')->name('user.sync_leader')->can('user.update');
        Route::post('/user/sync-plt', 'syncPlt')->name('user.sync_plt')->can('user.update');
    });
    Route::controller(RoleAndPermissionController::class)->group(function () {
        Route::get('/role-and-permission', 'roleAndPemissionManagePage')->name('role.browse')->can('role.browse');
        Route::post('/role', 'create')->name('role.create')->can('role.create');
        Route::put('/role/{role}', 'update')->name('role.update')->can('role.update');
        Route::delete('/role/{role}', 'delete')->name('role.delete')->can('role.delete');
        Route::get('/role/{role}/permissions', 'getRolePermission')->name('role.permission_list')->can('role.browse');
        Route::get('/role/{role}/users', 'getRoleUser')->name('role.user_list')->can('role.browse');
        Route::put('/role/{role}/switch-permission', 'switchPermission')->name('role.switch_permission')->can('role.assign_permission');
    });
    Route::controller(WarehouseController::class)->group(function () {
        Route::get('/warehouse', 'warehouseManagePage')->name('warehouse.browse')->can('warehouse.browse');
        Route::get('/warehouse/data-processing', 'dataProcessing')->name('warehouse.data_processing')->can('warehouse.browse');
        Route::post('/warehouse', 'create')->name('warehouse.create')->can('warehouse.create');
        Route::put('/warehouse/{warehouse}', 'update')->name('warehouse.update')->can('warehouse.update');
        Route::put('/warehouse/{warehouse:id}/switch-status', 'switchStatus')->name('warehouse.switch_status')->can('warehouse.update');
        Route::delete('/warehouse/{warehouse}', 'delete')->name('warehouse.delete')->can('warehouse.delete');
    });
    Route::controller(CctvController::class)->group(function () {
        Route::get('/cctv', 'cctvManagePage')->name('cctv.browse')->can('cctv.browse');
        Route::get('/cctv/data-processing', 'dataProcessing')->name('cctv.data_processing')->can('cctv.browse');
        Route::post('/cctv', 'create')->name('cctv.create')->can('cctv.create');
        Route::put('/cctv/{cctv}', 'update')->name('cctv.update')->can('cctv.update');
        Route::put('/cctv/{cctv:id}/switch-status', 'switchStatus')->name('cctv.switch_status')->can('cctv.update');
        Route::delete('/cctv/{cctv}', 'delete')->name('cctv.delete')->can('cctv.delete');
    });
    Route::controller(BagController::class)->group(function () {
        Route::get('/bag', 'bagManagePage')->name('bag.browse')->can('bag.browse');
        Route::get('/bag/data-processing', 'dataProcessing')->name('bag.data_processing')->can('bag.browse');
        Route::post('/bag', 'create')->name('bag.create')->can('bag.create');
        Route::post('/bag/{bag}', 'update')->name('bag.update')->can('bag.update');
        Route::put('/bag/{bag:id}/switch-status', 'switchStatus')->name('bag.switch_status')->can('bag.update');
        Route::delete('/bag/{bag}', 'delete')->name('bag.delete')->can('bag.delete');
    });
    Route::controller(ShiftController::class)->group(function () {
        Route::get('/shift', 'shiftManagePage')->name('shift.browse')->can('shift.browse');
        Route::get('/shift/data-processing', 'dataProcessing')->name('shift.data_processing')->can('shift.browse');
        Route::post('/shift', 'create')->name('shift.create')->can('shift.create');
        Route::put('/shift/{shift}', 'update')->name('shift.update')->can('shift.update');
        Route::put('/shift/{shift:id}/switch-status', 'switchStatus')->name('shift.switch_status')->can('shift.update');
        Route::delete('/shift/{shift}', 'delete')->name('shift.delete')->can('shift.delete');
    });
});
