<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%close_daily_adjust}}`.
 */
class m240112_074627_add_balance_in_qty_column_to_close_daily_adjust_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%close_daily_adjust}}', 'balance_in_qty', $this->float());
        $this->addColumn('{{%close_daily_adjust}}', 'sale_qty', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%close_daily_adjust}}', 'balance_in_qty');
        $this->dropColumn('{{%close_daily_adjust}}', 'sale_qty');
    }
}
