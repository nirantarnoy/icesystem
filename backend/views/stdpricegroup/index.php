<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StdpricegroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'กลุ่มราคารายงาน';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stdpricegroup-index">

    <p>
        <?= Html::a('Create Stdpricegroup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //  'id',
            'name',
            'description',
            ['attribute' => 'product_id',
                'value' => function ($data) {
                    return \backend\models\Product::findName($data->product_id);
                },],
            'price',
            'seq_no',
            ['attribute' => 'type_id',
                'value' => function ($data) {
                    return \backend\helpers\StdpricegroupType::getTypeById($data->type_id);
                },],
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
