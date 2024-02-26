<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%close_daily_adjust}}`.
 */
class m231214_052657_add_shift_seq_column_to_close_daily_adjust_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%close_daily_adjust}}', 'shift_seq', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%close_daily_adjust}}', 'shift_seq');
    }
}
