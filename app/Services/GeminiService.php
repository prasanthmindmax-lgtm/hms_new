<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.gemini.key');
        $this->model   = config('services.gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function generateWithPrompt(string $prompt, array $filesData = [])
    {
        try {
            // Process file contents
            $filesText = "";
            foreach ($filesData as $i => $data) {
                // Truncate very large files to prevent hitting API limits
                $filesText .= "\n\n--- File ".($i+1)." ---\n";
                $filesText .= strlen($data) > 10000 ? substr($data, 0, 10000)."... [truncated]" : $data;
            }

            $finalPrompt = $prompt;
            if (!empty($filesText)) {
                $finalPrompt .= "\n\nAttached Files Content:\n" . $filesText;
            }

            $response = Http::timeout(30)
                ->asJson()
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    "contents" => [
                        [
                            "parts" => [
                                ["text" => $finalPrompt]
                            ]
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return "Error: Failed to get response from Gemini API";
            }

            $responseData = $response->json();

            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return "Error: Unexpected response format from Gemini";
            }

            return $responseData['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: '.$e->getMessage());
            return "Error: Service unavailable. Please try again later.";
        }
    }
}