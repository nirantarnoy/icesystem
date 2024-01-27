<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "com_daily_cal".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $emp_1
 * @property int|null $emp_2
 * @property float|null $total_qty
 * @property float|null $total_amt
 * @property float|null $line_com_amt
 * @property float|null $line_com_special_amt
 * @property float|null $line_total_amt
 * @property int|null $created_at
 * @property int|null $route_id
 */
class ComDailyCal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'com_daily_cal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['emp_1', 'emp_2', 'created_at', 'route_id'], 'integer'],
            [['total_qty', 'total_amt', 'line_com_amt', 'line_com_special_amt', 'line_total_amt','line_com_amt_2'], 'number'],
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
            'emp_1' => 'Emp  1',
            'emp_2' => 'Emp  2',
            'total_qty' => 'Total Qty',
            'total_amt' => 'Total Amt',
            'line_com_amt' => 'Line Com Amt',
            'line_com_amt_2' => 'Line Com Amt 2',
            'line_com_special_amt' => 'Line Com Special Amt',
            'line_total_amt' => 'Line Total Amt',
            'created_at' => 'Created At',
            'route_id' => 'Route ID',
        ];
    }
}
