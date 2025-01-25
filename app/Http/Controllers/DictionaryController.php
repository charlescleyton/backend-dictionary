<?php

namespace App\Http\Controllers;

use App\Models\WordFavorite;
use App\Models\WordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $start = microtime(true);

        $cacheKey = "dictionary_words_{$search}_page_{$page}_limit_{$limit}";

        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            $response = $cachedResponse;
            $cacheStatus = 'HIT';
        } else {
            $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$search}");

            if ($response->successful()) {
                $words = $response->json();

                $wordList = array_map(function ($entry) {
                    return $entry['word'];
                }, $words);

                $totalDocs = count($wordList);
                $totalPages = ceil($totalDocs / $limit);
                $offset = ($page - 1) * $limit;
                $results = array_slice($wordList, $offset, $limit);

                Cache::put($cacheKey, [
                    'results' => $results,
                    'totalDocs' => $totalDocs,
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'hasNext' => $page < $totalPages,
                    'hasPrev' => $page > 1,
                ], now()->addMinutes(60));

                $cacheStatus = 'MISS';
                $response = [
                    'results' => $results,
                    'totalDocs' => $totalDocs,
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'hasNext' => $page < $totalPages,
                    'hasPrev' => $page > 1,
                ];
            } else {
                return response()->json([
                    'error' => 'Não foi possível recuperar os dados do dicionário.',
                ], 500);
            }
        }

        $end = microtime(true);
        $responseTime = round(($end - $start) * 1000, 2);

        return response()->json($response)
            ->header('x-cache', $cacheStatus)
            ->header('x-response-time', $responseTime);
    }

    public function getWordInfo($word)
    {
        $start = microtime(true);

        $cacheKey = "word_info_{$word}";

        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            $responseData = $cachedResponse;
            $cacheStatus = 'HIT';
        } else {
            $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

            if ($response->successful()) {
                $user = JWTAuth::user();
                $user->history()->create([
                    'word' => $word,
                    'accessed_at' => now(),
                ]);

                $responseData = $response->json();
                Cache::put($cacheKey, $responseData, now()->addMinutes(60));

                $cacheStatus = 'MISS';
            } else {
                return response()->json([
                    'error' => 'Palavra não encontrada.',
                ], 400);
            }
        }

        $end = microtime(true);
        $responseTime = round(($end - $start) * 1000, 2);

        return response()->json($responseData)
            ->header('x-cache', $cacheStatus)
            ->header('x-response-time', $responseTime);
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

        $favorite = $user->favorites()->where('word', $word)->first();

        if (!$favorite) {
            return response()->json([
                'message' => "A palavra '{$word}' não existe nos favoritos.",
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => "Palavra '{$word}' removida dos favoritos.",
        ], 200);
    }


    public function getUserProfile()
    {
        return response()->json(auth()->user(), 200);
    }

    public function getUserHistory(Request $request)
    {
        $user = JWTAuth::user();

        $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $query = WordHistory::where('user_id', $user->id)
            ->orderBy('accessed_at', 'desc');

        $history = $query->paginate($limit);

        $formattedHistory = $history->getCollection()->map(function ($item) {
            return [
                'word' => $item->word,
                'added' => $item->accessed_at,
            ];
        });

        return response()->json([
            'results' => $formattedHistory,
            'totalDocs' => $history->total(),
            'page' => $history->currentPage(),
            'totalPages' => $history->lastPage(),
            'hasNext' => $history->hasMorePages(),
            'hasPrev' => $history->currentPage() > 1,
        ], 200);
    }

    public function getUserFavorites()
    {
        $user = JWTAuth::user();

        $favorites = WordFavorite::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['word', 'created_at']);

        $formattedFavorites = $favorites->map(function ($item) {
            return [
                'word' => $item->word,
                'added' => $item->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'results' => $formattedFavorites,
        ], 200);
    }
}
