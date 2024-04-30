<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route_trans_price_cal}}`.
 */
class m240429_013013_create_route_trans_price_cal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%route_trans_price_cal}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'route_id' => $this->integer(),
            'std_price_type' => $this->integer(),
            'product_id' => $this->integer(),
            'price' => $this->float(),
            'line_total' => $this->float(),
            'qty' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%route_trans_price_cal}}');
    }
}
