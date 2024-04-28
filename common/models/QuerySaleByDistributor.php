<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "query_sale_by_distributor".
 *
 * @property int $id
 * @property string|null $order_no
 * @property int|null $customer_id
 * @property string|null $order_date
 * @property int|null $order_channel_id
 * @property int|null $status
 * @property int|null $company_id
 * @property int|null $branch_id
 * @property int|null $payment_method_id
 * @property int|null $payment_term_id
 * @property int|null $sale_channel_id
 * @property int|null $payment_status
 * @property int|null $sale_from_mobile
 * @property int|null $product_id
 * @property float|null $qty
 * @property float|null $price
 * @property float|null $line_total
 * @property int|null $order_line_status
 * @property string|null $code
 * @property string|null $name
 * @property int|null $is_distributor
 */
class QuerySaleByDistributor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'query_sale_by_distributor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'order_channel_id', 'status', 'company_id', 'branch_id', 'payment_method_id', 'payment_term_id', 'sale_channel_id', 'payment_status', 'sale_from_mobile', 'product_id', 'order_line_status', 'is_distributor'], 'integer'],
            [['order_date'], 'safe'],
            [['qty', 'price', 'line_total'], 'number'],
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
            'order_no' => 'Order No',
            'customer_id' => 'Customer ID',
            'order_date' => 'Order Date',
            'order_channel_id' => 'Order Channel ID',
            'status' => 'Status',
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
            'payment_method_id' => 'Payment Method ID',
            'payment_term_id' => 'Payment Term ID',
            'sale_channel_id' => 'Sale Channel ID',
            'payment_status' => 'Payment Status',
            'sale_from_mobile' => 'Sale From Mobile',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'price' => 'Price',
            'line_total' => 'Line Total',
            'order_line_status' => 'Order Line Status',
            'code' => 'Code',
            'name' => 'Name',
            'is_distributor' => 'Is Distributor',
        ];
    }
}
