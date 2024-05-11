<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%route_trans_expend}}`.
 */
class m240507_071319_add_cash_transfer_amount_column_to_route_trans_expend_daily_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%route_trans_expend_daily}}', 'cash_transfer_amount', $this->float());
        $this->addColumn('{{%route_trans_expend_daily}}', 'payment_transfer_amount', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%route_trans_expend_daily}}', 'cash_transfer_amount');
        $this->dropColumn('{{%route_trans_expend_daily}}', 'payment_transfer_amount');
    }
}
