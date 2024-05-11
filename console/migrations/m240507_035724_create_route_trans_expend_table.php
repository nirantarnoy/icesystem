<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route_trans_expend}}`.
 */
class m240507_035724_create_route_trans_expend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%route_trans_expend}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%route_trans_expend}}');
    }
}
