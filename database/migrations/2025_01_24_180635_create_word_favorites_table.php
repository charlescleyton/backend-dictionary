<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('word_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('word');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('word_favorites');
    }
}
