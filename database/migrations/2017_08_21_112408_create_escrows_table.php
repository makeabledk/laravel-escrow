<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEscrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escrows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('escrowable_type');
            $table->integer('escrowable_id');
            $table->decimal('deposit_amount');
            $table->string('deposit_currency');
            $table->boolean('status')->nullable();
            $table->timestamps();

            $table->index(['escrowable_type', 'escrowable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('escrows');
    }
}
