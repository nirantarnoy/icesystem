<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$company_id = 1;
$branch_id = 1;
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}

$this->title = 'รายงานสรุปการเบิก-ขาย-รับคืนแยกตามหน่วยรถ';
$pos_date = date('d/m/Y');
if ($show_pos_date != null) {
    $pos_date = date('d/m/Y', strtotime($show_pos_date));
}
$route_id = $selected_route_id;

//echo $isnew; return;

$model_line = $dataProvider->getModels();
//echo count($model_line);


//echo print_r($model_line);
//print_r($model_line);
?>
<!--<form action="--><? //= \yii\helpers\Url::to(['dailysum/index'], true) ?><!--"-->
<!--      method="post">-->
<!--    <div class="row">-->
<!---->
<!--        <div class="col-lg-3">-->
<!--            <div class="label">-->
<!--                เลือกดูตามวันที่-->
<!--            </div>-->
<!--            <div class="input-group">-->
<!--                --><?php
//                echo \kartik\date\DatePicker::widget([
//                    'name' => 'pos_date',
//                    'value' => $pos_date,
//                    'pluginOptions' => [
//                        'format' => 'dd/mm/yyyy',
//                        'todayHighlight' => true
//                    ]
//                ]);
//                ?>
<!--            </div>-->
<!---->
<!--        </div>-->
<!--        <div class="col-lg-3">-->
<!--            <div class="label">-->
<!--                สายส่ง-->
<!--            </div>-->
<!--            <div class="input-group">-->
<!--                --><?php
//                echo \kartik\select2\Select2::widget([
//                    'name' => 'route_id',
//                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->all(),'id','name'),
//                    'value' => $route_id,
//                    'options' => [
//                       'placeholder'=>'เลือกสายส่ง'
//                    ]
//                ]);
//                ?>
<!--            </div>-->
<!---->
<!--        </div>-->
<!--        <div class="col-lg-2">-->
<!--            <div class="label"-->
<!--                 style="color: white">-->
<!--                ค้นหา-->
<!--            </div>-->
<!--            <input type="submit"-->
<!--                   class="btn btn-primary"-->
<!--                   value="ค้นหา"></input>-->
<!--        </div>-->
<!---->
<!--    </div>-->
<!--</form>-->
<div id="div3">
    <div id="div1">
        <div class="row">
            <div class="col-lg-12">
                <?php echo $this->render('_search', ['model' => $searchModel, 'company_id' => $company_id, 'branch_id' => $branch_id]); ?>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-lg-12">
                <table class="table" style="width: 100%" id="table-data">
                    <tr>
                        <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                            <b>NO</b>
                        </td>
                        <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                            <b>สินค้า</b>
                        </td>
                        <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                            <b>ยกมา</b>
                        </td>
                        <td colspan="7" style="text-align: center;border: 1px solid grey"><b>สินค้าเข้า</b></td>
                        <td colspan="6" style="text-align: center;border: 1px solid grey"><b>สินค้าออก</b></td>
                        <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                            <b>คงเหลือ</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;border: 1px solid grey"><b>รับสินค้า</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>รับคืนสินค้า</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>รับโอนรถ</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>รับโอนคลัง</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>ปรับยอดเข้า</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>ยกเลิกขาย</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>รวมสินค้าเข้า</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>การขาย</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>คืนจากรถ</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>โอนไปรถ</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>ปรับยอดออก</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>เบิกสินค้า</b></td>
                        <td style="text-align: center;border: 1px solid grey"><b>รวมสินค้าออก</b></td>

                    </tr>
                    <?php if (1 == 1): ?>
                        <?php

                        $product_trans = getProductdaily5($company_id, $branch_id, $route_id, $show_pos_date);
                        //  $product_trans = getProductdaily4($company_id, $branch_id, $route_id, $show_pos_date);
                        $nums = 0;

                        // in
                        $total_issue_line = 0;
                        $total_issue_line_sum = 0;
                        $total_issue_sum = 0;

                        // out

                        $total_sale_qty_line = 0;
                        $total_sale_qty_line_sum = 0;
                        $total_sale_qty_sum_all = 0;
                        $total_sale_qty_line_sum_all = 0;
                        $total_car_return_qty_sum = 0;
                        $total_route_old_stock_sum = 0;
                        $total_transfer_in_qty_sum = 0;
                        $total_transfer_out_qty_sum = 0;
                        $prev_product = [];
                        ?>
                        <?php for ($i = 0; $i <= count($product_trans) - 1; $i++): ?>
                            <?php $nums += 1; ?>
                            <?php
                          //  if (in_array($product_trans[$i]['product_id'], $prev_product)) continue;
                            //in
                            $line_route_old_stock = getRouteOldStock($route_id, $product_trans[$i]['product_id'], $show_pos_date);
                            $total_route_old_stock_sum += $line_route_old_stock;

                            $issue_qty = getIssuecar($route_id, $product_trans[$i]['product_id'], $show_pos_date, $user_id);
                            $total_issue_line = ($issue_qty + $line_route_old_stock);
                            $total_issue_sum = $total_issue_sum + $total_issue_line;
                            $total_issue_line_sum = $total_issue_line_sum + $total_issue_line;
                            //out
                            $total_sale_qty_line = getSalecar($route_id, $product_trans[$i]['product_id'], $show_pos_date, $user_id);
                            $total_sale_qty_line_sum = $total_sale_qty_line;

                            $total_car_return_qty = getReturnCar($route_id, $product_trans[$i]['product_id'], $show_pos_date, $user_id);
                            $total_car_return_qty_sum = $total_car_return_qty_sum + $total_car_return_qty;

                            $total_sale_qty_sum_all = $total_sale_qty_sum_all + $total_sale_qty_line;
                            $total_sale_qty_line_sum_all = $total_sale_qty_line_sum_all + $total_sale_qty_line_sum;

                            $total_transfer_out_qty = getTransferout($route_id, $product_trans[$i]['product_id'], $show_pos_date, $user_id);
                            $total_transfer_out_qty_sum = $total_transfer_out_qty_sum + $total_transfer_out_qty;

                            $total_transfer_in_qty = getTransferin($route_id, $product_trans[$i]['product_id'], $show_pos_date, $user_id);
                            $total_transfer_in_qty_sum = $total_transfer_in_qty_sum + $total_transfer_in_qty;


                            if ($issue_qty <= 0 && $total_transfer_in_qty <=0) continue;

                            ?>

                            <tr>
                                <td style="text-align: center;border: 1px solid grey"><?= $nums ?></td>
                                <td style="text-align: left;border: 1px solid grey"><?= $product_trans[$i]['product_name'] ?></td>
                                <td style="text-align: center;border: 1px solid grey"><?= number_format($line_route_old_stock, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($issue_qty, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($total_transfer_in_qty, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format(($total_issue_line + $total_transfer_in_qty), 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($total_sale_qty_line, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($total_car_return_qty, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($total_transfer_out_qty, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($total_sale_qty_line_sum + $total_car_return_qty + $total_transfer_out_qty, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format(($total_issue_line + $total_transfer_in_qty) - ($total_sale_qty_line_sum + $total_car_return_qty + $total_transfer_out_qty), 2) ?></td>
                            </tr>
                            <?php //array_push($prev_product, $product_trans[$i]['product_id']); ?>
                        <?php endfor; ?>
                        <tfoot>
                        <tr style="background-color: #1abc9c">
                            <td colspan="2" style="text-align: center;border: 1px solid grey"><b>รวมทั้งหมด</b></td>
                            <td style="text-align: left;border: 1px solid grey">
                                <b><?= number_format($total_route_old_stock_sum, 2) ?></b></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_issue_sum, 2) ?></b>
                            </td>
                            <td style="text-align: center;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_transfer_in_qty_sum, 2) ?></b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format(($total_issue_line_sum + $total_transfer_in_qty_sum), 2) ?></b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_sale_qty_sum_all, 2) ?></b></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_car_return_qty_sum, 2) ?></b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_transfer_out_qty_sum, 2) ?></b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey"></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format($total_sale_qty_line_sum_all, 2) ?></b></td>
                            <td style="text-align: right;border: 1px solid grey">
                                <b><?= number_format(($total_issue_sum + $total_transfer_in_qty_sum) - ($total_sale_qty_line_sum_all) - $total_car_return_qty_sum - $total_transfer_out_qty_sum, 2) ?></b>
                            </td>
                        </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <br/>

    </div>
    <table width="100%" class="table-title">
        <td style="text-align: right">
            <button id="btn-export-excel-top" class="btn btn-secondary">Export Excel</button>
            <!--            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>-->
        </td>
    </table>
    <br/>
    <?php if (1 == 1): ?>
        <div id="div2">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table" style="width: 100%" id="table-data-2">
                        <tr>
                            <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                                <b>รหัส</b></td>
                            <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle"><b>รายการสินค้า</b>
                            </td>
                            <td rowspan="2" style="text-align: center;border: 1px solid grey;vertical-align: middle">
                                <b>หน่วยนับ</b>
                            </td>
                            <td colspan="3" style="text-align: center;border: 1px solid grey"><b>ขายสด</b></td>
                            <td colspan="3" style="text-align: center;border: 1px solid grey"><b>ขายเชื่อ</b></td>
                            <td colspan="2" style="text-align: center;border: 1px solid grey"><b>รวมขาย</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border: 1px solid grey"><b>ราคาต่อหน่วย</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>จำนวน</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>รวมเงิน</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>ราคาต่อหน่วย</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>จำนวน</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>รวมเงิน</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>จำนวน</b></td>
                            <td style="text-align: center;border: 1px solid grey"><b>ยอดเงิน</b></td>
                        </tr>
                        <?php
                        $total_all_cash = 0;
                        $total_all_credit = 0;
                        $total_all_qty_cash = 0;
                        $total_all_qty_credit = 0;
                        $last_product = '';
                        $total_all_qty_sum = 0;
                        ?>
                        <?php foreach ($model_line as $value): ?>
                            <?php
                            $is_group = 0;
                            //   $line_qty_sum = $value->line_qty_cash + $value->line_qty_credit;
                            $line_qty_sum = $value->qty;
                            $line_amount_sum = $value->line_total_cash + $value->line_total_credit;

                            $total_all_qty_sum = $total_all_qty_sum + $line_qty_sum;

                            $total_all_cash = $total_all_cash + $value->line_total_cash;
                            $total_all_credit = $total_all_credit + $value->line_total_credit;

                            //     $total_all_qty_cash = $total_all_qty_cash + $value->line_qty_cash;
                            //    $total_all_qty_credit = $total_all_qty_credit + $value->line_qty_credit;

                            if ($last_product == $value->code) {
                                $is_group = 1;
                            } else {
                                $is_group = 0;
                            }
                            $last_product = $value->code;
                            ?>
                            <tr style="background-color: <?= $value->line_qty_free > 0 ? '#5bc0de' : '' ?>">
                                <td style="text-align: center;border: 1px solid grey;"><?= $is_group == 1 ? '' : $value->code ?></td>
                                <td style="text-align: center;border: 1px solid grey"><?= $is_group == 1 ? '' : $value->name ?></td>
                                <td style="text-align: center;border: 1px solid grey"><?= \backend\models\Product::findUnitname($value->product_id) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->price, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->line_qty_cash, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->line_total_cash, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->price, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->line_qty_credit + $value->line_qty_free, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($value->line_total_credit, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($line_qty_sum, 2) ?></td>
                                <td style="text-align: right;border: 1px solid grey"><?= number_format($line_amount_sum, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tfoot>
                        <?php
                        $discount_data = orderdiscount($route_id);
                        ?>
                        <tr style="background-color: #e0a800;color: black">
                            <td colspan="5" style="text-align: right;border: 1px solid grey;text-align: right">
                                <b>ส่วนลด</b></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <?= number_format($discount_data[0]['discount_cash_amount']) ?>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right"></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">

                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <?= number_format($discount_data[0]['discount_credit_amount']) ?>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right"></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <?= number_format(($discount_data[0]['discount_cash_amount'] + $discount_data[0]['discount_credit_amount'])) ?>
                            </td>
                        </tr>
                        <tr style="background-color: #1abc9c">
                            <td colspan="3" style="text-align: right;border: 1px solid grey;;text-align: center">
                                <b>รวม</b>
                            </td>
                            <td colspan="2" style="text-align: right;border: 1px solid grey;text-align: right">
                                <b>รวมขายสด</b></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format(($total_all_cash - ($discount_data[0]['discount_cash_amount'])), 2) ?></b>
                            </td>
                            <td colspan="2" style="text-align: right;border: 1px solid grey;text-align: right">
                                <b>รวมขายเชื่อ</b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format(($total_all_credit - ($discount_data[0]['discount_credit_amount'])), 2) ?></b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format($total_all_qty_sum, 2) ?></b></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format(($total_all_cash + $total_all_credit) - ($discount_data[0]['discount_cash_amount'] + $discount_data[0]['discount_credit_amount']), 2) ?></b>
                            </td>
                        </tr>
                        <tr style="background-color: #1abc9c">
                            <td colspan="3" style="text-align: right;border: 1px solid grey;text-align: center"><b>ยอดรวมการรับชำระหนี้</b>
                            </td>
                            <td colspan="2" style="text-align: right;border: 1px solid grey;text-align: right">
                                <b>รับชำระหนี้</b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format(getPayment($route_id, $show_pos_date), 2) ?></b></td>
                            <td colspan="2" style="text-align: right;border: 1px solid grey;text-align: right"><b>รับชำระหนี้(ยกเว้นภาษี)</b>
                            </td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right"><b></b></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right"><b>รวม</b></td>
                            <td style="text-align: right;border: 1px solid grey;text-align: right">
                                <b><?= number_format(getPayment($route_id, $show_pos_date) + 0, 2) ?></b></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-lg-12">
                    <p>สรุปรายละเอียดการรับเงินทั้งหมดจากรายงานขายสดและชำระหนี้(รวมทุกกลุ่มสินค้า)</p>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-lg-6">
                    <table class="table" style="width: 100%">
                        <tr>
                            <td style="text-align: center;border: 1px solid grey">ที่</td>
                            <td style="text-align: center;border: 1px solid grey">ประเภท</td>
                            <td style="text-align: center;border: 1px solid grey">รายละเอียด</td>
                            <td style="text-align: center;border: 1px solid grey">รวมเงิน</td>
                        </tr>
                        <?php
                        $cash_pay = getPayment($route_id, $show_pos_date);
                        $bank_pay = getPaymentBank($route_id, $show_pos_date);
                        ?>
                        <?php if ($cash_pay > 0): ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey">1</td>
                                <td style="text-align: center;border: 1px solid grey">เงินสด</td>
                                <td style="text-align: center;border: 1px solid grey"></td>
                                <td style="text-align: center;border: 1px solid grey"><?= number_format($cash_pay + ($total_all_cash)) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($bank_pay > 0): ?>
                            <tr>
                                <td style="text-align: center;border: 1px solid grey">2</td>
                                <td style="text-align: center;border: 1px solid grey">โอนธนาคาร</td>
                                <td style="text-align: center;border: 1px solid grey"></td>
                                <td style="text-align: center;border: 1px solid grey"><?= number_format($bank_pay + ($total_all_cash)) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">

                </div>
                <div class="col-lg-2"></div>
            </div>
        </div>
    <?php endif; ?>
</div>
<br/>
<table width="100%" class="table-title">
    <td style="text-align: right">
        <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
        <button id="btn-print" class="btn btn-warning" onclick="printContent('div3')">Print</button>
    </td>
</table>

<?php
function getProductdaily($company_id, $branch_id, $route_id, $order_date)
{
    $data = [];
    $sql = "SELECT t1.product_id
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id
            WHERE  date(t2.order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " 
             AND t2.sale_channel_id = 1 
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
    if ($route_id != null) {
        $sql .= " AND t2.order_channel_id = " . $route_id;
    }
    $sql .= " GROUP BY product_id ORDER BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $product_code = \backend\models\Product::findCode($model[$i]['product_id']);
            $product_name = \backend\models\Product::findName($model[$i]['product_id']);
            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'product_code' => $product_code,
                'product_name' => $product_name,
            ]);
        }
    }
    return $data;
}

function getProductdaily2($company_id, $branch_id, $route_id, $order_date)
{
    $data = [];
    $data2 = [];
    $sql = "SELECT t1.product_id
              FROM query_order_daily_product as t1
            WHERE  date(t1.order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'";
    if ($route_id != null) {
        $sql .= " AND t1.order_channel_id = " . $route_id;
    }
    $sql .= " GROUP BY product_id ORDER BY t1.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $product_code = \backend\models\Product::findCode($model[$i]['product_id']);
            $product_name = \backend\models\Product::findName($model[$i]['product_id']);
            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'product_code' => $product_code,
                'product_name' => $product_name,
            ]);

        }
//        if ($data != null) {
//            // $data2 = $data;
//            for ($x = 0; $x <= count($data) - 1; $x++) {
//                $model_check = \backend\models\Stocktrans::find()->select('product_id')->where(['trans_ref_id' => $route_id, 'date(trans_date)' => date('Y-m-d', strtotime($order_date))])->andFilterWhere(['activity_type_id' => 7])->andFilterWhere(['!=', 'product_id', $data[$x]['product_id']])->groupBy(['product_id'])->one();
//                if ($model_check) {
//                    if (in_array($model_check->product_id, $data2)) {
//                        continue;
//                    }
//                    $product_code = \backend\models\Product::findCode($model_check->product_id);
//                    $product_name = \backend\models\Product::findName($model_check->product_id);
//
//                    array_push($data2, $model_check->product_id);
//                    array_push($data, [
//                        'product_id' => $model_check->product_id,
//                        'product_code' => $product_code,
//                        'product_name' => $product_name,
//                    ]);
//                }
//            }
//        }
    }
    return $data;
}

function getProductdaily3($company_id, $branch_id, $route_id, $order_date)
{
    $data = [];
    $data2 = [];
    $sql = "SELECT t1.product_id
              FROM order_stock as t1
            WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'";
    if ($route_id != null) {
        $sql .= " AND t1.route_id = " . $route_id;
    }
    $sql .= " GROUP BY t1.product_id ORDER BY t1.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $product_code = \backend\models\Product::findCode($model[$i]['product_id']);
            $product_name = \backend\models\Product::findName($model[$i]['product_id']);
            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'product_code' => $product_code,
                'product_name' => $product_name,
            ]);

        }
//        if ($data != null) {
//            // $data2 = $data;
//            for ($x = 0; $x <= count($data) - 1; $x++) {
//                $model_check = \backend\models\Stocktrans::find()->select('product_id')->where(['trans_ref_id' => $route_id, 'date(trans_date)' => date('Y-m-d', strtotime($order_date))])->andFilterWhere(['activity_type_id' => 7])->andFilterWhere(['!=', 'product_id', $data[$x]['product_id']])->groupBy(['product_id'])->one();
//                if ($model_check) {
//                    if (in_array($model_check->product_id, $data2)) {
//                        continue;
//                    }
//                    $product_code = \backend\models\Product::findCode($model_check->product_id);
//                    $product_name = \backend\models\Product::findName($model_check->product_id);
//
//                    array_push($data2, $model_check->product_id);
//                    array_push($data, [
//                        'product_id' => $model_check->product_id,
//                        'product_code' => $product_code,
//                        'product_name' => $product_name,
//                    ]);
//                }
//            }
//        }
    }
    return $data;
}

