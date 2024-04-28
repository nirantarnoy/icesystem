<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction_distributor_sale}}`.
 */
class m240418_141945_add_free_qty_column_to_transaction_distributor_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transaction_distributor_sale}}', 'free_qty', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transaction_distributor_sale}}', 'free_qty');
    }
}
