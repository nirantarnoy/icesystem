<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction_manager_daily}}`.
 */
class m240218_093044_create_transaction_manager_daily_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction_manager_daily}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'product_id' => $this->integer(),
            'price' => $this->float(),
            'cash_qty' => $this->float(),
            'credit_pos_qty' => $this->float(),
            'car_qty' => $this->float(),
            'other_branch_qty' => $this->float(),
            'qty_total' => $this->float(),
            'cash_amount' => $this->float(),
            'credit_pos_amount' => $this->float(),
            'car_amount' => $this->float(),
            'other_branch_amount' => $this->float(),
            'amount_total' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transaction_manager_daily}}');
    }
}
