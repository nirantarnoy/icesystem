<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sale_com}}`.
 */
class m240204_034443_add_product_id_column_to_sale_com_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sale_com}}', 'product_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sale_com}}', 'product_id');
    }
}
