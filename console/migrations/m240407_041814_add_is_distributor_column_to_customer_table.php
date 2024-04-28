<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer}}`.
 */
class m240407_041814_add_is_distributor_column_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer}}', 'is_distributor', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'is_distributor');
    }
}
