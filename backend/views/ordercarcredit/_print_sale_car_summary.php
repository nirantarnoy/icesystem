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
    <div id="div1">

        <form action="<?= \yii\helpers\Url::to(['ordercarcredit/printcarsummary'], true) ?>" method="post"
              id="form-search">
            <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
                <tr>

                    <td style="width: 20%">
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'from_date',
                            // 'value'=>'2015-10-19 12:00 AM',
                            // 'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                            'value' => $from_date != null ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d'),
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
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>
                    <td style="width: 20%">
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'to_date',
                            // 'value' => $to_date != null ? date('Y-m-d H:i', strtotime($to_date)) : date('Y-m-d H:i'),
                            'value' => $to_date != null ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d'),
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
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo \kartik\select2\Select2::widget([
                            'name' => 'find_user_id',
                            'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'name'),
                            'value' => $find_user_id,
                            'options' => [
                                'placeholder' => '--สายส่ง--'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multiple' => true,
                            ]
                        ]);
                        ?>
                    </td>
                    <td>
                        <select class="form-control" name="sale_type" id="">
                            <option value="0" <?= $sale_type == 0 ? 'selected' : '' ?>>--สด-เชื่อ--</option>
                            <option value="2" <?= $sale_type == 2 ? 'selected' : '' ?>>เชื่อ</option>
                            <option value="1" <?= $sale_type == 1 ? 'selected' : '' ?>>สด</option>
                            <option value="3" <?= $sale_type == 3 ? 'selected' : '' ?>>ฟรี</option>
                        </select>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-primary" value="ค้นหา">
                    </td>
                    <td style="width: 25%"></td>
                </tr>
            </table>
        </form>
        <br/>
        <table class="table-header" width="100%">
            <tr>
                <td style="text-align: center; font-size: 20px; font-weight: bold">
                    <?php
                    $head_title = 'สด-เชื่อ';
                    if ($sale_type == 1) {
                        $head_title = 'สด';
                    } else if ($sale_type == 2) {
                        $head_title = 'เชื่อ';
                    } else if ($sale_type == 3) {
                        $head_title = 'ฟรี';
                    }
                    ?>
                    รายงานยอดขาย<?= $head_title ?>(แยกสายส่ง)
                </td>
            </tr>
        </table>
        <br>
        <table class="table-header" width="100%">
            <tr>
                <td style="text-align: center; font-size: 20px; font-weight: normal">
                    จากวันที่ <span style="color: red"><?= date('Y-m-d', strtotime($from_date)) ?></span>
                    ถึง <span style="color: red"><?= date('Y-m-d', strtotime($to_date)) ?></span></td>
            </tr>
        </table>
        <br>
        <table class="table-header" width="100%">
        </table>
        <table class="table-title" id="table-data" style="width: 100%">
            <tr>
                <td style="text-align: left;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>สายส่ง</b>
                </td>
                <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>วันที่</b></td>
                <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>เลขเอกสาร</b></td>
                <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รหัสลูกค้า</b></td>
                <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รหัสสาย</b></td>
                <td style="text-align: left;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                    <b>ชื่อลูกค้า</b></td>
                <td style="text-align: left;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                    <b>รหัสสินค้า</b></td>
                <td style="text-align: left;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                    <b>ชื่อสินค้า</b></td>
                <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>จำนวน</b>
                </td>
                <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                    <b>ราคา</b>
                </td>
                <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                    <b>ส่วนลด</b>
                </td>
                <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>ราคารวม</b>
                </td>
            </tr>
            <?php
            $sum_qty_all = 0;
            $sum_total_all = 0;

            ?>
            <?php if ($find_user_id != null): ?>
                <?php
                //   echo count($find_user_id);return;
                ?>
                <?php for ($k = 0; $k <= count($find_user_id) - 1; $k++): ?>
                    <?php
                    $line_route_code = \backend\models\Deliveryroute::findName($find_user_id[$k]);
                    ?>
                    <tr>
                        <td colspan="7"><b><?= $line_route_code ?></b></td>
                    </tr>
                    <?php $find_order = getOrder($from_date, $to_date, $sale_type, $find_user_id[$k], $company_id, $branch_id); ?>
                    <?php if ($find_order != null): ?>
                        <?php
                        $loop_count = count($find_order);
                        $x = 0;
                        $sum_qty = 0;
                        $sum_total = 0;

                        ?>
                        <?php for ($i = 0; $i <= count($find_order) - 1; $i++): ?>
                            <?php
                            $x += 1;
                            $sum_qty += $find_order[$i]['qty'];
                            $sum_total += (($find_order[$i]['qty'] * $find_order[$i]['sale_price']) - $find_order[$i]['discount_amt']);

                            $sum_qty_all += $find_order[$i]['qty'];
                            $sum_total_all += (($find_order[$i]['qty'] * $find_order[$i]['sale_price']) - $find_order[$i]['discount_amt']);
                            ?>
                            <tr>
                                <td style="font-size: 16px;"><?= $find_order[$i]['route_name'] ?> </td>
                                <td style="font-size: 16px;"><?= date('Y-m-d H:i:s', strtotime($find_order[$i]['order_date'])) ?></td>
                                <td style="font-size: 16px;"><?= $find_order[$i]['order_no'] ?> </td>
                                <td style="font-size: 16px;"><?= $find_order[$i]['cus_code'] ?> </td>
                                <td style="font-size: 16px;"><?= $find_order[$i]['cus_branch_no'] ?> </td>
                                <td style="font-size: 16px;"><?= $find_order[$i]['cus_name'] ?></td>
                                <td style="font-size: 16px;"><?= \backend\models\Product::findCode($find_order[$i]['product_id']) ?></td>
                                <td style="font-size: 16px;"><?= \backend\models\Product::findName($find_order[$i]['product_id']) ?></td>
                                <td style="font-size: 16px;text-align: right;"><?= number_format($find_order[$i]['qty'], 2) ?></td>
                                <td style="font-size: 16px;text-align: right;"><?= number_format($find_order[$i]['sale_price'], 2) ?></td>
                                <td style="font-size: 16px;text-align: right;"><?= number_format($find_order[$i]['discount_amt'], 2) ?></td>
                                <td style="font-size: 16px;text-align: right;"><?= number_format(($find_order[$i]['sale_price'] * $find_order[$i]['qty']) - $find_order[$i]['discount_amt'], 2) ?></td>
                            </tr>
                            <?php if ($loop_count == $x): ?>
                                <tr>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;border-top: 1px solid black"></td>
                                    <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                                        <b><?= number_format($sum_qty, 2) ?></b></td>
                                    <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
                                    <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
                                    <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                                        <b><?= number_format($sum_total, 2) ?></b></td>
                                </tr>
                            <?php endif; ?>
                        <?php endfor ?>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php endif; ?>
            <tfoot>
            <tr>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 18px;border-top: 1px solid black"><b>รวมทั้งสิ้น</b></td>
                <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                    <b><?= number_format($sum_qty_all, 2) ?></b></td>
                <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
                <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black"></td>
                <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                    <b><?= number_format($sum_total_all, 2) ?></b></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <br/>
    <table width="100%" class="table-title">
        <tr>
            <td>
                <a class="btn btn-info" href="<?= \yii\helpers\Url::to(['ordercarcredit/carsummaryupdate'], true) ?>">อัพเดทใบส่งของ</a>
            </td>
            <td style="text-align: right">
                <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
                <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
            </td>
        </tr>

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
    </body>
    </html>