function getProductdaily4($company_id, $branch_id, $route_id, $order_date)
{
    $data = [];
    $sql = "SELECT t1.product_id
              FROM stock_trans as t1
            WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'";
    if ($route_id != null) {
        $sql .= " AND t1.trans_ref_id = " . $route_id;
    }
    $sql .= " GROUP BY product_id ORDER BY t1.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $product_code = \backend\models\Product::findCode($model[$i]['product_id']);
            $product_name = \backend\models\Product::findName($model[$i]['product_id']);
            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'product_code' => $product_code,
                'product_name' => $product_name,
            ]);
        }
    }
    return $data;
}

function getProductdaily5($company_id, $branch_id, $route_id, $order_date)
{
    $data = [];
    $sql = "SELECT t1.product_id
              FROM order_stock as t1
            WHERE  date(t1.trans_date) >=" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " 
             AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;
    if ($route_id != null) {
        $sql .= " AND t1.route_id = " . $route_id;
    }
    $sql .= " GROUP BY t1.product_id ORDER BY t1.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $product_code = \backend\models\Product::findCode($model[$i]['product_id']);
            $product_name = \backend\models\Product::findName($model[$i]['product_id']);
            array_push($data, [
                'product_id' => $model[$i]['product_id'],
                'product_code' => $product_code,
                'product_name' => $product_name,
            ]);
        }
    }
    return $data;
}

