<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "close_daily_adjust".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $shift
 * @property string|null $shift_date
 * @property int|null $emp_id
 * @property float|null $prodrec_qty
 * @property float|null $return_qty
 * @property float|null $transfer_qty
 * @property float|null $scrap_qty
 * @property float|null $counting_qty
 */
class CloseDailyAdjust extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'close_daily_adjust';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date', 'shift_date'], 'safe'],
            [['shift', 'emp_id'], 'integer'],
            [['prodrec_qty', 'return_qty', 'transfer_qty', 'scrap_qty', 'counting_qty','refill_qty','reprocess_qty','balance_in_qty','sale_qty'], 'number'],
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
            'shift' => 'Shift',
            'shift_date' => 'Shift Date',
            'emp_id' => 'Emp ID',
            'prodrec_qty' => 'Prodrec Qty',
            'return_qty' => 'Return Qty',
            'transfer_qty' => 'Transfer Qty',
            'reprocess_qty' => 'Reprocess Qty',
            'scrap_qty' => 'Scrap Qty',
            'counting_qty' => 'Counting Qty',
            'refill_qty'=>'Refill Qty',
        ];
    }
}
