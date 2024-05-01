<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Stdpricegroup */

$this->title = 'สร้างกลุ่มราคารายงาน';
$this->params['breadcrumbs'][] = ['label' => 'กลุ่มราคารายงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stdpricegroup-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
