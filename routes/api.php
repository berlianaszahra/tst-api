
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

// Route publik
Route::post('/login', [AuthController::class, 'login']);

Route::get('/ping', function () {
    return ['message' => 'API Laravel berjalan'];
});

// Route terlindungi JWT
Route::middleware('auth:api')->group(function () {

    Route::get('/me', function () {
    $user = auth('api')->user();

    return response()->json([
        'success' => true,
        'message' => 'Profil pengguna',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            ],
        ]);
    });

    Route::get('/transactions',
        [TransactionController::class, 'index']);

    Route::get('/transactions/{id}',
        [TransactionController::class, 'show'])
        ->whereNumber('id');

    Route::post('/transactions',
        [TransactionController::class, 'store']);

    Route::match(
        ['put', 'patch'],
        '/transactions/{id}',
        [TransactionController::class, 'update']
    )->whereNumber('id');

    Route::delete('/transactions/{id}',
        [TransactionController::class, 'destroy'])
        ->whereNumber('id');

    // Route categories dari project sebelumnya
    Route::apiResource('categories', CategoryController::class);
});
