<?php

?>
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
</head>
<body>
<div id="div1">
    <table style="width: 100%;border: 0px;">
        <tr>
            <td style="width: 70%"><h3>วรภัทรไอซ์</h3></td>
            <td><h3>ใบวางบิล/Billing Note</h3></td>
        </tr>
        <tr>
            <td>ชื่อ <b><?=\backend\models\Customer::findName($model->customer_id)?></b></td>
            <td>ใบวางบิลเลขที่ <b><?=$model->journal_no?></b></td>
        </tr>
        <tr>
            <td>ที่อยู่ <b><?=\backend\models\Customer::getAddress($model->customer_id)?></b></td>
            <td>วันที่ <b><?=date('d/m/Y', strtotime($model->trans_date))?></b></td>
        </tr>
        <tr>
            <td>โทร <b><?=\backend\models\Customer::getPhone($model->customer_id)?></b></td>
            <td></td>
        </tr>
    </table>
    <br />
    <?php
    $total_all = 0;
    $count_item = 0;
    $num = 0;
    $total_line = 0;
    ?>
    <table style="width: 100%">
      <tr>
          <td rowspan="2" style="text-align: center;padding: 0px;border: 1px solid grey">ลำดับ</td>
          <td rowspan="2" style="text-align: center;padding: 0px;border: 1px solid grey">เลขที่บิล</td>
          <td rowspan="2" style="text-align: center;padding: 0px;border: 1px solid grey" >วันที่บิล</td>
          <td rowspan="2" style="text-align: center;padding: 0px;border: 1px solid grey">จำนวนเงิน</td>
          <td colspan="2" style="text-align: center;padding: 0px;border: 1px solid grey">หมายเหตุ</td>

      </tr>
        <tr>
            <td style="text-align: center;border: 1px solid grey">ส่วนลด</td>
            <td style="text-align: center;border: 1px solid grey">สุทธิ</td>
        </tr>
        <?php foreach ($model_line as $value):?>
        <?php
            $num+=1;

            $total_line =  \backend\models\Orders::getlineremainsum($value->order_id, $model->customer_id);
            $total_all = $total_all + $total_line;
            ?>
        <tr>
            <td style="text-align: center;padding: 10px;border: 1px solid grey"><?=$num?></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><?=\backend\models\Orders::getNumber($value->order_id)?></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey" ><?=date('d/m/y', strtotime(\backend\models\Orders::getOrderdate($value->order_id)))?></td>
            <td style="text-align: right;padding: 0px;padding-right: 5px;border: 1px solid grey"><?=number_format($total_line)?></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey">0</td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><?=number_format($total_line)?></td>
        </tr>
        <?php endforeach;?>
        <tfoot>
        <tr>
            <td colspan="2" style="text-align: left;padding: 0px;text-indent: 15px;border: 1px solid grey;padding: 10px;"><b>รวม <span><?=$num?> ฉบับ</span></b></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><b>รวมเงิน</b></td>
            <td style="text-align: right;padding: 0px;padding-right: 5px;border: 1px solid grey"><b><?=number_format($total_all)?></b></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><b><?=number_format(0)?></b></td>
            <td style="text-align: center;padding: 0px;border: 1px solid grey"><b><?=number_format($total_all)?></b></td>
        </tr>
        </tfoot>
    </table>
    <br />
    <table style="width: 100%">
        <tr>
            <td style="width: 100%">
                ได้รับบิลตามรายการข้างต้นไว้ถูกต้องเรียบร้อยแล้ว
            </td>
        </tr>
    </table>
    <br />
    <table style="width: 100%">
        <tr>
            <td style="text-align: center;width: 50%">(..................................................................................)</td>
            <td style="text-align: center">(..................................................................................)</td>
        </tr>
        <tr>
            <td style="text-align: center;width: 50%">ผู้รับบิล</td>
            <td style="text-align: center">ผู้วางบิล</td>
        </tr>
    </table>
    <br />
    <table style="width: 100%">
        <tr>
            <td style="width: 5%;padding: 15px;"><input type="checkbox" class="form-control"></td>
            <td>เงินสด</td>
        </tr>
        <tr>
            <td style="width: 5%;padding: 15px;"><input type="checkbox" class="form-control"></td>
            <td>โอนธนาคาร</td>
        </tr>
    </table>
</div>
<br />
<button class="btn btn-info" onclick="printContent('div1')">พิมพ์ใบวางบิล</button>
</body>
</html>

<?php
$js=<<<JS
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
JS;
$this->registerJs($js,static::POS_END);
?>
