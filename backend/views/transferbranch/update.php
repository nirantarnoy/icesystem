<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Transferbranch */

$this->title = 'แก้ไขสาขารับโอน: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'สาขารับโอน', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>
<div class="transferbranch-update">

    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => $model_line,
    ]) ?>

</div>
