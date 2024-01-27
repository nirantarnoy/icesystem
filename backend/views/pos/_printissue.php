<?php
date_default_timezone_set('Asia/Bangkok');
use chillerlan\QRCode\QRCode;
use yii\web\Response;

//require_once __DIR__ . '/vendor/autoload.php';
//require_once 'vendor/autoload.php';
// เพิ่ม Font ให้กับ mPDF
$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp',
//$mpdf = new \Mpdf\Mpdf([
    //'tempDir' => '/tmp',
    'mode' => 'utf-8', 'format' => [80, 120],
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
<table class="table-header" style="width: 100%;font-size: 18px;" border="0">

</table>
<table class="table-header" width="100%">
<!--    <tr>-->
<!--        <td style="font-size: 20px;text-align: center;vertical-align: bottom">-->
<!--            <h5>น้ำแข็ง</h5>-->
<!--        </td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td style="font-size: 20px;text-align: center;vertical-align: top">-->
<!--            <h5>ใบเบิกสินค้า</h5>-->
<!--        </td>-->
<!--    </tr>-->

    <tr>
        <td style="width: 2%">
            <div style="height: 20px;width: 20px;">
                <?php
                \Yii::$app->response->format = Response::FORMAT_HTML;
                $data = $model->journal_no;
                $qr = new QRCode();
                echo '<img src="' . $qr->render($data) . '" />';
                ?>
<!--                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=--><?//=$model->journal_no?><!--&choe=UTF-8" alt="">-->
            </div>
        </td>
        <td style="width: 98%">
            <table class="table-header" width="100%">
                <tr>
                    <td style="font-size: 20px;text-align: center;vertical-align: bottom">
                        <h2>น้ำแข็ง</h2>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 20px;text-align: center;vertical-align: top">
                        <h2>ใบเบิกสินค้า</h2>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
<table class="table-header" width="100%">
    <tr>
        <td style="font-size: 18px;text-align: left">
            เลขที่ <span><?= $model->journal_no; ?></span>
        </td>
        <td style="font-size: 18px;text-align: left">
            วันที่ <span><?= date('d/m/Y', strtotime($model->trans_date)); ?></span>
        </td>
    </tr>
    <tr>
        <td style="font-size: 18px;text-align: left">
            <?php
            $driver_name = '';
              $driver_data = \common\models\QueryCarEmpData::find()->where(['id'=>$model->delivery_route_id,'is_driver'=>1,'date(trans_date)'=>date('Y-m-d')])->one();
              if($driver_data){
                  $driver_name = $driver_data->fname;
              }
            ?>
            สายส่ง <span><?= \backend\models\Deliveryroute::findName($model->delivery_route_id); ?> <?=$driver_name?></span>
        </td>
        <td style="font-size: 18px;text-align: left">
            เวลา <span><?=date('H:i:s')?></span>
        </td>
    </tr>

</table>
<table class="table-title">
    <tr>
        <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รายการ</b></td>
        <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>จำนวน</b></td>
    </tr>
    <?php
    $total_qty = 0;
    $total_amt = 0;
    $discount = 0;
    $change = 0;
    ?>
    <?php foreach ($model_line as $value): ?>
        <?php
        if($value->qty <=0)continue;
        $total_qty = $total_qty + $value->qty;
        ?>
        <tr>
            <td><?= \backend\models\Product::findName($value->product_id); ?></td>
            <td style="text-align: center"><?= number_format($value->qty,1) ?></td>
        </tr>

    <?php endforeach; ?>
    <tfoot>
    <tr>
        <td style="font-size: 18px;border-top: 1px dotted gray">จำนวนรายการ</td>
        <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"><?= number_format($total_qty,1) ?></td>
    </tr>
    </tfoot>
</table>
<table class="table-header">

</table>
<table class="table-header">
    <tr>
        <td style="font-size: 18px;">พนักงานขายหน้าบ้าน ........ <span style="position: absolute;"><?=\backend\models\User::findName(\Yii::$app->user->id)?></span> .........</td>
    </tr>
</table>
<br/>
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
//include("pdf_footer.php");
?>
<?php

//if(file_exists('../web/uploads/slip_car_pos/slip.pdf')){
//    unlink('../web/uploads/slip_car_pos/slip.pdf');
//}

$slip_path = '';
if($branch_id == 1){
    $slip_path = '../web/uploads/company1/slip_car_pos/slip.pdf';
}else if($branch_id == 2){
    $slip_path = '../web/uploads/company2/slip_car_pos/slip.pdf';
}
//    if(file_exists('../web/uploads/slip/slip_index.pdf')){
//        unlink('../web/uploads/slip/slip_index.pdf');
//    }
if(file_exists($slip_path)){
    unlink($slip_path);
}

$html = ob_get_contents(); // ทำการเก็บค่า HTML จากคำสั่ง ob_start()
$mpdf->WriteHTML($html); // ทำการสร้าง PDF ไฟล์
//$mpdf->Output( 'Packing02.pdf','F'); // ให้ทำการบันทึกโค้ด HTML เป็น PDF โดยบันทึกเป็นไฟล์ชื่อ MyPDF.pdf
ob_clean();
//$mpdf->SetJS('this.print();');
$mpdf->SetJS('this.print();');
//$mpdf->Output('../web/uploads/slip_car_pos/slip.pdf', 'F');
$mpdf->Output($slip_path, 'F');
ob_end_flush();
//header("location: system_stock/report_pdf/Packing.pdf");
?>
