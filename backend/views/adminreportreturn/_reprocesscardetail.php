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

if($find_route_id != null){
    $model_line = \common\models\StockTrans::find()->select(['trans_ref_id'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 7,'trans_ref_id'=>$find_route_id])->orderBy(['trans_ref_id' => SORT_ASC])->groupBy('trans_ref_id')->all();

}else{
    $model_line = \common\models\StockTrans::find()->select(['trans_ref_id'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 7])->orderBy(['trans_ref_id' => SORT_ASC])->groupBy('trans_ref_id')->all();

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
    <div class="col-lg-12">
        <form action="<?= \yii\helpers\Url::to(['adminreportreturn/index2'], true) ?>" method="post" id="form-search">
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
                    <td style="width: 20%">

                        <?php
                        echo \kartik\select2\Select2::widget([
                            'name' => 'find_route_id',
                            'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->all(), 'id', function ($data) {
                                return $data->code . ' ' . $data->name;
                            }),
                            'value' => $find_route_id,
                            'options' => [
                                'placeholder' => '--ลูกค้าสายส่ง--'
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
                    <td style="width: 25%; text-align: right">

                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<br/>
<div id="div1">
    <table class="table-header" width="100%">
        <tr>
            <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานสรุปสินค้าคืนสายส่ง</td>
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


    $all_return_qty = 0;
    $all_reprocess_qty = 0;

    $total_all_line_qty_data = [];


    $product_header = [];


    $total_product[0] = 0;
    $total_product[1] = 0;
    $total_product[2] = 0;
    $total_product[3] = 0;
    $total_product[4] = 0;
    $total_product[5] = 0;
    $total_product[6] = 0;
    $total_product[7] = 0;
    $total_product[8] = 0;
    $total_product[9] = 0;
    $total_product[10] = 0;
    $total_product[11] = 0;
    $total_product[12] = 0;
    $total_product[13] = 0;
    $total_product[14] = 0;


    //    $modelx = \common\models\StockTrans::find()->select(['product_id'])->join('inner join', 'product', 'stock_trans.product_id=product.id')->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
    //        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id, 'activity_type_id' => 7])->groupBy('product_id')->orderBy(['item_pos_seq' => SORT_ASC])->all();
    $modelx = \common\models\StockTrans::find()->select(['product_id'])->join('inner join', 'product', 'stock_trans.product_id=product.id')->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id, 'activity_type_id'=>[7,26]])->groupBy('product_id')->orderBy(['item_pos_seq' => SORT_ASC])->all();

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
        <tr style="font-weight: bold;">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">สายส่ง</td>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">ทะเบียน</td>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">พนักงานขับรถ</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">รวม</td>

        </tr>

        <?php foreach ($model_line as $value): ?>
            <?php
          //  if ($value->trans_ref_id == null || $value->trans_ref_id <= 0) continue;
            $num += 1;
            $line_qty_total = 0;
            $line_credit_amount_total = 0;
            $line_cash_amount_total = 0;
            $return_line_qty = 0;

            $car_data = getCardata($value->trans_ref_id, $from_date);
            ?>
            <tr>
                <td style="text-align: left;padding: 8px;border: 1px solid grey"><?= \backend\models\Deliveryroute::findName($value->trans_ref_id) ?></td>
                <td style="text-align: left;padding: 8px;border: 1px solid grey"><?= $car_data != null ? $car_data[0]['car_name'] : '' ?></td>
                <td style="text-align: left;padding: 8px;border: 1px solid grey"><?= $car_data != null ? $car_data[0]['emp_name'] : '' ?></td>
                <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                    <?php
                    $return_line_qty = getReturnCarQty($value->trans_ref_id, $product_header[$x], $from_date, $to_date);

                    $total_product[$x] = ($total_product[$x] + $return_line_qty);

                    $line_qty_total = ($line_qty_total + $return_line_qty);
                    $all_return_qty = ($all_return_qty + ($return_line_qty));

                    ?>
                    <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: skyblue;font-weight: bold;"><?= $return_line_qty == 0 ? '-' : number_format($return_line_qty, 1) ?></td>
                <?php endfor; ?>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: skyblue;font-weight: bold;"><?= $line_qty_total == 0 ? '-' : number_format($line_qty_total, 1) ?></td>
            </tr>
            <?php
            $ff_date = date_create($from_date);
            $tt_date = date_create($to_date);
            $diff_date = date_diff($ff_date,$tt_date);
            $cnt_day = $diff_date->format("%a");
           // echo $cnt_day;
            ?>
            <?php for($xx=0;$xx<=$cnt_day;$xx++):?>
            <?php
           // echo $xx;
                if($xx == 0){
                    $loop_date = $ff_date;
                }else{
                    $loop_date = date_add($ff_date,date_interval_create_from_date_string("1 day"));
                }

                $return_line_route_line_qty = 0;

                ?>
            <tr>
                <td style="border: 1px solid grey"></td>
                <td style="border: 1px solid grey"></td>
                <td style="text-align: center;border: 1px solid grey"><?=date_format($loop_date,"d-m-Y")?></td>
                <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                    <?php

                    $return_line_route_qty = getReturncarQtyByroute($value->trans_ref_id, $product_header[$x], $loop_date);
                    $return_line_route_line_qty = ($return_line_route_line_qty + $return_line_route_qty);
                    ?>
                    <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey"><?=number_format($return_line_route_qty,1)?></td>
                <?php endfor; ?>
                <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;"><?=number_format($return_line_route_line_qty,1)?></td>
            </tr>

            <?php endfor;?>
        <?php endforeach; ?>
        <tfoot>
        <tr>

            <td colspan="3" style="text-align: right;border: 1px solid gray;"><b>รวม</b></td>
            <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: skyblue;font-weight: bold;">
                    <?= $total_product[$x] == 0 ? '-' : number_format($total_product[$x], 1) ?>
                </td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;padding-right: 5px;border: 1px solid grey;background-color: skyblue;font-weight: bold;"><?= number_format($all_return_qty, 1) ?></td>
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
function getOrderQty2($route_id, $product_id, $from_date, $to_date)
{
    $data = 0;
    if ($route_id && $product_id) {
        $model_qty = \common\models\TransactionCarSale::find()->select(['SUM(credit_qty) as credit_qty', 'SUM(cash_qty) as cash_qty'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['route_id' => $route_id, 'product_id' => $product_id])->groupBy(['product_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->credit_qty + $model_qty->cash_qty);
        }
    }
    return $data;
}

function getFree($route_id, $from_date, $to_date)
{
    $data = 0;
    if ($route_id) {
        $model_qty = \common\models\TransactionCarSale::find()->select(['SUM(free_qty) as free_qty'])->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['route_id' => $route_id])->groupBy(['route_id'])->one();
        if ($model_qty != null) {
            $data = ($model_qty->free_qty);
        }
    }
    return $data;
}

function getReturncarQty($route_id, $product_id, $from_date, $to_date)
{
    $data = 0;
    if ($route_id && $product_id) {
        $model_qty = \common\models\StockTrans::find()->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['trans_ref_id' => $route_id, 'product_id' => $product_id, 'activity_type_id' => 7])->SUM('qty');
        if ($model_qty != null) {
            $data = ($model_qty);
        }
    }
    return $data;
}

function getReturncarQtyByroute($route_id, $product_id, $trans_date)
{
    $data = 0;
    if ($route_id && $product_id) {
        $model_qty = \common\models\StockTrans::find()->select(['qty'])->where(['date(trans_date)'=> date_format($trans_date,"Y-m-d")])
            ->andFilterWhere(['trans_ref_id' => $route_id, 'product_id' => $product_id, 'activity_type_id' => 7])->one();
        if ($model_qty != null) {
            $data = ($model_qty->qty);
        }
    }
    return $data;
}

function getReturncarReprocessQty($product_id, $from_date, $to_date)
{
    $data = 0;
    if ($product_id) {
        $model_qty = \common\models\StockTrans::find()->where(['BETWEEN', 'date(trans_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
            ->andFilterWhere(['product_id' => $product_id, 'activity_type_id' => 26])->SUM('qty');
        if ($model_qty != null) {
            $data = ($model_qty);
        }
    }
    return $data;
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
