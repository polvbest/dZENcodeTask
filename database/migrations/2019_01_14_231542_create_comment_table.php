<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('comment', function (Blueprint $table) {
      $table->increments('id');
      $table->longText('text');
      $table->unsignedInteger('author_id');
      $table->unsignedInteger('parent_id')->nullable();
      $table->timestamps();

      $table->foreign('author_id')
        ->references('id')->on('blog_user')
        ->onDelete('cascade');

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('comment');
  }
}
