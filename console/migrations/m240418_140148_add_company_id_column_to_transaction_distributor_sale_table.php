<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction_distributor_sale}}`.
 */
class m240418_140148_add_company_id_column_to_transaction_distributor_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transaction_distributor_sale}}', 'company_id', $this->integer());
        $this->addColumn('{{%transaction_distributor_sale}}', 'branch_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transaction_distributor_sale}}', 'company_id');
        $this->dropColumn('{{%transaction_distributor_sale}}', 'branch_id');
    }
}
