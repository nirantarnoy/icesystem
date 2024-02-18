<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%delivery_route}}`.
 */
class m240207_151422_add_is_other_branch_column_to_delivery_route_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%delivery_route}}', 'is_other_branch', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%delivery_route}}', 'is_other_branch');
    }
}
