<?php

namespace common\models;

use Yii;

class SaleComSummary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sale_com_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'],'required'],
            [['code'],'unique'],
            [['sale_price', 'com_extra'], 'number'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by','company_id','branch_id'], 'integer'],
            [['code', 'name'], 'string', 'max' => 255],
            [['from_date','to_date'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัส',
            'name' => 'ชื่อ',
            'sale_price' => 'ยอดขายที่ทำได้',
            'com_extra' => 'ค่าพิเศษ',
            'status' => 'สถานะ',
            'from_date'=>'ตั้งแต่วันที่',
            'to_date'=> 'ถึงวันที่',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}
