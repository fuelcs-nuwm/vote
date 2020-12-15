<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->foreign('group_id')
                ->references('id')->on('groups')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreignId('customer_id');
            $table->foreign('customer_id')
                ->references('id')->on('customers')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('customer_group');
    }
}
