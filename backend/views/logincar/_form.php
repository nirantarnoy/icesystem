<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Logincar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logincar-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login_date')->textInput() ?>

    <?= $form->field($model, 'route_id')->textInput() ?>

    <?= $form->field($model, 'car_id')->textInput() ?>

    <?= $form->field($model, 'emp_1')->textInput() ?>

    <?= $form->field($model, 'emp_2')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
