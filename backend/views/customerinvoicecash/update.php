<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customerinvoice */

$this->title = 'แก้ไขใบวางบิลเงินสด: ' . $model->journal_no;
$this->params['breadcrumbs'][] = ['label' => 'วางบิลเงินสด', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->journal_no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>
<div class="customerinvoice-update">
    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => $model_line,
    ]) ?>

</div>
