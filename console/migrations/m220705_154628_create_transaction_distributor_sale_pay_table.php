<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction_distributor_sale_pay}}`.
 */
class m220705_154628_create_transaction_distributor_sale_pay_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction_distributor_sale_pay}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'trans_date' => $this->datetime(),
            'cash_amount' => $this->float(),
            'transfer_amount' => $this->float(),
            'company_id' => $this->integer(),
            'branch_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transaction_distributor_sale_pay}}');
    }
}
