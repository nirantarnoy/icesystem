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

$model_customer_loan = null;
if ($is_start_find == 1) {
    if ($find_customer_id != null) {
//$customer_name = $trans_data[0]['customer_id']?getCustomername($connect, $trans_data[0]['customer_id']):$trans_data[0]['customer_name'];
//$model_customer_loan = \common\models\QueryProductTransDaily::find()->where(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
//$model_customer_loan = \common\models\QuerySaleMobileDataNew::find()->where(['BETWEEN', 'order_date', date('Y-m-d H:i', strtotime($from_date)), date('Y-m-d H:i', strtotime($to_date))])

        if ($is_find_date == 1) {
            $model_customer_loan = \common\models\QuerySaleMobileDataNew::find()->select(['route_id', 'customer_id'])->where(['>=', 'order_date', date('Y-m-d H:i', strtotime($from_date))])
                ->andFilterWhere(['<=','order_date',date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['IN', 'route_id', $find_customer_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id_select])
                ->distinct(['route_id', 'customer_id'])->orderBy(['route_id' => SORT_ASC])->all();
            // ->groupBy(['route_id', 'customer_id'])->orderBy(['route_id' => SORT_ASC])->all();


        } else {
            $model_customer_loan = \common\models\QuerySaleMobileDataNew::find()->select(['route_id', 'customer_id'])->where(['>=', 'order_date', date('Y-m-d H:i', strtotime($from_date))])
                ->andFilterWhere(['<=','order_date',date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['IN', 'route_id', $find_customer_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id_select])
                ->groupBy(['route_id', 'customer_id'])->orderBy(['route_id' => SORT_ASC])->all();
        }


    } else { // not select route
        if ($is_find_date == 1) {
            $model_customer_loan = \common\models\QuerySaleMobileDataNew::find()->select(['route_id', 'customer_id'])->where(['>=', 'order_date', date('Y-m-d H:i', strtotime($from_date))])
                ->andFilterWhere(['<=','order_date',date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id_select])
                ->groupBy(['route_id', 'customer_id'])->orderBy(['route_id' => SORT_ASC])->all();
        } else {
            $model_customer_loan = \common\models\QuerySaleMobileDataNew::find()->select(['route_id', 'customer_id'])->where(['<=', 'order_date', date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id_select])
                ->groupBy(['route_id', 'customer_id'])->orderBy(['route_id' => SORT_ASC])->all();
        }
    }

    $user_login_datetime = '';
    $model_c_login = LoginLog::find()->select('MIN(login_date) as login_date')->where(['user_id' => $user_id, 'status' => 1])->one();
    if ($model_c_login != null) {
        $user_login_datetime = date('Y-m-d H:i:s', strtotime($model_c_login->login_date));
    } else {
        $user_login_datetime = date('Y-m-d H:i:s');
    }
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
<body>

<form action="<?= \yii\helpers\Url::to(['paymentreceivecar/customerloanprint'], true) ?>" method="post"
      id="form-search">
    <input type="hidden" name="is_start_find" value="1">
    <div id="div1">
        <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
            <tr>
                <td>
                    <?php
                    $is_checked = "";
                    if ($is_find_date != null && $is_find_date != 0) {
                        $is_checked = "checked";
                    }
                    //  echo $is_find_date;
                    ?>
                    <input type="checkbox" value="<?= $is_find_date ?>" onchange="$(this).val(1)"
                           name="is_find_date" <?= $is_checked ?>> ค้นหาตามช่วงเวลา
                </td>
            </tr>
            <tr>

                <!--                <td style="width: 20%">-->
                <!--                    <label for="">ตั้งแต่วันที่</label>-->
                <!--                    --><?php
                //                    echo DateRangePicker::widget([
                //                        'name' => 'from_date',
                //                        // 'value'=>'2015-10-19 12:00 AM',
                //                        'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                //                        //    'useWithAddon'=>true,
                //                        'convertFormat' => true,
                //                        'options' => [
                //                            'class' => 'form-control',
                //                            'placeholder' => 'ตั้งแต่',
                //                            //  'onchange' => 'this.form.submit();',
                //                            'autocomplete' => 'off',
                //                        ],
                //                        'pluginOptions' => [
                //                            'timePicker' => true,
                //                            'timePickerIncrement' => 1,
                //                            'locale' => ['format' => 'Y-m-d H:i'],
                //                            'singleDatePicker' => true,
                //                            'showDropdowns' => true,
                //                            'timePicker24Hour' => true
                //                        ]
                //                    ]);
                //                    ?>
                <!--                </td>-->

                <td style="width: 20%">
                    <label>ตั้งแต่วันที่</label>
                    <?php
                    echo DateRangePicker::widget([
                        'name' => 'from_date',
                        'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                        //    'useWithAddon'=>true,
                        'convertFormat' => true,
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'ตั้งแต่วันที่',
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
                    <label for="">ถึงวันที่</label>
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
                <td style="width: 20%">
                    <label for="">สายส่ง</label>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_customer_id',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'name'),
                        'value' => $find_customer_id,
                        'options' => [
                            'placeholder' => '--สายส่ง--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ]
                    ]);
                    ?>
                </td>
                <td style="width: 20%">
                    <label for="">ลูกค้า</label>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_customer_id_select',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all(), 'id', 'name'),
                        'value' => $find_customer_id_select,
                        'options' => [
                            'placeholder' => '--ลูกค้า--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => true,
                        ]
                    ]);
                    ?>
                </td>
                <td style="width: 20%">
                    <label for="">มี/ไม่มีรายละเอียด</label>
                    <select name="find_has_detail" class="form-control" id="">

                        <option value="0" <?=$find_has_detail ==0?'selected':''?>>ทั้งหมด</option>
                        <option value="1" <?=$find_has_detail ==1?'selected':''?>>มี</option>
                        <option value="2" <?=$find_has_detail ==2?'selected':''?>>ไม่มี</option>
                    </select>
                </td>
                <td>
                    <label for="" style="color: white">ค้นหา</label>
                    <input type="submit" class="btn btn-primary" style="margin-top: 0px;" value="ค้นหา">
                </td>
                <td style="width: 25%"></td>
            </tr>
        </table>
</form>
<br/>
<table class="table-header" width="100%">
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานสรุปยอดหนี้ลูกค้าขายเชื่อ(แยกสายส่ง)
        </td>
    </tr>
</table>
<br>
<table class="table-header" width="100%">
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: normal">
            <!--            จากวันที่ <span style="color: red">-->
            <?php ////echo date('Y-m-d H:i', strtotime($from_date)) ?><!--</span>-->
            ถึง <span style="color: red"><?= date('Y-m-d H:i', strtotime($to_date)) ?></span></td>
    </tr>
</table>
<br>
<table class="table-header" width="100%">
</table>
<table class="table-title" id="table-data" style="width: 100%">
    <tr>
        <td style="border: 1px solid gray;text-align: center"><b>ลำดับ</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>สายส่ง</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>เลขเอกสาร</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>วันที่</b></td>
        <td style="border: 1px solid gray;text-align: left"><b>ลูกค้า</b></td>
        <td style="border: 1px solid gray;text-align: center;"><b>รายละเอียด</b></td>
        <td style="text-align: right;border: 1px solid gray"><b>จำนวนเงิน</b></td>
        <td style="text-align: right;border: 1px solid gray"><b>ชำระแล้ว</b></td>
        <td style="text-align: right;border: 1px solid gray"><b>ค้างชำระ</b></td>
    </tr>
    <?php
    $sum_line_total_all = 0;
    $sum_line_pay_all = 0;
    $line_remain_all = 0;
    $line_nums = 0;
    $old_line_route = '';
    $curren_route = '';
    $route_index = 0;
    ?>
    <?php if ($model_customer_loan != null): ?>
        <?php foreach ($model_customer_loan as $value): ?>
            <?php
            $route_index += 1;
            $line_route_code = \backend\models\Deliveryroute::findCode($value->route_id);
            $line_route_name = \backend\models\Deliveryroute::findName($value->route_id);
            $curren_route = $value->route_id;
            if ($route_index == 1) {
                $old_line_route = $curren_route;
            }
            ?>
            <?php //$find_order = getOrder($value->route_id, $value->customer_id, $from_date, $to_date, $company_id, $branch_id); ?>
            <?php $find_order = getOrder($value->route_id, $value->customer_id, $to_date, $company_id, $branch_id, $is_find_date, $from_date,$find_has_detail); ?>
            <?php if ($find_order != null): ?>
                <?php
                $loop_count = count($find_order);
                $xx = 0;
                $sum_line_total = 0;
                $sum_line_pay = 0;
                $line_remain = 0;
                $old_line_customer = '';
                $curren_customer = '';
                $is_sum = 0;

                ?>
                <?php for ($i = 0; $i <= count($find_order) - 1; $i++): ?>
                    <?php
                    $xx += 1;
                    $line_nums += 1;


//
//                    if ((double)$find_order[$i]['total_credit'] - (double)$find_order[$i]['total_pay'] <= 0) {
//                        continue;
//                    }


                    $sum_line_total = ($sum_line_total + $find_order[$i]['total_credit']);
                    $sum_line_pay = ($sum_line_pay + $find_order[$i]['total_pay']);
                    $line_remain = ($line_remain + ($find_order[$i]['total_credit'] - $find_order[$i]['total_pay']));

                    $sum_line_total_all += $find_order[$i]['total_credit'];
                    $sum_line_pay_all += $find_order[$i]['total_pay'];
                    $line_remain_all += ($find_order[$i]['total_credit'] - $find_order[$i]['total_pay']);

                    ?>

                    <tr>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: center"><?= $line_nums ?> </td>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: center"><?= $line_route_name ?> </td>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: center"><?= $find_order[$i]['order_no'] ?> </td>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: center"><?= date('Y-m-d H:i:s', strtotime($find_order[$i]['order_date'])) ?></td>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: left"><?= $find_order[$i]['customer_name'] ?></td>
                        <td style="font-size: 16px;border: 1px solid gray;text-align: left"><?= $find_order[$i]['customer_detail'] ?></td>
                        <td style="font-size: 16px;text-align: right;border: 1px solid gray"><?= number_format($find_order[$i]['total_credit'], 2) ?></td>
                        <td style="font-size: 16px;text-align: right;color: green;border: 1px solid gray"><?= number_format($find_order[$i]['total_pay'], 2) ?></td>
                        <td style="font-size: 16px;text-align: right;color: red;border: 1px solid gray"><?= number_format(($find_order[$i]['total_credit'] - $find_order[$i]['total_pay']), 2) ?></td>
                    </tr>

                <?php endfor ?>

                <?php //if ($old_line_customer <> $curren_customer): ?>
                <?php if ($xx == count($find_order) && $sum_line_total > 0): ?>

                    <tr style="background-color: #1fc8e3">
                        <td colspan="6" style="font-size: 16px;border: 1px solid grey;text-align: right"><b>รวม</b></td>
                        <td style="font-size: 16px;text-align: right;border: 1px solid grey;">
                            <b><?= number_format($sum_line_total, 2) ?></b></td>
                        <td style="font-size: 16px;text-align: right;border: 1px solid grey;">
                            <b><?= number_format($sum_line_pay, 2) ?></b></td>
                        <td style="font-size: 16px;text-align: right;border: 1px solid grey;">
                            <b><?= number_format($line_remain, 2) ?></b></td>
                    </tr>
                    <?php
                    $is_sum = 1;
                    $sum_line_total = 0;
                    $sum_line_pay = 0;
                    $line_remain = 0;
                    ?>
                <?php else: ?>
                    <?php $is_sum = 0; ?>
                <?php endif; ?>
                <?php
//                    $sum_line_total += (double)$find_order[$i]['total_credit'];
//                    $sum_line_pay += (double)$find_order[$i]['total_pay'];
//                    $line_remain = $sum_line_total - $sum_line_pay;
//
//                    if ($is_sum == 1) {
//                        $sum_line_total = 0;
//                        $sum_line_pay = 0;
//                        $line_remain = 0;
//                    }

                ?>

            <?php endif; ?>
            <?php $old_line_route = $curren_route; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <tfoot>
    <tr>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 18px;border-top: 1px solid black"><b>รวมทั้งสิ้น</b></td>
        <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b style="color: green"><?= number_format($sum_line_total_all, 2) ?></b></td>
        <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b><?= number_format($sum_line_pay_all, 2) ?></b></td>
        <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b style="color: red"><?= number_format($line_remain_all, 2) ?></b></td>
    </tr>
    </tfoot>
</table>

</div>
<br/>
<table width="100%" class="table-title">
    <tr>
        <!--        <td>-->
        <!--            <a class="btn btn-info" href="-->
        <? //= \yii\helpers\Url::to(['paymentreceivecar/carsummaryupdate'], true) ?><!--">อัพเดทใบส่งของ</a>-->
        <!--        </td>-->
        <td>
            <a class="btn btn-info" href="<?= \yii\helpers\Url::to(['paymentreceivecar/carsummaryupdate'], true) ?>">อัพเดทใบส่งของ</a>
        </td>
        <td style="text-align: right">
            <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
        </td>
    </tr>

</table>
<!--<table width="100%" class="table-title">-->
<!--    <td style="text-align: right">-->
<!--        <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>-->
<!--        <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>-->
<!--    </td>-->
<!--</table>-->
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
function getOrder($route_id, $customer_id, $t_date, $company_id, $branch_id, $is_find_date, $f_date, $find_has_detail)
{
//    WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//    AND t2.sale_channel_id = 1
    $data = [];
    if ($route_id != null) {
        if ($is_find_date == 0 || $is_find_date == null) {
            $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_mobile_data_new2 as t2
              WHERE  t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
              AND t2.route_id =" . $route_id . "              
              AND t2.payment_method_id = 2
              AND t2.payment_status = 0;
              AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            if ($customer_id != null || $customer_id != '') {
                $sql .= " AND t2.customer_id=" . $customer_id;
            }

            $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";
        } else {
//            $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
//             FROM query_sale_mobile_data_new2 as t2
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.route_id =" . $route_id . "
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//            if ($customer_id != null || $customer_id != '') {
//                $sql .= " AND t2.customer_id=" . $customer_id;
//            }
//
//            $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
//            $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";

            $sql = "SELECT t2.id,t1.customer_id, t2.order_no, t2.order_date , sum(t1.price * t1.qty) as total_credit
             FROM order_line as t1 inner join orders as t2 on t1.order_id = t2.id
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
             AND t2.order_channel_id =" . $route_id . "
             AND t2.payment_method_id = 2
             AND t1.line_total > 0
             AND t2.payment_status = 0
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;


            if ($customer_id != null || $customer_id != '') {
                $sql .= " AND t1.customer_id=" . $customer_id;
            }


            $sql .= " GROUP BY t2.id,t1.customer_id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t1.customer_id, t2.order_date,t2.order_no ";
        }
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                if($find_has_detail == 0){
                    array_push($data, [
                        'order_id' => $model[$i]['id'],
                        'customer_id' => $model[$i]['customer_id'],
                        'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                        'customer_detail' => \backend\models\Customer::findDescription($model[$i]['customer_id']),
                        'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_credit' => $model[$i]['total_credit'],
                        'total_pay' => 0,// getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
                    ]);
                }
                if($find_has_detail == 1){
                    $line_detail = \backend\models\Customer::findDescription($model[$i]['customer_id']);
                    if($line_detail == '' || $line_detail == null)continue;
                    array_push($data, [
                        'order_id' => $model[$i]['id'],
                        'customer_id' => $model[$i]['customer_id'],
                        'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                        'customer_detail' => $line_detail,
                        'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_credit' => $model[$i]['total_credit'],
                        'total_pay' => 0,// getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
                    ]);
                }
                if($find_has_detail == 2){
                    $line_detail = \backend\models\Customer::findDescription($model[$i]['customer_id']);
                    if($line_detail != '' || $line_detail != null)continue;
                    array_push($data, [
                        'order_id' => $model[$i]['id'],
                        'customer_id' => $model[$i]['customer_id'],
                        'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                        'customer_detail' => $line_detail,
                        'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_credit' => $model[$i]['total_credit'],
                        'total_pay' => 0,// getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
                    ]);
                }

            }
        }
    } else {

    }

    return $data;
}

