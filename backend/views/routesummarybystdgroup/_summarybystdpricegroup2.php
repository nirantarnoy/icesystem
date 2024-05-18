<?php

use kartik\daterange\DateRangePicker;

$product_header = [];
$route_name = '';

$find_date = date('Y-m-d');
$find_to_date = date('Y-m-d');
if ($route_id != null) {
    $route_name = \backend\models\Deliveryroute::findName($route_id);
}
if ($search_date != null) {
    $find_date = date('Y-m-d', strtotime($search_date));
}
if ($search_to_date != null) {
    $find_to_date = date('Y-m-d', strtotime($search_to_date));
}
$route_list = "(";
if ($route_id != null) {
    for ($x = 0; $x <= count($route_id) - 1; $x++) {
        $route_list .= $route_id[$x];
        if ($x != count($route_id) - 1) {
            $route_list .= ",";
        } else {
            $route_list .= ")";
        }
    }
} else {
    $route_list = null;
}

//
//    $modelx = \common\models\QuerySaleByDistributor::find()->select(['product_id'])->join('inner join', 'product', 'query_sale_by_distributor.product_id=product.id')->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product.item_pos_seq' => SORT_ASC])->all();

$sql = "SELECT id FROM product where status =1 order by item_pos_seq ASC";

$modelx = \Yii::$app->db->createCommand($sql)->queryAll();

if ($modelx) {
    for ($xx = 0; $xx <= count($modelx) - 1; $xx++) {
        if (!in_array($modelx[$xx]['id'], $product_header)) {
            array_push($product_header, $modelx[$xx]['id']);
        }
    }
}

$model_stdgroup = \backend\models\Stdpricegroup::find()->where(['type_id' => 1])->groupBy('seq_no')->orderBy(['seq_no' => SORT_ASC])->all();
$model_stdgroup2 = \backend\models\Stdpricegroup::find()->where(['type_id' => 2])->groupBy('seq_no')->orderBy(['seq_no' => SORT_ASC])->all();
$model_stdgroup3 = \backend\models\Stdpricegroup::find()->where(['type_id' => 3])->groupBy('seq_no')->orderBy(['seq_no' => SORT_ASC])->all();
$model_transfer_branch = \backend\models\Transferbrach::find()->where(['status' => 1])->all();


?>
<form id="form-find" action="index.php?r=routesummarybystdgroup/index2" method="post">
    <div class="row">
        <div class="col-lg-4">
            <label for="">สายส่ง</label>
            <?php
            echo \kartik\select2\Select2::widget([
                'name' => 'route_id',
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['status' => 1])->all(), 'id', 'name'),
                'value' => $route_id,
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
                'options' => [
                    'placeholder' => '--เลือกทั้งหมด--',
                    //'onchange'=> 'form.submit()',
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <label for="">วันที่</label>
            <?php
            echo DateRangePicker::widget([
                'name' => 'search_date',
                // 'value'=>'2015-10-19 12:00 AM',
                'value' => $search_date != null ? date('Y-m-d', strtotime($search_date)) : date('Y-m-d'),
                //    'useWithAddon'=>true,
                'convertFormat' => true,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'วันที่',
                    //  'onchange' => 'this.form.submit();',
                    'autocomplete' => 'off',
                ],
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 1,
                    'locale' => ['format' => 'Y-m-d'],
                    'singleDatePicker' => true,
                    'showDropdowns' => true,
                    'timePicker24Hour' => false
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-3">
            <label for="">วันที่</label>
            <?php
            echo DateRangePicker::widget([
                'name' => 'search_to_date',
                // 'value'=>'2015-10-19 12:00 AM',
                'value' => $search_to_date != null ? date('Y-m-d', strtotime($search_to_date)) : date('Y-m-d'),
                //    'useWithAddon'=>true,
                'convertFormat' => true,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'ถึงวันที่',
                    //  'onchange' => 'this.form.submit();',
                    'autocomplete' => 'off',
                ],
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 1,
                    'locale' => ['format' => 'Y-m-d'],
                    'singleDatePicker' => true,
                    'showDropdowns' => true,
                    'timePicker24Hour' => false
                ]
            ]);
            ?>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-sm btn-info" style="margin-top:33px;">ค้นหา</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-12" style="text-align: center"><h3>รวมยอดประจำวัน</h3></div>
</div>
<div class="row">
    <div class="col-lg-12" style="text-align: center"><h3><?= $route_name ?></h3></div>
