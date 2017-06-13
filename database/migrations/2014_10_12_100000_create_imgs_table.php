<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('imgs');
        Schema::create('imgs', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('url', 255);
            $table->string('path', 255);
            $table->string('thumb_url', 255);
            $table->string('thumb_path', 255);
            $table->integer('gallery__id');
            $table->foreign('gallery__id')
                ->references('id')
                ->on('galleries');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imgs');
    }
}
