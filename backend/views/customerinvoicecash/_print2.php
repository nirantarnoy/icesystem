<?php

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
<div id="div1">
    <table style="width: 100%;border: 0px;">
        <tr>
            <td style="width: 70%"><h3>วรภัทรไอซ์</h3></td>
            <td><h3>สรุปรายการส่งสินค้า</h3></td>
        </tr>
        <tr>
            <td>ชื่อ <b><?= \backend\models\Customer::findName($model->customer_id) ?></b></td>
            <td>เลขที่เอกสาร <b><?= $model->journal_no ?></b></td>
        </tr>
        <tr>
            <td>ที่อยู่ <b><?= \backend\models\Customer::getAddress($model->customer_id) ?></b></td>
            <td>วันที่ <b><?= date('d/m/Y', strtotime($model->trans_date)) ?></b></td>
        </tr>
        <tr>
            <td>โทร <b><?= \backend\models\Customer::getPhone($model->customer_id) ?></b></td>
            <td></td>
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
    $total_all_line_qty = 0;

    $total_all_line_qty_data = [];



    $product_header = [];

    foreach ($model_line as $valuex){
        $modelx = \backend\models\Orderline::find()->where(['order_id'=>$valuex->order_id])->orderBy(['product_id'=>SORT_ASC])->all();
        if($modelx){
            foreach($modelx as $valuexx){
                if(!in_array($valuexx->product_id, $product_header)){
                     array_push($product_header,$valuexx->product_id);
                }
            }
        }
    }

   // print_r($product_header);


    ?>
    <table style="width: 100%" id="table-data">
        <tr>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">ลำดับ</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">วันที่</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">เลขที่ใบส่งของ</td>
<!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for($y=0;$y<=count($product_header)-1;$y++):?>
                <td style="text-align: center;padding: 0px;border: 1px solid grey"><?=\backend\models\Product::findCode($product_header[$y])?></td>
            <?php endfor;?>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวนเงิน</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">หมายเหตุ</td>

        </tr>

        <?php foreach ($model_line as $value): ?>
            <?php
            $num += 1;

            $total_line = $value->remain_amount; // \backend\models\Orders::getlineremainsum($value->order_id, $model->customer_id);
            $total_all = $total_all + $total_line;

            $line_qty = getOrderQty($value->order_id);
            $total_all_line_qty = $total_all_line_qty + $line_qty;
//              $order_product = getOrderQty2($value->order_id);
//              $total_all_line_qty = 0;
            ?>
            <tr>
                <td style="text-align: center;padding: 10px;border: 1px solid grey"><?= $num ?></td>
                <td style="text-align: center;padding: 0px;border: 1px solid grey"><?= date('d/m/y', strtotime(\backend\models\Orders::getOrderdate($value->order_id))) ?></td>
                <td style="text-align: center;padding: 0px;border: 1px solid grey"><?= \backend\models\Orders::getCustomerrefno($value->order_id) ?></td>
                <?php for($x=0;$x<=count($product_header)-1;$x++):?>
                <?php
                    $product_line_qty = getOrderQty2($value->order_id,$product_header[$x]);
                    if($num == 1){
                        array_push($total_all_line_qty_data,['product_id'=>$product_header[$x],'qty'=>$product_line_qty]);
                    }else{
                       foreach($total_all_line_qty_data as $key => $val){
                            if($total_all_line_qty_data[$key]['product_id'] == $product_header[$x]){
                                $total_all_line_qty_data[$key]['qty'] = $val['qty'] + $product_line_qty;
                            }

                       }
                    }
                    ?>
                    <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"><?= number_format($product_line_qty) ?></td>
                <?php endfor;?>
<!--                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">--><?//= number_format($line_qty) ?><!--</td>-->
                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"><?= number_format($total_line,2) ?></td>
                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
        <tr>
            <td colspan="2"
                style="text-align: left;padding: 0px;text-indent: 15px;border: 1px solid grey;padding: 10px;"><b>รวม
                    <span><?= $num ?> ฉบับ</span></b></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><b>รวมเงิน</b></td>
            <?php for($z=0;$z<=count($total_all_line_qty_data)-1;$z++):?>
            <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($total_all_line_qty_data[$z]['qty']) ?></b></td>
            <?php endfor;?>
            <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">
                <b><?= number_format($total_all,2) ?></b></td>
            <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey"></td>
        </tr>
        </tfoot>
    </table>
    <br/>
    <table style="width: 100%">
        <tr>
            <td style="width: 100%">
                ได้รับบิลตามรายการข้างต้นไว้ถูกต้องเรียบร้อยแล้ว
            </td>
        </tr>
    </table>
    <br/>
    <table style="width: 100%">
        <tr>
            <td style="text-align: center;width: 50%">
                (..................................................................................)
            </td>
            <td style="text-align: center">
                (..................................................................................)
            </td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50%">ผู้รับบิล</td>
            <td style="text-align: center">ผู้วางบิล</td>
        </tr>
    </table>
    <br/>
    <table style="width: 100%">
        <tr>
            <td style="width: 5%;padding: 15px;"><input type="checkbox" class="form-control"></td>
            <td>เงินสด</td>
        </tr>
        <tr>
            <td style="width: 5%;padding: 15px;"><input type="checkbox" class="form-control"></td>
            <td>โอนธนาคาร</td>
        </tr>
    </table>
</div>
<br/>

<table width="100%" class="table-title">
    <td>
        <button class="btn btn-info" onclick="printContent('div1')">พิมพ์ใบวางบิล</button>
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

function getOrderQty2($order_id,$product_id)
{
    $data = 0;
    if ($order_id) {
        $model_qty = \backend\models\Orderline::find()->where(['order_id' => $order_id,'product_id'=>$product_id])->sum('qty');
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

