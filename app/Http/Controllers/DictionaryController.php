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

}
