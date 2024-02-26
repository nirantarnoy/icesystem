<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%close_daily_adjust}}`.
 */
class m231222_044838_add_reprocess_qty_column_to_close_daily_adjust_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%close_daily_adjust}}', 'reprocess_qty', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%close_daily_adjust}}', 'reprocess_qty');
    }
}
