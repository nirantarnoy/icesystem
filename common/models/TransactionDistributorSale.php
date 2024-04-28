<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction_distributor_sale".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $customer_id_id
 * @property int|null $product_id
 * @property float|null $credit_qty
 * @property float|null $cash_qty
 * @property float|null $credit_amount
 * @property float|null $cash_amount
 * @property float|null $cash_receive
 */
class TransactionDistributorSale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_distributor_sale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['customer_id', 'product_id','company_id','branch_id'], 'integer'],
            [['credit_qty', 'cash_qty', 'credit_amount', 'cash_amount', 'cash_receive','free_qty'], 'number'],
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
            'customer_id' => 'Customer Id',
            'product_id' => 'Product ID',
            'credit_qty' => 'Credit Qty',
            'cash_qty' => 'Cash Qty',
            'credit_amount' => 'Credit Amount',
            'cash_amount' => 'Cash Amount',
            'cash_receive' => 'Cash Receive',
        ];
    }
}
