<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlumniVerificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Send the uploaded document to Claude, extract identity data,
     * and compare it against what the user typed in the form.
     */
    public function verify(string $absoluteImagePath, string $claimedName): array
    {
        $mime = mime_content_type($absoluteImagePath) ?: 'image/jpeg';
        $base64 = base64_encode(file_get_contents($absoluteImagePath));

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-6',
                'max_tokens' => 500,
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mime,
                                'data' => $base64,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'This is a school ID, diploma, or yearbook photo submitted as proof of alumni status. '
                                . 'Extract the full name, school name, and graduation year if visible. '
                                . 'Respond ONLY with valid JSON, no other text, in this exact shape: '
                                . '{"name": "", "school": "", "year": "", "document_looks_legitimate": true}',
                        ],
                    ],
                ]],
            ]);

            if ($response->failed()) {
                Log::error('Alumni verification API call failed', ['body' => $response->body()]);
                return $this->failedResult();
            }

            $text = data_get($response->json(), 'content.0.text', '{}');
            $text = trim(str_replace(['```json', '```'], '', $text));
            $extracted = json_decode($text, true) ?? [];

        } catch (\Throwable $e) {
            Log::error('Alumni verification exception: ' . $e->getMessage());
            return $this->failedResult();
        }

        $extractedName = $extracted['name'] ?? '';
        similar_text(
            $this->normalize($extractedName),
            $this->normalize($claimedName),
            $percent
        );

        $legitimate = $extracted['document_looks_legitimate'] ?? false;
        $confidence = (int) round($percent);

        return [
            'extracted' => $extracted,
            'confidence' => $confidence,
            // Require both a strong name match AND the document to look legitimate
            'auto_verified' => $confidence >= 85 && $legitimate,
        ];
    }

    /**
     * Normalize a name for fuzzy comparison (lowercase, collapse whitespace).
     */
    protected function normalize(string $value): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $value)));
    }

    /**
     * Fallback result when the API call fails or throws.
     * Always routes to manual admin review instead of silently failing open.
     */
    protected function failedResult(): array
    {
        return [
            'extracted' => null,
            'confidence' => 0,
            'auto_verified' => false,
        ];
    }
}
