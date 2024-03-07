<?php

use kartik\daterange\DateRangePicker;

$f_date = date('Y-m-d');
$t_date = date('Y-m-d');

if ($from_date != null) {
    $fxdate = explode('-', $from_date);
    if (count($fxdate) > 1) {
        $f_date = $fxdate[2] . '/' . $fxdate[1] . '/' . $fxdate[0];
    }
}
if ($to_date != null) {
    $txdate = explode('-', $to_date);
    if (count($txdate) > 1) {
        $t_date = $txdate[2] . '/' . $txdate[1] . '/' . $txdate[0];
    }
}


$all_total = 0;
$data_group = [];

$sql2 = "SELECT t3.id,t3.name
              FROM customer_tax_invoice as t1 inner join customer_tax_invoice_line as t2 on t2.tax_invoice_id = t1.id inner join product_group as t3 on t2.product_group_id = t3.id
             WHERE date(t1.invoice_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t1.invoice_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " ";

$sql2 .= " GROUP BY t3.name";
$sql2 .= " ORDER BY t3.id";
$query2 = \Yii::$app->db->createCommand($sql2);
$model2 = $query2->queryAll();
if ($model2) {
    for ($i = 0; $i <= count($model2) - 1; $i++) {
        array_push($data_group, [
            'id' => $model2[$i]['id'],
            'name' => $model2[$i]['name'],
        ]);
    }
}


function fetchline($product_group_id, $f_date, $t_date)
{
    $data = [];
    if ($product_group_id) {
        $sql = "SELECT sum(t2.qty) as qty,sum(t2.line_total) as line_total,t2.price
              FROM customer_tax_invoice as t1 inner join customer_tax_invoice_line as t2 on t2.tax_invoice_id = t1.id inner join product_group as t3 on t2.product_group_id = t3.id
             WHERE t3.id = " . $product_group_id . " AND date(t1.invoice_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t1.invoice_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " ";


        $sql .= " GROUP BY t2.price";
        $sql .= " ORDER BY t2.price asc";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {

                array_push($data, [
                    'price' => $model[$i]['price'],
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
    }
    return $data;
}

?>
<form action="<?= \yii\helpers\Url::to(['customertaxinvoice/printcheck'], true) ?>" method="post">
    <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
        <tr>

            <td style="width: 20%">
                <div class="label">ตั้งแต่วันที่</div>
                <?php
                echo DateRangePicker::widget([
                    'name' => 'from_date',
                    'value' => $from_date != null ? date('d-m-Y', strtotime($from_date)) : date('d-m-Y'),
                    //    'useWithAddon'=>true,
                    'convertFormat' => true,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'ตั้งแต่วันที่',
                        //  'onchange' => 'this.form.submit();',
                        'autocomplete' => 'off',
                    ],
                    'pluginOptions' => [
                        'timePicker' => false,
                        'timePickerIncrement' => 1,
                        'locale' => ['format' => 'd-m-Y'],
                        'singleDatePicker' => true,
                        'showDropdowns' => true,
                        'timePicker24Hour' => true
                    ]
                ]);
                ?>
            </td>
            <td style="width: 20%">
                <div class="label">ถึงวันที่</div>
                <?php
                echo DateRangePicker::widget([
                    'name' => 'to_date',
                    'value' => $to_date != null ? date('d-m-Y', strtotime($to_date)) : date('d-m-Y'),
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
                        'locale' => ['format' => 'd-m-Y'],
                        'singleDatePicker' => true,
                        'showDropdowns' => true,
                        'timePicker24Hour' => true
                    ]
                ]);
                ?>
            </td>

            <td>
                <input type="submit" class="btn btn-primary" style="margin-top: 30px;" value="ค้นหา">
            </td>
            <td style="width: 25%"></td>
        </tr>
    </table>
</form>
<br/>
<div class="row">
    <div class="col-lg-12">
        ตั้งแต่วันที่ <b style="color: red;"><?= date('d-m-Y', strtotime($f_date)) ?></b> ถึงวันที่ <b
                style="color: red;"><?= date('d-m-Y', strtotime($t_date)) ?></b>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
            <th style="text-align: center;">ราคา</th>
            <th style="text-align: right;">จำนวน</th>
            <th style="text-align: right;">รวมเงิน</th>
            </thead>
            <tbody>
            <?php if ($data_group != null): ?>
                <?php for ($i = 0; $i <= count($data_group) - 1; $i++): ?>
                    <tr>
                        <td colspan="3"><b><?= $data_group[$i]['name'] ?></b></td>
                    </tr>
                    <?php
                    $data = fetchline($data_group[$i]['id'], $f_date, $t_date);
                    ?>
                    <?php if ($data != null): ?>
                        <?php for ($ii=0;$ii<=count($data)-1;$ii++): ?>
                        <?php  $all_total += (float)$data[$ii]['line_total'];?>
                        <tr>
                            <td style="text-align: center;"><?= number_format($data[$ii]['price']) ?></td>
                            <td style="text-align: right;"><?= number_format($data[$ii]['qty']) ?></td>
                            <td style="text-align: right;"><?= number_format($data[$ii]['line_total']) ?></td>
                        </tr>
                            <?php endfor; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php endif; ?>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;"><b>รวม</b></td>
                <td style="text-align: right;"><b><?= number_format($all_total, 2) ?></b></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>