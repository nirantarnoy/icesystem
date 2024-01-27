<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "price_group_line".
 *
 * @property int $id
 * @property int|null $price_group_id
 * @property int|null $product_id
 * @property float|null $sale_price
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class PriceGroupLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price_group_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_group_id', 'product_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['sale_price','sale_haft_price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'price_group_id' => Yii::t('app', 'Price Group ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'sale_price' => Yii::t('app', 'Sale Price'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }
}
