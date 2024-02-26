<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sale_pos_close_issue_car_qty".
 *
 * @property int $id
 * @property int|null $product_id
 * @property string|null $start_date
 * @property string|null $ent_date
 * @property float|null $qty
 * @property int|null $user_id
 * @property string|null $trans_date
 */
class SalePosCloseIssueCarQty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sale_pos_close_issue_car_qty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'user_id'], 'integer'],
            [['start_date', 'ent_date', 'trans_date'], 'safe'],
            [['qty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'start_date' => 'Start Date',
            'ent_date' => 'Ent Date',
            'qty' => 'Qty',
            'user_id' => 'User ID',
            'trans_date' => 'Trans Date',
        ];
    }
}
