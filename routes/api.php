<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Public routes
// Auths
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register-driver', [AuthController::class, 'registerDriver'])->name('registerDriver');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::post('/update_password/{id}', [UserController::class, 'updatePasswordUser'])->name('updatePasswordUser');
    Route::post('/update_password/{id}', [DriverController::class, 'updateDriverPasswordUser'])->name('updateDriverPasswordUser');

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
