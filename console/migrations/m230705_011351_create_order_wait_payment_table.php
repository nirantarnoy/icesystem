<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_wait_payment}}`.
 */
class m230705_011351_create_order_wait_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_wait_payment}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_wait_payment}}');
    }
}