function getRouteOldStock($route_id, $product_id, $order_date)
{
    $qty = 0;
    // $old_qty = \common\models\SaleRouteDailyClose::find()->where(['route_id' => $route_id, 'product_id' => $product_id])->sum('qty');
    $pre_date = null;
    $shif = 0;

    if ($order_date != null) {

        $max_shift2 = \common\models\SaleRouteDailyClose::find()->where(['route_id' => $route_id, 'date(trans_date)' => date('Y-m-d', strtotime($order_date))])->max('order_shift');
        if ($max_shift2 > 0 && $max_shift2 == 1) {
            $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
            $shif = $max_shift2;
        } else {
            $pre_date = date('Y-m-d', strtotime($order_date));
            $shif = $max_shift2 - 1;
        }

    }

//    $old_qty = \common\models\SaleRouteDailyClose::find()->where(['route_id' => $route_id, 'product_id' => $product_id])->orderBy(['id'=>SORT_DESC])->one();
    $old_qty = \common\models\SaleRouteDailyClose::find()->where(['route_id' => $route_id, 'product_id' => $product_id, 'date(trans_date)' => $pre_date, 'order_shift' => $shif])->orderBy(['id' => SORT_DESC])->one();
    if ($old_qty) {
        $qty = $old_qty->qty;
    }
    return $qty;
}

