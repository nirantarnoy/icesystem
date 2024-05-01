<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use common\models\QuerySaleorderByCustomerLoanSumNew;
use kartik\daterange\DateRangePicker;
use yii\web\Response;

//require_once __DIR__ . '/vendor/autoload.php';
//require_once 'vendor/autoload.php';
// เพิ่ม Font ให้กับ mPDF

$user_id = \Yii::$app->user->id;

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp',
//$mpdf = new \Mpdf\Mpdf([
    //'tempDir' => '/tmp',
    'mode' => 'utf-8',
    // 'mode' => 'utf-8', 'format' => [80, 120],
    'fontdata' => $fontData + [
            'sarabun' => [ // ส่วนที่ต้องเป็น lower case ครับ
                'R' => 'THSarabunNew.ttf',
                'I' => 'THSarabunNewItalic.ttf',
                'B' => 'THSarabunNewBold.ttf',
                'BI' => "THSarabunNewBoldItalic.ttf",
            ]
        ],
]);

//$mpdf->SetMargins(-10, 1, 1);
//$mpdf->SetDisplayMode('fullpage');
$mpdf->AddPageByArray([
    'margin-left' => 5,
    'margin-right' => 0,
    'margin-top' => 0,
    'margin-bottom' => 1,
]);

//$model_line = \common\models\QuerySaleByDistributor::find()->select(['customer_id'])->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
 //   ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['customer_id' => SORT_ASC])->groupBy('customer_id')->all();
$model_line = \common\models\Customer::find()->select(['id'])->where(['is_distributor'=>1])
    ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['id' => SORT_ASC])->all();

?>
<!DOCTYPE html>
<html>
<head>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>
    <link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">
    <style>
        /*body {*/
        /*    font-family: sarabun;*/
        /*    !*font-family: garuda;*!*/
        /*    font-size: 18px;*/
        /*}*/

        #div1 {
            font-family: sarabun;
            /*font-family: garuda;*/
            font-size: 14px;
        }

        table.table-header {
            border: 0px;
            border-spacing: 1px;
        }

        table.table-footer {
            border: 0px;
            border-spacing: 0px;
        }

        table.table-header td, th {
            border: 0px solid #dddddd;
            text-align: left;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        table.table-title {
            border: 0px;
            border-spacing: 0px;
        }

        table.table-title td, th {
            border: 0px solid #dddddd;
            text-align: left;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            /*background-color: #dddddd;*/
        }

        table.table-detail {
            border-collapse: collapse;
            width: 100%;
        }

        table.table-detail td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 2px;
        }

    </style>
    <!--    <script src="vendor/jquery/jquery.min.js"></script>-->
    <!--    <script type="text/javascript" src="js/ThaiBath-master/thaibath.js"></script>-->
