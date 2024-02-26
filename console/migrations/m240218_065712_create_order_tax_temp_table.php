<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_tax_temp}}`.
 */
class m240218_065712_create_order_tax_temp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_tax_temp}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_tax_temp}}');
    }
}
