<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Transferbranch */

$this->title = 'สร้างสาขารับโอน';
$this->params['breadcrumbs'][] = ['label' => 'สาขารับโอน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transferbranch-create">

    <?= $this->render('_form', [
        'model' => $model,
        'model_line' => null,
    ]) ?>

</div>
