<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AiController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Helper to validate request and verify note ownership if note_id is provided.
     */
    protected function validateAndCheckOwnership(Request $request): array
    {
        $validated = $request->validate([
            'content' => 'required|string|max:10000',
            'note_id' => 'nullable|integer|exists:notes,id',
            'target_language' => 'nullable|string|in:id,en,es,fr,de,ja,ko,zh,ar,pt,ru,hi,it,nl,tr,pl,sv,da,no,fi,th,vi',
        ]);

        if (!empty($validated['note_id'])) {
            $note = Note::findOrFail($validated['note_id']);
            if ($note->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        }

        return $validated;
    }

    public function summarize(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $result = $this->geminiService->summarize($validated['content']);
            Log::info("AI summarize success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI summarize failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function improve(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $result = $this->geminiService->improve($validated['content']);
            Log::info("AI improve success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI improve failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function continue(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $result = $this->geminiService->continueWriting($validated['content']);
            Log::info("AI continue success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI continue failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function title(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $result = $this->geminiService->generateTitle($validated['content']);
            Log::info("AI title success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI title failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function explain(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $result = $this->geminiService->explain($validated['content']);
            Log::info("AI explain success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI explain failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function translate(Request $request): JsonResponse
    {
        $validated = $this->validateAndCheckOwnership($request);
        try {
            $target = $validated['target_language'] ?? 'en';
            $result = $this->geminiService->translate($validated['content'], $target);
            Log::info("AI translate success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI translate failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|string|in:user,model',
            'history.*.text' => 'required|string|max:5000',
        ]);

        try {
            $result = $this->geminiService->chat(
                $validated['history'] ?? [],
                $validated['message']
            );
            Log::info("AI chat success for user " . Auth::id());
            return response()->json(['result' => $result]);
        } catch (Exception $e) {
            Log::error("AI chat failure for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to generate AI response. Please try again.'], 500);
        }
    }
}
