<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_checkin}}`.
 */
class m230611_100141_add_route_id_column_to_customer_checkin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_checkin}}', 'route_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_checkin}}', 'route_id');
    }
}
