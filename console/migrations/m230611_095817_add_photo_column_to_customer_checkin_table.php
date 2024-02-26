<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_checkin}}`.
 */
class m230611_095817_add_photo_column_to_customer_checkin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_checkin}}', 'photo', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_checkin}}', 'photo');
    }
}
