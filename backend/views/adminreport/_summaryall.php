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
$model_product = \backend\models\Product::find()->where(['status' => 1, 'branch_id' => $branch_id])->all();

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
        <form action="<?= \yii\helpers\Url::to(['adminreport/summaryall'], true) ?>" method="post" id="form-search">
            <input type="hidden" class="find-product-id" name="find_product_id" value="<?= $find_product_id ?>">
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
                        <div class="btn-group">
                            <?php foreach ($model_product as $product_value): ?>
                                <div class="btn btn-default btn-product-selected" id="<?= $product_value->id ?>"
                                     onclick="changeproductfind($(this))"><?= $product_value->code; ?></div>
                            <?php endforeach; ?>
                            <input type="submit" class="btn btn-primary" value="ค้นหา">
                        </div>

                    </td>

                </tr>
                <tr>
                    <td>
                        <div class="btn-group">
                            <div class="btn btn-default btn-data-type-selected" id="1"
                                 onclick="changeDatatype($(this))">ปกติ</div>
                            <div class="btn btn-default btn-data-type-selected" id="2"
                                 onclick="changeDatatype($(this))">ฉบับปรับปรุง</div>
                        </div>
                    </td>
                </tr>
            </table>
            <input type="hidden" class="find-data-type-id" name="data_type_selected" value="<?=$data_type_selected == null?1:$data_type_selected?>">
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
            <td style="text-align: center; font-size: 20px; font-weight: bold">ประวัติสรุปการขาย</td>
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
    $fdate = date_create($from_date);
    $tdate = date_create($to_date);
    $start_date = date('d-m-Y', strtotime($from_date));
    $date_loop_cnt = date_diff($fdate, $tdate);
    $day_qty = $date_loop_cnt->format('%d');
    // echo $day_qty;
    $day_qty += 1;

    $balancein_total = 0;
    $prodrecall_total = 0;

    $transform_total = 0;
    $transfer_total = 0;
    $out_total = 0;
    $refill_total = 0;
    $reprocess_total = 0;
    $scrap_total = 0;
    $return_total = 0;
    $balanceout_total = 0;
    $diff_total = 0;

    $shift_table = ['00:00-08:00', '08:00-16:00', '16:00-24.00'];
    $current_product_name = \backend\models\Product::findName($find_product_id);
    ?>
    <table style="width: 100%;border-collapse: collapse;" id="table-data">
        <tr>
            <td style="width:7%;text-align: center;">วันที่</td>
            <td style="width:7%;text-align: center;">กะ</td>
            <td style="width:7%;text-align: center;">รวมผลิต</td>
            <td style="width:7%;text-align: center;">สาขาอื่น</td>
            <td style="width:7%;text-align: center;">ยกมา</td>
            <td style="width:7%;text-align: center;">ผลิต</td>
            <td style="width:7%;text-align: center;">แปรสภาพ</td>
            <td style="width:7%;text-align: center;">รับคืน</td>
            <td style="width:7%;text-align: center;">น้ำแข็งออก</td>
            <td style="width:7%;text-align: center;">เบิกเติม</td>
            <td style="width:7%;text-align: center;">เสีย</td>
            <td style="width:7%;text-align: center;">ปรับปรุง</td>
            <td style="width:7%;text-align: center;">ยกไป</td>
        </tr>
        <?php for ($i = 0; $i <= $day_qty - 1; $i++): ?>
            <?php
            if ($i > 0) {
                $start_date = date_add($fdate, date_interval_create_from_date_string("1 day"));
            } else {
                $start_date = date_add($fdate, date_interval_create_from_date_string("0 day"));
            }

            ?>
            <?php
            $shift_id = 0;
            $shift_id2 = 0;
            $prodrec_total = 0;
            $prod_rec_all_day_qty = 0;

            ?>

            <?php for ($b = 0; $b <= 2; $b++): ?>
                <?php
                $shift_name = $shift_table[$b];
                $line_edit_transfer_color = '';
                $line_edit_prodrec_color = '';
                $line_edit_return_color = '';
                $line_edit_scrap_color = '';
                $line_edit_counting_color = '';
                $line_edit_refill_color = '';
                $line_edit_reprocess_color = '';

                $line_transfer_qty = 0;
                $line_prodrec_qty = 0;
                $line_return_qty = 0;
                $line_scrap_qty = 0;
                $line_refill_qty = 0;
                $line_reprocess_qty = 0;

                $line_in_total = 0;
                $line_out_total = 0;
                $line_diff_qty = 0;

