<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Routetransexpend */

$this->title = 'สร้างบันทึกค่าใช้จ่ายสายส่ง';
$this->params['breadcrumbs'][] = ['label' => 'ค่าใช้จ่ายสายส่ง', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="routetransexpend-create">
    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => null,
    ]) ?>

</div>
