<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "route_issue_daily_cal".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $route_id
 * @property int|null $issue_trans_type
 * @property int|null $transfer_branch_id
 * @property int|null $product_id
 * @property float|null $qty
 * @property float|null $total_amount
 */
class RouteIssueDailyCal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_issue_daily_cal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['route_id', 'issue_trans_type', 'transfer_branch_id', 'product_id'], 'integer'],
            [['qty', 'total_amount'], 'number'],
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
            'issue_trans_type' => 'Issue Trans Type',
            'transfer_branch_id' => 'Transfer Branch ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'total_amount' => 'Total Amount',
        ];
    }
}
