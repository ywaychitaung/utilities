<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UrlController extends Controller
{
    // Store a shortened URL
    public function store(Request $request)
    {
        try {
            $request->validate([
                'original_url' => 'required|url|max:2048',
            ]);

            // Encrypt the original URL
            $encryptedOriginalUrl = Crypt::encrypt($request->original_url);

            // Check if the encrypted URL already exists
            $existingUrl = Url::where('original_url', $encryptedOriginalUrl)->first();
            if ($existingUrl) {
                return response()->json([
                    'short_url' => $request->getSchemeAndHttpHost().'/'.$existingUrl->short_url,
                ], 200);
            }

            // Generate a unique short URL
            $plainShortUrl = Str::random(6); // For client display
            $hashedShortUrl = Hash::make($plainShortUrl); // For storage

            Url::create([
                'original_url' => $encryptedOriginalUrl,
                'short_url' => $hashedShortUrl,
                'expires_at' => now()->addHour(), // Set expiration to one hour from now
            ]);

            return response()->json([
                'short_url' => $request->getSchemeAndHttpHost().'/'.$plainShortUrl,
            ], 201);
        } catch (\Exception $e) {
            Log::error('URL shorten failed', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return response()->json([
                'error' => 'Failed to shorten URL',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Redirect to the original URL and delete the row
    public function redirect($shortUrl)
    {
        $urls = Url::all(); // Retrieve all URLs for comparison
    
        foreach ($urls as $url) {
            if (Hash::check($shortUrl, $url->short_url)) {
                // Check if the link has expired
                if ($url->expires_at && $url->expires_at->isPast()) {
                    $url->delete(); // Delete the expired link
                    return response()->json(['error' => 'URL has expired'], 404);
                }

                // Decrypt and redirect to the original URL
                $originalUrl = Crypt::decrypt($url->original_url);

                // Delete the row from the database
                $url->delete();

                return redirect($originalUrl); // Redirect to the original URL
            }
        }

        return response()->json(['error' => 'URL not found'], 404);
    }
}
