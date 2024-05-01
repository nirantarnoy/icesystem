<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "route_trans_price_cal".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $route_id
 * @property int|null $std_price_type
 * @property int|null $product_id
 * @property float|null $price
 * @property float|null $line_total
 * @property float|null $qty
 */
class RouteTransPriceCal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_trans_price_cal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['route_id', 'std_price_type', 'product_id'], 'integer'],
            [['price', 'line_total', 'qty'], 'number'],
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
            'std_price_type' => 'Std Price Type',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'line_total' => 'Line Total',
            'qty' => 'Qty',
        ];
    }
}
