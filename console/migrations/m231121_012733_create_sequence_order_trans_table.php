<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sequence_order_trans}}`.
 */
class m231121_012733_create_sequence_order_trans_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sequence_order_trans}}', [
            'id' => $this->primaryKey(),
            'order_type_id' => $this->integer(),
            'route_id' => $this->integer(),
            'last_no' => $this->string(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sequence_order_trans}}');
    }
}
