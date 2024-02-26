<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction_manager_daily".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $product_id
 * @property float|null $price
 * @property float|null $cash_qty
 * @property float|null $credit_pos_qty
 * @property float|null $car_qty
 * @property float|null $other_branch_qty
 * @property float|null $qty_total
 * @property float|null $cash_amount
 * @property float|null $credit_pos_amount
 * @property float|null $car_amount
 * @property float|null $other_branch_amount
 * @property float|null $amount_total
 */
class TransactionManagerDaily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_manager_daily';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['product_id'], 'integer'],
            [['price', 'cash_qty', 'credit_pos_qty', 'car_qty', 'other_branch_qty', 'qty_total', 'cash_amount', 'credit_pos_amount', 'car_amount', 'other_branch_amount', 'amount_total'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trans_date' => 'Trans Date',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'cash_qty' => 'Cash Qty',
            'credit_pos_qty' => 'Credit Pos Qty',
            'car_qty' => 'Car Qty',
            'other_branch_qty' => 'Other Branch Qty',
            'qty_total' => 'Qty Total',
            'cash_amount' => 'Cash Amount',
            'credit_pos_amount' => 'Credit Pos Amount',
            'car_amount' => 'Car Amount',
            'other_branch_amount' => 'Other Branch Amount',
            'amount_total' => 'Amount Total',
        ];
    }
}
