<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Rute untuk Categories
Route::apiResource('categories', CategoryController::class);

Route::get('/debug/n-plus-1', function () {
    DB::enableQueryLog();
    foreach (Transaction::with('category')->get() as $t) {
        $nama = $t->category?->nama;
    }
    return ['jumlah_query' => count(DB::getQueryLog())];
});

Route::get('/debug/transactions-query', function () {
    DB::enableQueryLog();
    
    // Mengambil data transaksi dengan relasi category (Eager Loading)
    $transactions = Transaction::with('category')->get();
    
    return [
        'jumlah_query' => count(DB::getQueryLog()),
        'queries' => DB::getQueryLog(),
        'data' => $transactions
    ];
});

Route::apiResource('categories', 'App\\Http\\Controllers\\CategoryController');

Route::get('/ping', function () {
    return ['message' => 'API Laravel berjalan'];
});

Route::get('/transactions', [TransactionController::class, 'index']);

Route::get('/transactions/{id}', [TransactionController::class, 'show'])->whereNumber('id');

Route::post('/transactions', [TransactionController::class, 'store']);

Route::match(['put', 'patch'], '/transactions/{id}', [TransactionController::class, 'update'])->whereNumber('id');

Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->whereNumber('id');

Route::apiResource('transactions', TransactionController::class);

