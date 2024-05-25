<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%delivery_daily_cal_status}}`.
 */
class m240519_144008_create_delivery_daily_cal_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%delivery_daily_cal_status}}', [
            'id' => $this->primaryKey(),
            'route_id' => $this->integer(),
            'cal_date' => $this->datetime(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%delivery_daily_cal_status}}');
    }
}
