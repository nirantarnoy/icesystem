<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer_checkin}}`.
 */
class m230611_071703_add_company_id_column_to_customer_checkin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer_checkin}}', 'company_id', $this->integer());
        $this->addColumn('{{%customer_checkin}}', 'branch_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer_checkin}}', 'company_id');
        $this->dropColumn('{{%customer_checkin}}', 'branch_id');
    }
}