</head>
<body>
<div class="row">
    <div class="col-lg-9">
        <form action="<?= \yii\helpers\Url::to(['salereportdistributor/index'], true) ?>" method="post"
              id="form-search">
            <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
                <tr>
                    <td style="width: 20%">
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'from_date',
                            // 'value'=>'2015-10-19 12:00 AM',
                            'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                            //    'useWithAddon'=>true,
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'ถึงวันที่',
                                //  'onchange' => 'this.form.submit();',
                                'autocomplete' => 'off',
                            ],
                            'pluginOptions' => [
                                'timePicker' => true,
                                'timePickerIncrement' => 1,
                                'locale' => ['format' => 'Y-m-d H:i'],
                                'singleDatePicker' => true,
                                'showDropdowns' => true,
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>
                    <td style="width: 20%">
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'to_date',
                            'value' => $to_date != null ? date('Y-m-d H:i', strtotime($to_date)) : date('Y-m-d H:i'),
                            //    'useWithAddon'=>true,
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'ถึงวันที่',
                                //  'onchange' => 'this.form.submit();',
                                'autocomplete' => 'off',
                            ],
                            'pluginOptions' => [
                                'timePicker' => true,
                                'timePickerIncrement' => 1,
                                'locale' => ['format' => 'Y-m-d H:i'],
                                'singleDatePicker' => true,
                                'showDropdowns' => true,
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>

                    <!--            <td>-->
                    <!--                --><?php
                    //                echo \kartik\select2\Select2::widget([
                    //                    'name' => 'find_emp_id',
                    //                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all(), 'id', 'name'),
                    //                    'value' => $find_emp_id,
                    //                    'options' => [
                    //                        'placeholder' => '--สายส่ง--'
                    //                    ],
                    //                    'pluginOptions' => [
                    //                        'allowClear' => true,
                    //                        'multiple' => true,
                    //                    ]
                    //                ]);
                    //                ?>
                    <!--            </td>-->
                    <td>
                        <input type="submit" class="btn btn-primary" value="ค้นหา">
                    </td>
                    <td style="width: 25%; text-align: right">

                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="col-lg-3" style="text-align: right;">


    </div>
</div>

<br/>
<div id="div1">
    <table class="table-header" width="100%">
        <tr>
            <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานสรุปขายแยกยี่ปั๊วะ</td>
        </tr>
    </table>
    <br>
    <table class="table-header" width="100%">
        <tr>
            <td style="text-align: center; font-size: 20px; font-weight: normal">
                จากวันที่ <span style="color: red"><?= date('Y-m-d H:i', strtotime($from_date)) ?></span>
                ถึง <span style="color: red"><?= date('Y-m-d H:i', strtotime($to_date)) ?></span></td>
        </tr>
    </table>
    <br>
    <?php
    $total_all = 0;
    $count_item = 0;
    $num = 0;
    $total_line = 0;
    $line_qty = 0;
    $total_line_qty = 0;
    $total_all_line_qty = 0;

    $all_qty = 0;
    $all_cash = 0;
    $all_credit = 0;

    $all_free = 0;
    $all_receive_cash = 0;
    $all_receive_transfer = 0;
    $all_return_qty = 0;

    $total_all_line_qty_data = [];


    $product_header = [];


    //
    //    $modelx = \common\models\QuerySaleByDistributor::find()->select(['product_id'])->join('inner join', 'product', 'query_sale_by_distributor.product_id=product.id')->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
    //        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product.item_pos_seq' => SORT_ASC])->all();

    $sql = "SELECT product_id FROM query_sale_by_distributor INNER JOIN product ON query_sale_by_distributor.product_id = product.id";
    $sql .= " WHERE query_sale_by_distributor.company_id=" . $company_id . " AND query_sale_by_distributor.branch_id=" . $branch_id;
    $sql .= " AND date(order_date) >= " ."'". date('Y-m-d', strtotime($from_date))."'";
    $sql .= " AND date(order_date) <= " ."'". date('Y-m-d', strtotime($to_date))."'";
    $sql .= " GROUP BY product_id";
    $sql .= " ORDER BY product.item_pos_seq ASC";

    $modelx = \Yii::$app->db->createCommand($sql)->queryAll();

    if ($modelx) {
//        foreach ($modelx as $valuexx) {
//            if (!in_array($valuexx->product_id, $product_header)) {
//                array_push($product_header, $valuexx->product_id);
//            }
//        }
        for ($xx=0;$xx<=count($modelx)-1;$xx++) {
            if (!in_array($modelx[$xx]['product_id'], $product_header)) {
                array_push($product_header, $modelx[$xx]['product_id']);
            }
        }
    }


   //  print_r($product_header);


    ?>
    <table id="table-data">
        <tr style="font-weight: bold;">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">ลูกค้า</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">ฟรี</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: yellow;">จำนวนรวม</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;">คืน</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;">ขายสด</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;">ขายเชื่อ</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;">ชำระหนี้สด</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;">ชำระหนี้โอน</td>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: mediumseagreen">รวมเงิน
            </td>
        </tr>

        <?php foreach ($model_line as $value): ?>
            <?php
            $num += 1;
            $line_qty_total = 0;

            $line_credit_amount_total = 0;
            $line_cash_amount_total = 0;

//            $total_line = $value->remain_amount; // \backend\models\Orders::getlineremainsum($value->order_id, $model->customer_id);
//            $total_all = $total_all + $total_line;
//
//            $line_qty = getOrderQty($value->order_id);
//            $total_all_line_qty = $total_all_line_qty + $line_qty;
//              $order_product = getOrderQty2($value->order_id);
//              $total_all_line_qty = 0;
            // $car_data = getCardata($value->route_id, $from_date);
            ?>
            <tr>
                <td style="text-align: left;padding: 8px;border: 1px solid grey"><?= \backend\models\Customer::findName($value->id) ?></td>
                <?php
                $product_line_free_qty = 0; // getFree($value->customer_id, $from_date, $to_date);
                $all_free = ($all_free + ($product_line_free_qty));

                //                $product_line_receive_cash = getPayment($from_date,$to_date,$value->route_id,$company_id,$branch_id);
                $product_line_receive_cash =  getReceiveCash($value->id, $from_date, $to_date);
                $all_receive_cash = ($all_receive_cash + ($product_line_receive_cash));

                $product_line_receive_transfer = 0; // getReceiveTransfer($value->customer_id, $from_date, $to_date);
                $all_receive_transfer = ($all_receive_transfer + ($product_line_receive_transfer));

                $return_line_qty = 0; // getReturnCarQty($value->customer_id, $from_date, $to_date);
                $all_return_qty = ($all_return_qty + ($return_line_qty));
                ?>
                <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                    <?php
                    $product_line_qty = getOrderQty2($value->id, $product_header[$x], $from_date, $to_date);
                    $line_qty_total = ($line_qty_total + $product_line_qty);

                    $all_qty = ($all_qty + $product_line_qty);

                    if ($num == 1) {
                        array_push($total_all_line_qty_data, ['product_id' => $product_header[$x], 'qty' => $product_line_qty]);
                    } else {
                        foreach ($total_all_line_qty_data as $key => $val) {
                            if ($total_all_line_qty_data[$key]['product_id'] == $product_header[$x]) {
                                $total_all_line_qty_data[$key]['qty'] = $val['qty'] + $product_line_qty;
                            }
                        }
                    }


                    $product_line_cash_amount = getCashAmount($value->id, $product_header[$x], $from_date, $to_date);
                    $line_cash_amount_total = ($line_cash_amount_total + $product_line_cash_amount);
                    $all_cash = ($all_cash + $product_line_cash_amount);

                    $product_line_credit_amount = getCreditAmount($value->id, $product_header[$x], $from_date, $to_date);
                    $line_credit_amount_total = ($line_credit_amount_total + $product_line_credit_amount);
                    $all_credit = ($all_credit + $product_line_credit_amount);
                    ?>
                    <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $product_line_qty == 0 ? '-' : number_format($product_line_qty, 1) ?></td>
                <?php endfor; ?>
                <!--                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">-->
                <? //= number_format($line_qty) ?><!--</td>-->
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: skyblue;font-weight: bold;"><?= $product_line_free_qty == 0 ? '-' : number_format($product_line_free_qty, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: yellow;font-weight: bold;"><?= number_format(($line_qty_total + $product_line_free_qty), 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $return_line_qty == 0 ? "-" : number_format($return_line_qty, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $line_cash_amount_total == 0 ? "-" : number_format($line_cash_amount_total, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $line_credit_amount_total == 0 ? "-" : number_format($line_credit_amount_total, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $product_line_receive_cash == 0 ? "-" : number_format($product_line_receive_cash, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $product_line_receive_transfer == 0 ? "-" : number_format($product_line_receive_transfer, 2) ?></td>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: mediumseagreen;font-weight: bold;"><?= number_format(($line_cash_amount_total + $line_credit_amount_total), 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
        <tr>
            <td style="text-align: right;padding: 5px;border: 0px solid grey"><b>รวม</b></td>
            <?php for ($z = 0; $z <= count($total_all_line_qty_data) - 1; $z++): ?>
                <td style="text-align: center;padding: 2px;padding-right: 5px;border: 1px solid grey">
                    <b><?= number_format($total_all_line_qty_data[$z]['qty'], 1) ?></b></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey;background-color: skyblue">
                <b><?= number_format($all_free, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey;background-color: yellow">
                <b><?= number_format(($all_qty + $all_free), 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($all_return_qty, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($all_cash, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($all_credit, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($all_receive_cash, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($all_receive_transfer, 2) ?></b></td>
            <td style="text-align: right;padding: 2px;padding-right: 5px;border: 1px solid grey;background-color: mediumseagreen">
                <b><?= number_format(($all_cash + $all_credit), 2) ?></b></td>
        </tr>
        </tfoot>
    </table>
</div>

<br/>
<table width="100%" class="table-title">
    <td style="text-align: right">
        <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
        <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
    </td>
</table>
<!--<script src="../web/plugins/jquery/jquery.min.js"></script>-->
<!--<script>-->
<!--    $(function(){-->
<!--       alert('');-->
<!--    });-->
<!--   window.print();-->
<!--</script>-->
<?php
//echo '<script src="../web/plugins/jquery/jquery.min.js"></script>';
//echo '<script type="text/javascript">alert();</script>';
?>
</body>
</html>

<?php
function getOrderQty2($customer_id, $product_id, $from_date, $to_date)
{
    $data = 0;
    if ($customer_id && $product_id) {
        $model_qty = \common\models\TransactionDistributorSale::find()->select(['SUM(credit_qty) as credit_qty', 'SUM(cash_qty) as cash_qty'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['customer_id' => $customer_id, 'product_id' => $product_id])->groupBy(['product_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->credit_qty + $model_qty->cash_qty);
        }
    }
    return $data;
}

function getFree($customer_id, $from_date, $to_date)
{
    $data = 0;
    if ($customer_id) {
        $model_qty = \common\models\TransactionDistributorSale::find()->select(['SUM(free_qty) as free_qty'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['customer_id' => $customer_id])->groupBy(['customer_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->free_qty);
        }
    }
    return $data;
}

function getReturncarQty($route_id, $from_date, $to_date)
{
    $data = 0;
//    if ($route_id) {
//        $model_qty = \common\models\TransactionCarSale::find()->select(['return_qty'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//            ->andFilterWhere(['route_id' => $route_id])->one();
//        if ($model_qty != null) {
//            $data = ($model_qty->return_qty);
//        }
//    }
    $model_qty = \common\models\TransactionCarSaleRoutePay::find()
        ->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['route_id' => $route_id])->sum('return_car_qty');
    if ($model_qty != null) {
        $data = ($model_qty);
    }
    return $data;
}

function getPayment($f_date, $t_date, $find_user_id, $company_id, $branch_id)
{
    $list_route_id = null;

    $data = [];
    $amount = 0;

    $sql = "SELECT SUM(t1.payment_amount) as amount  from query_payment_receive as t1 INNER JOIN customer as t2 on t2.id = t1.customer_id 
              WHERE (date(t1.trans_date)>= " . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
              AND date(t1.trans_date)<= " . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " )
              AND t1.status <> 100 
              AND t1.payment_method_id=2 AND  t2.delivery_route_id =" . $find_user_id . "
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;


    $sql .= " GROUP BY t1.route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
//            array_push($data, [
//                'pay' => $model[$i]['amount'],
//            ]);
            $amount = $model[$i]['amount'];
        }
    }
    return $amount;
}

//function getReceiveCash($route_id, $from_date, $to_date){ // from transaction
//    $data = 0;
//    if ($route_id) {
//        $model_qty = \common\models\QueryPaymentReceive::find()->select(['SUM(payment_amount) as payment_amount'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//            ->andFilterWhere(['payment_method_id'=>2])
//            ->andFilterWhere(['!=','status',100])
//            ->andFilterWhere(['route_id' => $route_id])->groupBy(['route_id'])->one();
//        if ($model_qty != null) {
//            $data = ($model_qty->payment_amount);
//        }
//    }
//    return $data;
//}

function getCashAmount($customer_id, $product_id, $from_date, $to_date)
{
    $data = 0;
    if ($customer_id && $product_id) {
        $model_qty = \common\models\TransactionDistributorSale::find()->select(['SUM(cash_amount) as cash_amount'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['customer_id' => $customer_id, 'product_id' => $product_id])->groupBy(['product_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->cash_amount);
        }
    }
    return $data;
}

function getCreditAmount($customer_id, $product_id, $from_date, $to_date)
{
    $data = 0;
    if ($customer_id && $product_id) {
        $model_qty = \common\models\TransactionDistributorSale::find()->select(['SUM(credit_amount) as credit_amount'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['customer_id' => $customer_id, 'product_id' => $product_id])->groupBy(['product_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->credit_amount);
        }
    }
    return $data;
}

function getReceiveCash($customer_id, $from_date, $to_date)
{
    $data = 0;
    if ($customer_id) {
//        $model_qty = \common\models\TransactionCarSale::find()->select(['receive_cashx'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//            ->andFilterWhere(['route_id' => $route_id])->andFilterWhere(['>','receive_cash',0])->groupBy(['route_id'])->one();
//        if ($model_qty != null) {
//            $data = ($model_qty->receive_cash);
//        }
        $model_qty = \common\models\TransactionDistributorSalePay::find()
            ->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['customer_id' => $customer_id])->sum('cash_amount');
        if ($model_qty != null) {
            $data = ($model_qty);
        }
    }
    return $data;
}

function getReceiveTransfer($customer_id, $from_date, $to_date)
{
    $data = 0;
//    if ($route_id) {
//        $model_qty = \common\models\TransactionCarSale::find()->select(['receive_transter'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//            ->andFilterWhere(['route_id' => $route_id])->groupBy(['route_id'])->one();
//        if ($model_qty != null) {
//            $data = ($model_qty->receive_transter);
//        }
//    }
    $model_qty = \common\models\TransactionDistributorSalePay::find()
        ->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['customer_id' => $customer_id])->sum('transfer_amount');
    if ($model_qty != null) {
        $data = ($model_qty);
    }
    return $data;
//    $data = 0;
//    if ($route_id) {
//        $model_qty = \common\models\QueryPaymentReceive::find()->select(['SUM(payment_amount) as payment_amount'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//            ->andFilterWhere(['payment_channel_id'=>2])
//            ->andFilterWhere(['route_id' => $route_id])->groupBy(['route_id'])->one();
//        if ($model_qty != null) {
//            $data = ($model_qty->payment_amount);
//        }
//    }
//    return $data;
}

function getCardata($route_id, $t_date)
{
    $data = [];
    if ($route_id) {
        $model = \common\models\QueryCarEmpData::find()->where(['date(trans_date)' => date('Y-m-d', strtotime($t_date)), 'id' => $route_id])->one();
        if ($model) {
            array_push($data, [
                'car_name' => $model->car_name_,
                'emp_name' => $model->fname,
            ]);
        }
    }
    return $data;
}


?>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $(function(){
    $("#btn-export-excel").click(function(){
          $("#table-data").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Excel Document Name"
          });
    });
 });
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
JS;
$this->registerJs($js, static::POS_END);
?>
