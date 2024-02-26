<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_tax_invoice".
 *
 * @property int $id
 * @property string|null $invoice_no
 * @property int|null $customer_id
 * @property string|null $invoice_date
 * @property int|null $payment_term_id
 * @property string|null $payment_date
 * @property string|null $remark
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property float|null $total_amount
 * @property float|null $vat_amount
 * @property float|null $net_amount
 * @property string|null $total_text
 */
class CustomerTaxInvoice extends \yii\db\ActiveRecord
{
    public $find_product_id;
    public $find_product_group_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_tax_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'payment_term_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['invoice_date', 'payment_date','find_product_id','find_product_group_id'], 'safe'],
            [['total_amount', 'vat_amount', 'net_amount'], 'number'],
            [['invoice_no', 'remark', 'total_text'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_no' => 'เลขที่ใบกำกับ',
            'customer_id' => 'ลูกค้า',
            'invoice_date' => 'วันที่ใบกำกับ',
            'payment_term_id' => 'เงื่อนไขชำระเงิน',
            'payment_date' => 'วันที่ชำระเงิน',
            'remark' => 'หมายเหตุ',
            'status' => 'สถานะ',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'total_amount' => 'ยอดเงิน',
            'find_product_id'=> 'สินค้า',
            'vat_amount' => 'ภาษี',
            'net_amount' => 'ยอดเงินสุทธิ',
            'total_text' => 'ยอดเงินตัวหนังสือ',
            'find_product_group_id'=>'ประเภทสินค้า',
        ];
    }
}
