<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? '';
        $this->model = config('services.gemini.model') ?? 'gemini-flash-latest';
    }

    /**
     * Get system instructions augmented with user preferences.
     */
    protected function getPreferencesInstruction(): string
    {
        if (!Auth::check()) {
            return "";
        }

        $settings = Auth::user()->getOrCreateSettings();
        
        $tone = $settings->ai_tone;
        $length = $settings->ai_response_length;
        $lang = $settings->ai_language;

        $instruction = "\n\nPlease strictly adhere to these user preferences:\n";
        $instruction .= "- Writing Tone: {$tone}. Adjust the output tone to be {$tone}.\n";
        $instruction .= "- Response Length: {$length}. Keep the length {$length}.\n";
        
        if ($lang === 'id') {
            $instruction .= "- Language: Output MUST be in Indonesian (Bahasa Indonesia).\n";
        } elseif ($lang === 'en') {
            $instruction .= "- Language: Output MUST be in English.\n";
        } else {
            $instruction .= "- Language: Auto-detect. Respond in the same language as the input content.\n";
        }

        return $instruction;
    }

    /**
     * Common generative content helper.
     */
    protected function generateContent(string $systemInstruction, string $userPrompt): string
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $systemInstruction .= $this->getPreferencesInstruction();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemInstruction . "\n\nContent:\n" . $userPrompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('AI request failed. Status: ' . $response->status());
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (is_null($text) || trim($text) === '') {
                throw new Exception('Received empty response from AI model.');
            }

            return trim($text);
        } catch (Exception $e) {
            Log::error('GeminiService exception', ['message' => $e->getMessage()]);
            throw new Exception('Failed to generate AI response. ' . $e->getMessage());
        }
    }

    public function summarize(string $content): string
    {
        return $this->generateContent(
            "Summarize the following note content. Keep the summary clear, concise, and focused on key points.",
            $content
        );
    }

    public function improve(string $content): string
    {
        return $this->generateContent(
            "Improve the writing quality of the following note content. Fix grammatical errors, enhance sentence structure, and maintain a professional yet natural tone. Keep the output as improved text only without explanations.",
            $content
        );
    }

    public function continueWriting(string $content): string
    {
        return $this->generateContent(
            "Continue writing the following note context naturally. Add relevant ideas or complete thoughts that fit the existing content. Output the continuation text only.",
            $content
        );
    }

    public function generateTitle(string $content): string
    {
        return $this->generateContent(
            "Generate a short, engaging, and relevant title (maximum 6-7 words) for the following note content. Return ONLY the title, with no quotation marks or explanations.",
            $content
        );
    }

    public function explain(string $content): string
    {
        return $this->generateContent(
            "Explain the concepts and key ideas described in the following note content in simple terms. Present it clearly.",
            $content
        );
    }

    public function translate(string $content, string $direction): string
    {
        $instruction = "Translate the following text to English if it is in Indonesian, or translate it to Indonesian if it is in English. Return ONLY the translated text.";
        if ($direction === 'id') {
            $instruction = "Translate the following text into Indonesian (Bahasa Indonesia). Return ONLY the translated text.";
        } elseif ($direction === 'en') {
            $instruction = "Translate the following text into English. Return ONLY the translated text.";
        }

        return $this->generateContent($instruction, $content);
    }
}