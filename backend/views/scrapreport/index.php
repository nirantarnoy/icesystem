<?php

use backend\models\Orders;use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocktransSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'รายงานสินค้าเสีย';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocktrans-index">
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel, 'f_date' => null, 't_date' => null]); ?>
    <br/>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showPageSummary' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}',
        ],
        'panel' => ['type' => 'info', 'heading' => 'รายงานสินค้าเสีย'],
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn',
//                'headerOptions' => ['style' => 'text-align: center'],
//                'contentOptions' => ['style' => 'text-align: center'],],
            //  'id',
//            'company_id',
//            'branch_id',
            [
                'attribute' => 'product_id',
                'label' => 'รหัสสินค้า',
                'width' => '310px',
                'value' => function ($model, $key, $index, $widget) {
                        return \backend\models\Product::findName($model->product_id);
                },

                'group' => true,  // enable grouping
                'groupHeader' => function ($model, $key, $index, $widget) { // Closure method

                    return [
                        'mergeColumns' => [[0, 4]], // columns to merge in summary
                        'content' => [             // content to show in each summary cell
                            1 => 'ยอดรวม (' . \backend\models\Product::findName($model->product_id) . ')',
                            5 => GridView::F_SUM,

                        ],
                        'contentFormats' => [      // content reformatting for each summary cell
                            5 => ['format' => 'number', 'decimals' => 2],

                        ],
                        'contentOptions' => [      // content html attributes for each summary cell
                            1 => ['style' => 'font-variant:small-caps'],
                            5 => ['style' => 'text-align:right'],

                        ],
                        // html attributes for group summary row
                        'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                    ];
                }
            ],
            [
                'attribute' => 'trans_date',
                'label' => 'วันที่',
                'value' => function ($model, $key, $index, $widget) {
                    return date('d/m/Y H:i', strtotime($model->trans_date));
                },
            ],

            [
                'attribute' => 'prod_rec_no',
                'label' => 'เลขที่รับเข้า',
                'width' => '250px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->prod_rec_no;
                },
            ],
            [
                'attribute' => 'prod_rec_no',
                'label' => 'เลขที่ใบขาย',
                'width' => '200px',
                'value' => function ($model, $key, $index, $widget) {
                    return \backend\models\Orders::getNumber($model->order_id);
                },
            ],
            [
                'attribute' => '',
                'label' => 'ลูกค้า',
                'width' => '200px',
                'value' => function ($model, $key, $index, $widget) {
                    return getCustomerorder($model->order_id);
                },
            ],

            [
                'attribute' => 'username',
                'label' => 'พนักงาน',
                // 'group' => true,
                //'subGroupOf' => 0
            ],
            [
                'attribute' => 'qty',
                'label' => 'จำนวน',
                'headerOptions' => ['style' => 'text-align: right'],
                'contentOptions' => ['style' => 'text-align: right'],
                'value' => function ($data, $key, $index) {
                    return $data->qty == null ? 0 : $data->qty;
                },
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <div class="row">
        <div class="col-lg-12">
            <?php

            $data = [];
            foreach ($model_bottom as $value) {
                if (in_array($value->product_id, $data)) {
                    continue;
                } else {
                    array_push($data, $value->product_id);
                }

//                if(array_search($value->product_id, array_keys($data))){
//                  array_push($data,['product_id'=>$value->product_id,'qty'=>$value->qty]);
//                }
            }
            //  print_r($data);

            ?>
        </div>
    </div>
    <br/>
    <h4>
        สรุปจำนวน</h4>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered"
                   width="100%">
                <thead>
                <tr>
                    <?php for ($i = 0; $i <= count($data) - 1; $i++): ?>
                        <th style="width: 10%; text-align: center"><?= \backend\models\Product::findCode($data[$i]) ?></th>
                    <?php endfor; ?>
                    <th style="width: 10%; text-align: center">
                        รวมทั้งหมด
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php $total = 0; ?>
                    <?php for ($i = 0; $i <= count($data) - 1; $i++): ?>
                        <?php $total += getQty($model_bottom, $data[$i]); ?>
                        <td style="width: 10%; text-align: center"><?= number_format(getQty($model_bottom, $data[$i]),2) ?></td>
                    <?php endfor; ?>
                    <td style="width: 10%; text-align: center"><?= number_format($total,2) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function getQty($model, $product_id)
{
    $qty = 0;
    if ($model) {
        foreach ($model as $val) {
            if ($val->product_id == $product_id) {
                $qty += $val->qty;
            }
        }
    }
    return $qty;
}
function getCustomerorder($id)
    {
        $cus_name = '';
        $model = Orders::find()->where(['id' => $id])->one();
        if($model){
            $cus_name = \backend\models\Customer::findName($model->customer_id);

        }else{
            $model = \common\models\OrderLine::find()->where(['order_id' => $id])->one();
            if($model){
                $cus_name = \backend\models\Customer::findName($model->customer_id);

            }
        }
        return $cus_name;
    }

?>
