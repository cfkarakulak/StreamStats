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
        Schema::create('twitch_stream_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_id');
            $table->string('tag');

            $table->foreign('stream_id')->references('id')->on('twitch_stream');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_stream_tag');
    }
};