</div>
<?php
$total_all = 0;
$grand_total_all = [];
?>
<div id="div1">
    <table id="table-data" style="width: 100%">
        <tr style="font-weight: bold;">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= date('d/m/Y', strtotime($find_date)) ?></td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">รวมเงิน</td>
        </tr>
        <?php
        $product_header_sum1 = [];
        $total_all_cash = 0;
        $total_all_credit = 0;
        $total_grand = 0;
        $total_all_amount = 0;
        ?>
        <tr>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">เบิก</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php
            $line_car_issue_qty = 0;
            ?>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $product_issue_qty = getIssuecar($route_list, $find_date, $find_to_date, $product_header[$y]);
                $line_car_issue_qty += $product_issue_qty;
                array_push($product_header_sum1, ['product' => $product_header[$y], 'qty' => $product_issue_qty]);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $product_issue_qty == 0 ? '-' : number_format($product_issue_qty, 2) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;font-weight: bold;">
                -
            </td>
        </tr>
        <?php foreach ($model_transfer_branch as $value_branch): ?>
            <tr>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $value_branch->name ?></td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php
                $line_car_issue_qty = 0;
                $line_total_branch_price = 10;
                ?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $product_transfer_qty = getReceiveTransfer($route_list, $find_date, $find_to_date, $product_header[$y], $value_branch->id);
                    //  $line_car_issue_qty+=$product_issue_qty;
                    array_push($product_header_sum1, ['product' => $product_header[$y], 'qty' => $product_transfer_qty]);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $product_transfer_qty == 0 ? '-' : number_format($product_transfer_qty, 2) ?></td>
                <?php endfor; ?>
                <?php
                $line_total_branch_price = getReceiveTransferTotalamount($route_list, $find_date, $find_to_date, $value_branch->id);
                ?>
                <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= $line_total_branch_price == 0 ? '-' : number_format($line_total_branch_price, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr style="background-color: lightgreen">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;font-weight: bold;">รวมเบิก</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $sum_issue = 0;
                for ($x = 0; $x <= count($product_header_sum1) - 1; $x++) {
                    if ($product_header_sum1[$x]['product'] == $product_header[$y]) {
                        $sum_issue += (float)$product_header_sum1[$x]['qty'];
                    }
                }
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $sum_issue == 0 ? '-' : number_format($sum_issue, 2) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= 1 > 0 ? '-' : number_format(0, 2) ?></td>
        </tr>
        <tr style="background-color: #b9ca4a">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">คืน</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $product_return_qty = getReturnCar($route_list, $product_header[$y], $find_date, $find_to_date);
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $product_return_qty == 0 ? '-' : number_format($product_return_qty, 2) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= 1 > 0 ? '-' : number_format(0, 2) ?></td>
        </tr>

        <?php
        $product_header_sum = [];
        ?>
        <?php foreach ($model_stdgroup as $value_group): ?>
            <tr>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $value_group->name; ?></td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php $price_group_line_qty = 0; ?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $line_product_price_qty = getQtyByPrice($route_list, $value_group->price, $value_group->type_id, $find_date, $find_to_date, $product_header[$y]);
                    $price_group_line_qty += $line_product_price_qty;
                    array_push($product_header_sum, ['product' => $product_header[$y], 'qty' => $line_product_price_qty]);
                    array_push($grand_total_all, ['product' => $product_header[$y], 'qty' => $line_product_price_qty]);
                    $total_all_cash += ($line_product_price_qty * $value_group->price);

                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $line_product_price_qty == 0 ? '-' : number_format($line_product_price_qty, 2) ?></td>
                <?php endfor; ?>
                <?php
                $total_all_amount += ($price_group_line_qty * $value_group->price);
                ?>
                <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= ($price_group_line_qty * $value_group->price) == 0 ? '-' : number_format(($price_group_line_qty * $value_group->price), 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr style="background-color: pink">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;font-weight: bold;">รวมขายสด</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $sum_cash = 0;
                for ($x = 0; $x <= count($product_header_sum) - 1; $x++) {
                    if ($product_header_sum[$x]['product'] == $product_header[$y]) {
                        $sum_cash += (float)$product_header_sum[$x]['qty'];
                    }
                }
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                    <b><?= $sum_cash == 0 ? '-' : number_format($sum_cash, 2) ?></b></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">
                <b><?= $total_all_cash == 0 ? '-' : number_format($total_all_cash, 2) ?></b></td>
        </tr>
        <?php $product_header_sum2 = []; ?>
        <?php foreach ($model_stdgroup2 as $value_group): ?>
            <tr>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $value_group->name; ?></td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php $price_group_line_qty = 0; ?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $line_product_price_qty = getQtyByPrice($route_list, $value_group->price, $value_group->type_id, $find_date, $find_to_date, $product_header[$y]);
                    $price_group_line_qty += $line_product_price_qty;
                    array_push($product_header_sum2, ['product' => $product_header[$y], 'qty' => $line_product_price_qty]);
                    array_push($grand_total_all, ['product' => $product_header[$y], 'qty' => $line_product_price_qty]);
                    $total_all_credit += ($line_product_price_qty * $value_group->price);
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $line_product_price_qty == 0 ? '-' : number_format($line_product_price_qty, 2) ?></td>
                <?php endfor; ?>
                <?php
                $total_all_amount += ($price_group_line_qty * $value_group->price);
                ?>
                <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">
                    <?= ($price_group_line_qty * $value_group->price) == 0 ? '-' : number_format(($price_group_line_qty * $value_group->price), 2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr style="background-color: lightcoral">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;font-weight: bold;">รวมขายเชื่อ</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $sum_cash2 = 0;
                for ($x = 0; $x <= count($product_header_sum2) - 1; $x++) {
                    if ($product_header_sum2[$x]['product'] == $product_header[$y]) {
                        $sum_cash2 += (float)$product_header_sum2[$x]['qty'];
                    }
                }
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                    <b><?= $sum_cash2 == 0 ? '-' : number_format($sum_cash2, 2) ?></b></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">
                <b><?= $total_all_credit == 0 ? '-' : number_format($total_all_credit, 2) ?></b></td>
        </tr>
        <?php foreach ($model_stdgroup3 as $value_group): ?>
            <tr style="background-color: orange">
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $value_group->name; ?></td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php $price_group_line_qty = 0; ?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                    $line_product_price_qty = getQtyByPrice($route_list, $value_group->price, $value_group->type_id, $find_date, $find_to_date, $product_header[$y]);
                    $price_group_line_qty += $line_product_price_qty;
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $line_product_price_qty == 0 ? '-' : number_format($line_product_price_qty, 2) ?></td>
                <?php endfor; ?>
                <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= $price_group_line_qty == 0 ? '-' : number_format($price_group_line_qty, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr style="background-color: skyblue">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;"><b>รวมขายทั้งหมด</b></td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <?php
                $sum_total = 0;
                for ($x = 0; $x <= count($grand_total_all) - 1; $x++) {
                    if ($grand_total_all[$x]['product'] == $product_header[$y]) {
                        $sum_total += (float)$grand_total_all[$x]['qty'];
                    }
                }
                ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;">
                    <b><?= $sum_total == 0 ? '-' : number_format($sum_total, 2) ?></b></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">
                <b><?= $total_all_amount == 0 ? '-' : number_format($total_all_amount, 2) ?></b></td>
        </tr>
    </table>
</div>
<br/>
  <?php

  $cash_pay = 0;
  $transfer_pay = 0;

  $oil_amount = 0;
  $extra_amount = 0;
  $water_amount = 0;
  $money_amount = 0;
  $plus_amount = 0;

  $deduct_amount = 0;
  $cash_transfer_amount = 0;
  $payment_transfer_amount = 0;

  $model_expend = getRoutededuct($route_list,$find_date,$find_to_date);
  $payment_data = getPayment($find_date,$find_to_date,$route_list);


  $send_money_amount = 0;

  if($model_expend!=null) {
      for ($x = 0; $x <= count($model_expend) - 1; $x++) {
          $oil_amount += $model_expend[$x]['oil_amount'];
          $extra_amount += $model_expend[$x]['extra_amount'];
          $water_amount += $model_expend[$x]['water_amount'];
          $money_amount += $model_expend[$x]['money_amount'];
          $deduct_amount += $model_expend[$x]['deduct_amount'];
          $cash_transfer_amount += $model_expend[$x]['cash_transfer_amount'];
          $payment_transfer_amount += $model_expend[$x]['payment_transfer_amount'];
          $plus_amount += $model_expend[$x]['plus_amount'];
      }
  }

  if($payment_data!=null){
      for($y=0;$y<=count($payment_data)-1;$y++){
          if($payment_data[$y]['status']=='เงินสด'){

              $cash_pay = $payment_data[$y]['pay'];
//              $send_money_amount = ($total_all_cash + $cash_pay) - ($oil_amount + $extra_amount + $water_amount + $money_amount + $deduct_amount + $cash_transfer_amount + $payment_transfer_amount);
          }else if($payment_data[$y]['status']=='เงินโอน'){

              $transfer_pay = $payment_data[$y]['pay'];
//              $send_money_amount = $total_all_cash  - ($oil_amount + $extra_amount + $water_amount + $money_amount + $deduct_amount + $cash_transfer_amount + $payment_transfer_amount);
          }
      }
      $send_money_amount = ($total_all_cash + $cash_pay + $plus_amount) - ($oil_amount + $extra_amount + $water_amount + $money_amount + $deduct_amount + $cash_transfer_amount + $payment_transfer_amount);
  }else{
      $send_money_amount = ($total_all_cash + $cash_pay + $plus_amount)  - ($oil_amount + $extra_amount + $water_amount + $money_amount + $deduct_amount + $cash_transfer_amount + $payment_transfer_amount);
  }
  ?>
<div class="row">
    <div class="col-lg-6">
        <table style="width: 50%;border: 1px solid grey;">
            <tr>
                <td style="border: 1px solid grey;padding: 5px;">หักค่าน้ำมัน</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($oil_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">หักค่าเบี้ยเลี้ยง</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($extra_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">หักค่าน้ำ</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($water_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">หักค่าเก็บเงิน</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($money_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">หัก</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($deduct_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">ขายสด(โอน)</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($cash_transfer_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;">ชำระหนี้โอน</td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($payment_transfer_amount,2)?></b></td>
            </tr>
            <tr>
                <td style="border: 1px solid grey;;padding: 5px;text-align: left;"><b>+</b></td>
                <td style="text-align: right;border: 1px solid grey;padding: 5px;"><b><?=number_format($plus_amount,2)?></b></td>
            </tr>
        </table>
    </div>
    <div class="col-lg-6">
        <table style="width: 70%">
            <tr>
                <td style="width: 30%;border: 1px solid lightgrey;padding: 5px;">ชำระหนี้เงินโอน</td>
                <td style="text-align: right;border: 1px solid lightgrey;"><b><?=number_format($transfer_pay,2)?></b></td>
            </tr>
            <tr>
                <td style="width: 30%;border: 1px solid lightgrey;padding: 5px;">ชำระหนี้เงินสด</td>
                <td style="text-align: right;border: 1px solid lightgrey;"><b><?=number_format($cash_pay,2)?></b></td>
            </tr>
            <tr>
                <td style="width: 30%;border: 1px solid lightgrey;padding: 5px;">รวมส่งเงิน</td>
                <td style="text-align: right;border: 1px solid lightgrey;font-size: 25px;background-color: #00b44e;"><b><?=number_format($send_money_amount,2)?></b></td>
            </tr>
        </table>
    </div>
</div>

<br />
<table width="100%" class="table-title">
    <td style="text-align: right">
        <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
        <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
    </td>
</table>
<?php

function getQtyByPrice($route_list, $price, $sale_type, $order_date,$find_to_date, $product_id)
{
    $sale_qty = 0;

    //if ($route_id > 0) {
    $sql = "SELECT SUM(qty) as qty";
    $sql .= " FROM route_trans_price_cal";
    $sql .= " WHERE product_id =" . $product_id;
    $sql .= " AND CAST(price as DECIMAL) = CAST(" . $price." as DECIMAL)";
    $sql .= " AND std_price_type = " . $sale_type;
    $sql .= " AND date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }
    $sql .= " GROUP BY price";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $sale_qty = $model[$i]['qty'];
        }
    }
    // }


    return $sale_qty;
}


function getReceiveTransfer($route_list, $order_date,$find_to_date, $product_id, $transfer_from_id)
{
    $sale_qty = 0;

    //if ($route_id > 0) {
    $sql = "SELECT SUM(qty) as qty";
    $sql .= " FROM route_issue_daily_cal";
    $sql .= " WHERE product_id =" . $product_id;
    $sql .= " AND issue_trans_type=2";
    $sql .= " AND transfer_branch_id=" . $transfer_from_id;
    $sql .= " AND date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }

    $sql .= " GROUP BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $sale_qty = $model[$i]['qty'];
        }
    }
    //}


    return $sale_qty;
}

function getReceiveTransferTotalamount($route_list, $order_date,$find_to_date, $transfer_from_id)
{
    $total_amount = 0;

    //if ($route_id > 0) {
    $sql = "SELECT SUM(total_amount) as total_amount";
    $sql .= " FROM route_issue_daily_cal";
    $sql .= " WHERE issue_trans_type=2";
    $sql .= " AND transfer_branch_id=" . $transfer_from_id;
    $sql .= " AND date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }

    $sql .= " GROUP BY transfer_branch_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $total_amount = $model[$i]['total_amount'];
        }
    }
    //}


    return $total_amount;
}

function getIssuecar($route_list, $order_date, $find_to_date, $product_id)
{
    $sale_qty = 0;

    // print_r($route_id);return;

    //if ($route_id > 0) {
    $sql = "SELECT SUM(qty) as qty";
    $sql .= " FROM route_issue_daily_cal";
    $sql .= " WHERE product_id =" . $product_id;
    $sql .= " AND issue_trans_type=1";
    $sql .= " AND date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }

    $sql .= " GROUP BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $sale_qty = $model[$i]['qty'];
        }
    }
    // }


    return $sale_qty;
}