//function getOrder($route_id, $customer_id, $t_date, $company_id, $branch_id)
//{
//    //  WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//    // AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
////    AND t2.sale_channel_id = 1
//    $data = [];
//    if ($route_id != null) {
//        $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
//              FROM query_sale_mobile_data_new2 as t2
//              WHERE  t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.route_id =" . $route_id . "
//
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//        $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
//        $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//
//                array_push($data, [
//                    'order_id' => $model[$i]['id'],
//                    'customer_id' => $model[$i]['customer_id'],
//                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
//                    'order_no' => $model[$i]['order_no'],
//                    'order_date' => $model[$i]['order_date'],
//                    'total_credit' => $model[$i]['total_credit'],
//                    'total_pay' => getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
//                ]);
//            }
//        }
//    }
//
//    return $data;
//}

function getPaytrans($order_id, $customer_id)
{
    $pay_total = 0;
    if ($order_id && $customer_id) {
//        $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->sum('payment_amount');
//        $pay_total = $model;
        $sql = "SELECT sum(payment_amount) as payment_amount
              FROM query_payment_receive_for_credit WHERE
              order_id =" . $order_id . "    
              AND customer_id=" . $customer_id;
        $sql .= " GROUP BY order_id,customer_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $pay_total = $model[$i]['payment_amount'];
            }
        }
    }
    return $pay_total;
}

function getPayline($order_id, $customer_id)
{
    $data = [];
    if ($order_id) {
        $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->andFilterWhere(['>', 'payment_amount', 0])->all();
        if ($model) {
            foreach ($model as $value) {
                array_push($data, [
                    'journal_no' => $value->journal_no,
                    'journal_date' => date('d/m/Y H:i:', strtotime($value->trans_date)),
                    'total_pay' => number_format($value->payment_amount),
                ]);
            }
        }
    }
    return $data;
}

?>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $(function(){
     $("td[colspan=6]").find(".table-detail").hide();
    $(".btn-show-more").click(function(event) {
        event.stopPropagation();
        var target = $(event.target);
        if ( target.closest("td").attr("colspan") > 1 ) {
            target.slideUp();
        } else {
            target.closest("tr").next().find(".table-detail").slideToggle();
        }                    
    });
    
    $("#btn-export-excel").click(function(){
          $("#table-data").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Excel Document Name"
          });
    });
    //$(".table-payment-line >tbody >tr").each(function(e,tr){
     //  var tr_line_pay = $(this).attr('data-var');
     //  console.log(tr_line_pay);
     //  if (typeof tr_line_pay === "undefined") {
     //       e.parent().parent().parent().hide();
     //   }
   // });
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
