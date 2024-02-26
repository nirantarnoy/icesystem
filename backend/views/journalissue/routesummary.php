<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'สรุปรายการเบิกแยกสายส่ง';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-index">
    <?php Pjax::begin(); ?>

    <?php echo $this->render('route_summary_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'showPageSummary' => true,
        'toolbar' => [
            '{toggleData}',
            '{export}',
        ],
        'panel' => ['type' => 'info', 'heading' => ''],
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'emptyCell' => '-',
        'layout' => "{items}\n{summary}\n<div class='text-center'>{pager}</div>",
        'summary' => "แสดง {begin} - {end} ของทั้งหมด {totalCount} รายการ",
        'showOnEmpty' => false,
        //    'bordered' => true,
        //     'striped' => false,
        //    'hover' => true,
        'id' => 'product-grid',
        //'tableOptions' => ['class' => 'table table-hover'],
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'emptyText' => '<div style="color: red;text-align: center;"> <b>ไม่พบรายการไดๆ</b></div>',
        'columns' => [
            [
                'attribute' => 'delivery_route_id',
                'width' => '310px',
                'value' => function ($model, $key, $index, $widget) {
                    return \backend\models\Deliveryroute::findName($model->delivery_route_id);
                },

                'group' => true,  // enable grouping
                'groupHeader' => function ($model, $key, $index, $widget) { // Closure method
                    return [
                        'mergeColumns' => [[0, 2]], // columns to merge in summary
                        'content' => [             // content to show in each summary cell
                            1 => 'ยอดรวม (' . \backend\models\Deliveryroute::findName($model->delivery_route_id) . ')',
                            4 => GridView::F_SUM,

                        ],
                        'contentFormats' => [      // content reformatting for each summary cell
                            4 => ['format' => 'number', 'decimals' => 2],

                        ],
                        'contentOptions' => [      // content html attributes for each summary cell
                            1 => ['style' => 'font-variant:small-caps'],
                            4 => ['style' => 'text-align:right'],

                        ],
                        // html attributes for group summary row
                        'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                    ];
                }
            ],
            [
                'attribute' => 'trans_date',
                'width' => '250px',
                'value' => function ($model, $key, $index, $widget) {
                    return date('d-m-Y', strtotime($model->trans_date));
                },
                'group' => true,  // enable grouping
                'subGroupOf' => 0, // supplier column index is the parent group,
            ],
            [
                'attribute' => 'product_id',
                'width' => '250px',
                'value' => function ($model, $key, $index, $widget) {
                    return \backend\models\Product::findName($model->product_id);
                },
                'group' => true,  // enable grouping
                'subGroupOf' => 0, // supplier column index is the parent group,
//                'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
//                    return [
//                        'mergeColumns' => [[1, 3]], // columns to merge in summary
//                        'content' => [              // content to show in each summary cell
//                            2 => 'ยอดรวม (' . \backend\models\Product::findName($model->product_id) . ')',
//                            4 => GridView::F_SUM,
//                        ],
//                        'contentFormats' => [      // content reformatting for each summary cell
//                            4 => ['format' => 'number', 'decimals' => 2],
//
//                        ],
//                        'contentOptions' => [      // content html attributes for each summary cell
//                            4 => ['style' => 'text-align:right'],
//
//                        ],
//                        // html attributes for group summary row
//                        'options' => ['class' => 'success table-success', 'style' => 'font-weight:bold;']
//                    ];
//                },
            ],
            [
                'attribute' => 'prod_rec_no',
                'label' => 'เลขที่รับเข้าผลิต'
            ],

            [
                'attribute' => 'qty',
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
        //  'pager' => ['class' => LinkPager::className()],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
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

?>