//                $prodrec_all_qty = getProrecAllQty($start_date->format('Y-m-d'), $find_product_id);
//                if ($prodrec_all_qty != null) {
//                    $prodrecall_total += $prodrec_all_qty[0]['qty'];
//                }

                $transfer_qty = getTransferQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($transfer_qty != null) {

                    if($data_type_selected == 2){
                        $transfer_adjust = getCloseAdjustTransferQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);
                        if($transfer_adjust != null){
                            $line_transfer_qty = ($transfer_adjust[0]['qty']);
                            $transfer_total += ($transfer_adjust[0]['qty']);
                            $line_in_total = ($line_in_total + $transfer_adjust[0]['qty']);
                            $line_edit_transfer_color = 'color: red;';
                        }else{
                            $line_transfer_qty = ($transfer_qty[0]['qty']);
                            $transfer_total += ($transfer_qty[0]['qty']);
                            $line_in_total = ($line_in_total + $transfer_qty[0]['qty']);
                            $line_edit_transfer_color = '';
                        }
                        //$line_edit_color = 'color:red;';
                    }else{
                        $line_transfer_qty = ($transfer_qty[0]['qty']);
                        $transfer_total += ($transfer_qty[0]['qty']);
                        $line_in_total = ($line_in_total + $transfer_qty[0]['qty']);
                    }


                    // $shift_id = $transfer_qty[0]['shift_id'];
                }

                $balance_in_qty = getBalanceInQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($balance_in_qty != null) {
                    // $shift_id = $balance_in_qty[0]['shift_id'];
                    $line_in_total = ($line_in_total + $balance_in_qty[0]['qty']);
                    $balancein_total += $balance_in_qty[0]['qty'];
                }

                $prod_rec_qty = getProdrecQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($prod_rec_qty != null) {
                    // $shift_id = $prod_rec_qty[0]['shift_id'];

                    $prodrec_adjust = getCloseAdjustProdrecQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);

                    $prodrec_total = ($prodrec_total + $prod_rec_qty[0]['qty']);

                    if($data_type_selected == 2){
                        if($prodrec_adjust != null){
                            $line_prodrec_qty = $prodrec_adjust[0]['qty'];
                            $prodrecall_total = ($prodrecall_total + $prodrec_adjust[0]['qty']);
                            $line_in_total = ($line_in_total + $prodrec_adjust[0]['qty']);
                            $line_edit_prodrec_color = 'color:red;';
                        }else{
                            $line_prodrec_qty = $prod_rec_qty[0]['qty'];
                            $prodrecall_total = ($prodrecall_total + $prod_rec_qty[0]['qty']);
                            $line_in_total = ($line_in_total + $prod_rec_qty[0]['qty']);
                            $line_edit_prodrec_color = 'xxx';
                        }
                        //$line_edit_color = 'color:red;';
                    }else{
                        $line_prodrec_qty = $prod_rec_qty[0]['qty'];
                        $prodrecall_total = ($prodrecall_total + $prod_rec_qty[0]['qty']);
                        $line_in_total = ($line_in_total + $prod_rec_qty[0]['qty']);
                    }


                }
                $prod_rec_all_day_qty = getProdrecAllDayQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);

                $reprocess_qty = getReprocessQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($reprocess_qty != null) {
                    // $shift_id = $reprocess_qty[0]['shift_id'];
                    $reprocess_adjust = getCloseAdjustReprocessQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);
                     if($data_type_selected == 2){
                         if($reprocess_adjust != null){
                             $line_reprocess_qty = $reprocess_adjust[0]['qty'];
                             $reprocess_total = ($reprocess_total + $reprocess_adjust[0]['qty']);
                             $line_in_total = ($line_in_total + $reprocess_adjust[0]['qty']);
                             $line_edit_reprocess_color = 'color:red;';
                         }else{
                             $line_reprocess_qty = $reprocess_qty[0]['qty'];
                             $reprocess_total = ($reprocess_total + $reprocess_qty[0]['qty']);
                             $line_in_total = ($line_in_total + $reprocess_qty[0]['qty']);
                             $line_edit_reprocess_color = '';
                         }

                     }else{
                         $line_reprocess_qty = $reprocess_qty[0]['qty'];
                         $reprocess_total = ($reprocess_total + $reprocess_qty[0]['qty']);
                         $line_in_total = ($line_in_total + $reprocess_qty[0]['qty']);
                     }

                }

                $return_qty = getReturnQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($return_qty != null) {
                    $return_adjust = getCloseAdjustReturnQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);
                    if($data_type_selected == 2){
                        if($return_adjust != null){
                            $line_return_qty = ($return_adjust[0]['qty']);
                            $return_total += ($return_adjust[0]['qty']);
                            $line_in_total = ($line_in_total + $return_adjust[0]['qty']);
                            $line_edit_return_color = 'color:red;';
                        }else{
                            $line_return_qty = ($return_qty[0]['qty']);
                            $return_total += ($return_qty[0]['qty']);
                            $line_in_total = ($line_in_total + $return_qty[0]['qty']);
                            // $shift_id = $return_qty[0]['shift_id'];
                            $line_edit_return_color = '';
                        }

                    }else{
                        $line_return_qty = ($return_qty[0]['qty']);
                        $return_total += ($return_qty[0]['qty']);
                        $line_in_total = ($line_in_total + $return_qty[0]['qty']);
                    }


                }

                $out_qty = getOutQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($out_qty != null) {
                    // $shift_id = $out_qty[0]['shift_id'];
                    $out_total = ($out_total + $out_qty[0]['qty']);
                    $line_out_total = ($line_out_total + $out_qty[0]['qty']);
                }

                $refill_qty = getRefillQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($refill_qty != null) {
                    //  $shift_id = $refill_qty[0]['shift_id'];
                    $refill_adjust = getCloseAdjustRefillQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);

                    if($data_type_selected == 2){
                        if($refill_adjust != null){
                            $line_refill_qty = ($refill_adjust[0]['qty']);
                            $refill_total += $refill_adjust[0]['qty'];
                            $line_out_total = ($line_out_total + $refill_adjust[0]['qty']);
                            $line_edit_refill_color = 'color:red;';
                        }else{
                            $line_refill_qty = ($refill_qty[0]['qty']);
                            $refill_total += $refill_qty[0]['qty'];
                            $line_out_total = ($line_out_total + $refill_qty[0]['qty']);
                            $line_edit_refill_color = '';
                        }
                    }else{
                        $line_refill_qty = ($refill_qty[0]['qty']);
                        $refill_total += $refill_qty[0]['qty'];
                        $line_out_total = ($line_out_total + $refill_qty[0]['qty']);
                    }

                }

                $scrap_qty = getScrapQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($scrap_qty != null) {
                    $scrap_adjust = getCloseAdjustScrapQty($start_date->format('Y-m-d'), $find_product_id, $shift_id , $b);

                    if($data_type_selected == 2){
                        if($scrap_adjust != null){
                            $line_scrap_qty = ($scrap_adjust[0]['qty']);
                            $scrap_total += $scrap_adjust[0]['qty'];
                            $line_out_total = ($line_out_total + $scrap_adjust[0]['qty']);
                            $line_edit_scrap_color = 'color:red;';
                        }else{
                            //  $shift_id = $scrap_qty[0]['shift_id'];
                            $line_scrap_qty = ($scrap_qty[0]['qty']);
                            $scrap_total += $scrap_qty[0]['qty'];
                            $line_out_total = ($line_out_total + $scrap_qty[0]['qty']);
                            $line_edit_scrap_color = '';
                        }

                    }else{
                        $line_scrap_qty = ($scrap_qty[0]['qty']);
                        $scrap_total += $scrap_qty[0]['qty'];
                        $line_out_total = ($line_out_total + $scrap_qty[0]['qty']);
                    }


                }

                $count_qty = getCountingQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($count_qty != null) {
                    // $shift_id = $count_qty[0]['shift_id'];
                }
                $balance_out_qty = getBalanceOutQty($start_date->format('Y-m-d'), $find_product_id, $shift_id2);
                if ($balance_out_qty != null) {
                    $shift_id2 = $balance_out_qty[0]['shift_id'];
                }
              //  $line_diff_qty = ($count_qty[0]['qty'] - $balance_out_qty[0]['qty']); // original
              //  $diff_total = ($diff_total + ($count_qty[0]['qty'] - $balance_out_qty[0]['qty']));
                $line_diff_qty = ($count_qty[0]['qty'] - ($line_in_total - $line_out_total));
             //   $line_diff_qty = ($line_out_total);
                $diff_total = ($diff_total + ($count_qty[0]['qty'] - ($line_in_total - $line_out_total)));


                $balance_out_qty = getCountingQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                if ($balance_out_qty != null) {
                    $shift_id = $balance_out_qty[0]['shift_id'];
                    $balanceout_total = ($balanceout_total + $balance_out_qty[0]['qty']);
                }
                ?>


                <?php if ($b == 1): ?>
                    <tr data-var="<?= date_format($start_date, 'd-m-Y') ?>" data-id="<?= $b ?>"
                        data-show="<?= $shift_name ?>" data-product="<?=$find_product_id?>" data-product-name="<?=$current_product_name?>" ondblclick="editshiftdata($(this))">
                        <td style="text-align: center;"><?= date_format($start_date, 'd-m-Y') ?></td>
                        <td style="text-align: center;"><?= $shift_name ?></td>
                        <td style="text-align: center;"><?= number_format($prod_rec_all_day_qty, 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_transfer_color?>"><?= number_format($line_transfer_qty, 2) ?></td>
                        <td style="text-align: center;"><?= number_format($balance_in_qty[0]['qty'], 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_prodrec_color?>"><?= number_format($line_prodrec_qty, 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_reprocess_color?>"><?= number_format($line_reprocess_qty, 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_return_color?>"><?= number_format($line_return_qty, 2) ?></td>
                        <td style="text-align: center;"><?= number_format($out_qty[0]['qty'], 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_refill_color?>"><?= number_format($line_refill_qty, 2) ?></td>
                        <td style="text-align: center;<?=$line_edit_scrap_color?>"><?= number_format($line_scrap_qty, 2) ?></td>
                        <td style="text-align: center;"><?= number_format($line_diff_qty, 2) ?></td>
                        <td style="text-align: center;"><?= number_format($balance_out_qty[0]['qty'], 2) ?></td>
                    </tr>
                <?php else: ?>
                    <?php
                    $show_border = '';
                    if ($b == 2) {
                        $show_border = 'border-bottom: 1px solid black;';
                    }
                    ?>
                    <tr data-var="<?= date_format($start_date, 'd-m-Y') ?>" data-id="<?= $b ?>"
                        data-show="<?= $shift_name ?>"  data-product="<?=$find_product_id?>" data-product-name="<?=$current_product_name?>"  ondblclick="editshiftdata($(this))">
                        <td style="text-align: center;<?= $show_border ?>"></td>
                        <td style="text-align: center;<?= $show_border ?>"><?= $shift_name ?></td>
                        <td style="text-align: center;<?= $show_border ?>"></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_transfer_color?>"><?= number_format($line_transfer_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?>"><?= number_format($balance_in_qty[0]['qty'], 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_prodrec_color?>"><?= number_format($line_prodrec_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_reprocess_color?>"><?= number_format($line_reprocess_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_return_color?>"><?= number_format($line_return_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?>"><?= number_format($out_qty[0]['qty'], 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_refill_color?>"><?= number_format($line_refill_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?><?=$line_edit_scrap_color?>"><?= number_format($line_scrap_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?>"><?= number_format($line_diff_qty, 2) ?></td>
                        <td style="text-align: center;<?= $show_border ?>"><?= number_format($balance_out_qty[0]['qty'], 2) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>


        <?php endfor; ?>
        <tr>
            <td style="width:7%;text-align: center;"></td>
            <td style="width:7%;text-align: center;"><b>รวม</b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($prodrecall_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($transfer_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($balancein_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($prodrecall_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($reprocess_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($return_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($out_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($refill_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($scrap_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($diff_total, 2) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($balanceout_total, 2) ?></b></td>
        </tr>
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


<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2b669a">
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span><h3 class="popup-product" style="color: white">ปรับจำนวน</h3></span>
                    </div>
                </div>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->
            <form id="form-edit-summary" action="<?= \yii\helpers\Url::to(['adminreport/editsummary'], true) ?>" method="post">
                <input type="hidden" name="search_from_date" value="<?=$from_date?>">
                <input type="hidden" name="search_to_date" value="<?=$to_date?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">วันที่</label>
                            <input type="text" class="form-control trans-date" name="trans_date" value="" readonly>
                        </div>
                        <div class="col-lg-4">
                            <label for="">กะทำงาน</label>
                            <input type="hidden" class="form-control shift-seq" name="shift_seq" value="">
                            <input type="text" class="form-control shift-show" name="shift_show" value="" readonly>
                        </div>
                        <div class="col-lg-4">
                            <label for="">สินค้า</label>
                            <input type="hidden" class="form-control edit-product-id" name="product_id" value="">
                            <input type="text" class="form-control edit-product-name" name="product_name" value="" readonly>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">จำนวนผลิต</label>
                            <input type="text" class="form-control" name="prod_rec_qty" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="">จำนวนคืน</label>
                            <input type="text" class="form-control" name="return_qty" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="">จำนวนเสีย</label>
                            <input type="text" class="form-control" name="scrap_qty" value="">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">จำนวนโอน</label>
                            <input type="text" class="form-control" name="transfer_qty" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="">จำนวนนับจริง</label>
                            <input type="text" class="form-control" name="counting_qty" value="">
                        </div>
                        <div class="col-lg-4">
                            <label for="">จำนวนเบิกเติม</label>
                            <input type="text" class="form-control" name="refill_qty" value="">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="">จำนวนแปรสภาพ</label>
                            <input type="text" class="form-control" name="reprocess_qty" value="">
                        </div>
                        <div class="col-lg-4">
                        </div>
                        <div class="col-lg-4">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-success btn-add-cart" data-dismiss="modalx"><i
                                class="fa fa-check"></i> บันทึกรายการ
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                class="fa fa-close text-danger"></i> ยกเลิก
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>

<?php
function getProdrecAllQty($t_date, $product_id, $shift)
{
    return rand(0, 1500);
}

function getProrecAllQty($t_date, $product_id)
{
    $data = [];
    $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->sum('prodrec_qty');
    if ($model) {
        array_push($data, ['shift_id' => 0, 'qty' => $model]);
    } else {
        array_push($data, ['shift_id' => 0, 'qty' => 0]);
    }
    return $data;
}

function getBalanceInQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->balance_in_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getProdrecQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->prodrec_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getProdrecAllDayQty($t_date, $product_id)
{
    $data = 0;

    $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->sum('prodrec_qty');

    if ($model) {
        $data = $model;
    }
    return $data;
}


function getTransferQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->issue_transfer_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getReprocessQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->reprocess_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getReturnQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->return_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getOutQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        // array_push($data, ['shift_id' => $model->shift, 'qty' => ($model->cash_qty + $model->credit_qty + $model->free_qty + $model->issue_car_qty)]);
        array_push($data, ['shift_id' => $model->shift, 'qty' => ($model->cash_qty + $model->credit_qty + $model->free_qty)]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getRefillQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->issue_refill_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getScrapQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->scrap_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getCountingQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        array_push($data, ['shift_id' => $model->shift, 'qty' => $model->counting_qty]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getBalanceOutQty($t_date, $product_id, $shift)
{
    $data = [];
    if ($shift != 0) {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->andFilterWhere(['>', 'shift', $shift])->orderBy(['id' => SORT_ASC])->one();
    } else {
        $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id, 'date(trans_date)' => date('Y-m-d', strtotime($t_date))])->orderBy(['id' => SORT_ASC])->one();
    }

    if ($model) {
        //    array_push($data, ['shift_id' => $model->shift, 'qty' => ($model->issue_transfer_qty + $model->balance_in_qty + $model->prodrec_qty + $model->reprocess_qty + $model->return_qty - ($model->cash_qty + $model->credit_qty + $model->free_qty + $model->issue_car_qty) - $model->issue_refill_qty - $model->scrap_qty)]);
        array_push($data, ['shift_id' => $model->shift, 'qty' => ($model->issue_transfer_qty + $model->balance_in_qty + $model->prodrec_qty + $model->reprocess_qty + $model->return_qty - ($model->cash_qty + $model->credit_qty + $model->free_qty) - $model->issue_refill_qty - $model->scrap_qty)]);
    } else {
        array_push($data, ['shift_id' => $shift, 'qty' => 0]);
    }
    return $data;
}

function getTransformQty($t_date, $product_id, $shift)
{
    return rand(0, 200);
}


//// === From Close Adjust ==



function getCloseAdjustProdrecQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->prodrec_qty > 0){
            array_push($data, ['qty' => $model->prodrec_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}

function getCloseAdjustReturnQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->return_qty > 0){
            array_push($data, ['qty' => $model->return_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}
function getCloseAdjustScrapQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->scrap_qty > 0){
            array_push($data, ['qty' => $model->scrap_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}
function getCloseAdjustTransferQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->transfer_qty > 0){
            array_push($data, ['qty' => $model->transfer_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}

function getCloseAdjustCountingQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->counting_qty > 0){
            array_push($data, ['qty' => $model->counting_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}

function getCloseAdjustRefillQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->refill_qty > 0){
            array_push($data, ['qty' => $model->refill_qty]);
        }
    } else {
        return $data;
    }
    return $data;
}

function getCloseAdjustReprocessQty($t_date, $product_id, $shift,$shift_seq)
{
    $data = [];
    $model = \common\models\CloseDailyAdjust::find()->where(['date(shift_date)' => date('Y-m-d', strtotime($t_date)),'product_id'=>$product_id,'shift_seq'=>$shift_seq])->one();

    if ($model) {
        if($model->reprocess_qty > 0){
            array_push($data, ['qty' => $model->reprocess_qty]);
        }
    } else {
        return $data;
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
    checkproductSelected();
    checkDataTypeSelected();
 });
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
function changeproductfind(e){
    //alert(e.attr("id"));
    $(".btn-group .btn-product-selected").each(function(){
        var cid = $(this).attr("id");
        if(e.attr("id") == cid){
            $(".find-product-id").val(cid);
           if(e.hasClass("btn-default")){
               e.removeClass("btn-default");
               e.addClass("btn-success");
           }else{
               e.removeClass("btn-success");
               e.addClass("btn-default");
           }
        }else{
            $(this).removeClass("btn-success");
            $(this).addClass("btn-default");
        }
    });
}     
function changeDatatype(e){
    //alert(e.attr("id"));
    $(".btn-data-type-selected").each(function(){
        var cid = $(this).attr("id");
        if(e.attr("id") == cid){
            $(".find-data-type-id").val(cid);
           if(e.hasClass("btn-default")){
               e.removeClass("btn-default");
               e.addClass("btn-success");
           }else{
               e.removeClass("btn-success");
               e.addClass("btn-default");
           }
        }else{
            $(this).removeClass("btn-success");
            $(this).addClass("btn-default");
        }
    });
}    
function checkproductSelected(){
    $(".btn-group .btn-product-selected").each(function(){
        var cid = $(this).attr("id");
        if($(".find-product-id").val() == cid){
           if($(this).hasClass("btn-default")){
               $(this).removeClass("btn-default");
               $(this).addClass("btn-success");
           }else{
               $(this).removeClass("btn-success");
               $(this).addClass("btn-default");
           }
        }else{
            $(this).removeClass("btn-success");
            $(this).addClass("btn-default");
        }
    });
}
function checkDataTypeSelected(){
    $(".btn-data-type-selected").each(function(){
        var cid = $(this).attr("id");
        if($(".find-data-type-id").val() == cid){
           if($(this).hasClass("btn-default")){
               $(this).removeClass("btn-default");
               $(this).addClass("btn-success");
           }else{
               $(this).removeClass("btn-success");
               $(this).addClass("btn-default");
           }
        }else{
            $(this).removeClass("btn-success");
            $(this).addClass("btn-default");
        }
    });
}
function editshiftdata(e){
    var t_date = e.attr("data-var");
    var t_shift = e.attr("data-id");
    var t_shift_show = e.attr("data-show");
    var t_product_id = e.attr("data-product");
    var t_product_name = e.attr("data-product-name");
    if(t_date !='' && t_shift != ''){
        $(".trans-date").val(t_date);
        $(".shift-seq").val(t_shift);
        $(".shift-show").val(t_shift_show);
        $(".edit-product-id").val(t_product_id);
        $(".edit-product-name").val(t_product_name);
        $("#editModal").modal("show");
    }
}
JS;
$this->registerJs($js, static::POS_END);
?>
