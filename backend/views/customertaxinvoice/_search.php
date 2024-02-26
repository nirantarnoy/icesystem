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
        <div class="col-lg-4"><?= $form->field($model, 'invoice_no')->textInput(['placeholder'=>'กรอกเลขที่ใบกำกับ'])->label(false) ?></div>
        <div class="col-lg-4"><?= $form->field($model, 'customer_id')->textInput(['placeholder'=>'ชื่อลูกค้า'])->label(false) ?></div>
        <div class="col-lg-4">  <div class="form-group">
                <?= Html::submitButton('ค้นหา', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('รีเซ็ต', ['class' => 'btn btn-outline-secondary']) ?>
            </div></div>
    </div>







    <?php ActiveForm::end(); ?>

</div>