function getIssuecar($route_id, $product_id, $order_date, $user_id)
{
    $issue_qty = 0;

    $sql = "SELECT SUM(t2.origin_qty) as qty";
    $sql .= " FROM journal_issue as t1 INNER JOIN journal_issue_line as t2 ON t2.issue_id = t1.id";
    $sql .= " WHERE t2.product_id =" . $product_id;
    $sql .= " AND not isnull(t1.delivery_route_id)";
    $sql .= " AND t1.status in (2)";
    $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    if ($route_id != null) {
        $sql .= " AND t1.delivery_route_id=" . $route_id;
    }
    $sql .= " GROUP BY t2.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getSalecar($route_id, $product_id, $order_date, $user_id)
{
    $issue_qty = 0;

//    $sql = "SELECT SUM(line_qty_cash + line_qty_credit) as qty";
    $sql = "SELECT id,product_id, SUM(qty) as qty";
    $sql .= " FROM query_sale_mobile_data_new";
    $sql .= " WHERE  product_id =" . $product_id;
    //$sql .= " AND  order_line_status <> 500";
    $sql .= " AND date(order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    if ($route_id != null) {
        $sql .= " AND route_id=" . $route_id;
    }
    if ($user_id != null) {
        $sql .= " AND created_by=" . $user_id;
    }
    $sql .= " GROUP BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $cancel_qty = getSalecarcancel($model[$i]['product_id'], $model[$i]['id']);
            //  $issue_qty = ($model[$i]['qty'] - $cancel_qty);
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getSalecarcancel($product_id, $order_id)
{
    $cancel_qty = 0;

//    $sql = "SELECT SUM(line_qty_cash + line_qty_credit) as qty";
    $sql = "SELECT SUM(qty) as qty";
    $sql .= " FROM query_sale_order_car_cancel";
    $sql .= " WHERE  product_id =" . $product_id;

    $sql .= " AND order_id=" . $order_id;

    $sql .= " GROUP BY product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $cancel_qty = $model[$i]['qty'];
        }
    }
    return $cancel_qty;
}

