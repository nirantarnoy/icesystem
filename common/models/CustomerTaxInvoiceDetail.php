<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_tax_invoice_detail".
 *
 * @property int $id
 * @property int|null $customer_tax_invoice_id
 * @property int|null $order_line_id
 * @property int|null $product_id
 * @property float|null $qty
 */
class CustomerTaxInvoiceDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_tax_invoice_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_tax_invoice_id', 'order_line_id', 'product_id'], 'integer'],
            [['qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_tax_invoice_id' => 'Customer Tax Invoice ID',
            'order_line_id' => 'Order Line ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
        ];
    }
}
