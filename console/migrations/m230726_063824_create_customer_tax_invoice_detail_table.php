<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_tax_invoice_detail}}`.
 */
class m230726_063824_create_customer_tax_invoice_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_tax_invoice_detail}}', [
            'id' => $this->primaryKey(),
            'customer_tax_invoice_id' => $this->integer(),
            'order_line_id' => $this->integer(),
            'product_id' => $this->integer(),
            'qty' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customer_tax_invoice_detail}}');
    }
}
