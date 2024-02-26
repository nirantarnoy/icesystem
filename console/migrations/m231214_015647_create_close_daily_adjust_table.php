<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%close_daily_adjust}}`.
 */
class m231214_015647_create_close_daily_adjust_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%close_daily_adjust}}', [
            'id' => $this->primaryKey(),
            'trans_date' => $this->datetime(),
            'shift' => $this->integer(),
            'shift_date' => $this->datetime(),
            'emp_id' => $this->integer(),
            'product_id' => $this->integer(),
            'prodrec_qty' => $this->float(),
            'return_qty' => $this->float(),
            'transfer_qty' => $this->float(),
            'scrap_qty' => $this->float(),
            'counting_qty' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%close_daily_adjust}}');
    }
}
