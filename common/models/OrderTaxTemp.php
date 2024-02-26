<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_tax_temp".
 *
 * @property int $id
 * @property int|null $order_id
 */
class OrderTaxTemp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_tax_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id','tax_invoice_id','product_group_id','product_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'tax_invoice_id'=>'Tax Invoice ID',
            'product_group_id'=>'Product group ID',
            'product_id'=>'Product ID',
        ];
    }
}
