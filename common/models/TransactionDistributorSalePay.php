<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction_distributor_sale_pay".
 *
 * @property int $id
 * @property int|null $route_id
 * @property string|null $trans_date
 * @property float|null $cash_amount
 * @property float|null $transfer_amount
 * @property int|null $company_id
 * @property int|null $branch_id
 */
class TransactionDistributorSalePay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_distributor_sale_pay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'company_id', 'branch_id'], 'integer'],
            [['trans_date'], 'safe'],
            [['cash_amount', 'transfer_amount'], 'number'],
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
            'trans_date' => 'Trans Date',
            'cash_amount' => 'Cash Amount',
            'transfer_amount' => 'Transfer Amount',
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
        ];
    }
}
