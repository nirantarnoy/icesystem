<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $description
 * @property int|null $customer_group_id
 * @property string|null $location_info
 * @property int|null $delivery_route_id
 * @property string|null $active_date
 * @property string|null $logo
 * @property string|null $shop_photo
 * @property int|null $status
 * @property int|null $company_id
 * @property int|null $branch_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Branch $branch
 * @property Company $company
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code','name'],'unique'],
            [['customer_group_id', 'delivery_route_id', 'status', 'company_id', 'branch_id', 'created_at', 'updated_at', 'created_by', 'updated_by','is_show_pos'], 'integer'],
            [['active_date'], 'safe'],
            [['code', 'name', 'description', 'location_info', 'logo', 'shop_photo','address','address2','phone','branch_no','sort_name'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['customer_type_id','payment_method_id','payment_term_id','is_invoice_req','is_distributor'],'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'รหัสลูกค้า'),
            'name' => Yii::t('app', 'ชื่อ'),
            'description' => Yii::t('app', 'รายละเอียด'),
            'customer_group_id' => Yii::t('app', 'กลุ่มลูกค้า'),
            'customer_type_id' => Yii::t('app', 'ประเภทลูกค้า'),
            'location_info' => Yii::t('app', 'พิกัดแผนที่'),
            'delivery_route_id' => Yii::t('app', 'สายส่ง'),
            'active_date' => Yii::t('app', 'วันที่เริ่มใช้งาน'),
            'logo' => Yii::t('app', 'Logo'),
            'sort_name' => 'รหัสสาย',
            'shop_photo' => Yii::t('app', 'Shop Photo'),
            'status' => Yii::t('app', 'สถานะ'),
            'payment_method_id' => Yii::t('app', 'วิธีชำระเงิน'),
            'payment_term_id' => Yii::t('app', 'เงื่อนไขชำระเงิน'),
            'address' => Yii::t('app', 'ที่อยู่วางบิล'),
            'address2' => Yii::t('app', 'ที่อยู่ส่งของ'),
            'phone' => Yii::t('app', 'โทร'),
            'is_invoice_req' => Yii::t('app', 'เอาใบกำกับ'),
            'is_distributor' => Yii::t('app', 'ยี่ปั๊วะ'),
            'branch_no' => Yii::t('app', 'รหัสสาขา'),
            'company_id' => Yii::t('app', 'Company ID'),
            'branch_id' => Yii::t('app', 'Branch ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
}
