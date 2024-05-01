<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$company_id = 0;
$branch_id = 0;

if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id= \Yii::$app->user->identity->branch_id;
}

$dash_date = date('d/m/Y H:i').'-'.date('d/m/Y H:i');
if ($f_date != null && $t_date != null) {
    $dash_date = date('d/m/Y H:i', strtotime($f_date)) . ' - ' . date('d/m/Y H:i', strtotime($t_date));
}

?>

<div class="stocktrans-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
     <div class="row">
         <div class="col-lg-12">
             <div class="input-group">

                 <?= $form->field($model, 'warehouse_id')->widget(\kartik\select2\Select2::className(), [
                     'data' => \yii\helpers\ArrayHelper::map(\backend\models\Warehouse::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->all(), 'id', function ($data) {
                         return $data->code . ' ' . $data->name;
                     }),
                     'options' => [
                         'placeholder' => '--เลือกคลังสินค้า--',
                         'onchange' => 'this.form.submit();'
                     ],
                     'pluginOptions' => [
                         'allowClear' => true,
                         'width'=> '300px',
                     ]
                 ])->label(false) ?>
                 <span style="margin-left: 2px;"></span>
                 <?= $form->field($model, 'product_id')->widget(\kartik\select2\Select2::className(), [
                     'data' => \yii\helpers\ArrayHelper::map(\backend\models\Product::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->all(), 'id', function ($data) {
                         return $data->code . ' ' . $data->name;
                     }),
                     'options' => [
                         'placeholder' => '--เลือกสินค้า--',
                         'onchange' => 'this.form.submit();'
                     ],
                     'pluginOptions' => [
                         'allowClear' => true,
                         'width'=> '150px',
                     ]
                 ])->label(false) ?>
                 <span style="margin-left: 2px;"></span>
                 <?= $form->field($model, 'created_by')->widget(\kartik\select2\Select2::className(), [
                     'data' => \yii\helpers\ArrayHelper::map(\backend\models\User::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->all(), 'id', function ($data) {
                         return $data->username;
                     }),
                     'options' => [
                         'placeholder' => '--พนักงาน--',
                         'onchange' => 'this.form.submit();'
                     ],
                     'pluginOptions' => [
                         'allowClear' => true,
                         'width'=> '150px',
                     ]
                 ])->label(false) ?>
                 <span style="margin-left: 2px;"></span>
                 <?= $form->field($model, 'prodrec_type')->widget(\kartik\select2\Select2::className(), [
                     'data' => \yii\helpers\ArrayHelper::map(\backend\helpers\ProductionType::asArrayObject(), 'id','name'),
                     'options' => [
                         'placeholder' => '--ประเภท--',
                         'onchange' => 'this.form.submit();'
                     ],
                     'pluginOptions' => [
                         'allowClear' => true,
                         'width'=> '150px',
                     ]
                 ])->label(false) ?>
                 <span style="margin-left: 2px;"></span>
                 <?= $form->field($model, 'transfer_branch_id')->widget(\kartik\select2\Select2::className(), [
                     'data' => \yii\helpers\ArrayHelper::map(\common\models\TransferBranch::find()->all(), 'id','name'),
                     'options' => [
                         'placeholder' => '--สาขา--',
                         'onchange' => 'this.form.submit();'
                     ],
                     'pluginOptions' => [
                         'allowClear' => true,
                         'width'=> '200px',
                     ]
                 ])->label(false) ?>
                 <span style="margin-left: 2px;"></span>
                 <?php
                 echo DateRangePicker::widget([
                     'model' => $model,
                     'attribute' => 'from_date',
                     //'name'=>'date_range_5',
                     'value'=>'2015-10-19 12:00 AM',
                     //    'useWithAddon'=>true,
                     'convertFormat'=>true,
                     'options' => [
                         'class' => 'form-control',
                         'placeholder'=>'ตั้งแต่วันที่',
                         'onchange' => 'this.form.submit();',
                         'autocomplete' => 'off',
                        ],
                     'pluginOptions'=>[
                         'timePicker'=>true,
                         'timePickerIncrement'=>1,
                         'locale'=>['format' => 'Y-m-d H:i'],
                         'singleDatePicker'=>true,
                         'showDropdowns'=>true,
                         'timePicker24Hour'=>true
                     ]
                 ]) ;
                 ?>
                 <span style="margin-left: 2px;"></span>
                 <?php
                 echo DateRangePicker::widget([
                     'model' => $model,
                     'attribute' => 'to_date',
                     //'name'=>'date_range_5',
                     'value'=>'2015-10-19 12:00 AM',
                     //    'useWithAddon'=>true,
                     'convertFormat'=>true,
                     'options' => [
                         'class' => 'form-control',
                         'placeholder'=>'ถึงวันที่',
                         'onchange' => 'this.form.submit();',
                         'autocomplete' => 'off',
                     ],
                     'pluginOptions'=>[
                         'timePicker'=>true,
                         'timePickerIncrement'=>1,
                         'locale'=>['format' => 'Y-m-d H:i'],
                         'singleDatePicker'=>true,
                         'showDropdowns'=>true,
                         'timePicker24Hour'=>true
                     ]
                 ]) ;
                 ?>
             </div>
         </div>
     </div>


    <?php ActiveForm::end(); ?>

</div>
