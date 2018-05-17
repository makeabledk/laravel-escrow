<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->string('source_type');
            $table->string('source_id');
            $table->string('destination_type');
            $table->string('destination_id');
            $table->decimal('amount');
            $table->string('currency_code');
            $table->integer('associated_escrow_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('associated_escrow_id')->references('id')->on('escrows')->onDelete('set null')->onUpdate('cascade');
            $table->index(['source_id', 'source_type']);
            $table->index(['destination_id', 'destination_type']);
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
