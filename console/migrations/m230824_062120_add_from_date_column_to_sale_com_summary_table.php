<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sale_com_summary}}`.
 */
class m230824_062120_add_from_date_column_to_sale_com_summary_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sale_com_summary}}', 'from_date', $this->datetime());
        $this->addColumn('{{%sale_com_summary}}', 'to_date', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sale_com_summary}}', 'from_date');
        $this->dropColumn('{{%sale_com_summary}}', 'to_date');
    }
}
