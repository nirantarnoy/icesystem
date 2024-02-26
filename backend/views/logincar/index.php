<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LogincarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ประวัติเข้าระบบขายรถ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index">
    <?php Pjax::begin(); ?>
    <div class="row">
        <div class="col-lg-10">

        </div>
        <div class="col-lg-2" style="text-align: right">
            <form id="form-perpage" class="form-inline" action="<?= Url::to(['location/index'], true) ?>"
                  method="post">
                <div class="form-group">
                    <label>แสดง </label>
                    <select class="form-control" name="perpage" id="perpage">
                        <option value="20" <?= $perpage == '20' ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $perpage == '50' ? 'selected' : '' ?> >50</option>
                        <option value="100" <?= $perpage == '100' ? 'selected' : '' ?>>100</option>
                    </select>
                    <label> รายการ</label>
                </div>
            </form>
        </div>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'emptyCell' => '-',
        'layout' => "{items}\n{summary}\n<div class='text-center'>{pager}</div>",
        'summary' => "แสดง {begin} - {end} ของทั้งหมด {totalCount} รายการ",
        'showOnEmpty' => false,
        //    'bordered' => true,
        //     'striped' => false,
        //    'hover' => true,
        'id' => 'product-grid',
        //'tableOptions' => ['class' => 'table table-hover'],
        'emptyText' => '<div style="color: red;text-align: center;"> <b>ไม่พบรายการไดๆ</b> <span> เพิ่มรายการโดยการคลิกที่ปุ่ม </span><span class="text-success">"สร้างใหม่"</span></div>',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align:center;'],
                'contentOptions' => ['style' => 'text-align: center'],
            ],
            [
                'attribute' => 'login_date',
                'value' => function ($data) {
                    return date('d-m-Y H:i:s', strtotime($data->login_date));
                }
            ],
            [
                'attribute' => 'route_id',
                'value' => function ($data) {
                    return \backend\models\Deliveryroute::findName($data->route_id);
                }
            ],
            [
                'attribute' => 'car_id',
                'value' => function ($data) {
                    return \backend\models\Car::findName($data->car_id);
                }
            ],
            [
                'attribute' => 'emp_1',
                'value' => function ($data) {
                    return \backend\models\Employee::findFullName($data->emp_1);
                }
            ],
            [
                'attribute' => 'emp_2',
                'value' => function ($data) {
                    return \backend\models\Employee::findFullName($data->emp_2);
                }
            ],
            [
                'attribute' => 'logout_date',
                'value' => function ($data) {
                    if($data->logout_date == null){
                        return '';
                    }else{
                        return date('d-m-Y H:i:s', strtotime($data->logout_date));
                    }

                }
            ],

        ],
        'pager' => ['class' => LinkPager::className()],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
