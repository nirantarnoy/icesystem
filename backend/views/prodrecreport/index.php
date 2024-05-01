<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocktransSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'รายงานยอดรับเข้าผลิต';
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
        'panel' => ['type' => 'info', 'heading' => 'รายงานยอดรับเข้าผลิต'],
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
                'value' => function ($data) {
                    return \backend\models\Product::findName($data->product_id);
                },
                'group' => true,
                'groupHeader' => function ($model, $key, $index, $widget) { // Closure method
                    return [
                        'mergeColumns' => [[0, 1]], // columns to merge in summary
                        'content' => [             // content to show in each summary cell
                            1 => backend\models\Product::findName($model->product_id),
                            6 => GridView::F_SUM,
                            // 8 => GridView::F_SUM,
//                        7 => GridView::F_SUM,
                        ],
                        'contentFormats' => [      // content reformatting for each summary cell
                            //4 => ['format' => 'number', 'decimals' => 0],
                            6 => ['format' => 'number', 'decimals' => 2],
                            //8 => ['format' => 'number', 'decimals' => 0],
//                        7 => ['format' => 'number', 'decimals' => 0],
                        ],
                        'contentOptions' => [      // content html attributes for each summary cell
                            1 => ['style' => 'font-variant:small-caps'],
                            //4 => ['style' => 'text-align:right'],
                            6 => ['style' => 'text-align:right'],
                            // 8 => ['style' => 'text-align:right'],
//                        7 => ['style' => 'text-align:right'],
                        ],
                        // html attributes for group summary row
                        'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                    ];
                },
            ],

            [
                'attribute' => 'trans_date',
                'value' => function ($data) {
                    return date('d/m/Y H:i:s', strtotime($data->trans_date));
                },
                // 'group' => true,
                //'subGroupOf' => 0
            ],
            'journal_no',
            [
                'attribute' => 'production_loc_id',
                'value' => function ($data) {
                    return \backend\models\Machine::findLocname($data->production_loc_id);
                }
            ],
            [
                'attribute' => 'transfer_branch_id',
                'value' => function ($data) {
                    return \backend\models\Transferbrach::findName($data->transfer_branch_id);
                }
            ],
            [
                'attribute' => 'status',
                'label' => 'สถานะ',
                'format' => 'raw',
                'headerOptions' => ['style' => 'text-align: center'],
                'contentOptions' => ['style' => 'text-align: center'],
                'value' => function ($data) {
                    if ($data->status == 3 || $data->status == 500) {
                        return '<div class="badge badge-warning">Cencel</div>';
                    } else {
                        return '';
                    }
                }
            ],

            [
                'attribute' => 'qty',
                'headerOptions' => ['style' => 'text-align: right'],
                'contentOptions' => ['style' => 'text-align: right'],
                'value' => function ($data) {
                    return $data->qty;
                },
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<!--<div class="row">-->
<!--    <div class="col-lg-12" style="text-align: right;">-->
<!--        FM-WAT-02 แก้ไขครั้งที่: 01 <br />-->
<!--        ประกาศใช้วันที่: 01/01/2565-->
<!--    </div>-->
<!--</div>-->
<div class="row">
    <div class="col-lg-12" style="text-align: right;">
        FM-WAT-02 แก้ไขครั้งที่: 00 <br />
        ประกาศใช้วันที่: 01/01/2566
    </div>
</div>
