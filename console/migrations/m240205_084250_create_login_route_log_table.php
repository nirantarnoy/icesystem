<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%login_route_log}}`.
 */
class m240205_084250_create_login_route_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%login_route_log}}', [
            'id' => $this->primaryKey(),
            'login_date' => $this->datetime(),
            'route_id' => $this->integer(),
            'car_id' => $this->integer(),
            'emp_1' => $this->integer(),
            'emp_2' => $this->integer(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%login_route_log}}');
    }
}
