<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Services\GeminiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $chatService;

    public function __construct(GeminiChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Kirim pesan ke Chatbot AI dan dapatkan respon interaktif.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:3000',
            'publication_id' => 'nullable|integer|exists:publications,id',
            'history' => 'nullable|array|max:20',
            'history.*' => 'array:role,text,content',
            'history.*.role' => 'nullable|string|in:user,model,assistant',
            'history.*.text' => 'nullable|string|max:4000',
            'history.*.content' => 'nullable|string|max:4000',
        ]);

        $message = trim($request->input('message'));
        $publicationId = $request->input('publication_id') ? (int) $request->input('publication_id') : null;
        $history = $request->input('history', []);

        $result = $this->chatService->chat($message, $history, $publicationId);

        return response()->json([
            'status' => 'success',
            'reply' => $result['reply'],
            'mode' => $result['mode'],
            'publication' => $result['publication'],
        ]);
    }

    /**
     * Dapatkan starter quick suggestions / context info untuk widget chatbot.
     */
    public function getSuggestions(Request $request)
    {
        $request->validate(['publication_id' => 'nullable|integer|exists:publications,id']);
        $publicationId = $request->input('publication_id');

        if ($publicationId) {
            $publication = Publication::query()->summarized()->find($publicationId);
            if ($publication) {
                return response()->json([
                    'mode' => 'document',
                    'title' => $publication->title,
                    'category' => $publication->category,
                    'year' => $publication->year,
                    'suggestions' => [
                        "Apa kesimpulan utama dokumen ini?",
                        "Sebutkan indikator statistik terpenting di publikasi ini.",
                        "Jelaskan tren data yang terjadi pada publikasi ini.",
                        "Apa poin-poin penting yang perlu saya ketahui?",
                    ]
                ]);
            }
        }

        // Mode Universal Global
        return response()->json([
            'mode' => 'universal',
            'title' => 'Katalog Publikasi BPS',
            'suggestions' => [
                "Ada di publikasi mana data IPM terbaru?",
                "Di mana saya bisa melihat data kemiskinan dan inflasi?",
                "Publikasi apa saja yang tersedia untuk tahun 2024?",
                "Apa itu Gini Ratio dan bagaimana cara membacanya?",
            ]
        ]);
    }
}
