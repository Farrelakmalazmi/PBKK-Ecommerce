<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function search(Request $request)
    {
        $query = $request->input('query'); // Ambil query dari request
    
        // Cari produk berdasarkan nama produk atau kategori
        $results = Product::with('category')
                          ->where('name', 'LIKE', "%{$query}%")
                          ->orWhereHas('category', function ($q) use ($query) {
                              $q->where('name', 'LIKE', "%{$query}%");
                          })
                          ->take(8) // Batasi jumlah produk
                          ->get();
    
        return response()->json($results); // Kembalikan hasil dalam bentuk JSON
    }
    
    
}
