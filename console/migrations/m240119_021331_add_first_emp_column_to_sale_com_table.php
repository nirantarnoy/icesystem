<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sale_com}}`.
 */
class m240119_021331_add_first_emp_column_to_sale_com_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sale_com}}', 'first_emp', $this->float());
        $this->addColumn('{{%sale_com}}', 'second_emp', $this->float());
        $this->addColumn('{{%sale_com}}', 'from_date', $this->datetime());
        $this->addColumn('{{%sale_com}}', 'to_date', $this->datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sale_com}}', 'first_emp');
        $this->dropColumn('{{%sale_com}}', 'second_emp');
        $this->dropColumn('{{%sale_com}}', 'from_date');
        $this->dropColumn('{{%sale_com}}', 'to_date');
    }
}
