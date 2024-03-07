<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use common\models\QuerySaleorderByCustomerLoanSumNew;
use kartik\daterange\DateRangePicker;
use yii\web\Response;
use yii\web\Session;

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
//$model_customer_loan = \common\models\QueryProductTransDaily::find()->where(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
//$model_order_mobile = \common\models\Orders::find()->where(['BETWEEN', 'order_date', date('Y-m-d H:i', strtotime($from_date)), date('Y-m-d H:i', strtotime($to_date))])
//    ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
//    ->andFilterWhere(['sale_from_mobile' => 1])
//    ->andFilterWhere(['OR', ['emp_1' => $find_emp_id], ['emp_2' => $find_emp_id]])
//    ->groupBy('order_channel_id')->orderBy(['order_channel_id' => SORT_ASC])->all();
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
<?php if (!empty(\Yii::$app->session->getFlash('msg-already'))) :?>
    <div class="alert alert-success"><?=\Yii::$app->session->getFlash('msg-already')?></div>
<?php endif;?>
<form action="<?=\yii\helpers\Url::to(['salecomreport/comdailycalbkt'],true)?>" id="form-cal" method="post">
    <div class="row">
        <div class="col-lg-3">
            <?php
            echo \kartik\select2\Select2::widget([
                'name' => 'select_delivery_route',
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => '--สายส่งที่ต้องการคำนวน--'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <input type="submit" class="btn btn-warning" value="คำนวน">
        </div>
        <div class="col-lg-3"></div>
        <div class="col-lg-3" style="text-align: right;">
            <a href="<?=\yii\helpers\Url::to(['salecomreport/index2'],true)?>" class="btn btn-info" target="_self"> คำนวนย้อนหลัง</a>
        </div>
    </div>
</form>
<br />
<form action="<?= \yii\helpers\Url::to(['salecomreport/index'], true) ?>" method="post" id="form-search">
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

            <td>
                <?php
                $is_driver = $branch_id == 1 ? 1 : 6;
                echo \kartik\select2\Select2::widget([
                    'name' => 'find_emp_id',
                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Employee::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1, 'position' => [1, 6]])->all(), 'id', 'fname'),
                    'value' => $find_emp_id,
                    'options' => [
                        'placeholder' => '--พนักงาน--'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
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
            <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานค่าคอมมิชชั่น</td>
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
            <td rowspan="2"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray;border-left: 1px solid gray">
                <b>สายส่ง</b></td>
            <td rowspan="2"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>ทะเบียนรถ</b></td>
            <td colspan="2"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>คนขับ</b></td>
            <td rowspan="2"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>จำนวน</b></td>
            <td rowspan="2"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>ราคารวม</b></td>
            <td colspan="4"
                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>ค่าคอม</b></td>
        </tr>
        <tr>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>คนที่1</b></td>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>คนที่2</b></td>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>ค่าคอม</b></td>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>ค่าคอม2</b></td>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>พิเศษ</b></td>
            <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b>รวม</b></td>
        </tr>
        <?php
        $total_qty_all = 0;
        $total_amt_all = 0;
        $total_com_all = 0;
        $total_com_all_2 = 0;
        $total_special_all = 0;
        $total_com_sum_all = 0;

        ?>
        <?php if ($find_emp_id != null): ?>
            <?php for ($k = 0; $k <= count($find_emp_id) - 1; $k++): ?>

                <?php
                //  $model_order_mobile = \common\models\ComDailyCal::find()->select(['trans_dates', 'route_id', 'car_id', 'emp_1', 'emp_2', 'line_total_amt', 'total_qty', 'total_amt', 'line_com_amt', 'line_com_special_amt'])->where(['BETWEEN', 'trans_date', date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])
//                $model_order_mobilex = \common\models\ComDailyCal::find()->select(['trans_date', 'route_id', 'car_id', 'emp_1', 'emp_2', 'line_total_amt', 'total_qty', 'total_amt', 'line_com_amt', 'line_com_special_amt'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//                    ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
//                    ->andFilterWhere(['OR', ['emp_1' => $find_emp_id[$k]], ['emp_2' => $find_emp_id[$k]]])
//                    ->groupBy(['route_id','date(trans_date)'])->orderBy(['route_id' => SORT_ASC,'date(trans_date)'=>SORT_ASC])->all();

                $model_order_mobilex = \common\models\ComDailyCal::find()->select(['route_id'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
                    ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])
                    ->andFilterWhere(['OR', ['emp_1' => $find_emp_id[$k]], ['emp_2' => $find_emp_id[$k]]])
                    ->groupBy(['route_id'])->orderBy(['route_id' => SORT_ASC])->all();
                ?>

                <?php foreach ($model_order_mobilex as $value): ?>
                    <?php
                    $com_rate = 0;
                    $route_emp_count = 0;
                    ?>
                    <?php
                    $route_total = null;
                    $route_name = \backend\models\Deliveryroute::findName($value->route_id);

                    //   $route_total = getRouteSumAll($value->order_channel_id, $from_date, $to_date, $company_id, $branch_id, substr($route_name, 0, 2), $find_emp_id[$k]);
//            if (substr($route_name, 0, 2) == 'CJ') {
//
//            }

                    ?>
                    <?php
                    //  $model_order_mobile = \common\models\ComDailyCal::find()->select(['trans_dates', 'route_id', 'car_id', 'emp_1', 'emp_2', 'line_total_amt', 'total_qty', 'total_amt', 'line_com_amt', 'line_com_special_amt'])->where(['BETWEEN', 'trans_date', date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])
                    $model_order_mobile = \common\models\ComDailyCal::find()->select(['trans_date', 'route_id', 'car_id', 'emp_1', 'emp_2', 'line_total_amt', 'total_qty', 'total_amt', 'line_com_amt','line_com_amt_2', 'line_com_special_amt'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
                        ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'route_id'=>$value->route_id])
                        ->andFilterWhere(['OR', ['emp_1' => $find_emp_id[$k]], ['emp_2' => $find_emp_id[$k]]])
                        ->groupBy(['date(trans_date)'])->orderBy(['route_id' => SORT_ASC,'date(trans_date)'=>SORT_ASC])->all();
                    ?>
                    <tr>
                        <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray;border-left: 1px solid gray"><?= $route_name ?></td>
                        <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= \backend\models\Car::findName($value->car_id) ?></td>
                        <!--                <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"></td>-->
                        <!--                <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"></td>-->
                        <td colspan="7" style="border-right: 1px solid gray"></td>
                        <!--                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <? //= number_format($route_total[0]['total_qty'], 2) ?><!--</td>-->
                        <!--                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <? //= number_format($route_total[0]['total_amt'], 2) ?><!--</td>-->
                        <!--                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <? //= number_format(0, 2) ?><!--</td>-->
                        <!--                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <? //= number_format(0, 2) ?><!--</td>-->
                        <!--                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <? //= number_format(0, 2) ?><!--</td>-->
                    </tr>
                    <?php

                    // $order_date = getOrder($value->order_channel_id, $from_date, $to_date, $company_id, $branch_id, $find_emp_id[$k]);

                    $group_total_qty = 0;
                    $group_total_amt = 0;
                    $group_total_com = 0;
                    $group_total_spacial = 0;
                    $group_total_all = 0;

                    ?>

                    <?php foreach ($model_order_mobile as $value): ?>
                        <?php
                        $com_rate = 0;
                        $route_emp_count = 0;
                        ?>
                        <?php


                        $line_special = $value->line_com_special_amt;
                        $line_com_total = $value->line_com_amt;
                        $line_com_total_2 = $value->line_com_amt_2;


                        $group_total_com = ($value->line_com_amt + $value->line_com_amt_2);
                        $group_total_spacial = ($group_total_spacial + $line_special);

                        $group_total_qty = ($group_total_qty + $value->total_qty);
                        $group_total_amt = ($group_total_amt + $value->total_amt);

                        $total_qty_all = ($total_qty_all +  $value->total_qty);
                        $total_amt_all = ($total_amt_all + $value->total_amt);

                        $group_total_all = $group_total_all + $group_total_com + $line_special;

                        $total_com_all = ($total_com_all + $line_com_total);
                        $total_com_all_2 = ($total_com_all_2 + $line_com_total_2);
                        $total_special_all = ($total_special_all + $line_special);
                        $total_com_sum_all = $total_com_sum_all + ($line_com_total + $line_com_total_2 + $line_special);
                        ?>
                        <tr style="background-color: #d0e9c6">
                            <td colspan="2"
                                style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray;border-left: 1px solid gray;text-indent: 50px;">
                                <span> </span> <?= date('d-m-Y H:i:s', strtotime($value->trans_date)) ?></td>
                            <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= \backend\models\Employee::findName2($value->emp_1) ?></td>
                            <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= \backend\models\Employee::findName2($value->emp_2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format($value->total_qty, 2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format($value->total_amt, 2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format($value->line_com_amt, 2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format($value->line_com_amt_2, 2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format($line_special,2) ?></td>
                            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray"><?= number_format(($line_com_total + $line_com_total_2 + $line_special), 2) ?></td>
                        </tr>

                        <!--                    <tr style="background-color: #d0e9c6">-->
                        <!--                        <td colspan="2"-->
                        <!--                            style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-left: 1px solid gray"></td>-->
                        <!--                        <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 0px solid gray"></td>-->
                        <!--                        <td style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>รวม</b></td>-->
                        <!--                        <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>--><?//= number_format($group_total_qty, 2) ?><!--</b></td>-->
                        <!--                        <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>--><?//= number_format($group_total_amt, 2) ?><!--</b></td>-->
                        <!--                        <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>--><?//= number_format($group_total_com, 2) ?><!--</b></td>-->
                        <!--                        <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>--><?//= number_format($group_total_spacial, 0) ?><!--</b></td>-->
                        <!--                        <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">-->
                        <!--                            <b>--><?//= number_format($group_total_all, 2) ?><!--</b></td>-->
                        <!--                    </tr>-->
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endfor; ?>
        <?php endif; ?>
        <tfoot>
        <tr>
            <td colspan="4"
                style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray;border-left: 1px solid gray">
                <b>รวมทั้งหมด</b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_qty_all, 2) ?></b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_amt_all, 2) ?></b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_com_all, 2) ?></b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_com_all_2, 2) ?></b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_special_all,2) ?></b></td>
            <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 1px solid gray">
                <b><?= number_format($total_com_sum_all, 2) ?></b></td>
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
