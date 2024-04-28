<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "std_price_group".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $product_id
 * @property float|null $price
 * @property int|null $type_id
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class StdPriceGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'std_price_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'type_id', 'created_at', 'updated_at','seq_no'], 'integer'],
            [['price'], 'number'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อราคา',
            'description' => 'รายละเอียด',
            'product_id' => 'สินค้า',
            'price' => 'ราคาขาย',
            'seq_no' => 'ลำดับแสดง',
            'type_id' => 'ประเภทการขาย',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
