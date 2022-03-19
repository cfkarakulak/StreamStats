<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_stream', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id');
            $table->string('game_name');
            $table->text('stream_title');
            $table->integer('number_of_viewers');
            $table->string('started_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_stream');
    }
};
