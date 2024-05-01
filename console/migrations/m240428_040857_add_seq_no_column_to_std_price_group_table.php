<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%std_price_group}}`.
 */
class m240428_040857_add_seq_no_column_to_std_price_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%std_price_group}}', 'seq_no', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%std_price_group}}', 'seq_no');
    }
}
