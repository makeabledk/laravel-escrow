<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge_id');
            $table->string('source_type');
            $table->integer('source_id');
            $table->string('destination_type');
            $table->integer('destination_id');
            $table->decimal('amount');
            $table->string('currency_code');
            $table->timestamps();

            $table->index(['charge_id']);
            $table->index(['source_type', 'source_id']);
            $table->index(['destination_type', 'destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transactions');
    }
}
