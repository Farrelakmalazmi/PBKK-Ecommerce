<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    /**
     * Show the image upload form.
     */
    public function showUploadForm()
    {
        return view('upload'); // View untuk halaman upload
    }

    /**
     * Handle image upload and classification.
     */
    public function uploadImage(Request $request)
    {
        // Validasi file gambar
        $request->validate([
            'file' => 'required|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:2048',
        ]);

        if (!$request->file('file')->isValid()) {
            return response()->json(['error' => 'Invalid file upload'], 400);
        }

        try {
            // Simpan gambar di direktori public/images
            $path = $request->file('file')->store('images', 'public');

            // Panggil fungsi untuk klasifikasi gambar
            $classification = $this->classifyImage($path);

            // Return hasil klasifikasi
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded and classified successfully!',
                'path' => Storage::url($path),
                'classification' => $classification,
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading and classifying image: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the image'], 500);
        }
    }

    /**
     * Kirim gambar ke Node.js API untuk klasifikasi.
     */
    private function classifyImage($imagePath)
    {
        $nodeApiUrl = 'http://localhost:3000/classify'; // URL Node.js

        try {
            // Ubah gambar ke Base64
            $imageBase64 = base64_encode(file_get_contents(storage_path('app/public/' . $imagePath)));

            // Kirim gambar ke Node.js server
            $response = Http::post($nodeApiUrl, [
                'imageBase64' => $imageBase64,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Classification failed: ' . $response->body());
            return ['error' => 'Classification failed'];
        } catch (\Exception $e) {
            Log::error('Error during classification: ' . $e->getMessage());
            return ['error' => 'Classification encountered an error'];
        }
    }
    
}