<?php
function getOrder($f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id)
{
    $list_route_id = null;

    $data = [];
    $model = null;
    if ($find_sale_type == 0) {
        $model = \common\models\QueryApiOrderDailySummaryNew::find()->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($f_date)), date('Y-m-d', strtotime($t_date))])
            ->andFilterWhere(['order_channel_id' => $find_user_id])
            ->andFilterWhere(['!=', 'order_line_status', 500])
            ->orderBy(['order_date'=>SORT_DESC,'price' => SORT_ASC])->all();
    } else {
        $model = \common\models\QueryApiOrderDailySummaryNew::find()->where(['sale_payment_method_id' => $find_sale_type])
            ->andFilterWhere(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($f_date)), date('Y-m-d', strtotime($t_date))])
            ->andFilterWhere(['order_channel_id' => $find_user_id])
            ->andFilterWhere(['!=', 'order_line_status', 500])
            ->orderBy(['order_date'=>SORT_DESC,'price' => SORT_ASC])->all();
    }
//    if($find_sale_type == 0){
//        $model = \common\models\QueryApiOrderDailySummaryNew::find()->where(['BETWEEN','order_date',date('Y-m-d H:i:s', strtotime($f_date)),date('Y-m-d H:i:s', strtotime($t_date))])
//            ->andFilterWhere(['status'=>100])
//            ->andFilterWhere(['order_channel_id'=>$find_user_id])
//            ->andFilterWhere(['!=','order_line_status',500])->all();
//    }else{
//        $model = \common\models\QueryApiOrderDailySummaryNew::find()->where(['sale_payment_method_id' => $find_sale_type])
//            ->andFilterWhere(['BETWEEN','order_date',date('Y-m-d H:i:s', strtotime($f_date)),date('Y-m-d H:i:s', strtotime($t_date))])
//            ->andFilterWhere(['status'=>100])
//            ->andFilterWhere(['order_channel_id'=>$find_user_id])
//            ->andFilterWhere(['!=','order_line_status',500])->all();
//    }
    if ($model) {
        foreach ($model as $value) {

            array_push($data, [
                'product_id' => $value->product_id,
                'order_no' => $value->order_no,
                'route_name' => \backend\models\Deliveryroute::findName($value->order_channel_id),
                'cus_code' => $value->code,
                'cus_branch_no' => getCustbranchno($value->code, $company_id, $branch_id),
                'cus_name' => $value->name,
                'qty' => $value->line_qty,
                'sale_price' => $value->price,
                'order_date' => $value->order_date,
                'discount_amt' => $value->discount_amt,
            ]);
        }
    }
    return $data;
}

