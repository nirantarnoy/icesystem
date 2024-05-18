<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%route_trans_expend_daily}}`.
 */
class m240516_043619_add_plus_amount_column_to_route_trans_expend_daily_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%route_trans_expend_daily}}', 'plus_amount', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%route_trans_expend_daily}}', 'plus_amount');
    }
}