function getReturnCar($route_list, $product_id, $order_date,$find_to_date)
{
    $issue_qty = 0;
    $sql = "SELECT SUM(qty) as qty";
    $sql .= " FROM route_issue_daily_cal";
    $sql .= " WHERE product_id =" . $product_id;
    $sql .= " AND issue_trans_type=3";
    $sql .= " AND date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }

    $sql .= " GROUP BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getRoutededuct($route_list, $order_date,$find_to_date)
{
    $data = [];
    $sql = "SELECT SUM(oil_amount) as oil_amount,SUM(wator_amount) as water_amount,SUM(extra_amount) as extra_amount,SUM(money_amount) as money_amount,SUM(deduct_amount) as deduct_amount,SUM(cash_transfer_amount) as cash_transfer_amount,SUM(payment_transfer_amount) as payment_transfer_amount,SUM(plus_amount) as plus_amount";
    $sql .= " FROM route_trans_expend_daily";
    $sql .= " WHERE date(trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND date(trans_date) <=" . "'" . date('Y-m-d', strtotime($find_to_date)) . "'" . " ";
    if ($route_list != null) {
        $sql .= " AND route_id in " . $route_list;
    }

    //$sql .= " GROUP BY route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            array_push($data, ['oil_amount'=>$model[$i]['oil_amount'],'water_amount'=>$model[$i]['water_amount'],'extra_amount'=>$model[$i]['extra_amount'],'money_amount'=>$model[$i]['money_amount'],'deduct_amount'=>$model[$i]['deduct_amount'],'cash_transfer_amount'=>$model[$i]['cash_transfer_amount'],'payment_transfer_amount'=>$model[$i]['payment_transfer_amount'],'plus_amount'=>$model[$i]['plus_amount']]);
        }
    }
    return $data;
}



