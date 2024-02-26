<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "query_order_wait_for_update_payment".
 *
 * @property int $id
 * @property string|null $journal_no
 * @property string|null $trans_date
 * @property int|null $order_id
 * @property string|null $order_no
 * @property int|null $payment_status
 */
class QueryOrderWaitForUpdatePayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'query_order_wait_for_update_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'payment_status','company_id','branch_id'], 'integer'],
            [['trans_date'], 'safe'],
            [['journal_no', 'order_no'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'journal_no' => 'Journal No',
            'trans_date' => 'Trans Date',
            'order_id' => 'Order ID',
            'order_no' => 'Order No',
            'payment_status' => 'Payment Status',
        ];
    }
}
