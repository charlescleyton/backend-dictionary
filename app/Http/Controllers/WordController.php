<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WordController extends Controller
{
    public function importWords()
    {
        set_time_limit(120);
        $url = 'https://raw.githubusercontent.com/dwyl/english-words/master/words.txt';

        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Não foi possível baixar o arquivo.'], 500);
        }

        $filePath = storage_path('app/words.txt');
        file_put_contents($filePath, $response->body());

        $words = explode("\n", $response->body());

        foreach ($words as $word) {
            $word = trim($word);
            if ($word !== '') {
                Word::create(['word' => $word]);
            }
        }

        return response()->json(['success' => 'Palavras importadas com sucesso.']);
    }
}
