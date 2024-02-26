<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_wait_payment".
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $created_at
 * @property int|null $created_by
 */
class OrderWaitPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_wait_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'created_at', 'created_by'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
