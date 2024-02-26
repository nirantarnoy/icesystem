<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sale_pos_close_issue_car_qty}}`.
 */
class m230709_081556_create_sale_pos_close_issue_car_qty_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sale_pos_close_issue_car_qty}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'start_date' => $this->datetime(),
            'ent_date' => $this->datetime(),
            'qty' => $this->float(),
            'user_id' => $this->integer(),
            'trans_date' => $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sale_pos_close_issue_car_qty}}');
    }
}
