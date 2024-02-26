<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customertaxinvoice */

$this->title = 'แก้ไขรายการใบกำกับ: ' . $model->invoice_no;
$this->params['breadcrumbs'][] = ['label' => 'ออกใบกำกับ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->invoice_no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>
<div class="customertaxinvoice-update">
    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => $model_line,
        'order_line_list' => $order_line_list,
    ]) ?>

</div>
