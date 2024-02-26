<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Customertaxinvoice */

$this->title = $model->invoice_no;
$this->params['breadcrumbs'][] = ['label' => 'Customertaxinvoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customertaxinvoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'invoice_no',
            'customer_id',
            'invoice_date',
            'payment_term_id',
            'payment_date',
            'remark',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'total_amount',
            'vat_amount',
            'net_amount',
            'total_text',
        ],
    ]) ?>

</div>
