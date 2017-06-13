<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('posts');
        Schema::create('posts', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('title', 255);
            $table->text('post');
            $table->integer('score');
            $table->integer('image__id');
            $table->foreign('image__id')
                ->references('id')
                ->on('images');
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
        Schema::dropIfExists('posts');
    }
}
