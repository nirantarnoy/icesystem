<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer_checkin}}`.
 */
class m230611_071436_create_customer_checkin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer_checkin}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'checkin_date' => $this->datetime(),
            'latlong' => $this->string(),
            'status' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customer_checkin}}');
    }
}
