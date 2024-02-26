<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="salecom-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-1">

        </div>
        <div class="col-lg-10">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'product_id')->Widget(\kartik\select2\Select2::className(),[
                    'data'=>\yii\helpers\ArrayHelper::map(\backend\models\Product::find()->where(['status'=>1])->all(),'id','name'),
                    'options'=>[
                            'placeholder'=>'--เลือกสินค้า--'
                    ]
            ]) ?>

            <?= $form->field($model, 'emp_qty')->textInput(['type' => 'number']) ?>

            <?= $form->field($model, 'com_extra')->textInput() ?>

            <?= $form->field($model, 'first_emp')->textInput() ?>

            <?= $form->field($model, 'second_emp')->textInput() ?>

            <?php $model->from_date = $model->isNewRecord? date('Y-m-d'):date('Y-m-d',strtotime($model->from_date));?>
            <?= $form->field($model, 'from_date')->widget(\kartik\date\DatePicker::className(), [
                'value' => date('Y-m-d'),
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                ]
            ]) ?>
            <?php $model->to_date = $model->isNewRecord? date('Y-m-d'):date('Y-m-d',strtotime($model->to_date));?>
            <?= $form->field($model, 'to_date')->widget(\kartik\date\DatePicker::className(), [
                'value' => date('Y-m-d'),
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
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
