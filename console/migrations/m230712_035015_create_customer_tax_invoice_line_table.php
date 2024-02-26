<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_tax_invoice_line}}`.
 */
class m230712_035015_create_customer_tax_invoice_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_tax_invoice_line}}', [
            'id' => $this->primaryKey(),
            'tax_invoice_id' => $this->integer(),
            'product_group_id' => $this->integer(),
            'qty' => $this->float(),
            'price' => $this->float(),
            'line_total' => $this->float(),
            'discount_amount' => $this->float(),
            'status' => $this->integer(),
            'remark' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customer_tax_invoice_line}}');
    }
}
