<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "login_route_log".
 *
 * @property int $id
 * @property string|null $login_date
 * @property int|null $route_id
 * @property int|null $car_id
 * @property int|null $emp_1
 * @property int|null $emp_2
 * @property int|null $status
 */
class LoginRouteLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'login_route_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login_date','logout_date'], 'safe'],
            [['route_id', 'car_id', 'emp_1', 'emp_2', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login_date' => 'เข้าระบบ',
            'logout_date' => 'ออกระบบ',
            'route_id' => 'สายส่ง',
            'car_id' => 'รถ',
            'emp_1' => 'คนขับ',
            'emp_2' => 'ผู้ติดตาม',
            'status' => 'สถานะ',
        ];
    }
}
