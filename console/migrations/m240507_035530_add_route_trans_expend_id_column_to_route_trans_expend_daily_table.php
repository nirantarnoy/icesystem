<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%route_trans_expend_daily}}`.
 */
class m240507_035530_add_route_trans_expend_id_column_to_route_trans_expend_daily_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%route_trans_expend_daily}}', 'route_trans_expend_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%route_trans_expend_daily}}', 'route_trans_expend_id');
    }
}
