<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use yii\web\Response;

//require_once __DIR__ . '/vendor/autoload.php';
//require_once 'vendor/autoload.php';
// เพิ่ม Font ให้กับ mPDF

//echo $print_type;
//$customer_name = $trans_data[0]['customer_id']?getCustomername($connect, $trans_data[0]['customer_id']):$trans_data[0]['customer_name'];
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
        body {
            font-family: sarabun;
            /*font-family: garuda;*/
            font-size: 18px;
        }

        table.table-header {
            border: 0px;
            border-spacing: 1px;
        }

        table.table-qrcode {
            border: 0px;
            border-spacing: 1px;
        }

        table.table-qrcode td, th {
            border: 0px solid #dddddd;
            text-align: left;
            padding-top: 2px;
            padding-bottom: 2px;
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

        @media print {
            @page {
                size: auto;
            }
        }

    </style>
    <!--    <script src="vendor/jquery/jquery.min.js"></script>-->
    <!--    <script type="text/javascript" src="js/ThaiBath-master/thaibath.js"></script>-->
</head>
<body>
<div id="print-all" style="height: 500px;width: 302px;">
    <div id="print-area" style="height: 450px;width: 350px;">
        <table class="table-qrcode" style="width: 100%">
            <tr>
                <td style="width: 20%">
                    <div style="height: 150px;width: 150px;">
                        <?php
                        \Yii::$app->response->format = Response::FORMAT_HTML;
                        $data = $model->order_no;
                        $qr = new QRCode();
                        echo '<img src="' . $qr->render($data) . '" />';
                        ?>
                    </div>
                </td>
                <td style="width: 80%">
                    <table class="table-header" width="100%">
                        <tr>
                            <td style="font-size: 20px;text-align: center;vertical-align: bottom">
                                <h2>น้ำแข็ง</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 20px;text-align: center;vertical-align: top">
                                <h2>ใบสั่งจ่าย</h2>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="table-header" style="width: 100%">
            <tr>
                <td style="font-size: 18px;text-align: left">เลขที่ <span><?php echo $model->order_no; ?></span>
                </td>
                <td style="font-size: 18px;text-align: left">
                    วันที่ <span><?= date('d/m/Y', strtotime($model->order_date)); ?></span>
                </td>
            </tr>
            <tr>
                <td style="font-size: 18px;text-align: left">
                    ลูกค้า <span><?= \backend\models\Customer::findName($model->customer_id); ?></span>
                </td>
                <td style="font-size: 18px;text-align: left">
                    เวลา <span><?= date('H:i:s', strtotime($model->order_date)); ?></span>
                </td>
            </tr>
        </table>
        <table class="table-title" style="width: 100%">
            <tr>
                <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รายการ</b></td>
                <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>จำนวน</b>
                </td>
                <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>ราคา</b>
                </td>
                <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รวม</b></td>
            </tr>
            <?php
            $total_qty = 0;
            $total_amt = 0;
            $discount = 0;
            $change = 0;
            $items_cnt = 0;
            ?>
            <?php foreach ($model_line as $value): ?>
                <?php
                $total_qty = $total_qty + $value->qty;
                $total_amt = $total_amt + ($value->qty * $value->price);
                $change = $change_amount == null ? 0 : $change_amount;//$value->change_amount;
                $items_cnt += 1;
                ?>
                <tr>
                    <td><?= \backend\models\Product::findName($value->product_id); ?></td>
                    <td style="text-align: center"><?= number_format($value->qty, 1) ?></td>
                    <td style="text-align: center"><?= number_format($value->price, 2) ?></td>
                    <td style="text-align: right"><?= number_format($value->qty * $value->price, 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tfoot>
            <tr>
                <td style="font-size: 18px;border-top: 1px dotted gray">จำนวนรายการ</td>
                <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"><?= number_format($items_cnt) ?></td>
                <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"></td>
                <td style="font-size: 18px;border-top: 1px dotted gray;text-align: right"><?= number_format($total_amt, 2) ?></td>
            </tr>
            <tr>
                <td style="font-size: 18px;">ส่วนลด</td>
                <td></td>
                <td></td>
                <td style="font-size: 18px;text-align: right"><?= number_format($discount, 2) ?></td>
            </tr>
            <tr>
                <td style="font-size: 18px;">จำนวนสุทธิ</td>
                <td></td>
                <td></td>
                <td style="font-size: 18px;text-align: right"> <?= number_format($total_amt - $discount, 2) ?></td>
            </tr>
            <tr>
                <td style="font-size: 18px;">ทอนเงิน</td>
                <td></td>
                <td></td>
                <td style="font-size: 18px;text-align: right"> <?= number_format($change, 2) ?></td>
            </tr>
            </tfoot>
        </table>
        <table class="table-header">
            <tr>
                <td></td>
            </tr>
        </table>
        <table class="table-header">
            <tr>
                <td style="font-size: 18px;">แคชเชียร์ ........ <span
                            style="position: absolute;"><?= \backend\models\User::findName(\Yii::$app->user->id) ?></span>
                    .........
                </td>
            </tr>
            <tr>
            </tr>
        </table>
        <br/>
        <br/>
    </div>
    <?php  if ($print_type == 2 || $print_type == '2'): ?>
        <div id="print-area-2" style="height: 450px;width: 350px;display: none;">
            <table class="table-qrcode" style="width: 100%">
                <tr>
                    <td style="width: 20%">
                        <div style="height: 150px;width: 150px;">
                            <?php
                            \Yii::$app->response->format = Response::FORMAT_HTML;
                            $data = $model->order_no;
                            $qr = new QRCode();
                            echo '<img src="' . $qr->render($data) . '" />';
                            ?>
                        </div>
                    </td>
                    <td style="width: 80%">
                        <table class="table-header" width="100%">
                            <tr>
                                <td style="font-size: 20px;text-align: center;vertical-align: bottom">
                                    <h2>น้ำแข็ง</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 20px;text-align: center;vertical-align: top">
                                    <h2>ใบส่งของ</h2>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table-header" style="width: 100%">
                <tr>
                    <td style="font-size: 18px;text-align: left">เลขที่ <span><?php echo $model->order_no; ?></span>
                    </td>
                    <td style="font-size: 18px;text-align: left">
                        วันที่ <span><?= date('d/m/Y', strtotime($model->order_date)); ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 18px;text-align: left">
                        ลูกค้า <span><?= \backend\models\Customer::findName($model->customer_id); ?></span>
                    </td>
                    <td style="font-size: 18px;text-align: left">
                        เวลา <span><?= date('H:i:s', strtotime($model->order_date)); ?></span>
                    </td>
                </tr>
            </table>
            <table class="table-title" style="width: 100%">
                <tr>
                    <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รายการ</b></td>
                    <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                        <b>จำนวน</b>
                    </td>
                    <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray">
                        <b>ราคา</b>
                    </td>
                    <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รวม</b>
                    </td>
                </tr>
                <?php
                $total_qty = 0;
                $total_amt = 0;
                $discount = 0;
                $change = 0;
                $items_cnt = 0;
                ?>
                <?php foreach ($model_line as $value): ?>
                    <?php
                    $total_qty = $total_qty + $value->qty;
                    $total_amt = $total_amt + ($value->qty * $value->price);
                    $change = $change_amount == null ? 0 : $change_amount;//$value->change_amount;
                    $items_cnt += 1;
                    ?>
                    <tr>
                        <td><?= \backend\models\Product::findName($value->product_id); ?></td>
                        <td style="text-align: center"><?= number_format($value->qty, 1) ?></td>
                        <td style="text-align: center"><?= number_format($value->price, 2) ?></td>
                        <td style="text-align: right"><?= number_format($value->qty * $value->price, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tfoot>
                <tr>
                    <td style="font-size: 18px;border-top: 1px dotted gray">จำนวนรายการ</td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"><?= number_format($items_cnt) ?></td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"></td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: right"><?= number_format($total_amt, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">ส่วนลด</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"><?= number_format($discount, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">จำนวนสุทธิ</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"> <?= number_format($total_amt - $discount, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">ทอนเงิน</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"> <?= number_format($change, 2) ?></td>
                </tr>
                </tfoot>
            </table>
            <table class="table-header">
                <tr>
                    <td></td>
                </tr>
            </table>
            <table class="table-header">
                <tr>
                    <td style="font-size: 18px;">แคชเชียร์ ........ <span
                                style="position: absolute;"><?= \backend\models\User::findName(\Yii::$app->user->id) ?></span>
                        .........
                    </td>
                </tr>
                <tr>
                </tr>
            </table>
            <br/>
            <br/>
        </div>
    <?php endif; ?>
</div>
<form id="form-print2" action="<?= \yii\helpers\Url::to(['pos/printtestnewdo', 'order' => $model->id], true) ?>" method="post">
    <input type="hidden" name="order_id" value="<?= $model->id ?>">
    <input type="hidden" class="ch-amt" name="ch_amt" value="<?= $change_amount ?>">
</form>
<input type="hidden" class="print-type" value="<?= $print_type ?>">
<form id="form-back-pos" action="<?= \yii\helpers\Url::to(['pos/indextest','id'=>0], true) ?>" method="post"></form>
</body>
</html>

<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
$(function(){
    //alert(print_type);
 setTimeout( printContent(),5000);
 //setTimeout( printContent2(),3000);
     
});
function printtest(){
     $("#print-area").printThis();
}
function printContent(){
    //$("#print-area-2").hide();
         var print_type = $(".print-type").val();
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById("print-area").innerHTML;
         document.body.innerHTML = printcontent;
        // window.open('', '', 'left=0,top=0,width=600,height=400,toolbar=1,scrollbars=1,status=0');
         window.print() 
         document.body.innerHTML = restorepage;
       
         console.log(1);
         
         window.onafterprint = function(){
             if(print_type == 2 || print_type == '2'){
                // setTimeout(printContent2(),3000);
               print2();
              // print3(restorepage);
             }else{
                 $("form#form-back-pos").submit();
             }
         }
}

function print2(){
    $("form#form-print2").submit();
}
function print3(printpage){
     var restorepage = document.body.innerHTML;
     console.log(restorepage);
         // document.body.innerHTML = printpage;
         // window.print();
         // document.body.innerHTML = printpage;
         // console.log(2);
}
function printContent2(){
        $("#print-area").hide();
        $("#print-area-2").show();
         var restorepage2 = document.body.innerHTML;
         var printcontent2 = document.getElementById("print-area-2").innerHTML;
         document.body.innerHTML = printcontent2;
        // alert();
        // window.open('', '', 'left=0,top=0,width=600,height=400,toolbar=1,scrollbars=1,status=0');
         window.print();
         document.body.innerHTML = restorepage2;
         console.log(2);
         
         window.onafterprint = function(){
            $("form#form-back-pos").submit();
         }
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
