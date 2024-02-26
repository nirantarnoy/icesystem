<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_invoice}}`.
 */
class m231118_040331_add_invoice_type_column_to_customer_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_invoice}}', 'invoice_type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_invoice}}', 'invoice_type');
    }
}
