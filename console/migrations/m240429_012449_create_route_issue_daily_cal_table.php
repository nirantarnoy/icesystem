<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route_issue_daily_cal}}`.
 */
class m240429_012449_create_route_issue_daily_cal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%route_issue_daily_cal}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'route_id' => $this->integer(),
            'issue_trans_type' => $this->integer(),
            'transfer_branch_id' => $this->integer(),
            'product_id' => $this->integer(),
            'qty' => $this->float(),
            'total_amount' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%route_issue_daily_cal}}');
    }
}
