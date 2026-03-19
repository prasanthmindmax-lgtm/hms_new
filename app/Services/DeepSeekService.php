<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.openrouter.key'); // store key in config/services.php
        $this->baseUrl = "https://openrouter.ai/api/v1/chat/completions";
    }
public function generateWithPrompt(string $prompt, array $filesData = [])
{
    try {
        $filesText = "";
        foreach ($filesData as $i => $data) {
            $filesText .= "\n\n--- File ".($i+1)." (".$data['name'].") ---\n";
            $content = $data['content'];

            $filesText .= strlen($content) > 10000
                ? substr($content, 0, 10000)."... [truncated]"
                : $content;
        }

        $finalPrompt = $prompt;
        if (!empty($filesText)) {
            $finalPrompt .= "\n\nAttached Files Content:\n" . $filesText;
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type'  => 'application/json',
            ])
            ->post($this->baseUrl, [
                "model" => "deepseek/deepseek-r1-0528:free",
                "messages" => [
                    ["role" => "user", "content" => $finalPrompt]
                ],
            ]);

        if (!$response->successful()) {
            Log::error('DeepSeek API Error', [
                'status'   => $response->status(),
                'response' => $response->json()
            ]);
            return "Error: Failed to get response from DeepSeek API";
        }

        $responseData = $response->json();

        if (!isset($responseData['choices'][0]['message']['content'])) {
            return "Error: Unexpected response format from DeepSeek";
        }

        return $responseData['choices'][0]['message']['content'];

    } catch (\Exception $e) {
        Log::error('DeepSeek Service Exception: '.$e->getMessage());
        return "Error: Service unavailable. Please try again later.";
    }
}

}
