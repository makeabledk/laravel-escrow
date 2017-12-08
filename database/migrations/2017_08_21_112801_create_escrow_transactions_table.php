<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEscrowTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('source');
            $table->morphs('destination');
            $table->decimal('amount');
            $table->string('currency_code');
            $table->integer('associated_escrow_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('associated_escrow_id')->references('id')->on('escrows')->onDelete('set null')->onUpdate('cascade');
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
