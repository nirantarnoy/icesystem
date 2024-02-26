<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_tax_invoice_line".
 *
 * @property int $id
 * @property int|null $tax_invoice_id
 * @property int|null $product_group_id
 * @property float|null $qty
 * @property float|null $price
 * @property float|null $line_total
 * @property float|null $discount_amount
 * @property int|null $status
 * @property string|null $remark
 * @property int|null $order_ref_id
 */
class CustomerTaxInvoiceLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_tax_invoice_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tax_invoice_id', 'product_group_id', 'status', 'order_ref_id'], 'integer'],
            [['qty', 'price', 'line_total', 'discount_amount'], 'number'],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tax_invoice_id' => 'Tax Invoice ID',
            'product_group_id' => 'Product Group ID',
            'qty' => 'Qty',
            'price' => 'Price',
            'line_total' => 'Line Total',
            'discount_amount' => 'Discount Amount',
            'status' => 'Status',
            'remark' => 'Remark',
            'order_ref_id' => 'Order Ref ID',
        ];
    }
}
