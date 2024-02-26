<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_checkin".
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string|null $checkin_date
 * @property string|null $latlong
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $company_id
 * @property int|null $branch_id
 */
class CustomerCheckin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_checkin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'company_id', 'branch_id','route_id'], 'integer'],
            [['checkin_date'], 'safe'],
            [['latlong'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'checkin_date' => 'Checkin Date',
            'latlong' => 'Latlong',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
        ];
    }
}
