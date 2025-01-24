<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('word_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('word');
            $table->timestamp('accessed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('word_histories');
    }
}
