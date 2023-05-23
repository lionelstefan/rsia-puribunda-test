<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\PromosController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeFrontController;
use App\Http\Controllers\RedeemPointController;
use App\Http\Controllers\GetPointController;
use Illuminate\Support\Facades\Route;

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

    route::get('addEvent', [EventsController::class, 'index']);
    Route::get('editEvent/{id}', [EventsController::class, 'editEventIndex'])->name('editEventPage');
    route::get('listEvent', [EventsController::class, 'listEventServerSide']);
    route::get('dataEvent', [EventsController::class, 'getDataEvent']);

    route::post('postEvent', [EventsController::class, 'createEvent'])->name('requestEvent.post');
    route::post('postEventEdit', [EventsController::class, 'editEvent'])->name('requestEditEvent.post');
    route::get('getEventDelete/{id}', [EventsController::class, 'softDeleteEvent'])->name('deleteEventdata');

    route::get('listPromo', [PromosController::class, 'listPromoServerSide']);
    route::get('dataPromo', [PromosController::class, 'getDataPromo']);
    Route::get('editPromo/{id}', [PromosController::class, 'editPromoIndex'])->name('editPromoPage');
    route::get('addPromo', [PromosController::class, 'index']);

    route::post('postPromo', [PromosController::class, 'createPromo'])->name('requestPromo.post');
    route::post('postPromoEdit', [PromosController::class, 'editPromo'])->name('requestEditPromo.post');
    route::get('getPromoDelete/{id}', [PromosController::class, 'softDeletePromo'])->name('deletePromodata');

    route::get('listTransaction', [TransactionController::class, 'index'])->name('listTransaction.index');
    route::get('dataTransaction', [TransactionController::class, 'getDataTransaction'])->name('requestDataTransaction.get');
});
