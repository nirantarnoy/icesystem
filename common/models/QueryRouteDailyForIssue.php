<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "query_route_daily_for_issue".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int $line_id
 * @property int|null $product_id
 * @property float|null $qty
 * @property float|null $avl_qty
 * @property float|null $origin_qty
 * @property int|null $delivery_route_id
 * @property int|null $status
 */
class QueryRouteDailyForIssue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'query_route_daily_for_issue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'line_id', 'product_id', 'delivery_route_id', 'status'], 'integer'],
            [['trans_date'], 'safe'],
            [['qty', 'avl_qty', 'origin_qty'], 'number'],
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
            'line_id' => 'Line ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'avl_qty' => 'Avl Qty',
            'origin_qty' => 'Origin Qty',
            'delivery_route_id' => 'Delivery Route ID',
            'status' => 'Status',
        ];
    }
}
