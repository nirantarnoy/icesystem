<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomertaxinvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customertaxinvoice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-1"><?= $form->field($model, 'invoice_no')->textInput(['placeholder'=>'กรอกเลขที่ใบกำกับ'])->label(false) ?></div>
        <div class="col-lg-3"><?= $form->field($model, 'find_product_group_id')->widget(\kartik\select2\Select2::className(),[
                'data'=>\yii\helpers\ArrayHelper::map(\backend\models\Productgroup::find()->where(['status'=>1])->all(),'id','name'),
                'options' => [
                        'placeholder'=>'--เลือกประเภทสินค้า--'
                ]
            ])->label(false) ?></div>
        <div class="col-lg-3"><?= $form->field($model, 'from_date')->widget(\kartik\date\DatePicker::className(),
                [
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'todayHighlight' => true,
                        'todayBtn' => true
                    ]
                ])->label(false) ?></div>
        <div class="col-lg-3"><?= $form->field($model, 'to_date')->widget(\kartik\date\DatePicker::className(),
                [
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'todayHighlight' => true,
                        'todayBtn' => true
                    ]
                ])->label(false) ?></div>
        <div class="col-lg-2">  <div class="form-group">
                <?= Html::submitButton('ค้นหา', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('รีเซ็ต', ['class' => 'btn btn-outline-secondary']) ?>
            </div></div>
    </div>







    <?php ActiveForm::end(); ?>

</div>
