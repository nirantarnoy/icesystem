<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Stdpricegroup */

$this->title = 'แก้ไขกลุ่มราคารายงาน: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'กลุ่มราคารายงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stdpricegroup-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
