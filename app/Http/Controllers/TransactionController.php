<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class TransactionController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        try {
            $transactions = Transaction::query()
                ->with('category:id,nama')
                ->select('id', 'item', 'total', 'catatan', 'category_id')
                ->when($request->query('category_id'), function ($query, $categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->latest('id')
                ->get();
            
            return $this->success($transactions, 'Daftar transaksi berhasil diambil');
        } catch (QueryException $e) {
            Log::error('Pesan konteks: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan pada database', 500);
        }
    }

    public function show(string $id): JsonResponse
{
try {
$transaction = Transaction::with('category:id,nama')->find($id);
if (!$transaction) {
return $this->error('Transaksi tidak ditemukan', 404);
}
return $this->success($transaction, 'Detail transaksi');
} catch (QueryException $e) {
Log::error('Gagal membaca detail transaksi: ' . $e->getMessage());
return $this->error('Terjadi kesalahan pada database', 500);
}
}

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item' => 'required|string|max:100',
            'total' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal', 422, $validator->errors());
        }

        try {
            $transaction = Transaction::create($validator->validated());

            return $this->success($transaction, 'Transaksi berhasil ditambahkan', 201);
        } catch (QueryException $e) {
            Log::error('Pesan konteks: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan pada database', 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return $this->error('Transaksi tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'item' => 'required|string|max:100',
            'total' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal', 422, $validator->errors());
        }

        try {
            $transaction->update($validator->validated());
            return $this->success($transaction, 'Transaksi berhasil diubah');
        } catch (QueryException $e) {
            Log::error('Pesan konteks: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan pada database', 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = Transaction::whereKey($id)->delete();
            if ($deleted === 0) {
                return $this->error('Transaksi tidak ditemukan', 404);
            }
            return $this->success(null, 'Transaksi berhasil dihapus');
        } catch (QueryException $e) {
            Log::error('Pesan konteks: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan pada database', 500);
        }
    }
}