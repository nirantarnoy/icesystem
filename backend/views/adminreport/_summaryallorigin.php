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
$model_product = \backend\models\Product::find()->where(['status' => 1, 'branch_id' => 1])->all();

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
                        </div>
                        <input type="submit" class="btn btn-primary" value="ค้นหา">
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
    $prodrec_total = 0;
    $transform_total = 0;
    $out_total = 0;
    $refill_total = 0;
    $reprocess_total = 0;
    $scrap_total = 0;
    $return_total = 0;
    $balanceout_total = 0;
    $diff_total = 0;
    ?>
    <table style="width: 100%;border-collapse: collapse;" id="table-data">
        <tr>
            <td style="width:7%;text-align: center;">วันที่</td>
            <td style="width:7%;text-align: center;">กะ</td>
            <td style="width:7%;text-align: center;">รวมผลิต</td>
            <td style="width:7%;text-align: center;">นข</td>
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
            <tr>
                <td style="text-align: center;padding: 0px;">
                    <table style="border: 1px solid grey;border-collapse: collapse;">
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;">##</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;"><?= date_format($start_date, 'd-m-Y') ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;">##</td>
                        </tr>
                    </table>
                </td>
                <td style="text-align: center;">
                    <table style="border: 1px solid grey;">
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;">00:00-08:00</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;">08:00-16:00</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;">16:00-24.00</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php
                        $prodrec_all_qty = getProrecAllQty($start_date->format('Y-m-d'), $find_product_id);
                        if ($prodrec_all_qty != null) {
                            $prodrecall_total += $prodrec_all_qty[0]['qty'];
                        }
                        ?>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: none;"><?= $prodrec_all_qty[0]['qty'] == 0 ? '-' : number_format($prodrec_all_qty[0]['qty'], 0) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey;"></td>
                        </tr>

                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $transfer_qty = getTransferQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($transfer_qty != null) {
                                $shift_id = $transfer_qty[0]['shift_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($transfer_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $balance_in_qty = getBalanceInQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($balance_in_qty != null) {
                                $shift_id = $balance_in_qty[0]['shift_id'];
                                $balancein_total += $balance_in_qty[0]['qty'];
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($balance_in_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($c = 0; $c <= 2; $c++): ?>
                            <?php
                            $prod_rec_qty = getProdrecQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($prod_rec_qty != null) {
                                $shift_id = $prod_rec_qty[0]['shift_id'];
                                $prodrec_total = ($prodrec_total + $prod_rec_qty[0]['qty']);
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($prod_rec_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border:1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $reprocess_qty = getReprocessQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($reprocess_qty != null) {
                                $shift_id = $reprocess_qty[0]['shift_id'];
                                $reprocess_total = ($reprocess_total + $reprocess_qty[0]['qty']);
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($reprocess_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $return_qty = getReturnQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($return_qty != null) {
                                $shift_id = $return_qty[0]['shift_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($return_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $out_qty = getOutQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($out_qty != null) {
                                $shift_id = $out_qty[0]['shift_id'];
                                $out_total = ($out_total + $out_qty[0]['qty']);
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($out_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border:1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $refill_qty = getRefillQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($refill_qty != null) {
                                $shift_id = $refill_qty[0]['shift_id'];
                                $refill_total += $refill_qty[0]['qty'];
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($refill_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $scrap_qty = getScrapQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($scrap_qty != null) {
                                $shift_id = $scrap_qty[0]['shift_id'];
                                $scrap_total += $scrap_qty[0]['qty'];
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($scrap_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>

                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php $shift_id2 = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $count_qty = getCountingQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($count_qty != null) {
                                $shift_id = $count_qty[0]['shift_id'];
                            }
                            $balance_out_qty = getBalanceOutQty($start_date->format('Y-m-d'), $find_product_id, $shift_id2);
                            if ($balance_out_qty != null) {
                                $shift_id2 = $balance_out_qty[0]['shift_id'];
                            }
                            $line_diff_qty = ($count_qty[0]['qty'] - $balance_out_qty[0]['qty']);
                            $diff_total = ($diff_total + ($count_qty[0]['qty'] - $balance_out_qty[0]['qty']));
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($line_diff_qty, 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
                <td>
                    <table style="border: 1px solid grey;">
                        <?php $shift_id = 0; ?>
                        <?php for ($b = 0; $b <= 2; $b++): ?>
                            <?php
                            $balance_out_qty = getCountingQty($start_date->format('Y-m-d'), $find_product_id, $shift_id);
                            if ($balance_out_qty != null) {
                                $shift_id = $balance_out_qty[0]['shift_id'];
                                $balanceout_total = ($balanceout_total + $balance_out_qty[0]['qty']);
                            }
                            ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey;"><?= number_format($balance_out_qty[0]['qty'], 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                </td>
            </tr>
        <?php endfor; ?>
        <tr>
            <td style="width:7%;text-align: center;"></td>
            <td style="width:7%;text-align: center;"><b>รวม</b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($prodrecall_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b>0</b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($balancein_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($prodrec_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($reprocess_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($return_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($out_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($refill_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($scrap_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($diff_total, 0) ?></b></td>
            <td style="width:7%;text-align: center;"><b><?= number_format($balanceout_total, 0) ?></b></td>
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
JS;
$this->registerJs($js, static::POS_END);
?>
