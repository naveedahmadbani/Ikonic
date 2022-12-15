<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/getData', [HomeController::class, 'getData'])->name('getData');
Route::get('/send_frnd_request', [HomeController::class, 'sendFrndRequest'])->name('send_frnd_request');
Route::get('/accept_frnd_request', [HomeController::class, 'acceptFrndRequest'])->name('accept_frnd_request');
Route::get('/withdraw_frnd_request', [HomeController::class, 'withdrawFrndRequest'])->name('withdraw_frnd_request');
Route::get('/remove_connection', [HomeController::class, 'removeConnection'])->name('remove_connection');
Route::get('/common_frnd', [HomeController::class, 'commonFrnd'])->name('common_frnd');
