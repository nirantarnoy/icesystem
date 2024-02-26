<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_tax_invoice_line}}`.
 */
class m230712_035227_add_order_ref_id_column_to_customer_tax_invoice_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_tax_invoice_line}}', 'order_ref_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_tax_invoice_line}}', 'order_ref_id');
    }
}