function getPayment($f_date, $t_date, $find_route_list)
{

    $data = [];

            $sql2 = "SELECT t1.payment_channel_id,sum(t1.payment_amount) as payment_amount
              FROM payment_receive_line as t1 INNER join payment_receive as t2 ON t1.payment_receive_id = t2.id inner join orders as t3 ON t1.order_id = t3.id
             WHERE date(t2.trans_date)>= " . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.trans_date)<= " . "'" . date('Y-m-d', strtotime($t_date)) . "'" . "
             AND t1.payment_amount >0 
             AND t1.payment_method_id =2 
             AND t2.status <> 100";
            if($find_route_list != null){
                $sql2 .= " AND t3.order_channel_id in " . $find_route_list;
            }
            $sql2 .= " GROUP BY t1.payment_channel_id";
            $query2 = \Yii::$app->db->createCommand($sql2);
            $model2 = $query2->queryAll();
            if ($model2) {
                for ($x = 0; $x <= count($model2) - 1; $x++) {
                    array_push($data, [
                        'pay' => $model2[$x]['payment_amount'],
                        'status'=> $model2[$x]['payment_channel_id'] == 1 ?'เงินสด':'เงินโอน',
                    ]);
                }
            }

    return $data;
}
function getPaymentLine($payment_id, $company_id, $branch_id)
{

    $data = [];
    $sql = "SELECT t1.order_id,t1.payment_amount,t1.status,t2.crated_by,t1.payment_channel_id
              FROM payment_receive_line as t1 INNER join payment_receive as t2 ON t1.payment_receive_id = t2.id
             WHERE  t1.payment_receive_id = " . $payment_id . "
             AND t1.payment_amount >0 
             AND t1.payment_method_id =2 
             AND t2.status <> 100
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
    $sql .= " GROUP BY t1.order_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            array_push($data, [
                'pay' => $model[$i]['payment_amount'],
                'status'=> $model[$i]['payment_channel_id'] == 1 ?'เงินสด':'เงินโอน',
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
