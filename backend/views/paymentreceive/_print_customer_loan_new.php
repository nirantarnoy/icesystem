<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use common\models\QuerySaleorderByCustomerLoanSumNew;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
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
if ($is_find_date == 1) {
   // echo "ok";
    if ($find_customer_id != null) {
//$customer_name = $trans_data[0]['customer_id']?getCustomername($connect, $trans_data[0]['customer_id']):$trans_data[0]['customer_name'];
//$model_customer_loan = \common\models\QueryProductTransDaily::find()->where(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
//$model_customer_loan = \common\models\QuerySalePosData::find()->where(['BETWEEN', 'order_date', date('Y-m-d H:i', strtotime($from_date)), date('Y-m-d H:i', strtotime($to_date))])

        if ($is_find_date == 1) {
            $model_customer_loan = \common\models\QuerySalePosData::find()->select(['customer_id'])->where(['BETWEEN', 'order_date', date('Y-m-d H:i', strtotime($from_date)), date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->groupBy('customer_id')->orderBy(['customer_id' => SORT_ASC])->all();
        } else {
            $model_customer_loan = \common\models\QuerySalePosData::find()->select(['customer_id'])->where(['<=', 'order_date', date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['IN', 'customer_id', $find_customer_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->groupBy('customer_id')->orderBy(['customer_id' => SORT_ASC])->all();
        }

    } else {
        if ($is_find_date == 1) {
           // echo "ok";
            $model_customer_loan = \common\models\QuerySalePosData::find()->select(['customer_id'])->where(['BETWEEN', 'order_date', date('Y-m-d H:i', strtotime($from_date)), date('Y-m-d H:i', strtotime($to_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                ->andFilterWhere(['>', 'line_total_credit', 0])
                ->groupBy('customer_id')->orderBy(['customer_id' => SORT_ASC])->all();
               // ->groupBy('customer_id')->orderBy(['customer_id' => SORT_ASC])->all();
        } else { // อ้อมน้อยช้า comment ไว้ก่อน
//        $model_customer_loan = \common\models\QuerySalePosData::find()->select(['customer_id'])->where(['<=', 'order_date', date('Y-m-d H:i', strtotime($to_date))])
//            ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
//            ->andFilterWhere(['>', 'line_total_credit', 0])
//            ->groupBy('customer_id')->orderBy(['customer_id' => SORT_ASC])->all();
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

<form action="<?= \yii\helpers\Url::to(['paymentreceive/customerloanprint'], true) ?>" method="post" id="form-search">
    <div id="div1">
        <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
            <tr>

                <!--                <td style="width: 20%">-->
                <!--                    --><?php
                //                    echo DateRangePicker::widget([
                //                        'name' => 'from_date',
                //                        // 'value'=>'2015-10-19 12:00 AM',
                //                        'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                //                        //    'useWithAddon'=>true,
                //                        'convertFormat' => true,
                //                        'options' => [
                //                            'class' => 'form-control',
                //                            'placeholder' => 'ถึงวันที่',
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
                <td style="width: 20%">
                    <div class="label">ตั้งแต่วันที่</div>
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
                    <div class="label">ถึงวันที่</div>
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
                    <div class="label">ลูกค้า</div>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_customer_id',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_show_pos' => 1,'status'=>1])->all(), 'id', 'name'),
                        'value' => $find_customer_id,
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
                <td>
                    <input type="submit" class="btn btn-primary" style="margin-top: 35px;" value="ค้นหา">
                </td>
                <td style="width: 25%"></td>
            </tr>
        </table>
</form>
<br/>
<table class="table-header" width="100%">
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานสรุปยอดหนี้ลูกค้าขายเชื่อ</td>
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
        <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>เลขเอกสาร</b></td>
        <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"></td>
        <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>จำนวนเงิน</b></td>
        <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>ชำระแล้ว</b></td>
        <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>ค้างชำระ</b></td>
        <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"></td>
    </tr>
    <?php
    $sum_line_total_all = 0;
    $sum_line_pay_all = 0;
    $line_remain_all = 0;
    ?>
    <?php if ($model_customer_loan != null): ?>
        <?php foreach ($model_customer_loan as $value): ?>
            <?php
            $line_customer_code = \backend\models\Customer::findCode($value->customer_id);
            $line_customer_name = \backend\models\Customer::findName($value->customer_id);

            $check_is_car_customer = \backend\models\Customer::find()->select(['delivery_route_id'])->where(['id' => $value->customer_id])->one();
            if (!$check_is_car_customer) {
                continue;
                // if($check_is_car_customer->delivery_route_id <= 0)continue;
            }
            ?>

            <tr data-var="">
                <td colspan="5"><b><?= $line_customer_code ?></b> <span
                            style="color: darkblue"> <?= $line_customer_name ?></span></td>
            </tr>
            <?php $find_order = getOrder($value->customer_id, $from_date, $to_date, $company_id, $branch_id, $is_find_date); ?>
            <?php if ($find_order != null): ?>
                <?php
                $loop_count = count($find_order);
                $x = 0;
                $sum_line_total = 0;
                $sum_line_pay = 0;
                $line_remain = 0;

                ?>
                <?php for ($i = 0; $i <= count($find_order) - 1; $i++): ?>
                    <?php
                    $x += 1;
                    $sum_line_total += $find_order[$i]['total_credit'];
                    $sum_line_pay += $find_order[$i]['total_pay'];
                    $line_remain = $sum_line_total - $sum_line_pay;

                    $line_remain_amount = $find_order[$i]['total_credit'] - $find_order[$i]['total_pay'];

                    if ($line_remain_amount < 0) {
                        $line_remain_amount = 0;
                    }
//
                    $sum_line_total_all += $find_order[$i]['total_credit'];
                    $sum_line_pay_all += $find_order[$i]['total_pay'];
                    $line_remain_all += ($find_order[$i]['total_credit'] - $find_order[$i]['total_pay']);

                    $background_color = '';
                    if (number_format($find_order[$i]['total_credit'], 2) <= number_format($find_order[$i]['total_pay'], 2)) {
                        $background_color = 'yellow';
                    }
                    ?>
                    <tr data-id="<?= $find_order[$i]['order_id'] ?>" style="background-color: <?= $background_color ?>"
                        ondblclick="showdetail($(this))">
                        <td style="font-size: 16px;"><?= $find_order[$i]['order_no'] ?>
                            <input type="hidden" class="order-date"
                                   value="<?= date('d-m-Y H:i', strtotime($find_order[$i]['order_date'])) ?>">
                            <input type="hidden" class="order-customer" value="<?= $find_order[$i]['customer_name'] ?>">
                        </td>
                        <td style="font-size: 16px;"><?= date('Y-m-d H:i:s', strtotime($find_order[$i]['order_date'])) ?></td>
                        <td style="font-size: 16px;text-align: right;"><?= number_format($find_order[$i]['total_credit'], 2) ?></td>
                        <td style="font-size: 16px;text-align: right;color: green"><?= number_format($find_order[$i]['total_pay'], 2) ?></td>
                        <td style="font-size: 16px;text-align: right;color: red"><?= number_format($line_remain_amount, 2) ?></td>
                        <td style="text-align: center;font-size: 16px;text-align: right"><span
                                    class="btn btn-success btn-show-more"><i class="fa fa-plus-circle"></i> </span></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="padding: 1px;">
                            <table class="table-detail" style="width: 100%;background-color: lightgrey">
                                <tr>
                                    <td style="text-align: center">#</td>
                                    <td>เลขที่</td>
                                    <td>วันที่ชำระ</td>
                                    <td>จำนวน</td>
                                    <td>รับชำระโดย</td>
                                </tr>
                                <?php
                                $pay_detail = getPayline($find_order[$i]['order_id']);
                                ?>
                                <?php $nums = 0; ?>
                                <?php
                                for ($x = 0; $x <= count($pay_detail) - 1; $x++):
                                    ?>
                                    <?php $nums += 1; ?>
                                    <tr>
                                        <td style="color: blue;text-align: center"><?= $nums ?></td>
                                        <td style="color: blue"><?= $pay_detail[$x]['journal_no'] ?></td>
                                        <td style="color: blue"><?= $pay_detail[$x]['journal_date'] ?></td>
                                        <td style="color: blue"><?= $pay_detail[$x]['total_pay'] ?></td>
                                        <td style="color: blue"><?= $pay_detail[$x]['emp_name'] ?></td>
                                    </tr>
                                <?php endfor; ?>
                            </table>
                        </td>
                    </tr>
                    <?php if ($loop_count == $x): ?>
                        <tr>
                            <td style="font-size: 16px;border-top: 1px solid black"></td>
                            <td style="font-size: 16px;border-top: 1px solid black"></td>
                            <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                                <b><?= number_format($sum_line_total, 2) ?></b></td>
                            <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                                <b><?= number_format($sum_line_pay, 2) ?></b></td>
                            <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                                <b><?= number_format($line_remain, 2) ?></b></td>
                            <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
                        </tr>
                    <?php endif; ?>
                <?php endfor ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <tfoot>
    <tr>
        <td style="font-size: 16px;border-top: 1px solid black"></td>
        <td style="font-size: 18px;border-top: 1px solid black"><b>รวมทั้งสิ้น</b></td>
        <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b style="color: green"><?= number_format($sum_line_total_all, 2) ?></b></td>
        <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b><?= number_format($sum_line_pay_all, 2) ?></b></td>
        <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
            <b style="color: red"><?= number_format($line_remain_all, 2) ?></b></td>
        <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
    </tr>
    </tfoot>
</table>

</div>
<br/>
<table width="100%" class="table-title">
    <tr>
        <td>
            <a class="btn btn-info" href="<?= \yii\helpers\Url::to(['paymentreceive/possummaryupdate'], true) ?>">อัพเดทใบส่งของ</a>
        </td>
        <td style="text-align: right">
            <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
        </td>
    </tr>

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

<div id="findModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4>รายละเอียดคำสั่งซื้อ วันที่ <span style="color: #0f6674" class="order-data"></span></h4>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list" width="100%">
                    <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th style="text-align: right">จำนวน</th>
                        <th style="text-align: right">ราคา</th>
                        <th style="text-align: right">รวม</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>

</body>
</html>

<?php
function getOrder($customer_id, $f_date, $t_date, $company_id, $branch_id, $is_find_date)
{
//    WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//    AND t2.sale_channel_id = 1
    $data = [];
    if ($customer_id != null) {
        if ($is_find_date == 0 || $is_find_date == null) {
            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_pos_data as t2
             WHERE t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t2.customer_id =" . $customer_id . " 
             AND t2.sale_channel_id = 2
             AND t2.payment_method_id = 2
           
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.order_date DESC";
        } else {

            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_pos_data as t2
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "AND t2.customer_id =" . $customer_id . " 
             AND t2.sale_channel_id = 2
             AND t2.payment_method_id = 2
             
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.order_date DESC";
        }
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                array_push($data, [
                    'order_id' => $model[$i]['id'],
                    'order_no' => $model[$i]['order_no'],
                    'order_date' => $model[$i]['order_date'],
                    'total_credit' => $model[$i]['total_credit'],
                    'total_pay' =>  getPaytrans($model[$i]['id']),
                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                ]);
            }
        }
    } else {
        if ($is_find_date == 0 || $is_find_date == null) {
            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_pos_data as t2
             WHERE t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t2.sale_channel_id = 2
             AND t2.payment_method_id = 2
        
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.order_date DESC";
        } else {

            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_pos_data as t2
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
             AND t2.sale_channel_id = 2
             AND t2.payment_method_id = 2
        
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.order_date DESC";
        }
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                array_push($data, [
                    'order_id' => $model[$i]['id'],
                    'order_no' => $model[$i]['order_no'],
                    'order_date' => $model[$i]['order_date'],
                    'total_credit' => $model[$i]['total_credit'],
                    'total_pay' =>  getPaytrans($model[$i]['id']),
                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                ]);
            }
        }
    }

    return $data;
}
//function getOrder($customer_id, $f_date, $t_date, $company_id, $branch_id, $is_find_date)
//{
////    WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
////AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//
//    $data = [];
//    if ($customer_id != null || $customer_id != 0) {
//
//        if ($is_find_date == 0 || $is_find_date == null) {
//            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
//              FROM query_sale_pos_data as t2
//             WHERE t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.customer_id =" . $customer_id . "
//             AND t2.sale_channel_id = 2
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
//            $sql .= " ORDER BY t2.order_date DESC";
//        } else {
//            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
//             FROM query_sale_pos_data as t2
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.customer_id =" . $customer_id . "
//             AND t2.sale_channel_id = 2
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
//            $sql .= " ORDER BY t2.order_date DESC";
//        }
//
//
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//
//                array_push($data, [
//                    'order_id' => $model[$i]['id'],
//                    'order_no' => $model[$i]['order_no'],
//                    'order_date' => $model[$i]['order_date'],
//                    'total_credit' => $model[$i]['total_credit'],
//                    'total_pay' => getPaytrans($model[$i]['id']),
//                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
//                ]);
//            }
//        }
//    } else {
//        if ($is_find_date == 0 || $is_find_date == null) {
//            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
//              FROM query_sale_pos_data as t2
//             WHERE t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.sale_channel_id = 2
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
//            $sql .= " ORDER BY t2.order_date DESC";
//        } else {
//            $sql = "SELECT t2.id, t2.order_no,t2.customer_id, t2.order_date , sum(t2.line_total_credit) as total_credit
//             FROM query_sale_pos_data as t2
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.sale_channel_id = 2
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//            $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
//            $sql .= " ORDER BY t2.order_date DESC";
//        }
//
//
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//
//                array_push($data, [
//                    'order_id' => $model[$i]['id'],
//                    'order_no' => $model[$i]['order_no'],
//                    'order_date' => $model[$i]['order_date'],
//                    'total_credit' => $model[$i]['total_credit'],
//                    'total_pay' => getPaytrans($model[$i]['id']),
//                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
//                ]);
//            }
//        }
//    }
//
//    return $data;
//}

//function getOrder($customer_id, $f_date, $t_date, $company_id, $branch_id)
//{
//
//    $data = [];
//    if ($customer_id != null) {
//        $sql = "SELECT t2.id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
//              FROM query_sale_pos_data as t2
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.customer_id =" . $customer_id . "
//             AND t2.sale_channel_id = 2
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//        $sql .= " GROUP BY t2.id, t2.order_no, t2.order_date";
//        $sql .= " ORDER BY t2.order_no ASC";
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//
//                array_push($data, [
//                    'order_id' => $model[$i]['id'],
//                    'order_no' => $model[$i]['order_no'],
//                    'order_date' => $model[$i]['order_date'],
//                    'total_credit' => $model[$i]['total_credit'],
//                    'total_pay' => getPaytrans($model[$i]['id']),
//                ]);
//            }
//        }
//    }
//
//    return $data;
//}

function getPaytrans($order_id)
{
    $pay_total = 0;
    if ($order_id) {
//        $model = \common\models\PaymentReceiveLine::find()->where(['order_id' => $order_id])->sum('payment_amount');
//        if ($model) {
//            $pay_total = $model;
//        }
        $sql = "SELECT sum(payment_amount) as payment_amount
              FROM query_payment_receive_for_pos_credit WHERE
              order_id =" . $order_id;
        $sql .= " GROUP BY order_id";
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

function getPayline($order_id)
{
    $data = [];
    if ($order_id) {
        $model = \common\models\PaymentReceiveLine::find()->where(['order_id' => $order_id])->andFilterWhere(['>', 'payment_amount', 0])->all();
        if ($model) {
            foreach ($model as $value) {
                $payment_date = date('Y-m-d H:i');
                $find_date = \backend\models\Paymentreceive::findDate($value->payment_receive_id);
                $find_user = \backend\models\Paymentreceive::findEmpid($value->payment_receive_id);
                if ($find_date != '') {
                    $payment_date = $find_date;
                }
                array_push($data, [
                    'journal_no' => \backend\models\Paymentreceive::findNo($value->payment_receive_id),
                    'journal_date' => date('d/m/Y H:i', strtotime($payment_date)),
                    'total_pay' => number_format($value->payment_amount),
                    'emp_name' => \backend\models\User::findName($find_user),
                ]);
            }
        }
    }
    return $data;
}

?>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$url_to_find_order_detail = Url::to(['orders/getorderdetail'], true);
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
 });
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
function showdetail(e){
    var order_id = e.attr("data-id");
    var order_date = e.closest("tr").find(".order-date").val();
    var order_customer = e.closest("tr").find(".order-customer").val();
    if(order_id!=null){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_order_detail",
              'data': {'id': order_id},
              'success': function(data) {
                    //alert(data);
                    $(".table-find-list tbody").html(data);
                    $("#findModal").modal("show").find(".order-data").html(order_date +" ลูกค้า "+ order_customer);
                 }
              });   
    }
    
}     
JS;
$this->registerJs($js, static::POS_END);
?>
