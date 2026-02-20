<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/api/ai', function (Request $request) {
    $apiKey = getenv('GEMINI_API_KEY');
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent";

    $payload = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $request->get("prompt"),
                    ]
                ]
            ]
        ]
    ];

    $jsonPayload = json_encode($payload);

    try {
        $ch = curl_init($url);

        if ($ch === false) {
            throw new Exception('Failed to initialize cURL session.');
        }

        $headers = [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('cURL Request Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            throw new Exception("HTTP Error Code {$httpCode}. API Response: {$response}");
        }

        $responseData = json_decode($response, true);
    } catch (Exception $error) {
        file_put_contents('php://stderr', "AI API Error: " . $error->getMessage() . "\n");
        return response()->json(['error' => $error->getMessage()], 500);
    }
    return $responseData;
});

require __DIR__ . '/settings.php';
