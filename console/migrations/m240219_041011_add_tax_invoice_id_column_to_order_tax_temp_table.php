<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_tax_temp}}`.
 */
class m240219_041011_add_tax_invoice_id_column_to_order_tax_temp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_tax_temp}}', 'tax_invoice_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_tax_temp}}', 'tax_invoice_id');
    }
}
