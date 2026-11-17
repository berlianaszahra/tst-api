<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        try {
            $categories = Category::all();
            return $this->success($categories, 'Daftar kategori berhasil diambil');
        } catch (QueryException $e) {
            Log::error('Pesan konteks: ' . $e->getMessage());
            return $this->error('Terjadi kesalahan pada database', 500);
        }
    }
}