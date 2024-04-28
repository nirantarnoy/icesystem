<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%std_price_group}}`.
 */
class m240428_032400_create_std_price_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%std_price_group}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'description' => $this->string(),
            'product_id' => $this->integer(),
            'price' => $this->float(),
            'type_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%std_price_group}}');
    }
}
