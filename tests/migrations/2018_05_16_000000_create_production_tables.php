<?php

use Illuminate\Database\Migrations\Migration;

require __DIR__.'/../../database/migrations/create_escrows_table.php.stub';
require __DIR__.'/../../database/migrations/create_escrow_transactions_table.php.stub';
require __DIR__.'/../../database/migrations/add_label_type_to_escrow_transactions_table.php.stub';

class CreateProductionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        (new CreateEscrowsTable())->up();
        (new CreateEscrowTransactionsTable())->up();
        (new AddLabelTypeToEscrowTransactionsTable())->up();
    }
}
