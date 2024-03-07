<?php

use yii\helpers\Html;

//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomertaxinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ออกใบกำกับ';
$this->params['breadcrumbs'][] = $this->title;
?>
<form action="<?=Url::to(['customertaxinvoice/recalno'],true)?>" method="post">
    <div class="row">
        <div class="col-lg-3">
            <?php
            echo DateRangePicker::widget([
                'name' => 'from_date',
                // 'value'=>'2015-10-19 12:00 AM',
                'value' => $from_date != null ? date('d-m-Y', strtotime($from_date)) : date('d-m-Y'),
                //    'useWithAddon'=>true,
                'convertFormat' => true,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'ถึงวันที่',
                    //  'onchange' => 'this.form.submit();',
                    'autocomplete' => 'off',
                ],
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 1,
                    'locale' => ['format' => 'd-m-Y'],
                    'singleDatePicker' => true,
                    'showDropdowns' => true,
                    'timePicker24Hour' => true
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <?php
            echo DateRangePicker::widget([
                'name' => 'to_date',
                // 'value'=>'2015-10-19 12:00 AM',
                'value' => $to_date != null ? date('d-m-Y', strtotime($to_date)) : date('d-m-Y'),
                //    'useWithAddon'=>true,
                'convertFormat' => true,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'ถึงวันที่',
                    //  'onchange' => 'this.form.submit();',
                    'autocomplete' => 'off',
                ],
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 1,
                    'locale' => ['format' => 'd-m-Y'],
                    'singleDatePicker' => true,
                    'showDropdowns' => true,
                    'timePicker24Hour' => true
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-sm btn-info">เรียงเลขที่ใหม่</button>
        </div>
        <div class="col-lg-3"></div>
    </div>
</form>
<br />
<div class="customertaxinvoice-index">
    <?php Pjax::begin(); ?>
    <div class="row">
        <div class="col-lg-10">
            <p>
                <?= Html::a(Yii::t('app', '<i class="fa fa-plus"></i> สร้างใหม่'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-lg-2" style="text-align: right">
            <form id="form-perpage" class="form-inline" action="<?= Url::to(['customertaxinvoice/index'], true) ?>"
                  method="post">
                <div class="form-group">
                    <label>แสดง </label>
                    <select class="form-control" name="perpage" id="perpage">
                        <option value="20" <?= $perpage == '20' ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $perpage == '50' ? 'selected' : '' ?> >50</option>
                        <option value="100" <?= $perpage == '100' ? 'selected' : '' ?>>100</option>
                        <option value="10000" <?= $perpage == '10000' ? 'selected' : '' ?>>All</option>
                    </select>
                    <label> รายการ</label>
                </div>
            </form>
        </div>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'emptyCell' => '-',
        'layout' => "{items}\n{summary}\n<div class='text-center'>{pager}</div>",
        'summary' => "แสดง {begin} - {end} ของทั้งหมด {totalCount} รายการ",
        'showOnEmpty' => false,
        'showPageSummary' => true,
        //    'bordered' => true,
        //     'striped' => false,
        //    'hover' => true,
        'id' => 'product-grid',
        //'tableOptions' => ['class' => 'table table-hover'],
        'emptyText' => '<div style="color: red;text-align: center;"> <b>ไม่พบรายการไดๆ</b> <span> เพิ่มรายการโดยการคลิกที่ปุ่ม </span><span class="text-success">"สร้างใหม่"</span></div>',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align:center;'],
                'contentOptions' => ['style' => 'text-align: center'],
            ],
            'invoice_no',
            [
                'attribute' => 'product_id',
                'label' => 'ประเภทสินค้า',
                'value' => function ($data) {
                    $group_id = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id' => $data->id])->one();
                    if($group_id){
                        return \backend\models\Productgroup::findName($group_id->product_group_id);
                    }else{
                        return '';
                    }

                }
            ],
            'invoice_date',
            [
                'attribute' => 'price',
                'label' => 'ราคา',
                'value' => function ($data) {
                    $pricex = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id' => $data->id])->one();
                    if($pricex){
                        return $pricex->price;
                    }else{
                        return 0;
                    }

                }
            ],
            [
                'attribute' => 'qty',
                'label' => 'จำนวน',
                'value' => function ($data) {
                    $allqty = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id' => $data->id])->sum('qty');
                    return $allqty;
                },
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
//            [
//                'attribute' => 'payment_term_id',
//                'value' => function ($data) {
//                    return \backend\models\Paymentterm::findName($data->payment_term_id);
//                }
//            ],
            //'payment_date',
            //'remark',
            //'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            [
                'attribute' => 'total_amount',
                'value' => function ($data) {
                    return $data->total_amount;
                },
                'format' => ['decimal', 2],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
            //'vat_amount',
            //'net_amount',
            //'total_text',

            [

                'header' => 'ตัวเลือก',
                'headerOptions' => ['style' => 'text-align:center;', 'class' => 'activity-view-link',],
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'text-align: center'],
                'template' => '{view} {update}{delete}',
                'buttons' => [
                    'view' => function ($url, $data, $index) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a(
                            '<span class="fas fa-eye btn btn-xs btn-default"></span>', $url, $options);
                    },
                    'update' => function ($url, $data, $index) {
                        $options = array_merge([
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                            'id' => 'modaledit',
                        ]);
                        return Html::a(
                            '<span class="fas fa-edit btn btn-xs btn-default"></span>', $url, [
                            'id' => 'activity-view-link',
                            //'data-toggle' => 'modal',
                            // 'data-target' => '#modal',
                            'data-id' => $index,
                            'data-pjax' => '0',
                            // 'style'=>['float'=>'rigth'],
                        ]);
                    },
                    'delete' => function ($url, $data, $index) {
                        $options = array_merge([
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            //'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            //'data-method' => 'post',
                            //'data-pjax' => '0',
                            'data-url' => $url,
                            'data-var' => $data->id,
                            'onclick' => 'recDelete($(this));'
                        ]);
                        return Html::a('<span class="fas fa-trash-alt btn btn-xs btn-default"></span>', 'javascript:void(0)', $options);
                    }
                ]
            ],
        ],
       // 'pager' => ['class' => LinkPager::className()],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

