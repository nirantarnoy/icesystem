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

$model_line = \common\models\TransactionPosSaleSum::find()->select(['user_id', 'shift', 'date(login_datetime) as login_datetime'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
    ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['shift' => SORT_ASC])->groupBy('shift')->all();


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
        <form action="<?= \yii\helpers\Url::to(['adminreport/posdaily'], true) ?>" method="post" id="form-search">
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
        <!--        <form action="--><? //= \yii\helpers\Url::to(['site/transaction'], true) ?><!--" method="post">-->
        <!--            <button class="btn btn-outline-success">-->
        <!--                <i class="fa fa-refresh"></i> ประมวลผล-->
        <!--            </button>-->
        <!--        </form>-->

    </div>
</div>

<br/>
<div id="div1">
    <table class="table-header" width="100%">
        <tr>
            <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานสรุปขายหน้าบ้าน</td>
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

    $sum_date = null;

    $product_header = [];


    $modelx = \common\models\TransactionPosSaleSum::find()->select(['product_id'])->join('inner join', 'product', 'transaction_pos_sale_sum.product_id=product.id')->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id])->groupBy('product_id')->orderBy(['item_pos_seq' => SORT_ASC])->all();

    if ($modelx) {
        foreach ($modelx as $valuexx) {
            if (!in_array($valuexx->product_id, $product_header)) {
                array_push($product_header, $valuexx->product_id);
            }
        }
    }

    // print_r($product_header);


    ?>
    <table id="table-data">

        <?php foreach ($model_line as $line_value): ?>

            <?php

            $sum_date = $line_value->login_datetime;

            $balance_in_qty[0] = 0;
            $balance_in_qty[1] = 0;
            $balance_in_qty[2] = 0;
            $balance_in_qty[3] = 0;
            $balance_in_qty[4] = 0;
            $balance_in_qty[5] = 0;
            $balance_in_qty[6] = 0;
            $balance_in_qty[7] = 0;
            $balance_in_qty[8] = 0;
            $balance_in_qty[9] = 0;
            $balance_in_qty[10] = 0;
            $balance_in_qty[11] = 0;
            $balance_in_qty[12] = 0;
            $balance_in_qty[13] = 0;
            $balance_in_qty[14] = 0;

            $prodrec_qty[0] = 0;
            $prodrec_qty[1] = 0;
            $prodrec_qty[2] = 0;
            $prodrec_qty[3] = 0;
            $prodrec_qty[4] = 0;
            $prodrec_qty[5] = 0;
            $prodrec_qty[6] = 0;
            $prodrec_qty[7] = 0;
            $prodrec_qty[8] = 0;
            $prodrec_qty[9] = 0;
            $prodrec_qty[10] = 0;
            $prodrec_qty[11] = 0;
            $prodrec_qty[12] = 0;
            $prodrec_qty[13] = 0;
            $prodrec_qty[14] = 0;

            $prodrec_re_qty[0] = 0;
            $prodrec_re_qty[1] = 0;
            $prodrec_re_qty[2] = 0;
            $prodrec_re_qty[3] = 0;
            $prodrec_re_qty[4] = 0;
            $prodrec_re_qty[5] = 0;
            $prodrec_re_qty[6] = 0;
            $prodrec_re_qty[7] = 0;
            $prodrec_re_qty[8] = 0;
            $prodrec_re_qty[9] = 0;
            $prodrec_re_qty[10] = 0;
            $prodrec_re_qty[11] = 0;
            $prodrec_re_qty[12] = 0;
            $prodrec_re_qty[13] = 0;
            $prodrec_re_qty[14] = 0;
            ?>
            <?php
            $sale_total_qty[0] = 0;
            $sale_total_qty[1] = 0;
            $sale_total_qty[2] = 0;
            $sale_total_qty[3] = 0;
            $sale_total_qty[4] = 0;
            $sale_total_qty[5] = 0;
            $sale_total_qty[6] = 0;
            $sale_total_qty[7] = 0;
            $sale_total_qty[8] = 0;
            $sale_total_qty[9] = 0;
            $sale_total_qty[10] = 0;
            $sale_total_qty[11] = 0;
            $sale_total_qty[12] = 0;
            $sale_total_qty[13] = 0;
            $sale_total_qty[14] = 0;


            $sale_refill_qty[0] = 0;
            $sale_refill_qty[1] = 0;
            $sale_refill_qty[2] = 0;
            $sale_refill_qty[3] = 0;
            $sale_refill_qty[4] = 0;
            $sale_refill_qty[5] = 0;
            $sale_refill_qty[6] = 0;
            $sale_refill_qty[7] = 0;
            $sale_refill_qty[8] = 0;
            $sale_refill_qty[9] = 0;
            $sale_refill_qty[10] = 0;
            $sale_refill_qty[11] = 0;
            $sale_refill_qty[12] = 0;
            $sale_refill_qty[13] = 0;
            $sale_refill_qty[14] = 0;

            $scrap_total_qty[0] = 0;
            $scrap_total_qty[1] = 0;
            $scrap_total_qty[2] = 0;
            $scrap_total_qty[3] = 0;
            $scrap_total_qty[4] = 0;
            $scrap_total_qty[5] = 0;
            $scrap_total_qty[6] = 0;
            $scrap_total_qty[7] = 0;
            $scrap_total_qty[8] = 0;
            $scrap_total_qty[9] = 0;
            $scrap_total_qty[10] = 0;
            $scrap_total_qty[11] = 0;
            $scrap_total_qty[12] = 0;
            $scrap_total_qty[13] = 0;
            $scrap_total_qty[14] = 0;

            $issue_car_total_qty[0] = 0;
            $issue_car_total_qty[1] = 0;
            $issue_car_total_qty[2] = 0;
            $issue_car_total_qty[3] = 0;
            $issue_car_total_qty[4] = 0;
            $issue_car_total_qty[5] = 0;
            $issue_car_total_qty[6] = 0;
            $issue_car_total_qty[7] = 0;
            $issue_car_total_qty[8] = 0;
            $issue_car_total_qty[9] = 0;
            $issue_car_total_qty[10] = 0;
            $issue_car_total_qty[11] = 0;
            $issue_car_total_qty[12] = 0;
            $issue_car_total_qty[13] = 0;
            $issue_car_total_qty[14] = 0;

            $issue_transfer_total_qty[0] = 0;
            $issue_transfer_total_qty[1] = 0;
            $issue_transfer_total_qty[2] = 0;
            $issue_transfer_total_qty[3] = 0;
            $issue_transfer_total_qty[4] = 0;
            $issue_transfer_total_qty[5] = 0;
            $issue_transfer_total_qty[6] = 0;
            $issue_transfer_total_qty[7] = 0;
            $issue_transfer_total_qty[8] = 0;
            $issue_transfer_total_qty[9] = 0;
            $issue_transfer_total_qty[10] = 0;
            $issue_transfer_total_qty[11] = 0;
            $issue_transfer_total_qty[12] = 0;
            $issue_transfer_total_qty[13] = 0;
            $issue_transfer_total_qty[14] = 0;

            $balance_out_total_qty[0] = 0;
            $balance_out_total_qty[1] = 0;
            $balance_out_total_qty[2] = 0;
            $balance_out_total_qty[3] = 0;
            $balance_out_total_qty[4] = 0;
            $balance_out_total_qty[5] = 0;
            $balance_out_total_qty[6] = 0;
            $balance_out_total_qty[7] = 0;
            $balance_out_total_qty[8] = 0;
            $balance_out_total_qty[9] = 0;
            $balance_out_total_qty[10] = 0;
            $balance_out_total_qty[11] = 0;
            $balance_out_total_qty[12] = 0;
            $balance_out_total_qty[13] = 0;
            $balance_out_total_qty[14] = 0;

            $balance_in_total_qty[0] = 0;
            $balance_in_total_qty[1] = 0;
            $balance_in_total_qty[2] = 0;
            $balance_in_total_qty[3] = 0;
            $balance_in_total_qty[4] = 0;
            $balance_in_total_qty[5] = 0;
            $balance_in_total_qty[6] = 0;
            $balance_in_total_qty[7] = 0;
            $balance_in_total_qty[8] = 0;
            $balance_in_total_qty[9] = 0;
            $balance_in_total_qty[10] = 0;
            $balance_in_total_qty[11] = 0;
            $balance_in_total_qty[12] = 0;
            $balance_in_total_qty[13] = 0;
            $balance_in_total_qty[14] = 0;

            $count_total_qty[0] = 0;
            $count_total_qty[1] = 0;
            $count_total_qty[2] = 0;
            $count_total_qty[3] = 0;
            $count_total_qty[4] = 0;
            $count_total_qty[5] = 0;
            $count_total_qty[6] = 0;
            $count_total_qty[7] = 0;
            $count_total_qty[8] = 0;
            $count_total_qty[9] = 0;
            $count_total_qty[10] = 0;
            $count_total_qty[11] = 0;
            $count_total_qty[12] = 0;
            $count_total_qty[13] = 0;
            $count_total_qty[14] = 0;

            $diff_total_qty[0] = 0;
            $diff_total_qty[1] = 0;
            $diff_total_qty[2] = 0;
            $diff_total_qty[3] = 0;
            $diff_total_qty[4] = 0;
            $diff_total_qty[5] = 0;
            $diff_total_qty[6] = 0;
            $diff_total_qty[7] = 0;
            $diff_total_qty[8] = 0;
            $diff_total_qty[9] = 0;
            $diff_total_qty[10] = 0;
            $diff_total_qty[11] = 0;
            $diff_total_qty[12] = 0;
            $diff_total_qty[13] = 0;
            $diff_total_qty[14] = 0;

            ?>

            <tr>
                <td colspan="<?= count($product_header) + 1 ?>" style="padding: 8px;border: 1px solid grey;color: red;">
                    <span style="font-size: 18px;"><b>พนักงาน <?= \backend\models\User::findName($line_value->user_id); ?></b></span>
                </td>

            </tr>
            <tr style="font-weight: bold;">
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width:10%">รายการ</td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;font-size: 18px;width: 7%">
                        <b><?= \backend\models\Product::findCode($product_header[$y]) ?></b></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดยกมา</td>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $qty = getBalanceInQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                    $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty);
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $balance_in_qty[$y] == 0 ? '-' : number_format($balance_in_qty[$y], 0) ?></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดผลิต</td>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $qty = getProdrecQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                    $prodrec_qty[$y] = ($prodrec_qty[$y] + $qty);
                    $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty);
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $prodrec_qty[$y] == 0 ? '-' : number_format($prodrec_qty[$y], 0) ?></td>
                <?php endfor; ?>
            </tr>
            <tr>
                <td style="padding: 8px;border: 1px solid grey;width: 10%">รับ Reprocess</td>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $qty = getProdReprocess(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                    $prodrec_re_qty[$y] = ($prodrec_re_qty[$y] + $qty);
                    $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty);
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $prodrec_re_qty[$y] == 0 ? '-' : number_format($prodrec_re_qty[$y], 0) ?></td>
                <?php endfor; ?>
            </tr>
            <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">รับ Reprocess รถ + อื่นๆ</td>
            <?php for ($y = 0;
                       $y <= count($product_header) - 1;
                       $y++): ?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $qty_reprocess_car = getReturnQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                    $issue_car_total_qty[$y] = ($issue_car_total_qty[$y] + $qty_reprocess_car);
                    $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty_reprocess_car);
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty_reprocess_car);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $issue_car_total_qty[$y] == 0 ? '-' : number_format($issue_car_total_qty[$y], 0) ?></td>
                <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;width: 10%">รับโอนต่างสาขา</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getIssueTransferQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $issue_transfer_total_qty[$y] = ($issue_transfer_total_qty[$y] + $qty);
                        $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty);
                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                            <b><?= $issue_transfer_total_qty[$y] == 0 ? '-' : number_format($issue_transfer_total_qty[$y], 0) ?></b>
                        </td>
                    <?php endfor; ?>
                </tr>
                <tr style="background-color: lightblue">
                    <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>ยอดรวม</b></td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php $balance_in_total_qty[$y] = $balance_in_qty[$y]; ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                            <b><?= ($balance_in_total_qty[$y]) == 0 ? '-' : number_format($balance_in_total_qty[$y], 0) ?></b>
                        </td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;width: 10%">หน้าบ้าน เงินสด</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getCashQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $free_qty = getFreeQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $sale_total_qty[$y] = ($sale_total_qty[$y] + $qty + $free_qty);
                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($qty + $free_qty));
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $qty == 0 ? '-' : number_format(($qty + $free_qty), 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">หน้าบ้าน เงินเชื่อ</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $car_issue_qty = getIssueCarQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->user_id);
                        $qty = getCreditQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $show_qty = 0;
                        if ($qty > 0 && $qty >= $car_issue_qty) {
                            $show_qty = ($qty - $car_issue_qty);
                            $sale_total_qty[$y] = ($sale_total_qty[$y] + ($qty - $car_issue_qty));
                            $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($qty - $car_issue_qty));
                        } else {
                            $show_qty = ($qty);
                            $sale_total_qty[$y] = ($sale_total_qty[$y] + ($qty));
                            $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($car_issue_qty));
                        }

                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= ($show_qty) == 0 ? '-' : number_format(($show_qty), 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">ยอดเบิกคนรถ</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getIssueCarQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->user_id);
                        $issue_car_total_qty[$y] = $qty;
                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $issue_car_total_qty[$y] == 0 ? '-' : number_format($issue_car_total_qty[$y], 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">ยอดเบิกต่างสาขา</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= 10 > 0 ? '-' : number_format(0, 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr style="background-color: yellow">
                    <td style="padding: 8px;border: 1px solid grey;"><b>ยอดรวม</b></td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                            <b><?= ($sale_total_qty[$y] + $issue_car_total_qty[$y]) == 0 ? '-' : number_format(($sale_total_qty[$y] + $issue_car_total_qty[$y]), 0) ?></b>
                        </td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">ยอดเบิกแปรสภาพ</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getIssueRefillQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $sale_refill_qty[$y] = ($sale_refill_qty[$y] + $qty);
                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $sale_refill_qty[$y] == 0 ? '-' : number_format($sale_refill_qty[$y], 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">ยอดเสีย</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getScrapQty(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                        $scrap_total_qty[$y] = ($scrap_total_qty[$y] + $qty);
                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $scrap_total_qty[$y] == 0 ? '-' : number_format($scrap_total_qty[$y], 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr style="background-color: orange">
                    <td style="padding: 8px;border: 1px solid grey;"><b>ยอดรวมออกน้ำแข็ง</b></td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php $balance_out_total_qty[$y] = ($sale_total_qty[$y] + $issue_car_total_qty[$y] + $sale_refill_qty[$y] + $scrap_total_qty[$y]) ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                            <b><?= ($sale_total_qty[$y] + $issue_car_total_qty[$y] + $sale_refill_qty[$y] + $scrap_total_qty[$y]) == 0 ? '-' : number_format($sale_total_qty[$y] + $issue_car_total_qty[$y] + $sale_refill_qty[$y] + $scrap_total_qty[$y], 0) ?></b>
                        </td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">เหลือยกไป</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= ($balance_in_total_qty[$y] - $balance_out_total_qty[$y]) == 0 ? '-' : number_format(($balance_in_total_qty[$y] - $balance_out_total_qty[$y]), 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td style="padding: 8px;border: 1px solid grey;">นับจริง</td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <?php
                        $qty = getDailycount($product_header[$y], date('Y-m-d', strtotime($from_date)), $line_value->shift);
                        $count_total_qty[$y] = ($count_total_qty[$y] + $qty);
                        ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $count_total_qty[$y] == 0 ? '-' : number_format($count_total_qty[$y], 0) ?></td>
                    <?php endfor; ?>
                </tr>
                <tr style="background-color: lightgreen">
                    <td style="padding: 8px;border: 1px solid grey;"><b>เกิน-ขาด</b></td>
                    <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                        <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                            <b><?= ($count_total_qty[$y] - ($balance_in_total_qty[$y] - $balance_out_total_qty[$y])) == 0 ? '-' : number_format(($count_total_qty[$y] - ($balance_in_total_qty[$y] - $balance_out_total_qty[$y])), 0) ?></b>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>

        <?php endforeach; ?>
    </table>
    <br/>
    <label for=""><h3>สรุปรวม</h3></label>
    <br/>
    <table id="table-data">
        <tr style="font-weight: bold;background-color: deeppink;color: white;">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 10%">รายการ</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
            <?php endfor; ?>
        </tr>

        <?php
        $balance_in_qty[0] = 0;
        $balance_in_qty[1] = 0;
        $balance_in_qty[2] = 0;
        $balance_in_qty[3] = 0;
        $balance_in_qty[4] = 0;
        $balance_in_qty[5] = 0;
        $balance_in_qty[6] = 0;
        $balance_in_qty[7] = 0;
        $balance_in_qty[8] = 0;
        $balance_in_qty[9] = 0;
        $balance_in_qty[10] = 0;
        $balance_in_qty[11] = 0;
        $balance_in_qty[12] = 0;
        $balance_in_qty[13] = 0;
        $balance_in_qty[14] = 0;

        $prodrec_qty[0] = 0;
        $prodrec_qty[1] = 0;
        $prodrec_qty[2] = 0;
        $prodrec_qty[3] = 0;
        $prodrec_qty[4] = 0;
        $prodrec_qty[5] = 0;
        $prodrec_qty[6] = 0;
        $prodrec_qty[7] = 0;
        $prodrec_qty[8] = 0;
        $prodrec_qty[9] = 0;
        $prodrec_qty[10] = 0;
        $prodrec_qty[11] = 0;
        $prodrec_qty[12] = 0;
        $prodrec_qty[13] = 0;
        $prodrec_qty[14] = 0;

        $prodrec_re_qty[0] = 0;
        $prodrec_re_qty[1] = 0;
        $prodrec_re_qty[2] = 0;
        $prodrec_re_qty[3] = 0;
        $prodrec_re_qty[4] = 0;
        $prodrec_re_qty[5] = 0;
        $prodrec_re_qty[6] = 0;
        $prodrec_re_qty[7] = 0;
        $prodrec_re_qty[8] = 0;
        $prodrec_re_qty[9] = 0;
        $prodrec_re_qty[10] = 0;
        $prodrec_re_qty[11] = 0;
        $prodrec_re_qty[12] = 0;
        $prodrec_re_qty[13] = 0;
        $prodrec_re_qty[14] = 0;
        ?>
        <?php
        $sale_total_qty[0] = 0;
        $sale_total_qty[1] = 0;
        $sale_total_qty[2] = 0;
        $sale_total_qty[3] = 0;
        $sale_total_qty[4] = 0;
        $sale_total_qty[5] = 0;
        $sale_total_qty[6] = 0;
        $sale_total_qty[7] = 0;
        $sale_total_qty[8] = 0;
        $sale_total_qty[9] = 0;
        $sale_total_qty[10] = 0;
        $sale_total_qty[11] = 0;
        $sale_total_qty[12] = 0;
        $sale_total_qty[13] = 0;
        $sale_total_qty[14] = 0;


        $sale_refill_qty[0] = 0;
        $sale_refill_qty[1] = 0;
        $sale_refill_qty[2] = 0;
        $sale_refill_qty[3] = 0;
        $sale_refill_qty[4] = 0;
        $sale_refill_qty[5] = 0;
        $sale_refill_qty[6] = 0;
        $sale_refill_qty[7] = 0;
        $sale_refill_qty[8] = 0;
        $sale_refill_qty[9] = 0;
        $sale_refill_qty[10] = 0;
        $sale_refill_qty[11] = 0;
        $sale_refill_qty[12] = 0;
        $sale_refill_qty[13] = 0;
        $sale_refill_qty[14] = 0;

        $scrap_total_qty[0] = 0;
        $scrap_total_qty[1] = 0;
        $scrap_total_qty[2] = 0;
        $scrap_total_qty[3] = 0;
        $scrap_total_qty[4] = 0;
        $scrap_total_qty[5] = 0;
        $scrap_total_qty[6] = 0;
        $scrap_total_qty[7] = 0;
        $scrap_total_qty[8] = 0;
        $scrap_total_qty[9] = 0;
        $scrap_total_qty[10] = 0;
        $scrap_total_qty[11] = 0;
        $scrap_total_qty[12] = 0;
        $scrap_total_qty[13] = 0;
        $scrap_total_qty[14] = 0;

        $issue_car_total_qty[0] = 0;
        $issue_car_total_qty[1] = 0;
        $issue_car_total_qty[2] = 0;
        $issue_car_total_qty[3] = 0;
        $issue_car_total_qty[4] = 0;
        $issue_car_total_qty[5] = 0;
        $issue_car_total_qty[6] = 0;
        $issue_car_total_qty[7] = 0;
        $issue_car_total_qty[8] = 0;
        $issue_car_total_qty[9] = 0;
        $issue_car_total_qty[10] = 0;
        $issue_car_total_qty[11] = 0;
        $issue_car_total_qty[12] = 0;
        $issue_car_total_qty[13] = 0;
        $issue_car_total_qty[14] = 0;

        $issue_transfer_total_qty[0] = 0;
        $issue_transfer_total_qty[1] = 0;
        $issue_transfer_total_qty[2] = 0;
        $issue_transfer_total_qty[3] = 0;
        $issue_transfer_total_qty[4] = 0;
        $issue_transfer_total_qty[5] = 0;
        $issue_transfer_total_qty[6] = 0;
        $issue_transfer_total_qty[7] = 0;
        $issue_transfer_total_qty[8] = 0;
        $issue_transfer_total_qty[9] = 0;
        $issue_transfer_total_qty[10] = 0;
        $issue_transfer_total_qty[11] = 0;
        $issue_transfer_total_qty[12] = 0;
        $issue_transfer_total_qty[13] = 0;
        $issue_transfer_total_qty[14] = 0;

        $balance_out_total_qty[0] = 0;
        $balance_out_total_qty[1] = 0;
        $balance_out_total_qty[2] = 0;
        $balance_out_total_qty[3] = 0;
        $balance_out_total_qty[4] = 0;
        $balance_out_total_qty[5] = 0;
        $balance_out_total_qty[6] = 0;
        $balance_out_total_qty[7] = 0;
        $balance_out_total_qty[8] = 0;
        $balance_out_total_qty[9] = 0;
        $balance_out_total_qty[10] = 0;
        $balance_out_total_qty[11] = 0;
        $balance_out_total_qty[12] = 0;
        $balance_out_total_qty[13] = 0;
        $balance_out_total_qty[14] = 0;

        $count_total_qty[0] = 0;
        $count_total_qty[1] = 0;
        $count_total_qty[2] = 0;
        $count_total_qty[3] = 0;
        $count_total_qty[4] = 0;
        $count_total_qty[5] = 0;
        $count_total_qty[6] = 0;
        $count_total_qty[7] = 0;
        $count_total_qty[8] = 0;
        $count_total_qty[9] = 0;
        $count_total_qty[10] = 0;
        $count_total_qty[11] = 0;
        $count_total_qty[12] = 0;
        $count_total_qty[13] = 0;
        $count_total_qty[14] = 0;

        $diff_total_qty[0] = 0;
        $diff_total_qty[1] = 0;
        $diff_total_qty[2] = 0;
        $diff_total_qty[3] = 0;
        $diff_total_qty[4] = 0;
        $diff_total_qty[5] = 0;
        $diff_total_qty[6] = 0;
        $diff_total_qty[7] = 0;
        $diff_total_qty[8] = 0;
        $diff_total_qty[9] = 0;
        $diff_total_qty[10] = 0;
        $diff_total_qty[11] = 0;
        $diff_total_qty[12] = 0;
        $diff_total_qty[13] = 0;
        $diff_total_qty[14] = 0;

        ?>

        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดยกมา</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getBalanceInQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y], 1);
                $balance_in_qty[$y] = ($balance_in_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $balance_in_qty[$y] == 0 ? '-' : number_format($balance_in_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดผลิต</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getProdrecQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $prodrec_qty[$y] = ($prodrec_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $prodrec_qty[$y] == 0 ? '-' : number_format($prodrec_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">รับ Reprocess</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getProdReprocessSum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $prodrec_re_qty[$y] = ($prodrec_re_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $prodrec_re_qty[$y] == 0 ? '-' : number_format($prodrec_re_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">รับ Reprocess รถ + อื่นๆ</td>
            <?php for ($y = 0;
            $y <= count($product_header) - 1;
            $y++): ?>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty_reprocess_car = getReturnQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $issue_car_total_qty[$y] = ($issue_car_total_qty[$y] + $qty_reprocess_car);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty_reprocess_car);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $issue_car_total_qty[$y] == 0 ? '-' : number_format($issue_car_total_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>รับโอนต่างสาขา</b></td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getIssueTransferQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->shift);
                $issue_transfer_total_qty[$y] = ($issue_transfer_total_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] + $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                    <b><?= $issue_transfer_total_qty[$y] == 0 ? '-' : number_format($issue_transfer_total_qty[$y], 0) ?></b>
                </td>
            <?php endfor; ?>
        </tr>
        <tr style="background-color: lightblue">
            <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>ยอดรวม</b></td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php $balance_in_qty[$y] = ($balance_in_qty[$y] + $prodrec_qty[$y] + $prodrec_re_qty[$y] + $issue_car_total_qty[$y] + $issue_transfer_total_qty[$y]) ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                    <b><?= $balance_in_qty[$y] == 0 ? '-' : number_format($balance_in_qty[$y], 0) ?></b>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">หน้าบ้าน เงินสด</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getCashQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $sale_total_qty[$y] = ($sale_total_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $qty == 0 ? '-' : number_format($qty, 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">หน้าบ้าน เงินเชื่อ</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $car_issue_qty = getIssueCarQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->user_id);
                $qty = getCreditQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
//                        $sale_total_qty[$y] = ($sale_total_qty[$y] + ($qty - $issue_car_x));
//                        $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($qty - $issue_car_x));

                $show_qty = 0;
                if ($qty > 0 && $qty >= $car_issue_qty) {
                    $show_qty = ($qty - $car_issue_qty);
                    $sale_total_qty[$y] = ($sale_total_qty[$y] + ($qty - $car_issue_qty));
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($qty - $car_issue_qty));
                } else {
                    $show_qty = ($qty);
                    $sale_total_qty[$y] = ($sale_total_qty[$y] + ($qty));
                    $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - ($car_issue_qty));
                }
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= ($show_qty) == 0 ? '-' : number_format(($show_qty), 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดเบิกคนรถ</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getIssueCarQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y], $line_value->user_id);
                $issue_car_total_qty[$y] = $qty;
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $issue_car_total_qty[$y] == 0 ? '-' : number_format($issue_car_total_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดเบิกต่างสาขา</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= 10 > 1 ? '-' : number_format(0, 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr style="background-color: yellow">
            <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>ยอดรวม</b></td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                    <b><?= ($issue_car_total_qty[$y] + $sale_total_qty[$y]) == 0 ? '-' : number_format(($sale_total_qty[$y] + $issue_car_total_qty[$y]), 0) ?></b>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดเบิกเติม</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getIssueRefillQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $sale_refill_qty[$y] = ($sale_refill_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $sale_refill_qty[$y] == 0 ? '-' : number_format($sale_refill_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">ยอดเสีย</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $qty = getScrapQtySum(date('Y-m-d', strtotime($from_date)), $product_header[$y]);
                $scrap_total_qty[$y] = ($scrap_total_qty[$y] + $qty);
                $balance_out_total_qty[$y] = ($balance_out_total_qty[$y] - $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $scrap_total_qty[$y] == 0 ? '-' : number_format($scrap_total_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr style="background-color: orange">
            <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>ยอดรวมออกน้ำแข็ง</b></td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php $balance_out_total_qty[$y] = ($sale_total_qty[$y] + $issue_car_total_qty[$y] + $sale_refill_qty[$y] + $scrap_total_qty[$y]); ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                    <b><?= $balance_out_total_qty[$y] == 0 ? '-' : number_format($balance_out_total_qty[$y], 0) ?></b>
                </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">เหลือยกไป</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= ($balance_in_qty[$y] - $balance_out_total_qty[$y]) == 0 ? '-' : number_format(($balance_in_qty[$y] - $balance_out_total_qty[$y]), 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <td style="padding: 8px;border: 1px solid grey;width: 10%">นับจริง</td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                // $qty = getDailycountSum($product_header[$y], date('Y-m-d', strtotime($from_date)));
                $qty = getDailycountLasted($product_header[$y], date('Y-m-d', strtotime($from_date)));
                $count_total_qty[$y] = ($count_total_qty[$y] + $qty);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%"><?= $count_total_qty[$y] == 0 ? '-' : number_format($count_total_qty[$y], 0) ?></td>
            <?php endfor; ?>
        </tr>
        <tr style="background-color: lightgreen">
            <td style="padding: 8px;border: 1px solid grey;width: 10%"><b>เกิน-ขาด</b></td>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;width: 7%">
                    <b><?= ($count_total_qty[$y] - ($balance_in_qty[$y] - $balance_out_total_qty[$y])) == 0 ? '-' : number_format(($count_total_qty[$y] - ($balance_in_qty[$y] - $balance_out_total_qty[$y])), 0) ?></b>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>

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
function getBalanceInQtySum($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->select(['balance_in_qty'])->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->orderBy(['id' => SORT_ASC])->one();
        if ($model) {
            $qty = $model->balance_in_qty;
        }
    }
    return $qty;
}

function getBalanceInQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('balance_in_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getProdrecQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('prodrec_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getProdreprocess($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('reprocess_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getReturnQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('return_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getCashQty($trans_date, $product_id, $shift)
{

    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('cash_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}
function getFreeQty($trans_date, $product_id, $shift)
{

    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('free_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getCreditQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('credit_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getIssueCarQty($trans_date, $product_id, $user_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->select('issue_car_qty')->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'user_id' => $user_id])->one();
        if ($model) {
            $qty = $model->issue_car_qty;
        }
//        $model2 = \common\models\SalePosCloseIssueCarQty::find()->select('qty')->where(['product_id' => $product_id,'user_id'=>$user_id])->andFilterWhere(['date(trans_date)'=>$trans_date])->one();
//        if ($model2) {
//            $qty = $model2->qty;
//        }
    }


    return $qty;
}

function getIssueTransferQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('issue_transfer_qty');
        //  $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('issue_transfer_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getIssueTransferQtySum($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        // $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date,'shift'=>$shift])->sum('issue_transfer_qty');
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('issue_transfer_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getIssueRefillQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('issue_refill_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getScrapQty($trans_date, $product_id, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('scrap_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getDailycount($product_id, $trans_date, $shift)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date, 'shift' => $shift])->sum('counting_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getDailycountLasted($product_id, $trans_date)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->orderBy(['id' => SORT_DESC])->one();
        if ($model) {
            $qty = $model->counting_qty;
        }
    }
    return $qty;
}

//function getDailycount($product_id, $t_date)
//{
//    $qty = 0;
//    if ($product_id != null) {
//        $model = \common\models\DailyCountStock::find()->where(['product_id' => $product_id, 'status' => 0])->andFilterWhere(['date(trans_date)' => date('Y-m-d', strtotime($t_date))])->all();
//        if ($model) {
//            foreach ($model as $value) {
//                $qty += $value->qty;
//            }
//        }
//    }
//    return $qty;
//}


function getProdrecQtySum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('prodrec_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getProdreprocessSum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('reprocess_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getReturnQtySum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('return_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getCashQtySum($trans_date, $product_id)
{

    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('cash_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getCreditQtySum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('credit_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getIssueCarQtySum($trans_date, $product_id, $user_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('issue_car_qty');
        if ($model) {
            $qty = $model;
        }
//        $model2 = \common\models\SalePosCloseIssueCarQty::find()->where(['product_id' => $product_id])->andFilterWhere(['date(trans_date)' => $trans_date])->sum('qty');
//        if ($model2) {
//            $qty = $model2;
//        }
    }
    return $qty;
}

//function getIssueTransferQtySum($trans_date, $product_id)
//{
//    $qty = 0;
//    if ($product_id && $trans_date != null) {
//        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('issue_transfer_qty');
//        if ($model) {
//            $qty = $model;
//        }
//    }
//    return $qty;
//}

function getIssueRefillQtySum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('issue_refill_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getScrapQtySum($trans_date, $product_id)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('scrap_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}

function getDailycountSum($product_id, $trans_date)
{
    $qty = 0;
    if ($product_id && $trans_date != null) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => $trans_date])->sum('counting_qty');
        if ($model) {
            $qty = $model;
        }
    }
    return $qty;
}


function getOrder($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type)
{
    $data = [];
    $sql = "SELECT t2.order_no, t3.code , t3.name, t1.qty, t1.price, t2.order_date, t2.order_channel_id 
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id 
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . " 
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status=1
             AND t2.sale_channel_id = 2
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

    if ($find_sale_type != null && $find_sale_type != 0) {
        if ($find_sale_type == 1) {
            $sql .= " AND t2.payment_method_id=" . $find_sale_type;
        }
        if ($find_sale_type == 2) {
            $sql .= " AND t2.order_channel_id is null AND t2.payment_method_id=" . $find_sale_type;
        }
        if ($find_sale_type == 3) {
            $sql .= " AND t2.order_channel_id > 0";
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