function getCustbranchno($cus_id, $company_id, $branch_id)
{
    $no = '';
    if ($cus_id) {
        $model = \backend\models\Customer::find()->select('sort_name')->where(['code' => $cus_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model) {
            $no = $model->sort_name;
        }
    }
    return $no;
}

//function getOrder($f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id)
//{
//    $list_route_id = null;
//
//    $data = [];
//    $sql = "SELECT t2.order_no, t3.code , t3.name, t1.qty, t1.price, t2.order_date, t2.order_channel_id, t1.product_id, SUM(t1.qty) as qty, t1.price
//              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t1.customer_id=t3.id
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.status=100
//             AND t2.sale_channel_id = 1
//             AND t1.sale_payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
////      if($find_sale_type != null && $find_sale_type != 0){
////          if($find_sale_type == 1){
////              $sql.= " AND t2.payment_method_id=".$find_sale_type;
////          }
////          if($find_sale_type == 2){
////              $sql.= " AND t2.order_channel_id is null AND t2.payment_method_id=".$find_sale_type;
////          }
////          if($find_sale_type == 3 ){
////              $sql.= " AND t2.order_channel_id > 0";
////          }
////      }
////    if ($find_user_id != null) {
////        for ($m = 0; $m <= count($find_user_id) - 1; $m++) {
////            if ($m == count($find_user_id) - 1) {
////                $list_route_id .= $find_user_id[$m];
////            } else {
////                $list_route_id .= $find_user_id[$m] . ',';
////            }
////
////        }
////        $sql .= " AND t2.order_channel_id IN (" . $list_route_id . ")";
////    }
//    $sql .= " AND t2.order_channel_id =".$find_user_id;
//    $sql.=" GROUP BY t2.order_no,t1.customer_id, t1.product_id, t1.price";
//    $query = \Yii::$app->db->createCommand($sql);
//    $model = $query->queryAll();
//    if ($model) {
//        for ($i = 0; $i <= count($model) - 1; $i++) {
//            $customer_code = $model[$i]['code'];
//            $customer_name = $model[$i]['name'];
//            if ($model[$i]['code'] == null) {
//                $customer_code = \backend\models\Deliveryroute::findCode($model[$i]['order_channel_id']);
//                $customer_name = \backend\models\Deliveryroute::findName($model[$i]['order_channel_id']);
//            }
//
//            array_push($data, [
//                'product_id' => $model[$i]['product_id'],
//                'order_no' => $model[$i]['order_no'],
//                'route_name' => \backend\models\Deliveryroute::findName($model[$i]['order_channel_id']),
//                'cus_code' => $customer_code,
//                'cus_name' => $customer_name,
//                'qty' => $model[$i]['qty'],
//                'sale_price' => $model[$i]['price'],
//                'order_date' => $model[$i]['order_date'],
//            ]);
//        }
//    }
//    return $data;
//}

?>
<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $("#btn-export-excel").click(function(){
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