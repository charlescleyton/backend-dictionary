<?php

namespace App\Http\Controllers;

use App\Models\WordFavorite;
use App\Models\WordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class DictionaryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getDictionaryWords(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$search}");

        if ($response->successful()) {
            $words = $response->json();

            $totalDocs = count($words);
            $totalPages = ceil($totalDocs / $limit);
            $offset = ($page - 1) * $limit;
            $results = array_slice($words, $offset, $limit);

            return response()->json([
                'results' => $results,
                'totalDocs' => $totalDocs,
                'page' => $page,
                'totalPages' => $totalPages,
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1,
            ]);
        }

        return response()->json([
            'error' => 'Não foi possível recuperar os dados do dicionário.'
        ], 500);
    }

    public function getWordInfo($word)
    {
        $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

        if ($response->successful()) {
            $user = JWTAuth::user();
            $user->history()->create([
                'word' => $word,
                'accessed_at' => now(),
            ]);

            return response()->json([$response->json()], 200);
        }

        return response()->json([
            'error' => 'Palavra não encontrada.',
        ], 400);
    }

    public function addToFavorites($word)
    {
        $user = JWTAuth::user();

        $user->favorites()->create([
            'word' => $word,
        ]);

        return response()->json([
            'message' => "Palavra '{$word}' adicionada aos favoritos.",
        ], 200);
    }

    public function removeFromFavorites($word)
    {
        $user = JWTAuth::user();

        $user->favorites()->where('word', $word)
            ->delete();

        return response()->json([
            'message' => "Palavra '{$word}' removida dos favoritos.",
        ], 200);
    }
}
