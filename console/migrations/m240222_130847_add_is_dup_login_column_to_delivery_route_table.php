<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%delivery_route}}`.
 */
class m240222_130847_add_is_dup_login_column_to_delivery_route_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%delivery_route}}', 'is_dup_login', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%delivery_route}}', 'is_dup_login');
    }
}
