<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "route_trans_expend_daily".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $route_id
 * @property float|null $oil_amount
 * @property float|null $wator_amount
 * @property float|null $extra_amount
 * @property float|null $money_amount
 * @property float|null $deduct_amount
 * @property int|null $route_trans_expend_id
 */
class RouteTransExpendDaily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_trans_expend_daily';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['route_id', 'route_trans_expend_id'], 'integer'],
            [['oil_amount', 'wator_amount', 'extra_amount', 'money_amount', 'deduct_amount'], 'number'],
            [['cash_transfer_amount','payment_transfer_amount','plus_amount'], 'number'],
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
            'route_id' => 'Route ID',
            'oil_amount' => 'Oil Amount',
            'wator_amount' => 'Wator Amount',
            'extra_amount' => 'Extra Amount',
            'money_amount' => 'Money Amount',
            'deduct_amount' => 'Deduct Amount',
            'route_trans_expend_id' => 'Route Trans Expend ID',
        ];
    }
}
