<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_line}}`.
 */
class m230804_050136_add_tax_status_column_to_order_line_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_line}}', 'tax_status', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_line}}', 'tax_status');
    }
}
