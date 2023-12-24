<?php

use kartik\select2;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;


$this->title = "ลูกค้าค้างชำระ";

$company_id = 1;
$branch_id = 1;
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}
?>

<br/>
<?php echo $this->render('_customerloan_search', ['model' => $searchModel, 'company_id' => $company_id, 'branch_id' => $branch_id]); ?>

<div class="row">
    <div class="col-lg-12">
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            //    'filterModel' => $searchModel,
            'showPageSummary' => true,
            'pjax' => true,
            'striped' => true,
            'hover' => true,
            'options' => [
                'style' => 'font-size: 14px;'
            ],
            'toolbar' => [
//        [
//            'content' =>
//               '<input type="text" class="form-control">' . ' '.
//                Html::a('<i class="fas fa-search"></i>', '', [
//                    'class' => 'btn btn-outline-secondary',
//                    'title'=>Yii::t('app', 'Reset Grid'),
//                    'data-pjax' => 0,
//                ]),
//            'options' => ['class' => 'btn-group mr-2']
//        ],
                '{toggleData}',
                '{export}',

            ],
            'panel' => ['type' => 'info', 'heading' => 'แสดงรายการลูกค้าค้างชำระ'],
            'toggleDataContainer' => ['class' => 'btn-group mr-2'],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
//        [
//            'class' => '\kartik\grid\ExpandRowColumn',
//            'value' => function ($model, $key, $index, $column) {
//                return GridView::ROW_COLLAPSED;
//            },
//            //'detailUrl' => Yii::$app->request->getBaseUrl() . '..../expand-view',
//            'detail' => function ($model, $key, $index, $column) {
//                return Yii::$app->controller->renderPartial('_expand-row-details', ['model' => $model]);
//            },
//            'headerOptions' => ['class' => 'kartik-sheet-style'],
//            'expandOneOnly' => true
//        ],
                [
                    'attribute' => 'car_ref_id',
                    'label' => 'รถคันที่',
                    'width' => '10%',
                    'value' => function ($model, $key, $index, $widget) {
                        return $model->car_name;
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(\backend\models\Car::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy('name')->asArray()->all(), 'id', 'name'),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => ['placeholder' => '--เลือกรถ--'],
                    'group' => true,  // enable grouping,
                    'groupHeader' => function ($model, $key, $index, $widget) { // Closure method
                        return [
                            'mergeColumns' => [[1, 4]], // columns to merge in summary
                            'content' => [             // content to show in each summary cell
                                1 => 'ยอดรวมรถ (' . $model->car_name . ')',
                                4 => GridView::F_SUM,
                                5 => GridView::F_SUM,
                                6 => GridView::F_SUM,
                                7 => GridView::F_SUM,
                            ],
                            'contentFormats' => [      // content reformatting for each summary cell
                                //4 => ['format' => 'number', 'decimals' => 0],
                                4 => ['format' => 'number', 'decimals' => 0],
                                5 => ['format' => 'number', 'decimals' => 0],
                                6 => ['format' => 'number', 'decimals' => 0],
                                7 => ['format' => 'number', 'decimals' => 0],
                            ],
                            'contentOptions' => [      // content html attributes for each summary cell
                                1 => ['style' => 'font-variant:small-caps'],
                                //4 => ['style' => 'text-align:right'],
                                4 => ['style' => 'text-align:right'],
                                5 => ['style' => 'text-align:right'],
                                6 => ['style' => 'text-align:right'],
                                7 => ['style' => 'text-align:right'],
                            ],
                            // html attributes for group summary row
                            'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                        ];
                    },
//            'groupedRow' => true,                    // move grouped column to a single grouped row
//            'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
//            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class
                ],
                [
                    'attribute' => 'customer_id',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center;vertical-align: middle'],
                    'label' => 'ลูกค้า',
                    'width' => '10%',
                    'value' => function ($model, $key, $index, $widget) {
                        return $model->cus_name;
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->orderBy('name')->asArray()->all(), 'id', 'name'),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => ['placeholder' => '--เลือกลูกค้า--'],

//            'groupedRow' => true,                    // move grouped column to a single grouped row
//            'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
//            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class
                ],
//                [
//                    'attribute' => 'customer_type_id',
//                    'label' => 'ประเภทลูกค้า',
//                   // 'width' => '10%',
//                    'value' => function ($model, $key, $index, $widget) {
//                        return \backend\models\Customertype::findName($model->customer_type_id);
//                    },
//                    'filterType' => GridView::FILTER_SELECT2,
//                    'filter' => ArrayHelper::map(\backend\models\Customertype::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy('name')->asArray()->all(), 'id', 'name'),
//                    'filterWidgetOptions' => [
//                        'pluginOptions' => ['allowClear' => true],
//                    ],
//                    'filterInputOptions' => ['placeholder' => '--เลือกประเภทลูกค้า--'],
//                    'group' => true,  // enable grouping
//                    'subGroupOf' => 2 // supplier column index is the parent group
//                ],
//        [
//            'class' => '\kartik\grid\ExpandRowColumn',
//            'value' => function ($model, $key, $index, $column) {
//                return GridView::ROW_COLLAPSED;
//            },
//            //'detailUrl' => Yii::$app->request->getBaseUrl() . '..../expand-view',
//            'detail' => function ($model, $key, $index, $column) {
//                return Yii::$app->controller->renderPartial('_expand-row-details', ['model' => $model]);
//            },
//            'headerOptions' => ['class' => 'kartik-sheet-style'],
//            'expandOneOnly' => true
//        ],
                [
                    'attribute' => 'order_date',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center;vertical-align: middle'],
                    'width' => '10%',
                    'value' => function ($model, $key, $index, $widget) {
                        return date('d/m/Y', strtotime($model->order_date));
                    },
                ],
                [
                    'attribute' => 'order_no',
                    'label' => 'เลขที่ขาย',
                    'width' => '10%',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center;vertical-align: middle'],
                    // 'pageSummary' => 'Page Summary',
                ],
                [
                    'attribute' => 'line_total',
                    'label' => 'มูลค่า',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right;vertical-align: middle'],
                    'width' => '150px',
                    'hAlign' => 'right',
                    'format' => ['decimal', 2],
                    'pageSummary' => true,
                    'pageSummaryFunc' => GridView::F_SUM
                ],
                [
                    'attribute' => 'payment_amount',
                    'label' => 'ชำระแล้ว',
                    //'format' => 'html',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right;vertical-align: middle'],
                    'width' => '150px',
                    'hAlign' => 'right',
                    'format' => ['decimal', 0],
                    'pageSummary' => true,
                    'pageSummaryFunc' => GridView::F_SUM
                ],
                [
                    'label' => 'ค้างชำระ',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right;vertical-align: middle'],
                    'value' => function ($model, $key, $index, $widget) {
                        return ($model->line_total - $model->payment_amount);
                    },
                    'width' => '150px',
                    'hAlign' => 'right',
                    'format' => ['decimal', 0],
                    'pageSummary' => true,
                    'pageSummaryFunc' => GridView::F_SUM
                ],
                [
                    'label' => 'ประวัติชำระ',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center; width:10%;vertical-align: middle'],
                    'value' => function ($data) {
                        if ($data->payment_amount > 0) {
                            return '<div class="btn btn-info" data-route="'.$data->route_code.'" data-cusname="'.$data->cus_name.'" data-var="'.$data->customer_id.'" data-id="' . $data->order_id . '" onclick="showhistory($(this))">' . 'ดูประวัติ' . '</div>';
                        } else {
                            return '';
                        }
                    }
                ]

//        [
//            'class' => 'kartik\grid\FormulaColumn',
//            'header' => 'Amount In Stock',
//            'value' => function ($model, $key, $index, $widget) {
//                $p = compact('model', 'key', 'index');
//                return $widget->col(4, $p) * $widget->col(5, $p);
//            },
//            'mergeHeader' => true,
//            'width' => '150px',
//            'hAlign' => 'right',
//            'format' => ['decimal', 2],
//            'pageSummary' => true
//        ],
            ],
        ]);
        ?>
    </div>
