<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['journal', 'conference']);
            $table->text('name');
            $table->text('paper_title');
            $table->string('authors');
            $table->date('date');
            $table->smallInteger('volume')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('number')->nullable();
            $table->string('indexed_in');
            $table->string('page_numbers');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('scholar_id');

            $table->foreign('scholar_id')->references('id')->on('scholars')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publications');
    }
}