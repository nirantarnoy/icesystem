<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Routetransexpend */

$this->title = 'แก้ไขบันทึกค่าใช้จ่ายสายส่ง: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'ค่าใช้จ่ายสายส่ง', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>
<div class="routetransexpend-update">

    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => $model_line,
    ]) ?>

</div>
