<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source_type');
            $table->integer('source_id');
            $table->text('source_data')->nullable();
//            $table->string('destination_type');
//            $table->integer('destination_id');
//            $table->string('transfer_type');
//            $table->string('transfer_id');
            $table->decimal('amount');
            $table->string('currency_code');
            $table->boolean('is_refund')->default(0);
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
//            $table->index(['destination_type', 'destination_id']);
//            $table->index(['transfer_type', 'transfer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::drop('transactions');
    }
}
