<?php
$model = \backend\models\Customertaxinvoice::find()->where(['id' => $print_id])->one();
$modelline = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id' => $print_id])->all();


$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp',
//$mpdf = new \Mpdf\Mpdf([
    //'tempDir' => '/tmp',
    //'mode' => 'utf-8', 'format' => [210, 149],
    'mode' => 'utf-8', 'format' => 'A5','orientation'=>'L',
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
$mpdf->SetDisplayMode('fullpage');
$mpdf->AddPageByArray([
    'margin-left' => 3,
    'margin-right' => 3,
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
        body {
            font-family: sarabun;
            /*font-family: garuda;*/
            font-size: 16px;
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
        table.table-invoice-no {
            border-collapse: collapse;
            font-size: 12px;
        }
        table.table-list-detail{
            font-size: 12px;
        }

    </style>
    <!--    <script src="vendor/jquery/jquery.min.js"></script>-->
    <!--    <script type="text/javascript" src="js/ThaiBath-master/thaibath.js"></script>-->
</head>
<body>
<table style="100%">
    <tr>
        <td style="width: 33.33%;border: none;">
            <div class="row">
                <div class="col-lg-12" style="text-align: left;">
                    <h6>บริษัท วรภัทร จำกัด (สาขาที่ 00001)</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" style="text-align: left;">
                    <p style="font-size: 12px;">167 หมู่ 6 ต.ห้วยจรเข้ อ.เมืองนครปฐม จ.นครปฐม 73000 <br>เลขประจำตัวผู้เสียภาษีอากร
                        0-7355-17000-11-1</p>
                </div>
            </div>
        </td>
        <td style="width: 33.33%;border: none;">
            <div class="row">
                <div class="col-lg-12" style="text-align: center;">
                    <h6>ใบกำกับภาษี/ใบส่งสินค้า</h6><h5>TAX INVOICE/INVOICE</h5>
                </div>
            </div>
        </td>
        <td style="width: 33.33%;border: none;text-align: right;vertical-align: top;">
            <h6>เอกสารออกเป็นชุด</h6>
        </td>
    </tr>
</table>
<div class="row">
    <div class="col-lg-12">
        <table style="width: 100%;border-collapse: collapse;" class="table-invoice-no">
            <tr>
                <td style="width: 65%">
                    <table style="width: 100%;border-collapse: collapse;">
                        <tr><td style="width: 20%;border:none;padding: 0px;">รหัสลูกค้า</td><td style="border: none;padding: 0px"><?=\backend\models\Customer::findCode($model->customer_id)?></td></tr>
                        <tr><td style="border: none;padding: 0px">นามลูกค้า</td><td style="border: none;padding: 0px"><?=\backend\models\Customer::findName($model->customer_id)?></td></tr>
                        <tr><td style="border: none;padding: 0px">ที่อยู่</td><td style="border: none;padding: 0px"><?=\backend\models\Customer::getAddress($model->customer_id)?></td></tr>
                        <tr><td style="color: white;border: none;padding: 0px">xx</td><td style="border: none;padding: 0px"></td></tr>
                    </table>
                </td>
                <td style="width: 35%">
                    <table style="width: 100%;border-collapse: collapse;border:none;">
                        <tr><td style="width: 50%;border: none;padding: 0px">วันที่</td><td style="border: none;padding: 0px"><?=date('d/m/Y')?></td></tr>
                        <tr><td style="border: none;padding: 0px">เลขที่ใบกำกับ</td><td style="border: none;padding: 0px"><?=$model->invoice_no?></td></tr>
                        <tr><td style="border: none;padding: 0px">กำหนดชำระเงิน</td><td style="border: none;padding: 0px"></td></tr>
                        <tr><td style="border: none;padding: 0px">ครบกำหนด</td><td style="border: none;padding: 0px"></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="height: 2px;"></div>
<div class="row">
    <div class="col-lg-12">
        <table class="table-list-detail" style="widht: 100%">
            <thead>
            <tr>
                <th style="text-align: center;padding: 10px;">รหัสสินค้า/รายละเอียด</th>
                <th style="text-align: center;width: 10%">จำนวน</th>
                <th style="text-align: center;width: 10%">หน่วยละ</th>
                <th style="text-align: center;width: 10%">ส่วนลด</th>
                <th style="text-align: right;width: 15%">จำนวนเงิน</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($modelline as $value): ?>
                <tr>
                    <td><?= \backend\models\Productgroup::findName($value->product_group_id) ?></td>
                    <td style="text-align: center;"><?= number_format($value->qty, 2) ?></td>
                    <td style="text-align: center;"><?= number_format($value->price, 2) ?></td>
                    <td style="text-align: center;"><?= number_format($value->discount_amount, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($value->line_total, 2) ?></td>
                </tr>
                <?php $total = ($total + $value->line_total); ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="border-right: 0px;text-align: center;"><b>(<?=$model->total_text?>)</b></td>
                <td colspan="2" style="text-align: left;"><b>รวมเงิน</b></td>
                <td style="text-align: right"><?= number_format($total, 2) ?></td>
            </tr>
            <tr>
                <td colspan="2" rowspan="2" style="border-left: 0px;border-bottom: 0px;border-right: 0px;border-top: 0px;text-align: left;padding-bottom: -5px;">1. ได้รับสินค้าตามรายการข้างบนนี้ไว้ถูกต้องเรียบร้อยแล้ว<br />2. สินค้าตามใบส่งสินค้านี้ หากมีการเสียหายหรือขาดตกบกพร่องประการใด โปรดแจ้งให้ทราบภายใน 3 วัน <br /> นับจากวันที่ได้รับสินค้า มิฉะนั้นจะไม่รับการเรียกค่าชดเชยใดๆทั้งสิ้น</td>
                <td colspan="2" style="text-align: left;border-right: 0px;border-top: 0px;"><b>ภาษีมูลค่าเพิ่ม</b></td>
                <td style="text-align: right"><?= number_format($model->vat_amount, 2) ?></td>
            </tr>
            <tr>
<!--                <td colspan="2"style="border-left: 0px;border-bottom: 0px;border-right: 0px;border-top: 0px;text-align: left;top: -5px;"><p></p></td>-->
                <td colspan="2" style="text-align: left;border-right: 0px;border-top: 0px;"><b>ยอดเงินสุทธิ</b></td>
                <td style="text-align: right"><?= number_format($model->net_amount, 2) ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<br/>
<div class="row">
    <table style="width: 100%">
        <tr>
            <td style="width: 33.33%;border: none;">
                <table style="width: 100%;border: 1px solid grey;border-radius: 20px;" class="table-list-detail">
                    <tr>
                        <td style="text-align: center;border-bottom: 0px;">ได้รับสินค้าตามรายการถูกต้องแล้ว</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;border-bottom: 0px;">.................................................................................................................</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;">วันที่ .......................................................................................................</td>
                    </tr>
                </table>
            </td>
            <td style="width: 33.33%;border: none;">
                <table style="width: 100%;border-radius: 10px" class="table-list-detail">
                    <tr>
                        <td style="text-align: center;border-bottom: 0px;">ผู้ส่งของ</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;border-bottom: 0px;">.................................................................................................................</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;">วันที่ .......................................................................................................</td>
                    </tr>
                </table>
            </td>
            <td style="width: 33.33%;border: none;">
                <table style="width: 100%;border-radius: 10px" class="table-list-detail">
                    <tr>
                        <td style="text-align: center;border-bottom: 0px;">ผู้อนุมัติ</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;border-bottom: 0px;">.................................................................................................................</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;border-top:0px;">วันที่ .......................................................................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<!--<div class="row">-->
<!--    <div class="col-lg-4">-->
<!--       -->
<!--    </div>-->
<!--    <div class="col-lg-4">-->
<!--        -->
<!--    </div>-->
<!--    <div class="col-lg-4">-->
<!--        -->
<!--    </div>-->
<!--</div>-->
</body>
</html>

<?php
$slip_path = '';
if($branch_id == 1){
    $slip_path = '../web/uploads/company1/sliptax/slip_tax_full.pdf';
}else if($branch_id == 2){
    $slip_path = '../web/uploads/company2/sliptax/slip_tax_full.pdf';
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

//$mpdf->Output('../web/uploads/slip/slip_index.pdf', 'F');
$mpdf->Output($slip_path, 'F');

ob_end_flush();

//header("location: system_stock/report_pdf/Packing.pdf");

?>
<!--ดาวโหลดรายงานในรูปแบบ PDF <a class="btn-export-pdf" href="MyPDF.pdf">คลิกที่นี้</a>-->