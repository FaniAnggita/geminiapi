<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public function sendPrompt(Request $request)
    {
        $prompt = $request->input('prompt');
        $apiKey = env('GOOGLE_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);


        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);


        curl_close($curl);

        if ($error) {
            return response()->json([
                'error' => 'Curl error',
                'message' => $error,
            ], 500);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return response()->json(json_decode($response, true), $httpCode);
        }

        return response()->json([
            'error' => 'Request failed',
            'status_code' => $httpCode,
            'response' => json_decode($response, true),
        ], $httpCode);
    }
}