function getReturnCar($route_id, $product_id, $order_date, $user_id)
{
    $issue_qty = 0;

//    $sql = "SELECT SUM(t1.qty) as qty";
//    $sql .= " FROM stock_trans as t1 INNER JOIN orders as t2 ON t1.trans_ref_id=t2.id";
//    $sql .= " WHERE  t1.product_id =" . $product_id;
//    $sql .= " AND t1.activity_type_id=7";
//    $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
//    if ($route_id != null) {
//        $sql .= " AND t2.order_channel_id=" . $route_id;
//    }
    $sql = "SELECT SUM(t1.qty) as qty";
    $sql .= " FROM stock_trans as t1 ";
    $sql .= " WHERE  t1.product_id =" . $product_id;
    $sql .= " AND t1.activity_type_id in (7,26)";
    $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    if ($route_id != null) {
        $sql .= " AND t1.trans_ref_id=" . $route_id;
    }
    if ($user_id != null) {
        $sql .= " AND created_by=" . $user_id;
    }
    $sql .= " GROUP BY t1.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getTransferout($route_id, $product_id, $order_date, $user_id)
{
    $issue_qty = 0;

    $sql = "SELECT SUM(t2.qty) as qty";
    $sql .= " FROM journal_transfer as t1 INNER JOIN transfer_line as t2 ON t2.transfer_id=t1.id";
    $sql .= " WHERE  t2.product_id =" . $product_id;
    if ($route_id != null) {
        $sql .= " AND t1.from_route_id=" . $route_id;
    }
    if ($user_id != null) {
        $sql .= " AND t1.created_by=" . $user_id;
    }

    $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";

    $sql .= " GROUP BY t2.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getTransferin($route_id, $product_id, $order_date, $user_id)
{
    $issue_qty = 0;

    $sql = "SELECT SUM(t2.qty) as qty";
    $sql .= " FROM journal_transfer as t1 INNER JOIN transfer_line as t2 ON t2.transfer_id=t1.id";
    $sql .= " WHERE  t2.product_id =" . $product_id;
    if ($route_id != null) {
        $sql .= " AND t1.to_route_id=" . $route_id;
    }
    $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";

    $sql .= " GROUP BY t2.product_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $issue_qty = $model[$i]['qty'];
        }
    }
    return $issue_qty;
}

