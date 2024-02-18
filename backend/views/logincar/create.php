<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Logincar */

$this->title = 'Create Logincar';
$this->params['breadcrumbs'][] = ['label' => 'Logincars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logincar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
