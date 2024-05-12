<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
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

//$customer_name = $trans_data[0]['customer_id']?getCustomername($connect, $trans_data[0]['customer_id']):$trans_data[0]['customer_name'];
//$model_product_daily = \common\models\QueryProductTransDaily::find()->where(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
$model_product_daily = \common\models\StockTrans::find()->select("product_id")->where(['BETWEEN', 'trans_date', date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])->andFilterWhere(['activity_type_id' => 5, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product_id' => SORT_ASC])->all();

$user_login_datetime = '';
$model_c_login = LoginLog::find()->select('MIN(login_date) as login_date')->where(['user_id' => $user_id, 'status' => 1])->one();
if ($model_c_login != null) {
    $user_login_datetime = date('Y-m-d H:i:s', strtotime($model_c_login->login_date));
} else {
    $user_login_datetime = date('Y-m-d H:i:s');
}

$product_header_2 = [];
$product_header_3 = [];
$model_line = null;

$model_product = \backend\models\Product::find()->where(['status' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['item_pos_seq' => SORT_ASC])->all();
if ($model_product != null) {
    foreach ($model_product as $value) {
        array_push($product_header_2, [$value->id]);
        array_push($product_header_3, [$value->id]);
    }
}

if ($from_date != null && $to_date != null) {

    $model_line = \common\models\QueryOrderCustomerProduct::find()->select(['id', 'order_no','order_date','customer_id','order_channel_id'])

        ->where(['BETWEEN', 'order_date', $from_date, $to_date])
        ->andFilterWhere(['status' => [1, 100]])
        ->andFilterWhere(['>', 'qty', 0]);

    if ($find_user_id != null) {
        $model_line = $model_line->andFilterWhere(['created_by' => $find_user_id]);
    }
    if ($is_invoice_req != null) {
        $model_line = $model_line->andFilterWhere(['is_invoice_req' => $is_invoice_req]);
    }
    if ($find_sale_type != null && $find_sale_type != 0) {
        if ($find_sale_type == 1) {
            $model_line = $model_line->andFilterWhere(['payment_method_id' => $find_sale_type]);
        }
        if ($find_sale_type == 2) {
            // $sql .= " AND (t2.order_channel_id = 0 OR t2.order_channel_id is null) AND t2.payment_method_id=" . $find_sale_type;
            $model_line = $model_line->andFilterWhere(['or', ['order_channel_id' => 0], ['is', 'order_channel_id', new \yii\db\Expression('null')]])->andFilterWhere(['payment_method_id' => $find_sale_type]);
        }
        if ($find_sale_type == 3) {
//            $sql .= " AND t2.order_channel_id > 0";
//            $sql .= " AND t4.is_other_branch = 0";
            $model_line = $model_line->andFilterWhere(['>', 'order_channel_id', 0])->andFilterWhere(['is_other_branch' => 0]);
        }
        if ($find_sale_type == 4) {
//            $sql .= " AND t2.order_channel_id > 0";
//            $sql .= " AND t4.is_other_branch = 1";
            $model_line = $model_line->andFilterWhere(['>', 'order_channel_id', 0])->andFilterWhere(['is_other_branch' => 1]);
        }
    }

    $model_line = $model_line->groupBy(['id'])->orderBy(['id' => SORT_DESC])->all();

}

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
            font-size: 18px;
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
<div>

    <form action="<?= \yii\helpers\Url::to(['pos/printsummarydindang'], true) ?>" method="post" id="form-search">
        <input type="hidden" class="btn-order-type" name="btn_order_type" value="<?= $btn_order_type ?>">
        <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
            <tr>
                <td style="padding: 10px;"><span>เรียงตาม <div class="btn-group"><div
                                    class="btn btn-sm <?= $btn_order_type == 1 ? "btn-success" : "btn-default" ?> btn-order-date">วันที่ขาย</div><div
                                    class="btn btn-sm <?= $btn_order_type == 2 ? "btn-success" : "btn-default" ?> btn-order-price">ราคาขาย</div></div></span>
                </td>
            </tr>
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
                <td>
                    <select name="find_sale_type" class="form-control" id="">
                        <option value="0">--ประเภทขาย--</option>
                        <option value="1" <?php if ($find_sale_type == 1) {
                            echo "selected";
                        } ?>>ขายสด
                        </option>
                        <option value="2" <?php if ($find_sale_type == 2) {
                            echo "selected";
                        } ?>>ขายเชื่อหน้าบ้าน
                        </option>
                        <option value="3" <?php if ($find_sale_type == 3) {
                            echo "selected";
                        } ?>>ขายเชื่อรถ
                        </option>
                        <option value="4" <?php if ($find_sale_type == 4) {
                            echo "selected";
                        } ?>>ขายเชื่อรถต่างสาขา
                        </option>
                    </select>
                </td>
                <td>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_user_id',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\User::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'username'),
                        'value' => $find_user_id,
                        'options' => [
                            'placeholder' => '--พนักงานขาย--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ]);
                    ?>
                </td>
                <td>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'is_invoice_req',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\helpers\CustomerInvoiceReqType::asArrayObject(), 'id', 'name'),
                        'value' => $is_invoice_req,
                        'options' => [
                            'placeholder' => '--ทั้งหมด--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
                        ]
                    ]);
                    ?>
                </td>
                <td>
                    <input type="submit" class="btn btn-primary" value="ค้นหา">
                </td>
                <td style="width: 25%"></td>
            </tr>
        </table>
    </form>
    <br/>
    <div id="div1">
        <table class="table-header" width="100%">
            <tr>
                <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานยอดขายแยกตามประเภทสินค้า</td>
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
        <table class="table-header" width="100%">
        </table>
        <table class="table-title" id="table-data" style="width: 100%">
            <tr>
                <td style="border: 1px solid grey;text-align: center;"><b>ลำดับ</b></td>
                <td style="text-align: center;border: 1px solid grey">
                    <b>เลขที่ขาย</b></td>

                <td style="text-align: center;border: 1px solid grey">
                    <b>วันที่</b></td>
                <td style="text-align: center;border: 1px solid grey">
                    <b>ลูกค้า</b></td>
                <?php for ($y = 0; $y <= count($product_header_2) - 1; $y++): ?>
                    <td style="text-align: center;border: 1px solid grey"><?= \backend\models\Product::findName($product_header_2[$y]) ?></td>
                <?php endfor; ?>

                <td style="text-align: right;border: 1px solid grey"><b>จำนวน</b>
                </td>
                <td style="text-align: right;border: 1px solid grey"><b>จำนวนเงิน</b></td>
            </tr>
            <?php
            $sum_qty_all = 0;
            $sum_total_all = 0;
            $loop_num = 0;
            $total_all_line_qty_data = [];
            $line_total_amt = 0;
            $line_all_amt = 0;
            $total_all_qty = 0;
            $line_qty = 0;
            $product_list_text = '';
            for($m=0;$m<=count($product_header_2)-1;$m++){
                if($m < count($product_header_2)-1){
                    $product_list_text .= $product_header_2[$m][0].',';
                }else{
                    $product_list_text .= $product_header_2[$m][0];
                }
            }

            ?>
            <?php if ($model_line != null): ?>
                <?php foreach ($model_line as $value): ?>
                    <?php
                    $loop_num += 1;
                    $line_total_qty = 0;
                    $line_total_amt = 0;
//                    echo '<pre>'    ;
//                    print_r($product_header_2);
//                    echo '</pre>'    ;

                $customer_name = '';
                if($value->customer_id != null){
                    $customer_name = \backend\models\Customer::findName($value->customer_id);
                }else{
                    $customer_name = \backend\models\Deliveryroute::findName($value->order_channel_id);
                }


                    $main_order_new = getOrderMain($value->id, $product_list_text);
                    ?>
                    <tr>
                        <td style="text-align: center;border: 1px solid grey"><?= $loop_num ?></td>
                        <td style="text-align: center;border: 1px solid grey">
                            <?= $value->order_no; ?>
                        </td>
<<<<<<< HEAD
                        <td style="text-align: center;border: 1px solid grey"><?= $value->name; ?></td>
=======
                        <td style="text-align: center;border: 1px solid grey">
                            <?= date('d-m-Y H:i:s', strtotime($value->order_date)); ?>
                        </td>
                        <td style="text-align: center;border: 1px solid grey">
                            <?= $customer_name; ?>
                        </td>
>>>>>>> f70eadc9231dd4b061446a19f05151bdff36870c
                        <?php for ($k = 0; $k <= count($product_header_3) - 1; $k++): ?>
                            <?php

                            $product_line_qty = 0;
                            $product_line_amt = 0;
//                            $getorder_data = getOrderNew($value->id, $product_header_3[$k][0]);
////                            $product_line_qty = getOrderQty2($value->id, $product_header_2[$y]);
////                            $product_line_amt = getOrderAmount($value->id, $product_header_2[$y]);
//                            if ($getorder_data != null) {
//                                $product_line_qty = $getorder_data[0]['qty'];
//                                $product_line_amt = $getorder_data[0]['amount'];
//                            }

                            if($main_order_new!=null){
                                for($a=0;$a<=count($main_order_new)-1;$a++){
                                    if($main_order_new[$a]['product_id']==$product_header_3[$k][0]){
                                        $product_line_qty = $main_order_new[$a]['qty'];
                                        $product_line_amt = $main_order_new[$a]['amount'];
                                    }
                                }
                            }

                            $line_total_qty = ($line_total_qty + $product_line_qty);
                            $total_all_qty = ($total_all_qty + $product_line_qty);
                            $line_qty = $line_qty + $product_line_qty;

                            $line_total_amt = ($line_total_amt + $product_line_amt);
                            $line_all_amt = ($line_all_amt + $product_line_amt);

                            if ($loop_num == 1) {
                                array_push($total_all_line_qty_data, ['product_id' => $product_header_3[$k][0], 'qty' => $product_line_qty]);
                            } else {
                                foreach ($total_all_line_qty_data as $key => $val) {
                                    if ($total_all_line_qty_data[$key]['product_id'] == $product_header_3[$k][0]) {
                                        $total_all_line_qty_data[$key]['qty'] = $val['qty'] + $product_line_qty;
                                    }
                                }
                            }
                            ?>
                            <td style="text-align: center;border: 1px solid grey"><?= $product_line_qty > 0 ? number_format($product_line_qty, 1) : '-' ?></td>
<!--                            <td style="text-align: center;border: 1px solid grey">--><?php //= (int)$product_header_3[$x] ?><!--</td>-->
                        <?php endfor; ?>
                        <td style="text-align: right;border: 1px solid grey">
                            <b><?= number_format($line_total_qty, 2) ?></b></td>
                        <td style="text-align: right;border: 1px solid grey">
                            <b><?= number_format($line_total_amt, 2) ?></b></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tfoot>
            <tr>
<<<<<<< HEAD
                <td style="font-size: 16px;border: 1px solid grey"></td>
                <td colspan="2" style="font-size: 16px;border: 1px solid grey;text-align: center;"><b>รวมทั้งสิ้น</b></td>
=======
                <td colspan="3" style="font-size: 16px;border: 1px solid grey"></td>
                <td style="font-size: 16px;border: 1px solid grey;text-align: center;"><b>รวมทั้งสิ้น</b></td>
>>>>>>> f70eadc9231dd4b061446a19f05151bdff36870c
                <?php for ($z = 0; $z <= count($total_all_line_qty_data) - 1; $z++): ?>
                    <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                        <b><?= $total_all_line_qty_data[$z]['qty'] > 0 ? number_format($total_all_line_qty_data[$z]['qty'], 1) : '-' ?></b>
                    </td>
                <?php endfor; ?>
                <td style="font-size: 18px;text-align: right;border: 1px solid grey">
                    <b><?= number_format($total_all_qty, 2) ?></b></td>
                <td style="font-size: 18px;text-align: right;border: 1px solid grey">
                    <b><?= number_format($line_all_amt, 2) ?></b></td>
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

    <br/>
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
</div>
</body>
</html>

<?php
function getOrderMain($order_id,$product_list_text)
{
    $data = [];
    $sql = "SELECT product_id,sum(qty) as qty,sum(line_total) as line_total FROM order_line WHERE order_id=" . $order_id. " AND product_id in(" . $product_list_text . ")" . " group by product_id";

    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {

            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'qty' => $model[$i]['qty'],
                'amount' => $model[$i]['line_total'],
            ]);
        }
    }
    return $data;
}
function getOrderNew($order_id,$product_id)
{
    $data = [];
    $sql = "SELECT sum(qty) as qty,sum(line_total) as line_total FROM order_line WHERE order_id=" . $order_id. " AND product_id=" . $product_id. " group by product_id";

    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {

            array_push($data, [
                'qty' => $model[$i]['qty'],
                'amount' => $model[$i]['line_total'],
            ]);
        }
    }
    return $data;
}

