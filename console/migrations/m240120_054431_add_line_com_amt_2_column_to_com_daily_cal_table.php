<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%com_daily_cal}}`.
 */
class m240120_054431_add_line_com_amt_2_column_to_com_daily_cal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%com_daily_cal}}', 'line_com_amt_2', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%com_daily_cal}}', 'line_com_amt_2');
    }
}
