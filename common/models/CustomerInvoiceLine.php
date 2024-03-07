<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_invoice_line".
 *
 * @property int $id
 * @property int|null $customer_invoice_id
 * @property int|null $order_id
 * @property float|null $amount
 * @property int|null $status
 * @property float|null $remain_amount
 */
class CustomerInvoiceLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_invoice_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_invoice_id', 'order_id', 'status','emp_id','created_at','created_by','updated_at','updated_by','unit_id'], 'integer'],
            [['amount', 'remain_amount','recieve_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_invoice_id' => 'Customer Invoice ID',
            'order_id' => 'Order ID',
            'amount' => 'Amount',
            'status' => 'Status',
            'unit_id'=>'Unit',
            'remain_amount' => 'Remain Amount',
        ];
    }
}
