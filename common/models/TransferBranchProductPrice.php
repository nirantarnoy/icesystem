<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transfer_branch_product_price".
 *
 * @property int $id
 * @property int|null $transfer_branch_id
 * @property int|null $product_id
 * @property float|null $price
 */
class TransferBranchProductPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer_branch_product_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transfer_branch_id', 'product_id'], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transfer_branch_id' => 'Transfer Branch ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
        ];
    }
}
