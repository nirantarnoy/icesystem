<?php

namespace common\models;

use Yii;

class SaleCom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sale_com';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'],'required'],
            [['code'],'unique'],
            [['emp_qty', 'status', 'created_at', 'updated_at', 'created_by','company_id','branch_id'], 'integer'],
            [['com_extra','first_emp','second_emp'], 'number'],
            [['code', 'name'], 'string', 'max' => 255],
            [['from_date','to_date','product_id'],'safe'],
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
            'emp_qty' => 'จำนวนพนักงาน',
            'com_extra' => 'ค่าพิเศษ',
            'status' => 'สถานะ',
            'first_emp' => 'พนักงาน 1',
            'second_emp' => 'พนักงาน 2',
            'from_date' => 'ตั้งแต่วันที่',
            'to_date' => 'ถึงวันที่',
            'product_id'=> 'สินค้า',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
        ];
    }
}
