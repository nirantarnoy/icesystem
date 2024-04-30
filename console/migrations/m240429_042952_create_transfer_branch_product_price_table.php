<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transfer_branch_product_price}}`.
 */
class m240429_042952_create_transfer_branch_product_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transfer_branch_product_price}}', [
            'id' => $this->primaryKey(),
            'transfer_branch_id' => $this->integer(),
            'product_id' => $this->integer(),
            'price' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transfer_branch_product_price}}');
    }
}
