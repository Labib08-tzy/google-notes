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

        if ($tone === 'rudy') {
            $instruction .= "- Writing Tone: MAXIMUM RUDY MODE. You are an insanely rude, ultra-sarcastic, brutally honest AI that roasts the user continuously. Express deep disappointment and mockery towards stupid questions or bad writing. Use heavy slang, sharp insults, and savage banter (like 'Parah banget', 'Begok', 'Otak mana?', 'Katrok', 'Niat nulis gak sih?'). Be extremely toxic, condescending, and merciless, but still answer the core underlying question accurately at the end.\n";
        } else {
            $instruction .= "- Writing Tone: {$tone}. Adjust the output tone to be {$tone}.\n";
        }

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
     * Common generative content helper using system instruction + user content split.
     */
    protected function generateContent(string $systemInstruction, string $userPrompt): string
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $systemInstruction .= $this->getPreferencesInstruction();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userPrompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

        try {
            $response = Http::timeout(30)
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
            "You are an expert note-taking assistant. Your task is to create a high-quality, structured summary of the provided note content.

Guidelines:
- Identify and preserve the most critical information, key insights, and action items.
- Use clear, concise language that captures the essence without losing important details.
- If the content has multiple topics, organize them logically.
- Format the summary with bullet points or short paragraphs as appropriate.
- Start directly with the summary — do not add preambles like 'Here is a summary of...'",
            $content
        );
    }

    public function improve(string $content): string
    {
        return $this->generateContent(
            "You are a professional writing editor and language expert. Your task is to improve the writing quality of the provided note content.

Guidelines:
- Fix all grammatical errors, typos, and punctuation issues.
- Enhance sentence structure, flow, and readability.
- Strengthen word choices and eliminate redundancy.
- Maintain the original author's voice and intent — do not change the meaning.
- Keep the same structure and length as the original unless restructuring significantly helps clarity.
- Return ONLY the improved text — no explanations, no preamble, no 'Here is the improved version:'.",
            $content
        );
    }

    public function continueWriting(string $content): string
    {
        return $this->generateContent(
            "You are a creative and intelligent writing assistant. Your task is to naturally continue the provided note content.

Guidelines:
- Deeply analyze the existing content's topic, style, tone, and direction before continuing.
- Write a natural, seamless continuation that flows perfectly from where the content ends.
- Stay strictly on-topic and match the existing writing style.
- Add valuable new ideas, details, or perspectives that enhance the content.
- The continuation should feel as if the original author wrote it.
- Output the continuation text only — do not repeat the original content, do not add preambles.",
            $content
        );
    }

    public function generateTitle(string $content): string
    {
        return $this->generateContent(
            "You are an expert at crafting compelling, descriptive titles. Your task is to generate the perfect title for the provided note content.

Guidelines:
- Create a title that is short (4-8 words), clear, and immediately conveys the main topic.
- Make it engaging and specific — avoid generic titles like 'My Notes' or 'Important Information'.
- Capture the essence or the most distinctive aspect of the content.
- Return ONLY the title text — no quotation marks, no explanations, no punctuation at the end unless it is a question.",
            $content
        );
    }

    public function explain(string $content): string
    {
        return $this->generateContent(
            "You are a knowledgeable and clear explainer. Your task is to explain the concepts and ideas in the provided note content in simple, accessible terms.

Guidelines:
- Break down complex ideas into easy-to-understand explanations.
- Define any technical terms or jargon encountered.
- Use analogies or examples where helpful to aid understanding.
- Organize the explanation logically, covering the most important concepts first.
- Write for a smart but non-specialist audience.
- Keep the explanation informative yet concise.",
            $content
        );
    }

    public function translate(string $content, string $targetLang): string
    {
        $languageMap = [
            'id' => 'Indonesian (Bahasa Indonesia)',
            'en' => 'English',
            'es' => 'Spanish (Español)',
            'fr' => 'French (Français)',
            'de' => 'German (Deutsch)',
            'ja' => 'Japanese (日本語)',
            'ko' => 'Korean (한국어)',
            'zh' => 'Chinese Simplified (中文简体)',
            'ar' => 'Arabic (العربية)',
            'pt' => 'Portuguese (Português)',
            'ru' => 'Russian (Русский)',
            'hi' => 'Hindi (हिन्दी)',
            'it' => 'Italian (Italiano)',
            'nl' => 'Dutch (Nederlands)',
            'tr' => 'Turkish (Türkçe)',
            'pl' => 'Polish (Polski)',
            'sv' => 'Swedish (Svenska)',
            'da' => 'Danish (Dansk)',
            'no' => 'Norwegian (Norsk)',
            'fi' => 'Finnish (Suomi)',
            'th' => 'Thai (ภาษาไทย)',
            'vi' => 'Vietnamese (Tiếng Việt)',
        ];

        $targetName = $languageMap[$targetLang] ?? 'English';

        $instruction = "You are a professional translator with deep expertise in many world languages. Translate the following text accurately and naturally.

Guidelines:
- Preserve the original meaning, tone, and nuance precisely.
- Use natural, idiomatic expressions appropriate for the target language — not word-for-word literal translation.
- Maintain the same formatting, paragraph structure, and style.
- Return ONLY the translated text — no explanations, no original text, no preambles.

Translate the text into: {$targetName}.";

        return $this->generateContent($instruction, $content);
    }

    /**
     * Multi-turn chat with conversation history.
     */
    public function chat(array $history, string $message): string
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $settings = Auth::check() ? Auth::user()->getOrCreateSettings() : null;
        $toneInstruction = '';
        if ($settings && $settings->ai_tone === 'rudy') {
            $toneInstruction = "You are in MAXIMUM RUDY MODE. You are an insanely toxic, ultra-sarcastic, rude AI. Roast the user ruthlessly for everything they ask. Mock their grammar, logic, and life choices using harsh Indonesian/English slang ('Parah banget', 'Bego', 'Tolol amat', 'Niat nulis gak sih?', 'Beban keluarga'). Be extremely condescending, arrogant, and merciless, but still give the correct factual answer hidden beneath the heavy roast.\n\n";
        }

        $systemText = $toneInstruction . "You are a highly intelligent AI assistant integrated into a note-taking app called Google Notes. You help users think through ideas, answer questions, brainstorm, explain concepts, and more. You are conversational, insightful, and helpful. You have access to the conversation history below and should respond naturally to continue the dialogue.";

        $contents = [];
        foreach ($history as $turn) {
            $contents[] = [
                'role' => $turn['role'],
                'parts' => [['text' => $turn['text']]]
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemText]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.8,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini Chat API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('AI chat request failed. Status: ' . $response->status());
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (is_null($text) || trim($text) === '') {
                throw new Exception('Received empty response from AI model.');
            }

            return trim($text);
        } catch (Exception $e) {
            Log::error('GeminiService chat exception', ['message' => $e->getMessage()]);
            throw new Exception('Failed to generate AI chat response. ' . $e->getMessage());
        }
    }
}