<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Fullstack Challenge 🏅 - Dictionary'
    ]);
});

Route::get('/user/me', [DictionaryController::class,'getUserProfile']);

Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

Route::get('/entries/en', [DictionaryController::class, 'getDictionaryWords']);
Route::get('/entries/en/{word}', [DictionaryController::class, 'getWordInfo']);

Route::post('/entries/en/{word}/favorite', [DictionaryController::class, 'addToFavorites']);
Route::delete('/entries/en/{word}/unfavorite', [DictionaryController::class, 'removeFromFavorites']);

Route::get('/user/me/history', [DictionaryController::class, 'getUserHistory']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
