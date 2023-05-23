<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\UnitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;

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
//login page
route::get('/', [LoginController::class, 'login']);
route::post('postLogin', [LoginController::class, 'checkLogin']);
Route::get('logout', [LoginController::class, 'logout']);

Route::middleware('dashboard.auth')->group(function () {
    route::get('home', [HomeController::class, 'index'])->name('backend.home');

    route::get('list-unit', [UnitController::class, 'list']);
    route::get('get-all-unit', [UnitController::class, 'getAllUnit']);
    route::get('create-unit', [UnitController::class, 'create']);
    route::post('add-new-unit', [UnitController::class, 'addNewUnit'])->name('unit.add');
    Route::get('edit-unit/{id}', [UnitController::class, 'editUnit'])->name('unit.edit');
    route::get('delete-unit/{id}', [UnitController::class, 'deleteUnit'])->name('unit.delete');
    route::post('confirm-edit-unit', [UnitController::class, 'confirmEditUnit'])->name('unit.confirm-edit');

    route::get('list-jabatan', [JabatanController::class, 'list']);
    route::get('get-all-jabatan', [JabatanController::class, 'getAllJabatan']);
    route::get('create-jabatan', [JabatanController::class, 'create']);
    route::post('add-new-jabatan', [JabatanController::class, 'addNewJabatan'])->name('jabatan.add');
    Route::get('edit-jabatan/{id}', [JabatanController::class, 'editJabatan'])->name('jabatan.edit');
    route::get('delete-jabatan/{id}', [JabatanController::class, 'deleteJabatan'])->name('jabatan.delete');
    route::post('confirm-edit-jabatan', [JabatanController::class, 'confirmEditJabatan'])->name('jabatan.confirm-edit');

    route::get('list-karyawan', [KaryawanController::class, 'list']);
    route::get('get-all-karyawan', [KaryawanController::class, 'getAllKaryawan']);
    route::get('create-karyawan', [KaryawanController::class, 'create']);
    route::post('add-new-karyawan', [KaryawanController::class, 'addNewKaryawan'])->name('karyawan.add');
    Route::get('edit-karyawan/{id}', [KaryawanController::class, 'editKaryawan'])->name('karyawan.edit');
    route::get('delete-karyawan/{id}', [KaryawanController::class, 'deleteKaryawan'])->name('karyawan.delete');
    route::post('confirm-edit-karyawan', [KaryawanController::class, 'confirmEditKaryawan'])->name('karyawan.confirm-edit'); 
});
