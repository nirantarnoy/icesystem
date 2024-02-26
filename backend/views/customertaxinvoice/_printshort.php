<?php
$model = \backend\models\Customertaxinvoice::find()->where(['id'=>$print_id])->one();
$modelline = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id'=>$print_id])->all();


$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp',
//$mpdf = new \Mpdf\Mpdf([
    //'tempDir' => '/tmp',
    'mode' => 'utf-8', 'format' => [80, 250],
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

    </style>
    <!--    <script src="vendor/jquery/jquery.min.js"></script>-->
    <!--    <script type="text/javascript" src="js/ThaiBath-master/thaibath.js"></script>-->
</head>
<body>
<table style="width: 100%;">
    <tr>
        <td style="text-align: right;width: 100%;border: none;">
            <h4>เลขที่ <?=$model->invoice_no?> </h4>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;width: 100%;border: none;">
            <h4><u>ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ</u></h4>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;width: 100%;border: none;">
            <h4>บริษัท วรภัทร จำกัด (สาขาที่ 00001)</h4>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;width: 100%;border: none;">
            <p>167 หมู่ 6 ต.ห้วยจรเข้ อ.เมืองนครปฐม จ.นครปฐม 73000 <br>เลขประจำตัวผู้เสียภาษีอากร 0-7355-17000-11-1</p>
        </td>
    </tr>
    <tr>
        <td style="text-align: right;width: 100%;border: none;">
            <h5>วันที่ <?=date('d/m/Y', strtotime($model->invoice_date))?> </h5>
        </td>
    </tr>
</table>

<div class="row">
    <div class="col-lg-12">
        <table>
            <thead>
            <tr>
                <th style="text-align: center;padding: 10px;">รายการ</th>
                <th style="text-align: right;">จำนวน</th>
                <th style="text-align: right;">หน่วยละ</th>
                <th style="text-align: right;">จำนวนเงิน</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0;?>
            <?php foreach ($modelline as $value):?>
            <tr>
                <td><?=\backend\models\Productgroup::findName($value->product_group_id)?></td>
                <td style="text-align: right;"><?=number_format($value->qty,2)?></td>
                <td style="text-align: right;"><?=number_format($value->price,2)?></td>
                <td style="text-align: right;"><?=number_format($value->line_total,2)?></td>
            </tr>
                <?php $total = ($total + $value->line_total);?>
            <?php endforeach;?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="border-left: 0px;border-bottom: 0px;border-right: 0px;text-align: center;">(ราคารวมภาษีมูลค่าเพิ่ม)</td>
                <td style="text-align: right;border-left: 0px;border-bottom: 0px;"><b>รวมเงิน</b></td>
                <td style="text-align: right"><?=number_format($total,2)?></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<br />
<div class="row">
    <div class="col-lg-12" style="text-align: right;">
        <h4>ผู้รับเงิน.......................... </h4>
    </div>
</div>
</body>
</html>
<?php
$slip_path = '';
if($branch_id == 1){
    $slip_path = '../web/uploads/company1/sliptax/slip_tax.pdf';
}else if($branch_id == 2){
    $slip_path = '../web/uploads/company2/sliptax/slip_tax.pdf';
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