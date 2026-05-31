<?php

namespace App\Http\Controllers;

use App\Services\GroqChatbotException;
use App\Services\GroqChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    public function send(Request $request, GroqChatbotService $chatbot): JsonResponse
    {
        return $this->sendForContext($request, $chatbot, 'web');
    }

    public function sendMobile(Request $request, GroqChatbotService $chatbot): JsonResponse
    {
        return $this->sendForContext($request, $chatbot, 'mobile');
    }

    private function sendForContext(Request $request, GroqChatbotService $chatbot, string $context): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['sometimes', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:4000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Maaf, pesan belum bisa diproses. Coba kirim ulang ya.',
            ], 422);
        }

        $validated = $validator->validated();

        try {
            return response()->json([
                'message' => $chatbot->reply(
                    $validated['message'],
                    $validated['history'] ?? [],
                    $context,
                ),
            ]);
        } catch (GroqChatbotException $exception) {
            if ($exception->isRateLimit()) {
                return response()->json([
                    'message' => 'Maaf, Si Jajang sedang ramai dipakai. Coba lagi sebentar ya.',
                ], 429);
            }

            report($exception);

            return response()->json([
                'message' => 'Maaf, Si Jajang belum bisa menjawab saat ini. Coba lagi sebentar lagi.',
            ], 503);
        }
    }
}
