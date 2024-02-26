<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%login_route_log}}`.
 */
class m240205_154134_add_logout_date_column_to_login_route_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%login_route_log}}', 'logout_date', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%login_route_log}}', 'logout_date');
    }
}