function getPayment($route_id, $order_date)
{
    $pay_amount = 0;

    $sql = "SELECT SUM(t1.payment_amount) as pay_amount";
    $sql .= " FROM query_payment_receive as t1 INNER JOIN customer as t2 ON t1.customer_id = t2.id";
    $sql .= " WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND t1.payment_method_id = 2";
    if ($route_id != null) {
        $sql .= " AND t2.delivery_route_id=" . $route_id;
    }
    $sql .= " GROUP BY t2.delivery_route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $pay_amount = $model[$i]['pay_amount'];
        }
    }
    return $pay_amount;
}

function getPaymentBank($route_id, $order_date)
{
    $pay_amount = 0;

    $sql = "SELECT SUM(t1.payment_amount) as pay_amount";
    $sql .= " FROM query_payment_receive as t1 INNER JOIN customer as t2 ON t1.customer_id = t2.id";
    $sql .= " WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
    $sql .= " AND t1.payment_channel_id = 3";
    if ($route_id != null) {
        $sql .= " AND t2.delivery_route_id=" . $route_id;
    }
    $sql .= " GROUP BY t2.delivery_route_id";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $pay_amount = $model[$i]['pay_amount'];
        }
    }
    return $pay_amount;
}

function orderdiscount($route_id)
{

    $data = [];
    $cash_dis = 0;
    $credit_dis = 0;

    $model_cash_amt = \common\models\Orders::find()->innerJoin('order_line_trans', 'orders.id=order_line_trans.order_id')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_trans.status', 500])->sum('discount_amt');
    $model_credit_amt = \common\models\Orders::find()->innerJoin('order_line_trans', 'orders.id=order_line_trans.order_id')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_trans.status', 500])->sum('discount_amt');
    // $model = \common\models\Orders::find()->where(['id'=>131])->all();
    //  $model = \common\models\Orders::find()->where(['car_ref_id' => $car_id])->all();
    if ($model_cash_amt != null) {
        $cash_dis = $model_cash_amt;
    }
    if ($model_credit_amt != null) {
        $credit_dis = $model_credit_amt;
    }

    array_push($data, [
        'discount_cash_amount' => $cash_dis,
        'discount_credit_amount' => $credit_dis,
    ]);

    return $data;
}

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
