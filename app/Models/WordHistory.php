<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordHistory extends Model
{
    use HasFactory;
    protected $table = 'word_histories';

    protected $fillable = ['user_id', 'word', 'accessed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
