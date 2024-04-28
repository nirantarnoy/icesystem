<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "query_sale_distributor_data".
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string|null $order_no
 * @property string|null $order_date
 * @property int|null $sale_channel_id
 * @property int|null $product_id
 * @property float|null $qty
 * @property float|null $price
 * @property float|null $line_total
 * @property float|null $line_qty_cash
 * @property float|null $line_qty_credit
 * @property float|null $line_qty_free
 * @property float|null $line_total_cash
 * @property float|null $line_total_credit
 * @property string|null $code
 * @property string|null $name
 * @property int|null $item_pos_seq
 * @property int|null $created_by
 * @property int|null $company_id
 * @property int|null $branch_id
 * @property int|null $payment_method_id
 * @property float|null $nw
 */
class QuerySaleDistributorData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'query_sale_distributor_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'sale_channel_id', 'product_id', 'item_pos_seq', 'created_by', 'company_id', 'branch_id', 'payment_method_id'], 'integer'],
            [['order_date'], 'safe'],
            [['qty', 'price', 'line_total', 'line_qty_cash', 'line_qty_credit', 'line_qty_free', 'line_total_cash', 'line_total_credit', 'nw'], 'number'],
            [['order_no', 'code', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'order_no' => 'Order No',
            'order_date' => 'Order Date',
            'sale_channel_id' => 'Sale Channel ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'price' => 'Price',
            'line_total' => 'Line Total',
            'line_qty_cash' => 'Line Qty Cash',
            'line_qty_credit' => 'Line Qty Credit',
            'line_qty_free' => 'Line Qty Free',
            'line_total_cash' => 'Line Total Cash',
            'line_total_credit' => 'Line Total Credit',
            'code' => 'Code',
            'name' => 'Name',
            'item_pos_seq' => 'Item Pos Seq',
            'created_by' => 'Created By',
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
            'payment_method_id' => 'Payment Method ID',
            'nw' => 'Nw',
        ];
    }
}
