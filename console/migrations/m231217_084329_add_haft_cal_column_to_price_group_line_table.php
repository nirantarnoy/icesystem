<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%price_group_line}}`.
 */
class m231217_084329_add_haft_cal_column_to_price_group_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%price_group_line}}', 'haft_cal', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%price_group_line}}', 'haft_cal');
    }
}
