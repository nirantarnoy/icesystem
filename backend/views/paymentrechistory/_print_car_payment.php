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
$user_name = \Yii::$app->user->identity->username;

$has_allow = 0;
if ($user_name == 'adpang' || $user_name == 'beau' || $user_name == 'dow') {
    $has_allow = 1;
}

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

        <form action="<?= \yii\helpers\Url::to(['paymentrechistory/printcarpayment'], true) ?>" method="post"
              id="form-search">
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
                    <?php if ($has_allow == 1): ?>
                        <td>
                            <?php
                            echo \kartik\select2\Select2::widget([
                                'name' => 'find_customer_id',
                                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1, 'is_show_pos' => 0])->all(), 'id', 'name'),
                                'value' => $find_customer_id,
                                'options' => [
                                    'placeholder' => '--ลูกค้า--'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'multiple' => true,
                                ]
                            ]);
                            ?>
                        </td>
                    <?php endif; ?>
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
                    รายงานรับชำระเงินเชื่อ(สายส่ง)
                </td>
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
                <td style="text-align: center;border: 1px solid grey"><b>ลำดับ</b>
                <td style="text-align: center;border: 1px solid grey"><b>สายส่ง</b>
                </td>
                <td style="border-top: 1px dotted gray;border: 1px solid grey"><b>วันที่</b></td>
                <td style="border-top: 1px dotted gray;border: 1px solid grey;text-align: center"><b>เลขเอกสาร</b></td>
                <td style="border-top: 1px dotted gray;border: 1px solid grey;text-align: center"><b>รหัสลูกค้า</b></td>
                <td style="text-align: left;border: 1px solid grey">
                    <b>ชื่อลูกค้า</b></td>
                <td style="text-align: left;border: 1px solid grey;text-align: center">
                    <b>ประเภท</b></td>
                <td style="text-align: right;border: 1px solid grey">
                    <b>รวมรับชำระ</b>
                </td>
            </tr>
            <?php
            $sum_qty_all = 0;
            $sum_total_all = 0;

            $payment_cash = 0;
            $payment_transfer = 0;

            ?>
            <?php if ($find_user_id != null): ?>
                <?php
                //   echo count($find_user_id);return;
                ?>
                <?php for ($k = 0; $k <= count($find_user_id) - 1; $k++): ?>
                    <?php
                    $line_route_code = \backend\models\Deliveryroute::findName($find_user_id[$k]);
                    $customer_id = '';
                    if ($find_customer_id != null) {
                        for($z=0;$z<=count($find_customer_id)-1;$z++){
                            if($z==0){
                                $customer_id = $find_customer_id[$z];
                            }else{
                                $customer_id = $customer_id.','.$find_customer_id[$z];
                            }
                        }
                     //   $customer_id = $find_customer_id[$k];
                    }
                    ?>

                    <?php $find_order = getPayment($from_date, $to_date, 0, $find_user_id[$k], $company_id, $branch_id, $customer_id, $has_allow); ?>
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
                            $sum_qty += $find_order[$i]['pay'];
                            $sum_total += $find_order[$i]['pay'];

                            $sum_qty_all += $find_order[$i]['pay'];
                            $sum_total_all += $find_order[$i]['pay'];
                            ?>
                            <tr>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: center"><?= $x ?> </td>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: center"><?= $line_route_code ?> </td>
                                <td style="font-size: 16px;border: 1px solid grey"><?= date('Y-m-d H:i:s', strtotime($find_order[$i]['trans_date'])) ?></td>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: center"><?= $find_order[$i]['journal_no'] ?> </td>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: center"><?= $find_order[$i]['cus_code'] ?> </td>
                                <td style="font-size: 16px;border: 1px solid grey"><?= $find_order[$i]['cus_name'] ?></td>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: center"><?= $find_order[$i]['cus_type'] ?></td>
                                <td style="font-size: 16px;border: 1px solid grey;text-align: right;"><?= number_format($find_order[$i]['pay'], 2) ?></td>
                            </tr>
                            <?php
                            $payline = getPaymentLine($find_order[$i]['journal_id'], $company_id, $branch_id);
                            if ($payline != null):?>
                                <tr style="background-color: #1abc9c">
                                    <td colspan="4" style="border: 1px solid grey">
                                        <table style="font-size: 14px;">
                                            <tr>
                                                <td>วันที่</td>
                                                <td>เลขที่ขาย</td>
                                                <td>ยอดค้างชำระ</td>
                                                <td>รับชำระ</td>
                                                <td>คงเหลือ</td>
                                                <td>สถานะ</td>
                                                <td>รับชำระโดย</td>
                                            </tr>
                                            <?php for ($k = 0; $k <= count($payline) - 1; $k++): ?>
                                                <?php
                                                $order_credit = \backend\models\Orders::getlinesumcredit($payline[$k]['order_id']);
                                                if ($payline[$k]['status'] == 'เงินสด') {
                                                    $payment_cash = ($payment_cash + $payline[$k]['pay']);
                                                } else if ($payline[$k]['status'] == 'เงินโอน') {
                                                    $payment_transfer = ($payment_transfer + $payline[$k]['pay']);
                                                }
                                                ?>
                                                <tr>
                                                    <td><?= \backend\models\Orders::getOrderdate($payline[$k]['order_id']) ?></td>
                                                    <td><?= \backend\models\Orders::getNumber($payline[$k]['order_id']) ?></td>
                                                    <td><?= number_format($order_credit, 2) ?></td>
                                                    <td><?= number_format($payline[$k]['pay'], 2) ?></td>
                                                    <td><?= number_format($order_credit - $payline[$k]['pay'], 2) ?></td>
                                                    <td style="color: red"><?= $payline[$k]['status'] ?></td>
                                                    <td style="color: red"><?= $payline[$k]['user'] ?></td>
                                                </tr>
                                            <?php endfor; ?>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($loop_count == $x): ?>
                                <!--                                <tr>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;border-top: 1px solid black"></td>-->
                                <!--                                    <td style="font-size: 16px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">-->
                                <!--                                        <b>--><?php ////echo number_format($sum_total, 2) ?><!--</b></td>-->
                                <!--                                </tr>-->
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
                <td style="font-size: 18px;border-top: 1px solid black"><b>รวมทั้งสิ้น</b></td>
                <td style="font-size: 18px;text-align: right;border-top: 1px solid black;border-bottom: 1px solid black">
                    <b><?= number_format($sum_total_all, 2) ?></b></td>
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
    <br/>
    <br/>
    <div class="row">
        <div class="col-lg-4">
            <table style="border: 1px solid grey;">
                <tr>
                    <td style="width: 20%">เงินสด</td>
                    <td><?= number_format($payment_cash, 2) ?></td>
                </tr>
                <tr>
                    <td>เงินโอน</td>
                    <td><?= number_format($payment_transfer, 2) ?></td>
                </tr>
            </table>
        </div>
    </div>
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
function getPayment($f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $find_customer_id, $has_allow)
{
    $list_route_id = null;

    $data = [];
//    $sql = "SELECT t1.id,t1.journal_no,t1.trans_date,t1.customer_id,SUM(t2.payment_amount) as amount
//              FROM payment_receive as t1 INNER JOIN payment_receive_line as t2 ON t2.payment_receive_id = t1.id INNER JOIN customer as t3 ON t1.customer_id = t3.id
//             WHERE  t1.trans_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t1.trans_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t1.status = 1
//             AND t2.payment_amount > 0
//             AND t3.delivery_route_id = " . $find_user_id . "
//             AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;

    $sql = "SELECT t1.id,t1.journal_no,t1.customer_code,t1.customer_name,t1.customer_id,SUM(t1.payment_amount) as amount,t1.trans_date  from query_payment_receive as t1 INNER JOIN customer as t2 on t2.id = t1.customer_id 
              WHERE (date(t1.trans_date)>= " . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
              AND date(t1.trans_date)<= " . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " )
              AND t1.status <> 100 
              AND t1.payment_method_id=2
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;

    if ($find_user_id > 0) {
        $sql .= " AND t2.delivery_route_id =" . $find_user_id;
    }
    if ($has_allow == 1) {
        if ($find_customer_id > 0) {
            $sql .= " AND t1.customer_id in (" . $find_customer_id . ")" ;
        }
    }

    $sql .= " GROUP BY t1.id,t1.journal_no";
    $query = \Yii::$app->db->createCommand($sql);
    $model = $query->queryAll();
    if ($model) {
        for ($i = 0; $i <= count($model) - 1; $i++) {
            $customer_code = '';
            $customer_name = '';
            $customer_type = '';
            if ($model[$i]['customer_id'] != null) {
//                $customer_code = \backend\models\Customer::findCode($model[$i]['customer_id']);
//                $customer_name = \backend\models\Customer::findName($model[$i]['customer_id']);
                $customer_type = \backend\models\Customer::findType($model[$i]['customer_id']);
            }

            array_push($data, [
                'journal_id' => $model[$i]['id'],
                'journal_no' => $model[$i]['journal_no'],
                'cus_code' => $model[$i]['customer_code'],
                'cus_name' => $model[$i]['customer_name'],
                'cus_type' => $customer_type,
                'pay' => $model[$i]['amount'],
                'trans_date' => $model[$i]['trans_date'],
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
                'order_id' => $model[$i]['order_id'],
                'pay' => $model[$i]['payment_amount'],
//                'status'=> $model[$i]['status'] == 3 ?'ยกเลิก':'ชำระแล้ว',
                'status' => $model[$i]['payment_channel_id'] == 1 ? 'เงินสด' : 'เงินโอน',
                'user' => \backend\models\User::findName($model[$i]['crated_by']),
            ]);
        }
    }
    return $data;
}

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