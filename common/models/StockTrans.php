<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "stock_trans".
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $branch_id
 * @property string|null $journal_no
 * @property string|null $trans_date
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property int|null $location_id
 * @property string|null $lot_no
 * @property int|null $qty
 * @property int|null $created_at
 * @property int|null $created_by
 */
class StockTrans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock_trans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'branch_id', 'product_id', 'warehouse_id', 'location_id', 'qty', 'created_at', 'created_by','stock_type','activity_type_id','production_type','transfer_branch_id'], 'integer'],
            [['trans_date'], 'safe'],
            [['journal_no', 'lot_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
            'journal_no' => 'เลขที่',
            'trans_date' => 'วันที่',
            'product_id' => 'รหัสสินค้า',
            'warehouse_id' => 'คลังสินค้า',
            'stock_type' => 'ประเภทสต๊อก',
            'activity_type_id' => 'กิจกรรม',
            'production_type' => 'ประเภทการผลิต',
            'location_id' => 'Location',
            'lot_no' => 'Lot No',
            'qty' => 'จำนวน',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
