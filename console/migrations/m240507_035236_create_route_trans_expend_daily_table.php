<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%route_trans_expend_daily}}`.
 */
class m240507_035236_create_route_trans_expend_daily_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%route_trans_expend_daily}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'route_id' => $this->integer(),
            'oil_amount' => $this->float(),
            'wator_amount' => $this->float(),
            'extra_amount' => $this->float(),
            'money_amount' => $this->float(),
            'deduct_amount' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%route_trans_expend_daily}}');
    }
}
