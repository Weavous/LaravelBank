<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [ApiController::class, 'register']);

Route::prefix('auth')->group(function () {
    Route::post('login', [ApiController::class, 'login']);
    Route::post('logout', [ApiController::class, 'logout']);
    Route::get('user', [ApiController::class, 'user']);
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::resource('wallets', WalletController::class);

    Route::post('withdraw', [TransactionController::class, 'withdraw']);
    Route::post('deposit', [TransactionController::class, 'deposit']);

    Route::get("config", function () {
        return response()->json([
            "timestamp" => now()
        ]);
    });
});
