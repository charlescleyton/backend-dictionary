<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WordFavorite;
use App\Models\WordHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DictionaryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        return [
            'Authorization' => "Bearer $token",
        ];
    }

    public function test_get_user_profile()
    {
        $headers = $this->authenticate();

        $response = $this->getJson('/api/user/me', $headers);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_get_dictionary_words_with_cache()
    {
        $headers = $this->authenticate();

        Cache::shouldReceive('get')
            ->once()
            ->andReturn([
                'results' => ['word1', 'word2'],
                'totalDocs' => 2,
                'page' => 1,
                'totalPages' => 1,
                'hasNext' => false,
                'hasPrev' => false,
            ]);

        $response = $this->getJson('/api/entries/en?search=test', $headers);

        $response->assertStatus(200)
            ->assertHeader('x-cache', 'HIT')
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev',
            ]);
    }

    public function test_get_dictionary_words_without_cache()
    {
        $headers = $this->authenticate();

        Http::fake([
            'https://api.dictionaryapi.dev/api/v2/entries/en/*' => Http::response([
                ['word' => 'test'],
                ['word' => 'example'],
            ], 200),
        ]);

        $response = $this->getJson('/api/entries/en?search=test', $headers);

        $response->assertStatus(200)
            ->assertHeader('x-cache', 'MISS')
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev',
            ]);
    }

    public function test_get_word_info_with_cache()
    {
        $headers = $this->authenticate();

        Cache::shouldReceive('get')
            ->once()
            ->with('word_info_test')
            ->andReturn(['word' => 'test', 'meanings' => []]);

        $response = $this->getJson('/api/entries/en/test', $headers);

        $response->assertStatus(200)
            ->assertHeader('x-cache', 'HIT')
            ->assertJsonStructure([
                'word',
                'meanings',
            ]);
    }

    public function test_add_word_to_favorites()
    {
        $headers = $this->authenticate();
        $word = 'example';

        $response = $this->postJson("/api/entries/en/{$word}/favorite", [], $headers);

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Palavra '{$word}' adicionada aos favoritos.",
            ]);

        $this->assertDatabaseHas('word_favorites', ['word' => $word]);
    }

    public function test_remove_word_from_favorites()
    {
        $headers = $this->authenticate();
        $word = 'example';

        $user = auth('api')->user();
        $user->favorites()->create(['word' => $word]);

        $response = $this->deleteJson("/api/entries/en/{$word}/unfavorite", [], $headers);

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Palavra '{$word}' removida dos favoritos.",
            ]);

        $this->assertDatabaseMissing('word_favorites', ['word' => $word]);
    }

    public function test_get_user_favorites()
    {
        $headers = $this->authenticate();

        $user = auth('api')->user();
        $user->favorites()->create(['word' => 'test']);

        $response = $this->getJson('/api/user/me/favorites', $headers);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => ['word', 'added'],
                ],
            ]);
    }

    public function test_get_user_history()
    {
        $headers = $this->authenticate();

        $user = auth('api')->user();
        $user->history()->create([
            'word' => 'test',
            'accessed_at' => now(),
        ]);

        $response = $this->getJson('/api/user/me/history', $headers);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => ['word', 'added'],
                ],
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev',
            ]);
    }

}
