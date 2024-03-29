<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_reserv_line".
 *
 * @property int $id
 * @property int|null $reserv_id
 * @property int|null $product_id
 * @property float|null $qty
 * @property int|null $status
 */
class OrderReservLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_reserv_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reserv_id', 'product_id', 'status'], 'integer'],
            [['qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reserv_id' => 'Reserv ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'status' => 'Status',
        ];
    }
}
