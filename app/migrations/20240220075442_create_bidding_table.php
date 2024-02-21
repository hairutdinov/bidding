<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBiddingTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bidding');
        $table->addColumn('trade_number', 'string')
            ->addColumn('url', 'string')
            ->addColumn('benefit_information', 'text', ['null' => true])
            ->addColumn('contact_person_email', 'string')
            ->addColumn('contact_person_phone_number', 'string')
            ->addColumn('debtor_inn', 'string')
            ->addColumn('bankruptcy_case_number', 'string')
            ->addColumn('date_of_bidding', 'datetime')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

        $lots_table = $this->table('lots');
        $lots_table->addColumn('bidding_id', 'integer', ['signed' => false])
            ->addColumn('name', 'text')
            ->addColumn('lot_number', 'string')
            ->addColumn('starting_price', 'string', ['null' => true])
            ->addForeignKey('bidding_id', 'bidding', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