</div>
<div id="payhistoryModal"
     class="modal fade"
     role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <!--        <form id="form-transfer-sale" action="-->
        <? //= \yii\helpers\Url::to(['orders/savetransfersale'], true) ?><!--"-->
        <!--              method="post">-->

        <!--            <input type="hidden" class="transfer-order-id" name="transfer_order_id" value="0">-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row"
                     style="width: 100%">
                    <div class="col-lg-11">
                        <h2 style="color: #255985">
                            <i class="fa fa-list-alt"></i>
                            ประวัติการชำระ
                        </h2>
                    </div>
                    <div class="col-lg-1">
                        <button type="button"
                                class="close"
                                data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h4>
                            สายส่ง
                            <span class="route-name" style="color: dodgerblue"></span>
                        </h4>
                    </div>
                    <div class="col-lg-6">
                        <h4>
                            ลูกค้า
                            <span class="customer-name" style="color: dodgerblue"></span>
                        </h4>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-list">
                    <thead>
                    <tr>
                        <th style="text-align: center">
                            เลขที่
                        </th>
                        <th style="text-align: center">
                            วันที่
                        </th>
                        <th style="text-align: right">
                            จำนวนเงิน
                        </th>
                        <th style="text-align: center">
                            วิธีชำระ
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <!--                    <button class="btn btn-outline-success btn-transfer-sale-submit" data-dismiss="modalx"><i-->
                <!--                                class="fa fa-check"></i> ตกลง-->
                <!--                    </button>-->
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i
                            class="fa fa-close text-danger"></i>
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>
        <!--        </form>-->
    </div>
</div>

<?php
$url_to_find_item = \yii\helpers\Url::to(['paymentreceive/findpayhistory'], true);
$url_to_find_customer = \yii\helpers\Url::to(['paymentreceive/findcustomer'], true);
$js = <<<JS
$(function(){
    
});

function showhistory(e){
    var ids = e.attr('data-id');
    var customer_id = e.attr('data-var');
    var route_code = e.attr('data-route');
    var cus_name = e.attr('data-cusname');
    if(ids){
     //   alert(ids);
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {'order_id': ids,'customer_id': customer_id },
              'success': function(data) {
                   // alert(data);
                   $(".table-list tbody").html(data);
                   
                   $(".route-name").html(route_code);
                   $(".customer-name").html(cus_name);
                   
                   $("#payhistoryModal").modal('show');
                 }
        });
    }
}

function getcustomer(e){
    //alert(e.val());
    if(e.val() != null){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_customer",
              'data': {'customer_list': e.val()},
              'success': function(data) {
                 //   alert(data);
                  
                   $("#customer-select").html(data);
                 }
              });
    }
}

JS;
$this->registerJs($js, static::POS_END);
?>
