<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use common\models\QuerySaleorderByCustomerLoanSumNew;
use kartik\daterange\DateRangePicker;
use yii\web\Response;


$company_id = 0;
$branch_id = 0;

if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}
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

$model_customer_loan = null;

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
<body>
<form action="<?= \yii\helpers\Url::to(['assetsitem/checkinprint'], true) ?>" method="post"
      id="form-search">
    <input type="hidden" name="is_start_find" value="1">
    <div id="div1">
        <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
            <tr>

                <!--                <td style="width: 20%">-->
                <!--                    <label for="">ตั้งแต่วันที่</label>-->
                <!--                    --><?php
                //                    echo DateRangePicker::widget([
                //                        'name' => 'from_date',
                //                        // 'value'=>'2015-10-19 12:00 AM',
                //                        'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                //                        //    'useWithAddon'=>true,
                //                        'convertFormat' => true,
                //                        'options' => [
                //                            'class' => 'form-control',
                //                            'placeholder' => 'ตั้งแต่',
                //                            //  'onchange' => 'this.form.submit();',
                //                            'autocomplete' => 'off',
                //                        ],
                //                        'pluginOptions' => [
                //                            'timePicker' => true,
                //                            'timePickerIncrement' => 1,
                //                            'locale' => ['format' => 'Y-m-d H:i'],
                //                            'singleDatePicker' => true,
                //                            'showDropdowns' => true,
                //                            'timePicker24Hour' => true
                //                        ]
                //                    ]);
                //                    ?>
                <!--                </td>-->

                <td style="width: 20%">
                    <label>ตั้งแต่วันที่</label>
                    <?php
                    echo DateRangePicker::widget([
                        'name' => 'from_date',
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
                </td>
                <td style="width: 20%">
                    <label for="">ถึงวันที่</label>
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
                <td style="width: 25%">
                    <label for="">สายส่ง</label>
                    <?php
                    echo \kartik\select2\Select2::widget([
                        'name' => 'find_customer_id',
                        'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all(), 'id', 'name'),
                        'value' => $find_customer_id,
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
                    <label for="" style="color: white">ค้นหา</label>
                    <input type="submit" class="btn btn-primary" style="margin-top: 0px;" value="ค้นหา">
                </td>
                <td style="width: 25%"></td>
            </tr>
        </table>
</form>
<br/>
<table class="table-header" width="100%">
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: bold">เช็คอินตำแหน่งร้านค้า
        </td>
    </tr>
</table>
<br>
<table class="table-header" width="100%">
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: normal">สายส่ง</td>
    </tr>
    <tr>
        <td style="text-align: center; font-size: 20px; font-weight: normal">
            จากวันที่ <span style="color: red">
            <?php echo date('Y-m-d', strtotime($from_date)) ?><!--</span>-->
            ถึง <span style="color: red"><?= date('Y-m-d', strtotime($to_date)) ?></span></td>
    </tr>
</table>
<br>

<?php
$model_asset_check = \common\models\CustomerCheckin::find()->where(['route_id' => $find_customer_id])->andFilterWhere(['BETWEEN', 'checkin_date', $from_date, $to_date])->all();
?>
<table class="table-header" width="100%">
</table>
<table class="table-title" id="table-data" style="width: 100%">
    <tr>
        <td style="border: 1px solid gray;text-align: center"><b>ลำดับ</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>วันที่</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>ลูกค้า</b></td>
        <td style="border: 1px solid gray;text-align: center"><b>จุดเช็คอิน</b></td>
        <td style="text-align: center;border: 1px solid gray"><b>สายส่ง</b></td>
        <td style="text-align: center;border: 1px solid gray;width: 20%"><b>รูปภาพ</b></td>
    </tr>
    <?php $i = 0; ?>
    <?php foreach ($model_asset_check as $value): ?>
        <?php $i += 1; ?>
        <tr>
            <td style="border: 1px solid gray;text-align: center;width: 5%"><?= $i ?></td>
            <td style="border: 1px solid gray;text-align: center"><?= date('d-m-Y H:i:s', strtotime($value->checkin_date)) ?></td>
            <td style="border: 1px solid gray;text-align: center"><?= \backend\models\Customer::findName($value->customer_id) ?></td>
            <td style="border: 1px solid gray;text-align: center"><a href="https://www.google.com/search?q=<?=$value->latlong?>" target="_blank"><?=$value->latlong?></a></td>
            <td style="text-align: center;border: 1px solid gray;width: 10%"><?=\backend\models\Deliveryroute::findName($value->route_id)?></td>
            <td style="text-align: center;border: 1px solid gray;width: 20%">
                <?php
                  $photolist = explode(',',$value->photo);
                ?>
                <?php for($x=0;$x<=count($photolist)-1;$x++):?>
                <img src="<?=\Yii::$app->urlManagerFrontend->getBaseUrl()?>/uploads/assetcheck/<?=$photolist[$x]?>" alt="" width="20%">
                <?php endfor;?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</div>
<br/>
<table width="100%" class="table-title">
    <tr>
        <!--        <td>-->
        <!--            <a class="btn btn-info" href="-->
        <? //= \yii\helpers\Url::to(['paymentreceivecar/carsummaryupdate'], true) ?><!--">อัพเดทใบส่งของ</a>-->
        <!--        </td>-->
<!--        <td>-->
<!--            <a class="btn btn-info" href="--><?//= \yii\helpers\Url::to(['paymentreceivecar/carsummaryupdate'], true) ?><!--">อัพเดทใบส่งของ</a>-->
<!--        </td>-->
        <td style="text-align: right">
            <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>
            <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>
        </td>
    </tr>

</table>
<!--<table width="100%" class="table-title">-->
<!--    <td style="text-align: right">-->
<!--        <button id="btn-export-excel" class="btn btn-secondary">Export Excel</button>-->
<!--        <button id="btn-print" class="btn btn-warning" onclick="printContent('div1')">Print</button>-->
<!--    </td>-->
<!--</table>-->
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
function getOrder($route_id, $customer_id, $t_date, $company_id, $branch_id, $is_find_date, $f_date)
{
//    WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//    AND t2.sale_channel_id = 1
    $data = [];
    if ($route_id != null) {
        if ($is_find_date == 0 || $is_find_date == null) {
            $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
              FROM query_sale_mobile_data_new2 as t2
              WHERE  t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
              AND t2.route_id =" . $route_id . "              
              AND t2.payment_method_id = 2
              AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            if ($customer_id != null || $customer_id != '') {
                $sql .= " AND t2.customer_id=" . $customer_id;
            }

            $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";
        } else {
            $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
             FROM query_sale_mobile_data_new2 as t2
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
             AND t2.route_id =" . $route_id . " 
             AND t2.payment_method_id = 2
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            if ($customer_id != null || $customer_id != '') {
                $sql .= " AND t2.customer_id=" . $customer_id;
            }

            $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
            $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";
        }
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {

                array_push($data, [
                    'order_id' => $model[$i]['id'],
                    'customer_id' => $model[$i]['customer_id'],
                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
                    'order_no' => $model[$i]['order_no'],
                    'order_date' => $model[$i]['order_date'],
                    'total_credit' => $model[$i]['total_credit'],
                    'total_pay' => getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
                ]);
            }
        }
    } else {

    }

    return $data;
}

//function getOrder($route_id, $customer_id, $t_date, $company_id, $branch_id)
//{
//    //  WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//    // AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
////    AND t2.sale_channel_id = 1
//    $data = [];
//    if ($route_id != null) {
//        $sql = "SELECT t2.id,t2.customer_id, t2.order_no, t2.order_date , sum(t2.line_total_credit) as total_credit
//              FROM query_sale_mobile_data_new2 as t2
//              WHERE  t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.route_id =" . $route_id . "
//
//             AND t2.payment_method_id = 2
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
//
//        $sql .= " GROUP BY t2.id,t2.customer_id, t2.order_no, t2.order_date";
//        $sql .= " ORDER BY t2.customer_id, t2.order_date,t2.order_no ";
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//
//                array_push($data, [
//                    'order_id' => $model[$i]['id'],
//                    'customer_id' => $model[$i]['customer_id'],
//                    'customer_name' => \backend\models\Customer::findName($model[$i]['customer_id']),
//                    'order_no' => $model[$i]['order_no'],
//                    'order_date' => $model[$i]['order_date'],
//                    'total_credit' => $model[$i]['total_credit'],
//                    'total_pay' => getPaytrans($model[$i]['id'], $model[$i]['customer_id']),
//                ]);
//            }
//        }
//    }
//
//    return $data;
//}

function getPaytrans($order_id, $customer_id)
{
    $pay_total = 0;
    if ($order_id) {
        $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->sum('payment_amount');
        $pay_total = $model;
    }
    return $pay_total;
}

function getPayline($order_id, $customer_id)
{
    $data = [];
    if ($order_id) {
        $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->andFilterWhere(['>', 'payment_amount', 0])->all();
        if ($model) {
            foreach ($model as $value) {
                array_push($data, [
                    'journal_no' => $value->journal_no,
                    'journal_date' => date('d/m/Y H:i:', strtotime($value->trans_date)),
                    'total_pay' => number_format($value->payment_amount),
                ]);
            }
        }
    }
    return $data;
}

?>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
 $(function(){
     $("td[colspan=6]").find(".table-detail").hide();
    $(".btn-show-more").click(function(event) {
        event.stopPropagation();
        var target = $(event.target);
        if ( target.closest("td").attr("colspan") > 1 ) {
            target.slideUp();
        } else {
            target.closest("tr").next().find(".table-detail").slideToggle();
        }                    
    });
    
    $("#btn-export-excel").click(function(){
          $("#table-data").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Excel Document Name"
          });
    });
    //$(".table-payment-line >tbody >tr").each(function(e,tr){
     //  var tr_line_pay = $(this).attr('data-var');
     //  console.log(tr_line_pay);
     //  if (typeof tr_line_pay === "undefined") {
     //       e.parent().parent().parent().hide();
     //   }
   // });
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