function getOrderQty2($order_id, $product_id)
{
    $data = 0;
    if ($order_id) {
        $model_qty = \backend\models\Orderline::find()->where(['order_id' => $order_id, 'product_id' => $product_id])->sum('qty');
        if ($model_qty) {
            $data = $model_qty;
//           foreach($model_qty as $value){
//            //   $name = \backend\models\Product::findCode($value->product_id);
//               array_push($data,['product_name'=>$name,'qty'=>$value->qty]);
//           }
        }
    }
    return $data;
}

function getOrderAmount($order_id, $product_id)
{
    $data = 0;
    if ($order_id) {
        $model_amount = \backend\models\Orderline::find()->where(['order_id' => $order_id, 'product_id' => $product_id])->sum('line_total');
        if ($model_amount) {
            $data = $model_amount;
//           foreach($model_qty as $value){
//            //   $name = \backend\models\Product::findCode($value->product_id);
//               array_push($data,['product_name'=>$name,'qty'=>$value->qty]);
//           }
        }
    }
    return $data;
}

function getOrder($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type)
{
    $data = [];
    $sql = "SELECT t2.order_no, t3.code , t3.name, t1.qty, t1.price, t2.order_date, t2.order_channel_id, t1.line_total 
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id LEFT JOIN delivery_route as t4 on t2.order_channel_id = t4.id
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . " 
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t2.sale_channel_id = 2
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

    if ($find_sale_type != null && $find_sale_type != 0) {
        if ($find_sale_type == 1) {
            $sql .= " AND t2.payment_method_id=" . $find_sale_type;
        }
        if ($find_sale_type == 2) {
            $sql .= " AND (t2.order_channel_id = 0 OR t2.order_channel_id is null) AND t2.payment_method_id=" . $find_sale_type;
        }
        if ($find_sale_type == 3) {
            $sql .= " AND t2.order_channel_id > 0";
            $sql .= " AND t4.is_other_branch = 0";
        }
        if ($find_sale_type == 4) {
            $sql .= " AND t2.order_channel_id > 0";
            $sql .= " AND t4.is_other_branch = 1";
        }
    }
    if ($find_user_id != null) {
        $sql .= " AND t2.created_by=" . $find_user_id;
    }
    if ($is_invoice_req != null) {
        $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
    }
    // $sql .=" ORDER BY t1.price ASC";
    if ($btn_order_type == 1) {
        $sql .= " ORDER BY t2.order_no ASC";
    } else if ($btn_order_type == 2) {
        $sql .= " ORDER BY t1.price ASC";
    } else {
        $sql .= " ORDER BY t2.order_no ASC";
    }

    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $customer_code = $model[$i]['code'];
            $customer_name = $model[$i]['name'];
            if ($model[$i]['code'] == null) {
                $customer_code = \backend\models\Deliveryroute::findCode($model[$i]['order_channel_id']);
                $customer_name = \backend\models\Deliveryroute::findName($model[$i]['order_channel_id']);
            }

            array_push($data, [
                'order_no' => $model[$i]['order_no'],
                'cus_code' => $customer_code,
                'cus_name' => $customer_name,
                'qty' => $model[$i]['qty'],
                'sale_price' => $model[$i]['price'],
                'line_total' => $model[$i]['line_total'],
                'order_date' => $model[$i]['order_date'],
            ]);
        }
    }
    return $data;
}

?>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $("#btn-export-excel").click(function(){
  $("#table-data").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Excel Document Name"
  });
});
$(".btn-order-date").click(function(){
    $(".btn-order-type").val(1);
    if($(".btn-order-price").hasClass("btn-success")){
        $(".btn-order-price").removeClass("btn-success");
        $(".btn-order-price").addClass("btn-default");
    }
    if($(this).hasClass("btn-default")){
        $(this).removeClass("btn-default")
        $(this).addClass("btn-success");
    }
    
});
$(".btn-order-price").click(function(){
    $(".btn-order-type").val(2);
      if($(".btn-order-date").hasClass("btn-success")){
        $(".btn-order-date").removeClass("btn-success");
        $(".btn-order-date").addClass("btn-default");
    }
    if($(this).hasClass("btn-default")){
        $(this).removeClass("btn-default")
        $(this).addClass("btn-success");
    }
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
