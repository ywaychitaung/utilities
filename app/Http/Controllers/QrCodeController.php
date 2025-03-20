<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        try {
            $qrcode = QrCode::format('png')
                ->size(240)
                ->margin(1)
                ->generate($request->content);

            return response($qrcode)
                ->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}