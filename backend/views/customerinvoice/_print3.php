<?php
//print_r($model_min_max);
$from_date = date('Y-m-d');
$to_date = date('Y-m-d');

$cnt_arr = count($model_min_max);
if ($cnt_arr > 0) {
    $from_date = date('Y-m-d', strtotime($model_min_max[0]['date']));
    $to_date = date('Y-m-d', strtotime($model_min_max[$cnt_arr - 1]['date']));
}
$to_date_new = '';
$has_29_02 = 0;
$xxtodate = explode('-', $to_date);
if (count($xxtodate) > 1) {
    if ($xxtodate[1] == '02' && $xxtodate[2] == '29') {
        $has_29_02 = 1;
        $to_date_new = '29-02-' . ($xxtodate[0] + 543);
    } else {
        $to_date_new = ($xxtodate[0] + 543) . '/' . $xxtodate[1] . '/' . $xxtodate[2];
    }

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
            /*width: 100%;*/
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
    <table style="border: 0px;">
        <!--    <table style="border: 0px;">-->
        <tr>
            <td style="text-align: left;border: none" colspan="2"><h3>สรุปรายการส่งน้ำแข็ง</h3></td>
        </tr>
        <tr>
            <td style="text-align: left;border: none">ชื่อ
                <b><?= \backend\models\Customer::findDescription($model->customer_id) ?></b></td>

        </tr>

        <tr>
            <td colspan="2" style="text-align: left;border: none">วันที่เริ่ม
                <span><b><?= date('d-m-Y', strtotime('+543 years', strtotime($from_date))) ?></b></span> ถึงวันที่
                <span><b><?= $has_29_02 == 0 ? date('d-m-Y', strtotime('+543 years', strtotime($to_date))) : $to_date_new ?></b></td>
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


    $product_check = [];
    $product_header = [];
    $product_header_new = [];

    foreach ($model_line as $valuex) {
        // $modelx = \common\models\QueryOrderData::find()->join('inner join','product','order_line.product_id=product.id')->where(['order_id' => $valuex->order_id])->orderBy(['product.item_pos_seq' => SORT_DESC])->all();
        $sql = "SELECT description,item_pos_seq";
        $sql .= " FROM query_order_data";
        $sql .= " WHERE  id =" . $valuex->order_id;
        $sql .= " GROUP BY item_pos_seq";
        $sql .= " ORDER BY item_pos_seq asc";
        $query = \Yii::$app->db->createCommand($sql);
        $modelx = $query->queryAll();
        if ($modelx) {
            for ($x = 0; $x <= count($modelx) - 1; $x++) {
                if (!in_array($modelx[$x]['description'], $product_check)) {
                    array_push($product_check, $modelx[$x]['description']);
                    //  $item_seq = \backend\models\Product::find()->select('item_pos_seq')->where(['id'=>$valuexx->product_id])->one();
//                    array_push($product_header,$modelx[$x]['description']);
                    array_push($product_header_new, ['name' => $modelx[$x]['description'], 'sort_no' => $modelx[$x]['item_pos_seq']]);
                }
            }
//            foreach ($modelx as $valuexx) {
//                if (!in_array($valuexx->product_id, $product_check)) {
//                    array_push($product_check, $valuexx->product_id);
//                    $item_seq = \backend\models\Product::find()->select('item_pos_seq')->where(['id'=>$valuexx->product_id])->one();
//                    array_push($product_header_new,$item_seq->item_pos_seq);
//                }
//            }
        }
    }
    // asort($product_header_new);
    // print_r($product_header_new);return;

    //    if($product_header_new!=null){
    //       // asort($product_header_new);
    //        //  print_r($product_header_new);echo"<br />";
    //        $new_arr = [];
    //        foreach ($product_header_new as $keys=>$value){
    //            $product_x = \backend\models\Product::find()->select('id')->where(['item_pos_seq' => $value,'branch_id'=>1])->one();
    //            array_push($product_header, $product_x->id);
    //        }
    ////        for($c=0;$c<=count($product_header_new)-1;$c++){
    //////            array_push($new_arr,$product_header_new[])
    ////          //  echo $product_header_new[$c];
    ////          //  if (!in_array($product_header_new[$c], $product_header)) {
    ////                $product_x = \backend\models\Product::find()->select('id')->where(['item_pos_seq' => $product_header_new[$c],'branch_id'=>1])->one();
    ////                array_push($product_header, $product_x->id);
    ////          //  }
    ////        }
    //
    //    }

    if ($product_header_new != null) {
        for ($a = 1; $a <= 21; $a++) {
            for ($b = 0; $b <= count($product_header_new) - 1; $b++) {
                if ($a == $product_header_new[$b]['sort_no']) {
                    array_push($product_header, $product_header_new[$b]['name']);
                }
            }
        }
    }

    //print_r($product_header);return;


    ?>
    <table id="table-data">
        <tr>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">ลำดับ</td>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">วันที่</td>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">เลขที่ใบส่งของ</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $product_header[$y] ?></td>
            <?php endfor; ?>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">จำนวนเงิน</td>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">หมายเหตุ</td>

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
            $is_29_02 = 0;
            $find_order_date = date('Y-m-d');
            $find_or_date = \backend\models\Orders::getOrderdate($value->order_id);
            $xdate = explode(' ', $find_or_date);
            $xxdate = null;
            if (count($xdate) > 1) {
                $xxdate = explode('-', $xdate[0]);
                if (count($xxdate) > 1) {
                    if ($xxdate[1] == '02' && $xxdate[2] == '29') {
                        $is_29_02 = 1;
                        $find_order_date = '29-02-' . ($xxdate[0] + 543);
                    } else {
                        $find_order_date = ($xxdate[0] + 543) . '/' . $xxdate[1] . '/' . $xxdate[2];
                    }

                }
            }

            ?>
            <tr>
                <td style="text-align: center;padding: 8px;border: 1px solid grey"><?= $num ?><input type="hidden"
                                                                                                     value="<?= $value->id; ?>">
                </td>
                <td style="text-align: center;padding: 8px;border: 1px solid grey"><?= $is_29_02 == 1 ? $find_order_date : date('d-m-Y', strtotime($find_order_date)) ?></td>
                <td style="text-align: center;padding: 8px;border: 1px solid grey"><?= \backend\models\Orders::getCustomerrefno($value->order_id) ?></td>
                <?php for ($x = 0; $x <= count($product_header) - 1; $x++): ?>
                    <?php
                    $product_line_qty = getOrderQty2($value->order_id, $product_header[$x]);
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
                    <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $product_line_qty == 0 ? '-' : number_format($product_line_qty, 1) ?></td>
                <?php endfor; ?>
                <!--                <td style="text-align: center;padding: 0px;padding-right: 5px;border: 1px solid grey">-->
                <? //= number_format($line_qty) ?><!--</td>-->
                <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $total_line == 0 ? '-' : number_format($total_line, 2) ?></td>
                <td style="text-align: center;padding: 8px;padding-right: 5px;border: 1px solid grey"><?= $value->note ?></td>
            </tr>
        <?php endforeach; ?>
        <tfoot>
        <tr>
            <td colspan="2"
                style="text-align: left;padding: 0px;text-indent: 15px;border: 0px solid grey;padding: 10px;"></td>
            <td style="text-align: right;padding: 5px;border: 0px solid grey"><b>รวม</b></td>
            <?php for ($z = 0; $z <= count($total_all_line_qty_data) - 1; $z++): ?>
                <td style="text-align: center;padding: 2px;padding-right: 5px;border: 1px solid grey">
                    <b><?= $total_all_line_qty_data[$z]['qty'] == 0 ? '-' : number_format($total_all_line_qty_data[$z]['qty'], 1) ?></b>
                </td>
            <?php endfor; ?>
            <td style="text-align: center;padding: 2px;padding-right: 5px;border: 1px solid grey">
                <b><?= $total_all == 0 ? '-' : number_format($total_all, 2) ?></b></td>
            <td style="text-align: center;padding: 2px;padding-right: 5px;border: 1px solid grey"></td>
        </tr>
        </tfoot>
    </table>
    <br/>
    <br/>
    <br/>
    <table style="border: none">
        <tr>
            <td style="text-align: center;border: none;">
                ผู้รับวางบิล : ..................................................................................
            </td>
        </tr>
        <tr>
            <td style="text-align: center;border: none;">
                วันที่ : ..................................................................................
            </td>
        </tr>
    </table>
    <br/>
    <table style="border: none">
        <tr>
            <td style="text-align: center;border: none;">
                ผู้วางบิล : ..................................................................................
            </td>
        </tr>
        <tr>
            <td style="text-align: center;border: none;">
                วันที่ : ..................................................................................
            </td>
        </tr>
    </table>

</div>
<br/>

<table class="table-title">
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

function getOrderQty2($order_id, $product_description)
{
    $data = 0;
    if ($order_id) {
        $sql = "SELECT SUM(qty) as qty";
        $sql .= " FROM query_order_data";
        $sql .= " WHERE  description =" . "'" . $product_description . "'";
        $sql .= " AND  id =" . $order_id;
        $sql .= " GROUP BY description";
        $query = \Yii::$app->db->createCommand($sql);
        $modelx = $query->queryAll();
        if ($modelx) {
            $data = $modelx[0]['qty'];
        }
//        $model_qty = \backend\models\Orderline::find()->where(['order_id' => $order_id, 'product_id' => $product_id])->sum('qty');
//        if ($model_qty) {
//            $data = $model_qty;
////           foreach($model_qty as $value){
////            //   $name = \backend\models\Product::findCode($value->product_id);
////               array_push($data,['product_name'=>$name,'qty'=>$value->qty]);
////           }
//        }
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

