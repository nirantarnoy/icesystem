<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_tax_invoice}}`.
 */
class m230712_034809_create_customer_tax_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_tax_invoice}}', [
            'id' => $this->primaryKey(),
            'invoice_no' => $this->string(),
            'customer_id' => $this->integer(),
            'invoice_date' => $this->datetime(),
            'payment_term_id' => $this->integer(),
            'payment_date' => $this->datetime(),
            'remark' => $this->string(),
            'status' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'total_amount' => $this->float(),
            'vat_amount' => $this->float(),
            'net_amount' => $this->float(),
            'total_text' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customer_tax_invoice}}');
    }
}
