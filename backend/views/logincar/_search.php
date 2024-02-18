<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LogincarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logincar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'login_date')->widget(\kartik\date\DatePicker::className(), [
                'value' => date('Y-m-d'),
                'options' => [

                ],
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'allowClear'=>true,
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'route_id')->widget(\kartik\select2\Select2::className(),[
                    'data'=>\yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['status'=>1])->all(),'id','code'),
                'options' => [
                        'placeholder'=>'--เลือกสายส่ง--'
                ],
                'pluginOptions' => [
                    'allowClear'=>true,
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
           <div class="label" style="color: transparent;height: 30px;">ค้นหา</div>
                <?= Html::submitButton('ค้นหา', ['class' => 'btn btn-primary']) ?>

        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
