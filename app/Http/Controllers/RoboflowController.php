<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoboflowController extends Controller
{
    public function detect(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Simulasi pemrosesan gambar (ubah dengan pemrosesan Roboflow API Anda)
            $path = $image->path(); // Path file
            $predictions = [
                ['class' => 'Coffee Cup', 'confidence' => 0.85],
                ['class' => 'Machine Part', 'confidence' => 0.73]
            ]; // Simulasi hasil deteksi
            
            return response()->json([
                'predictions' => $predictions,
            ]);
        }

        return response()->json([
            'error' => 'No image uploaded',
        ], 400);
    }
}
