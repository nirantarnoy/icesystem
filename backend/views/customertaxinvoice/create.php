<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Customertaxinvoice */

$this->title = 'สร้างรายการใบกำกับ';
$this->params['breadcrumbs'][] = ['label' => 'ออกใบกำกับ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customertaxinvoice-create">

    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => $model_line,
        'order_line_list' => null,
    ]) ?>

</div>
