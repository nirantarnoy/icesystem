<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocktransSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'รายงานยอดรับเข้าผลิต';
$this->params['breadcrumbs'][] = $this->title;

$modelx = $dataProvider->getModels();
?>
    <div class="stocktrans-index">
        <?php Pjax::begin(); ?>
        <?php echo $this->render('_search', ['model' => $searchModel, 'f_date' => null, 't_date' => null]); ?>
        <br/>
        <!--    --><? //= GridView::widget([
        //        'dataProvider' => $dataProvider,
        //        //'filterModel' => $searchModel,
        //        'showPageSummary' => true,
        //        'toolbar' => [
        //            '{toggleData}',
        //            '{export}',
        //        ],
        //        'panel' => ['type' => 'info', 'heading' => 'รายงานยอดรับเข้าผลิต'],
        //        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        //        'columns' => [
        ////            ['class' => 'yii\grid\SerialColumn',
        ////                'headerOptions' => ['style' => 'text-align: center'],
        ////                'contentOptions' => ['style' => 'text-align: center'],],
        //            //  'id',
        ////            'company_id',
        ////            'branch_id',
        //            [
        //                'attribute' => 'product_id',
        //                'value' => function ($data) {
        //                    return \backend\models\Product::findName($data->product_id);
        //                },
        //            ],
        //
        //            [
        //                'attribute' => 'qty',
        //                'headerOptions' => ['style' => 'text-align: right'],
        //                'contentOptions' => ['style' => 'text-align: right'],
        //                'value' => function ($data) {
        //                    $cancel_qty = 0; // \backend\models\Stocktrans::findCancelqty($data->product_id,$from_date,$to_date,$data->company_id,$data->branch_id);
        //
        //
        //                    return ($data->qty - $cancel_qty);
        //                },
        //                'format' => ['decimal', 2],
        //                'hAlign' => 'right',
        //                'pageSummary' => true,
        //                'pageSummaryFunc' => GridView::F_SUM
        //            ],
        //
        //        ],
        //    ]); ?>
        <!---->
        <!--    --><?php //Pjax::end(); ?>

        <?php
        $total_rec = 0;
        $total_rec_transfer = 0;
        $total_cancel = 0;
        $total_cancel_transfer = 0;
        $total_qty = 0;
        ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><b>รหัสสินค้า</b></th>
                <th style="text-align: right"><b>จำนวนผลิต</b></th>
                <th style="text-align: right"><b>จำนวนรับโอน</b></th>
                <th style="text-align: right"><b>ยกเลิกผลิต</b></th>
                <th style="text-align: right"><b>ยกเลิกรับโอน</b></th>
                <th style="text-align: right"><b>จำนวนรับผลิต</b></th>
            </tr>

            </thead>
            <tbody>
            <?php foreach ($modelx as $value): ?>
                <?php
                //    $cancel_qty = \backend\models\Stocktrans::findCancelqty($value->product_id,$from_date,$to_date,$value->company_id,$value->branch_id);
                //   $cancel_qty2 = \backend\models\Stocktrans::findCancelqty2($value->product_id,$from_date,$to_date,$value->company_id,$value->branch_id);
                // $listdata = getTransData($value->product_id,$from_date,$to_date,$value->company_id,$value->branch_id);


                $cancel_qty = getCancelRepQty($value->product_id, $from_date, $to_date, $value->company_id, $value->branch_id);
                $cancel_qty2 = getCancelRtfQty($value->product_id, $from_date, $to_date, $value->company_id, $value->branch_id);

                $line_qty = getRepQty($value->product_id, $from_date, $to_date, $value->company_id, $value->branch_id);
                $line_transfer_qty = getRtfQty($value->product_id, $from_date, $to_date, $value->company_id, $value->branch_id);
