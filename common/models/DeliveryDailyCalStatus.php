<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "delivery_daily_cal_status".
 *
 * @property int $id
 * @property int|null $route_id
 * @property string|null $cal_date
 * @property int|null $status
 */
class DeliveryDailyCalStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_daily_cal_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'status'], 'integer'],
            [['cal_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'Route ID',
            'cal_date' => 'Cal Date',
            'status' => 'Status',
        ];
    }
}
