<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Salecomcon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="salecomcon-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-1">

        </div>
        <div class="col-lg-10">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'sale_price')->textInput() ?>

            <?= $form->field($model, 'com_extra')->textInput() ?>


            <?= $form->field($model, 'from_date')->widget(\kartik\date\DatePicker::className(),[
                    'value' => $model->from_date == null ? date('d/m/Y'): date('d/m/Y',strtotime($model->from_date)),
                    'pluginOptions' => [
                            'autoclose'=> true,
                            'format'=> 'dd/mm/yyyy',
                    ]
            ]) ?>
            <?= $form->field($model, 'to_date')->widget(\kartik\date\DatePicker::className(),[
                'value' => $model->to_date == null ? date('d/m/Y'): date('d/m/Y',strtotime($model->to_date)),
                'pluginOptions' => [
                    'autoclose'=> true,
                    'format'=> 'dd/mm/yyyy',
                ]
            ]) ?>

            <label for=""><?= $model->getAttributeLabel('status') ?></label>
            <?php echo $form->field($model, 'status')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label(false) ?>
            <br>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