//            if($listdata != null){
//                for($m=0;$m<=count($listdata)-1;$m++){
//                    if(substr($listdata[$m]['journal_no'],0,3) == 'REP'){
//                        $line_qty = $listdata[$m]['qty'];
//                    }else if(substr($listdata[$m]['journal_no'],0,3) == 'RTF'){
//                        $line_transfer_qty = $listdata[$m]['qty'];
//                    }else{
//                       // $line_transfer_qty = 100;
//                    }
//                }
//
//            }

                $total_qty = $total_qty + (($line_qty + $line_transfer_qty) - $cancel_qty);
                $total_rec = ($total_rec + $line_qty);
                $total_rec_transfer = ($total_rec_transfer + $line_transfer_qty);
                $total_cancel = ($total_cancel + $cancel_qty);
                $total_cancel_transfer = ($total_cancel_transfer + $cancel_qty2);
                ?>
                <tr>
                    <td>
                        <?= \backend\models\Product::findName($value->product_id) . ' ' . substr($value->journal_no, 1, 3) ?>
                    </td>
                    <td style="text-align: right">
                        <?php //echo number_format(($line_qty - $cancel_qty), 2) ?>
                        <?= number_format(($line_qty), 2) ?>
                    </td>
                    <td style="text-align: right">
                        <?php //echo number_format(($line_transfer_qty - $cancel_qty2), 2) ?>
                        <?= number_format(($line_transfer_qty), 2) ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format(($cancel_qty), 2) ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format(($cancel_qty2), 2) ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format((($line_qty + $line_transfer_qty) - $cancel_qty - $cancel_qty2), 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr style="background-color: #1abc9c">
                <td></td>
                <td style="text-align: right">  <?= number_format($total_rec, 2) ?></td>
                <td style="text-align: right">  <?= number_format($total_rec_transfer, 2) ?></td>
                <td style="text-align: right">  <?= number_format($total_cancel, 2) ?></td>
                <td style="text-align: right">  <?= number_format($total_cancel_transfer, 2) ?></td>
                <td style="text-align: right"> <?= number_format(($total_rec + $total_rec_transfer - $total_cancel - $total_cancel_transfer), 2) ?></td>
            </tr>
            </tfoot>

        </table>
        <table style="width: 100%;border: 0px;">
            <tr>
                <td style="text-align: right;border: 0px;">
                    FM-WAT-02
                </td>
            </tr>
        </table>

    </div>
<?php
function getTransData($product_id, $from_date, $to_date, $company_id, $branch_id)
{
    $data = [];
    $sql = "SELECT journal_no, qty  from stock_trans
              WHERE (date(trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . " 
              AND date(trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
              AND activity_type_id = 15 
              AND product_id =" . $product_id . "
              AND company_id=" . $company_id . " AND branch_id=" . $branch_id;

    // $sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            array_push($data, [
                'journal_no' => $model[$i]['journal_no'],
                'qty' => $model[$i]['qty'],
            ]);
        }
    }
    return $data;
}

function getRepQty($product_id, $from_date, $to_date, $company_id, $branch_id)
{
    $data = 0;
    $sql = "SELECT journal_no, qty  from stock_trans
              WHERE (date(trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . " 
              AND date(trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
              AND activity_type_id = 15 
              AND product_id =" . $product_id . "
              AND company_id=" . $company_id . " AND branch_id=" . $branch_id;

    //$sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            if (substr($model[$i]['journal_no'], 0, 3) == 'RTF') {
                continue;
            } else {
                $data = ($data + $model[$i]['qty']);
            }

        }
    }
    return $data;
}

function getRtfQty($product_id, $from_date, $to_date, $company_id, $branch_id)
{
    $data = 0;
    $sql = "SELECT journal_no, qty  from stock_trans
              WHERE (date(trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . " 
              AND date(trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
              AND activity_type_id = 15 
              AND product_id =" . $product_id . "
              AND company_id=" . $company_id . " AND branch_id=" . $branch_id;

    //$sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            if (substr($model[$i]['journal_no'], 0, 3) == 'REP') {
                continue;
            } else {
                $data = ($data + $model[$i]['qty']);
            }

        }
    }
    return $data;
}

function getCancelRepQty($product_id, $from_date, $to_date, $company_id, $branch_id)
{
    $data = 0;
    $sql = "SELECT t2.journal_no, t2.qty  from stock_trans as t1 inner join stock_trans as t2 on t1.trans_ref_id = t2.id
              WHERE (date(t2.trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . " 
              AND date(t2.trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
              AND t1.activity_type_id = 28 
              AND t2.product_id =" . $product_id . "
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;

    //$sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            if (substr($model[$i]['journal_no'], 0, 3) == 'RTF') {
                continue;
            } else {
                $data = ($data + $model[$i]['qty']);
            }

        }
    }
    return $data;
}

function getCancelRtfQty($product_id, $from_date, $to_date, $company_id, $branch_id)
{
    $data = 0;
    $sql = "SELECT t2.journal_no, t2.qty  from stock_trans as t1 inner join stock_trans as t2 on t1.trans_ref_id = t2.id
              WHERE (date(t2.trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . "
              AND date(t2.trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
              AND t1.activity_type_id = 28
              AND t2.product_id =" . $product_id . "
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;


    // omnoi

//    $sql = "SELECT t2.journal_no, t2.qty  from stock_trans as t1 inner join stock_trans as t2 on t2.trans_ref_id = t1.id
//              WHERE (date(t2.trans_date)>= " . "'" . date('Y-m-d', strtotime($from_date)) . "'" . "
//              AND date(t2.trans_date)<= " . "'" . date('Y-m-d', strtotime($to_date)) . "'" . " )
//              AND t2.activity_type_id = 28
//              AND t2.product_id =" . $product_id . "
//              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;

    //$sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            if (substr($model[$i]['journal_no'], 0, 3) == 'REP') {
                continue;
            } else {
                $data = ($data + $model[$i]['qty']);
            }

        }
    }
    return $data;
}

?>