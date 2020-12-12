<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id');
            $table->foreign('question_id')
                ->references('id')->on('questions')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('is_active')->default(false);
            $table->integer('vote_time')->default(30);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('answer_yes')->nullable();
            $table->integer('answer_no')->nullable();
            $table->integer('answer_abstained')->nullable();
            $table->integer('didnt_vote')->nullable();
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
        Schema::dropIfExists('votes');
    }
}
