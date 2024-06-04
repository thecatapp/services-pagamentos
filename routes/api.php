<?php

use App\Http\Controllers\PessoaController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Middleware\ValidarTipoPessoaMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::match(
    ["get", "post"], "logout", [\App\Http\Controllers\AuthController::class, "logout"]
)->name("logout")->middleware('auth:api');

Route::group(['prefix' => 'usuario'], function () {
    Route::post('/info', [\App\Http\Controllers\UserController::class, 'info'])->middleware("auth:api");
});

Route::group(['prefix' => 'pessoa'], function () {
    Route::post('/cadastrarPessoa', [PessoaController::class, 'cadastrarPessoa'])->middleware("auth:api");
});

Route::group(['prefix' => 'transferencia'], function () {
    Route::post('/transferirValores', [TransferenciaController::class, 'transferirValores'])->middleware(
        [
            "auth:api",
            ValidarTipoPessoaMiddleware::class
        ]
    );
});

