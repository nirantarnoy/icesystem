<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Deliveryroute */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="deliveryroute-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1">
        </div>
        <div class="col-lg-10">
            <?= $form->field($model, 'type_id')->Widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\helpers\DeliveryrouteType::asArrayObject(), 'id', 'name'),
            ]) ?>
        </div>
        <div class="col-lg-1">
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'status')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label() ?>

        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'is_other_branch')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label() ?>

        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <?= $form->field($model, 'is_dup_login')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label() ?>

        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div class="col-lg-1"></div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
