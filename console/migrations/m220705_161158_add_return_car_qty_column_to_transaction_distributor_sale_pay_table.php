<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction_distributor_sale_pay}}`.
 */
class m220705_161158_add_return_car_qty_column_to_transaction_distributor_sale_pay_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transaction_distributor_sale_pay}}', 'return_car_qty', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transaction_distributor_sale_pay}}', 'return_car_qty');
    }
}
