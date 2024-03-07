<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_tax_invoice_line}}`.
 */
class m240307_041114_add_unit_id_column_to_customer_tax_invoice_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_tax_invoice_line}}', 'unit_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_tax_invoice_line}}', 'unit_id');
    }
}
