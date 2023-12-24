<?php
//print_r($model_min_max);
use kartik\daterange\DateRangePicker;

$company_id = 0;
$branch_id = 0;
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}

$from_date = date('Y-m-d');
$to_date = date('Y-m-d');

if ($find_from_date != null) {
    $from_date = date('Y-m-d H:i', strtotime($find_from_date));
    $to_date = date('Y-m-d H:i', strtotime($find_to_date));
}


?>
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
</head>
<body>
<form action="<?= \yii\helpers\Url::to(['salereport/indexcar'], true) ?>" method="post">
    <div class="row">

        <div class="col-lg-3">
            <label for="">ตั้งแต่วันที่</label>
            <?php
            echo DateRangePicker::widget([
                'name' => 'from_date',
                // 'value'=>'2015-10-19 12:00 AM',
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
        </div>
        <div class="col-lg-3">
            <label for="">ตั้งแต่วันที่</label>
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
        </div>
        <div class="col-lg-3">
            <label for="">ลูกค้า</label>
            <?php
            echo \kartik\select2\Select2::widget([
                'name' => 'find_customer_id',
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1,'is_show_pos'=>0])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'value' => $find_customer_id,
                'options' => [
                    'placeholder' => '--ลูกค้าสายส่ง--'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <div style="height: 30px;"></div>
            <button class="btn btn-info">ค้นหา</button>
        </div>

    </div>
</form>
<br/>
<div id="div1">
    <table style="width: 100%;border: 0px;">
        <tr>
            <td style="text-align: center;border: none" colspan="2"><h3>สรุปรายการขายน้ำแข็ง(ลูกค้าสายส่ง)</h3></td>
        </tr>
        <tr>
            <td style="text-align: center;border: none">ชื่อ
                <b><?= \backend\models\Customer::findName($find_customer_id) ?></b></td>

        </tr>

        <tr>
            <td colspan="2" style="text-align: center;border: none">วันที่เริ่ม
                <span><b><?= date('d-m-Y', strtotime($from_date)) ?></b></span> ถึงวันที่
                <span><b><?= date('d-m-Y', strtotime($to_date)) ?></b></td>

        </tr>
    </table>
    <br/>
    <?php
    $total_all = 0;
    $count_item = 0;
    $num = 0;
    $total_line = 0;
    $line_qty = 0;
    $total_line_qty = 0;
    $total_all_qty = 0;
    $total_all_line_qty = 0;

    $line_total_amt = 0;
    $line_all_amt = 0;
    $total_all_line_qty_data = [];


    $product_header = [];
    $model_line = null;

    if ($find_customer_id != null || $find_customer_id != '') {
        $modelx = \common\models\QueryOrderCustomerCarProduct::find()->select(['product_id'])->where(['customer_id' => $find_customer_id])
            ->andFilterWhere(['BETWEEN', 'order_date', $from_date, $to_date])
            ->groupBy('product_id')->orderBy(['product_id' => SORT_ASC])->all();

        if ($modelx) {
            foreach ($modelx as $valuexx) {
                if (!in_array($valuexx->product_id, $product_header)) {
                    array_push($product_header, $valuexx->product_id);
                }
            }
        }
        $model_line = \common\models\QueryOrderCustomerCarProduct::find()->select(['id', 'order_no'])->where(['customer_id' => $find_customer_id])
            ->andFilterWhere(['BETWEEN', 'order_date', $from_date, $to_date])->andFilterWhere(['>','qty',0])->groupBy(['id'])->all();
    }


    // print_r($product_header);


    ?>
    <table style="width: 100%" id="table-data">
        <tr>
            <td style="text-align: center;padding: 0px;border: 1px solid grey;width: 5%">ลำดับ</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey;width: 10%">วันที่</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey;width: 10%">เลขที่ขาย</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 0px;border: 1px solid grey;"><?= \backend\models\Product::findDescription($product_header[$y]) ?></td>
            <?php endfor; ?>
            <td style="text-align: center;padding: 0px;border: 1px solid grey;width: 15%">จำนวนรวม</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey;width: 15%">จำนวนเงิน</td>

        </tr>
        <?php if ($model_line !=null): ?>
            <?php foreach ($model_line as $value): ?>
                <?php
                $num += 1;
                $line_total_qty = 0;
                $line_total_amt = 0;
                ?>
                <tr>
                    <td style="text-align: center;padding: 10px;border: 1px solid grey"><?= $num ?></td>
                    <td style="text-align: center;padding: 0px;border: 1px solid grey"><?= date('d-m-Y', strtotime('+543 years', strtotime(\backend\models\Orders::getOrderdate($value->id)))) ?></td>
                    <td style="text-align: center;padding: 0px;border: 1px solid grey"><?= $value->order_no ?></td>
                    <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                        <?php
                        $product_line_qty = getOrderQty2($value->id, $product_header[$x]);
                        $product_line_amt = getOrderAmount($value->id, $product_header[$x]);

                        $line_total_qty = ($line_total_qty + $product_line_qty);
                        $total_all_qty = ($total_all_qty + $product_line_qty);
                        $line_qty = $line_qty + $product_line_qty;

                        $line_total_amt = ($line_total_amt + $product_line_amt);
                        $line_all_amt = ($line_all_amt + $product_line_amt);

                        if ($num == 1) {
                            array_push($total_all_line_qty_data, ['product_id' => $product_header[$x], 'qty' => $product_line_qty]);
                        } else {
                            foreach ($total_all_line_qty_data as $key => $val) {
                                if ($total_all_line_qty_data[$key]['product_id'] == $product_header[$x]) {
                                    $total_all_line_qty_data[$key]['qty'] = $val['qty'] + $product_line_qty;
                                }
                            }
                        }
                        ?>
                        <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"><?= number_format($product_line_qty,1) ?></td>
                    <?php endfor; ?>
                    <!--                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">-->
                    <? //= number_format($line_qty) ?><!--</td>-->
                    <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"><?= number_format($line_total_qty, 1) ?></td>
                    <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"><?= number_format($line_total_amt, 2) ?></td>
                </tr>
            <?php endforeach; ?>

            <tfoot>
            <tr>
                <td colspan="2"
                    style="text-align: left;padding: 0px;text-indent: 15px;border: 0px solid grey;padding: 10px;"></td>
                <td style="text-align: right;padding: 5px;border: 0px solid grey"><b>รวม</b></td>
                <?php for ($z = 0; $z <= count($total_all_line_qty_data) - 1; $z++): ?>
                    <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                        <b><?= number_format($total_all_line_qty_data[$z]['qty'],1) ?></b></td>
                <?php endfor; ?>
                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                    <b><?= number_format($total_all_qty, 1) ?></b></td>
                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                    <b><?= number_format($line_all_amt, 2) ?></b>
                </td>
            </tr>
            </tfoot>
        <?php endif; ?>
    </table>
    <br/>
    <br/>


</div>
<br/>

<table width="100%" class="table-title">
    <!--    <td>-->
    <!--        <button class="btn btn-info" onclick="printContent('div1')">พิมพ์ใบวางบิล</button>-->
    <!--    </td>-->
    <td>
        <a class="btn btn-info" href="<?= \yii\helpers\Url::to(['salereport/indexcarupdate'], true) ?>">อัพเดทใบส่งของ</a>
    </td>
    <td style="text-align: right">
        <button id="btn-export-excel-top" class="btn btn-secondary">Export Excel</button>
        <!--            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>-->
    </td>
</table>
</body>
</html>

<?php
function getOrderQty($order_id)
{
    $qty = 0;
    if ($order_id) {
        $model_qty = \backend\models\Orderline::find()->where(['order_id' => $order_id])->sum('qty');
        if ($model_qty) {
            $qty = $model_qty;
        }
    }
    return $qty;
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

?>


<?php
//$js = <<<JS
//function printContent(el)
//      {
//         var restorepage = document.body.innerHTML;
//         var printcontent = document.getElementById(el).innerHTML;
//         document.body.innerHTML = printcontent;
//         window.print();
//         document.body.innerHTML = restorepage;
//     }
//JS;
//$this->registerJs($js, static::POS_END);
?>


<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $("#btn-export-excel").click(function(){
  $("#table-data-2").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Excel Document Name"
  });
});
$("#btn-export-excel-top").click(function(){
  $("#table-data").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Excel Document Name"
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

