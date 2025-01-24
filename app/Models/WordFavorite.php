<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordFavorite extends Model
{
    use HasFactory;

    protected $table = 'word_favorites';

    protected $fillable = [
        'user_id',
        'word'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
