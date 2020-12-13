<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('event_id');
            $table->foreign('event_id')
                ->references('id')->on('events')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedInteger('message_id')->nullable();
            $table->string('message', 5000);
            $table->dateTime('date');
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
        Schema::dropIfExists('event_chat_messages');
    }
}
