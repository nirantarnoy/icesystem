<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use kartik\daterange\DateRangePicker;
use yii\web\Response;

//require_once __DIR__ . '/vendor/autoload.php';
//require_once 'vendor/autoload.php';
// เพิ่ม Font ให้กับ mPDF

$user_id = \Yii::$app->user->id;
$find_sale_type = 0;
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
//$model_product_daily = \common\models\QueryProductTransDaily::find()->where(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
//$model_product_daily = \common\models\StockTrans::find()->select("product_id")->where(['BETWEEN', 'trans_date', date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])->andFilterWhere(['activity_type_id' => 5, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product_id' => SORT_ASC])->all();
$model_product_daily = \backend\models\Product::find()->where(['status' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['item_pos_seq' => SORT_ASC])->all();
//$user_login_datetime = '';
//$model_c_login = LoginLog::find()->select('MIN(login_date) as login_date')->where(['user_id' => $user_id, 'status' => 1])->one();
//if ($model_c_login != null) {
//    $user_login_datetime = date('Y-m-d H:i:s', strtotime($model_c_login->login_date));
//} else {
//    $user_login_datetime = date('Y-m-d H:i:s');
//}
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
            font-size: 16px;
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
            border: 1px solid grey;
            border-spacing: 0px;
        }

        table.table-title td, th {
            border: 1px solid #dddddd;
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

<div>
    <form action="<?= \yii\helpers\Url::to(['pos/printpossummary'], true) ?>" method="post" id="form-search">
        <input type="hidden" class="btn-order-type" name="btn_order_type" value="<?= $btn_order_type ?>">
        <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
            <tr>
                <td style="padding: 10px;"><span>เรียงตาม <div class="btn-group"><div
                                    class="btn btn-sm <?= $btn_order_type == 1 ? "btn-success" : "btn-default" ?> btn-order-date">วันที่ขาย</div><div
                                    class="btn btn-sm <?= $btn_order_type == 2 ? "btn-success" : "btn-default" ?> btn-order-price">ราคาขาย</div></div></span>
                </td>

            </tr>
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
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_user_id',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\User::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'username'),
                        'value' => $find_user_id,
                        'options' => [
                            'placeholder' => '--พนักงานขาย--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ]);
                    ?>
                </td>
                <td>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'is_invoice_req',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\helpers\CustomerInvoiceReqType::asArrayObject(), 'id', 'name'),
                        'value' => $is_invoice_req,
                        'options' => [
                            'placeholder' => '--ทั้งหมด--'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'multiple' => false,
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
                <td style="text-align: center; font-size: 20px; font-weight: bold">รายงานแสดงยอดขายหน้าบ้าน</td>
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
                    style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-left: 2px solid black">
                    <b>รหัสสินค้า</b></td>
                <td rowspan="2" style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray"><b>ชื่อสินค้า</b>
                </td>
                <td rowspan="2"
                    style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 2px solid black">
                    <b>ราคา</b></td>
                <td colspan="5"
                    style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid grey;border-right: 2px solid black">
                    <b>จำนวนน้ำแข็ง</b></td>
                <td colspan="5"
                    style="text-align: center;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 2px solid black;">
                    <b>จำนวนเงิน</b></td>
            </tr>
            <tr>

                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray"><b>ขายสด</b>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray"><b>ขายเชื่อหน้าบ้าน</b>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายรถ</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายเชื่อต่างสาขา</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid grey;border-right: 2px solid black">
                    <b>รวม</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายสด</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายเชื่อหน้าบ้าน</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายรถ</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray">
                    <b>ขายเชื่อต่างสาขา</b>
                </td>
                <td style="text-align: right;border-top: 1px solid gray;border-bottom: 1px solid gray;border-right: 2px solid black">
                    <b>รวม</b>
                </td>
            </tr>
            <?php
            $sum_qty_all = 0;
            $sum_total_all = 0;

            $total_qty = 0;
            $total_qty2 = 0;
            $total_qty3 = 0;
            $total_qty4 = 0;
            $total_qty5 = 0;
            $total_qty_all = 0;

            $total_amount = 0;
            $total_amount2 = 0;
            $total_amount3 = 0;
            $total_amount4 = 0;
            $total_amount5 = 0;
            $total_amount_all = 0;
            ?>
            <?php foreach ($model_product_daily as $value): ?>
                <?php
                $line_product_code = \backend\models\Product::findCode($value->id);
                $line_product_name = \backend\models\Product::findName($value->id);

                $line_product_price_list = getProductpricelist($value->id, $from_date, $to_date, $company_id, $branch_id);

                ?>
                <tr class="line-detail-sum" data-var="<?= $value->id ?>">
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold;border-left: 2px solid black"><?= $line_product_code ?></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"><?= $line_product_name ?></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold;border-right: 2px solid black;"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold;border-right: 2px solid black;"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold"></td>
                    <td style="text-align: right;background-color: lightskyblue;font-weight: bold;border-right: 2px solid black"></td>
                </tr>
                <?php if ($line_product_price_list != null): ?>
                    <?php for ($x = 0; $x <= count($line_product_price_list) - 1; $x++): ?>

                        <?php


                        $line_qty = $line_product_price_list[$x]['cash_qty'];
                        $line_qty2 = $line_product_price_list[$x]['credit_pos_qty'];
                        $line_qty4 = $line_product_price_list[$x]['other_branch_qty'];
                        $line_qty5 = $line_product_price_list[$x]['car_qty'];

                        $line_total_qty = ($line_qty + $line_qty2 + $line_qty4 + $line_qty5);
                        $total_qty_all = ($total_qty_all + $line_total_qty);

                        $line_amount = $line_product_price_list[$x]['cash_amount'];
                        $line_amount2 = $line_product_price_list[$x]['credit_pos_amount'];
                        $line_amount4 = $line_product_price_list[$x]['other_branch_amount'];
                        $line_amount5 = $line_product_price_list[$x]['car_amount'];

                        $line_total_amt = ($line_amount + $line_amount2 + $line_amount4 + $line_amount5);
                        $total_amount_all = ($total_amount_all + $line_total_amt);

                        $total_qty = ($total_qty + $line_qty);
                        $total_qty2 = ($total_qty2 + $line_qty2);
                        //  $total_qty3 = ($total_qty3 + $line_qty3);
                        $total_qty4 = ($total_qty4 + $line_qty4);
                        $total_qty5 = ($total_qty5 + $line_qty5);

                        $total_amount = ($total_amount + $line_amount);
                        $total_amount2 = ($total_amount2 + $line_amount2);
                        //  $total_amount3 = ($total_amount3 + $line_amount3);
                        $total_amount4 = ($total_amount4 + $line_amount4);
                        $total_amount5 = ($total_amount5 + $line_amount5);


                        ?>
                        <tr class="line-detail" data-var="<?= $value->id ?>">
                            <td style="border-left: 2px solid black"></td>
                            <td></td>
                            <td style="text-align: center;border-right: 2px solid black"><?= number_format($line_product_price_list[$x]['price'], 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_qty, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_qty2, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_qty5, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_qty4, 2) ?></td>
                            <td style="text-align: right;border-right: 2px solid black;"><?= number_format($line_total_qty, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_amount, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_amount2, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_amount5, 2) ?></td>
                            <td style="text-align: right;"><?= number_format($line_amount4, 2) ?></td>
                            <td style="text-align: right;border-right: 2px solid black"><?= number_format($line_total_amt, 2) ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>

            <?php endforeach; ?>

            <tfoot>
            <tr>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;border-left: 2px solid black"></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;"></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;border-right: 2px solid black"></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_qty, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_qty2, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_qty5, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_qty4, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;border-right: 2px solid black">
                    <b><?= number_format($total_qty_all, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_amount, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_amount2, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_amount5, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;">
                    <b><?= number_format($total_amount4, 2) ?></b></td>
                <td style="text-align: right;border-top: 3px solid black;border-bottom: 3px solid black;padding: 5px;border-right: 2px solid black">
                    <b><?= number_format($total_amount_all, 2) ?></b></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <br/>
    <table width="100%" class="table-title" style="border: none;">
        <td style="text-align: right">
            <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
        </td>
    </table>

    <br/>
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
</div>
</body>
</html>
<?php
function getProductpricelist($product_id, $f_date, $t_date, $company_id, $branch_id)
{
    $data = [];
    $sql = "SELECT price,sum(cash_qty) as cash_qty,sum(credit_pos_qty) as credit_pos_qty,sum(car_qty) as car_qty,sum(other_branch_qty) as other_branch_qty,
       sum(qty_total) as qty_total,sum(cash_amount) as cash_amount,sum(credit_pos_amount) as credit_pos_amount,sum(car_amount) as car_amount,sum(other_branch_amount) as other_branch_amount,
       sum(amount_total) as amount_total
              FROM transaction_manager_daily as t1
             WHERE  t1.trans_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . " 
             AND t1.trans_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id;
    $sql .= " GROUP BY t1.price";
    $sql .= " ORDER BY t1.price asc";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            array_push($data, [
                'price' => $model[$i]['price'],
                'cash_qty' => $model[$i]['cash_qty'],
                'credit_pos_qty' => $model[$i]['credit_pos_qty'],
                'car_qty' => $model[$i]['car_qty'],
                'other_branch_qty' => $model[$i]['other_branch_qty'],
                'qty_total' => $model[$i]['qty_total'],
                'cash_amount' => $model[$i]['cash_amount'],
                'credit_pos_amount' => $model[$i]['credit_pos_amount'],
                'car_amount' => $model[$i]['car_amount'],
                'other_branch_amount' => $model[$i]['other_branch_amount'],
                'amount_total' => $model[$i]['cash_qty'],
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
      
     $("#table-data tr.line-detail-sum").each(function(){
         var header_product = $(this).attr('data-var');
         var line_qty = 0;
         var line_qty2 = 0;
         var line_qty5 = 0;
         var line_qty4 = 0;
         var line_total_qty = 0;
         var line_amount = 0;
         var line_amount2 = 0;
         var line_amount5 = 0;
         var line_amount4 = 0;
         var line_total_amount = 0;
         $("#table-data tr.line-detail").each(function(){
             var line_product = $(this).attr('data-var');
             if(line_product == header_product){
                // line_qty = parseFloat(line_qty) + parseFloat($(this).closest('tr').find('.line-qty').val());
                 line_qty = parseFloat(line_qty) + parseFloat($(this).closest('tr').find('td:eq(3)').html().replace(',','').replace(',',''));
                 line_qty2 = parseFloat(line_qty2) + parseFloat($(this).closest('tr').find('td:eq(4)').html().replace(',','').replace(',',''));
                 line_qty5 = parseFloat(line_qty5) + parseFloat($(this).closest('tr').find('td:eq(5)').html().replace(',','').replace(',',''));
                 line_qty4 = parseFloat(line_qty4) + parseFloat($(this).closest('tr').find('td:eq(6)').html().replace(',','').replace(',',''));
                 line_total_qty = parseFloat(line_total_qty) + parseFloat($(this).closest('tr').find('td:eq(7)').html().replace(',','').replace(',',''));
                 
                 line_amount = parseFloat(line_amount) + parseFloat($(this).closest('tr').find('td:eq(8)').html().replace(',','').replace(',',''));
                 line_amount2 = parseFloat(line_amount2) + parseFloat($(this).closest('tr').find('td:eq(9)').html().replace(',','').replace(',',''));
                 line_amount5 = parseFloat(line_amount5) + parseFloat($(this).closest('tr').find('td:eq(10)').html().replace(',','').replace(',',''));
                 line_amount4 = parseFloat(line_amount4) + parseFloat($(this).closest('tr').find('td:eq(11)').html().replace(',','').replace(',',''));
                 line_total_amount = parseFloat(line_total_amount) + parseFloat($(this).closest('tr').find('td:eq(12)').html().replace(',','').replace(',',''));
             }
             
         });
        
        $(this).closest('tr').find('td:eq(3)').html(addCommas(parseFloat(line_qty).toFixed(2)));
        $(this).closest('tr').find('td:eq(4)').html(addCommas(parseFloat(line_qty2).toFixed(2)));
        $(this).closest('tr').find('td:eq(5)').html(addCommas(parseFloat(line_qty5).toFixed(2)));
        $(this).closest('tr').find('td:eq(6)').html(addCommas(parseFloat(line_qty4).toFixed(2)));
        $(this).closest('tr').find('td:eq(7)').html(addCommas(parseFloat(line_total_qty).toFixed(2)));
        
        $(this).closest('tr').find('td:eq(8)').html(addCommas(parseFloat(line_amount).toFixed(2)));
        $(this).closest('tr').find('td:eq(9)').html(addCommas(parseFloat(line_amount2).toFixed(2)));
        $(this).closest('tr').find('td:eq(10)').html(addCommas(parseFloat(line_amount5).toFixed(2)));
        $(this).closest('tr').find('td:eq(11)').html(addCommas(parseFloat(line_amount4).toFixed(2)));
        $(this).closest('tr').find('td:eq(12)').html(addCommas(parseFloat(line_total_amount).toFixed(2)));
     });
 });
 $("#btn-export-excel").click(function(){
  $("#table-data").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Excel Document Name"
  });
});
$(".btn-order-date").click(function(){
    $(".btn-order-type").val(1);
    if($(".btn-order-price").hasClass("btn-success")){
        $(".btn-order-price").removeClass("btn-success");
        $(".btn-order-price").addClass("btn-default");
    }
    if($(this).hasClass("btn-default")){
        $(this).removeClass("btn-default")
        $(this).addClass("btn-success");
    }
    
});
$(".btn-order-price").click(function(){
    $(".btn-order-type").val(2);
      if($(".btn-order-date").hasClass("btn-success")){
        $(".btn-order-date").removeClass("btn-success");
        $(".btn-order-date").addClass("btn-default");
    }
    if($(this).hasClass("btn-default")){
        $(this).removeClass("btn-default")
        $(this).addClass("btn-success");
    }
});
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
function addCommas(nStr) {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
 }
JS;
$this->registerJs($js, static::POS_END);
?>
