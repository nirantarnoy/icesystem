<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%price_group_line}}`.
 */
class m231226_032407_add_sale_haft_price_column_to_price_group_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%price_group_line}}', 'sale_haft_price', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%price_group_line}}', 'sale_haft_price');
    }
}
