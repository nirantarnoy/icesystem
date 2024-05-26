<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$company_id = 1;
$branch_id = 1;
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}
$t_date = date('d/m/Y');

$is_admin = \backend\models\User::checkIsAdmin(\Yii::$app->user->id);
$date_enable = true;
if ($is_admin == 1) {
    $date_enable = false;
}
if(\Yii::$app->user->identity->username == 'beau' || \Yii::$app->user->identity->username == 'dow'){
    $date_enable = false;
}

?>

<div class="paymentreceive-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'form-receive', 'enctype' => 'multipart/form-data']]); ?>
    <input type="hidden" name="removelist" class="remove-list" value="">
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'journal_no')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <div class="col-lg-3">
            <?php $model->trans_date = $model->isNewRecord ? $t_date : date('d/m/Y', strtotime($model->trans_date)); ?>
            <?= $form->field($model, 'trans_date')->widget(\kartik\date\DatePicker::className(), [
                'disabled' => $date_enable,
                'options' => [

                ],
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?php
            $disabled = false;
            if (!$model->isNewRecord) {

            }
            ?>

            <?= $form->field($model, 'customer_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_show_pos' => 1,'status'=>1])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกลูกค้าหน้าบ้าน--',
                    'onchange' => 'getpaymentrec($(this));'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <label for="">ลูกค้าสายรถ</label>
            <?php

            echo \kartik\select2\Select2::widget([
                'name' => 'customer_car_id',
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_show_pos' => 0,'status'=>1])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกลูกค้า--',
                    'onchange' => 'getpaymentrecfromcar($(this));'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ])
            ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped table-bordered table-list">
                <thead>
                <tr>
                    <th style="text-align: center" width="5%">#</th>
                    <th style="text-align: center">เลขที่</th>
                    <th style="text-align: center">วันที่</th>
                    <th style="text-align: center">ช่องทางชำระ</th>
                    <!--                    <th style="text-align: center">แนบเอกสาร</th>-->
                    <th style="text-align: center">ค้างชำระ</th>
                    <th style="text-align: center">ยอดชำระ</th>
                </tr>
                </thead>
                <tbody>
                <!--                <tr>-->
                <!--                    <td></td>-->
                <!--                    <td></td>-->
                <!--                    <td></td>-->
                <!--                    <td></td>-->
                <!--                    <td></td>-->
                <!--                </tr>-->
                <?php if ($model_line != null): ?>
                    <?php $i = 0; ?>
                    <?php foreach ($model_line as $value): ?>
                        <?php
                        if ($value->remain_amount <= 0) continue;
                        $i += 1;
                        $order_date = \backend\models\Orders::getOrderdate($value->order_id);
                        ?>
                        <tr data-id="<?= $value->id ?>">
                            <td style="text-align: center"><?= $i ?></td>
                            <td style="text-align: center"><?= \backend\models\Orders::getNumber($value->order_id) ?></td>
                            <td style="text-align: center"><?= date('d/m/Y', strtotime($order_date)) ?></td>
                            <td>
<!--                                <select name="line_pay_type[]" id="" class="form-control"-->
<!--                                        onchange="checkpaytype($(this))">-->
<!--                                    <option value="1">เงินสด</option>-->
<!--                                    <option value="2">โอนธนาคาร</option>-->
<!--                                </select>-->
                                <div class="btn-group">
                                    <div class="btn btn-success line-pay-cash" data-var="1"
                                         onclick="checkpaytype2($(this))">เงินสด
                                    </div>
                                    <div class="btn btn-default line-pay-bank" data-var="2"
                                         onclick="checkpaytype3($(this))">โอนธนาคาร
                                    </div>

                                </div>
                                <input type="hidden" class="line-payment-type" name="line_pay_type[]" value="0">
                                <input type="file" class="line-doc" name="line_doc[]" style="display: none">
                                <input type="hidden" class="line-order-id" name="line_order_id[]"
                                       value="<?= $value->order_id ?>">
                                <input type="hidden" class="line-number" name="line_number[]" value="<?= ($i - 1) ?>">
                                <input type="hidden" class="line-id" name="line_id[]" value="<?= $value->id ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control line-remain" style="text-align: right"
                                       name="line_remain[]" value="<?= number_format($value->remain_amount, 2) ?>"
                                       readonly>
                                <input type="hidden" class="line-remain-qty"
                                       value="<?= number_format($value->remain_amount, 2) ?>">
                            </td>
                            <td>
                                <input type="number" class="form-control line-pay" name="line_pay[]"
                                       value="<?= $value->payment_amount ?>>" min="0" step="any"
                                       onchange="linepaychange($(this))">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right">รวม</td>
                    <td style="text-align: right;font-weight: bold"><span class="line-pay-remain">0</span></td>
                    <td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php if ($model->isNewRecord): ?>
        <div class="row show-button-payment" style="display: none;">
            <div class="col-lg-9">
                <div class="btn btn-primary" onclick="selectallcash();">ชำระด้วยเงินสดทั้งหมด</div>
                <div class="btn btn-warning" onclick="selectallbank();">ชำระด้วยเงินโอนทั้งหมด</div>

            </div>
            <div class="col-lg-3" style="text-align: right">

            </div>
        </div>
    <?php endif; ?>

    <br />

    <div class="form-group show-save" style="display: none">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$url_to_get_receive = \yii\helpers\Url::to(['paymentreceive/getitem'], true);
$url_to_get_receive_new = \yii\helpers\Url::to(['paymentreceive/getitemnew'], true);
$url_to_get_receive_new_car = \yii\helpers\Url::to(['paymentreceive/getitemnewcar'], true);
$js = <<<JS
var removelist = [];
var selecteditem = [];
$(function(){
   
});

function linepaychange(e){
   // alert();
    var remain_amount = e.closest('tr').find('.line-remain-qty').val();
    var pay = e.val();
    //alert(e.val());
   // e.val(100);
    // if( parseFloat(pay).toFixed(2) > parseFloat(remain_amount).toFixed(2)){
    //     alert('ชำระเงินมากกว่ายอดค้างชำระ');
    //     e.val(parseFloat(remain_amount).toFixed(2));
    //     e.focus();
    //     return false;
    // }
   calpayment();
}

function selectallcash(){
    $(".table-list tbody tr").each(function(){
        $(this).closest('tr').find('.line-payment-type').val(1);
        $(this).closest('tr').find('.line-pay-bank').removeClass('btn-success');
        $(this).closest('tr').find('.line-pay-bank').addClass('btn-default');
        $(this).closest('tr').find('.line-pay-cash').removeClass('btn-default');
        $(this).closest('tr').find('.line-pay-cash').addClass('btn-success');
    });
}
function selectallbank(){
    $(".table-list tbody tr").each(function(){
        $(this).closest('tr').find('.line-payment-type').val(2);
        $(this).closest('tr').find('.line-pay-cash').removeClass('btn-success');
        $(this).closest('tr').find('.line-pay-cash').addClass('btn-default');
        $(this).closest('tr').find('.line-pay-bank').removeClass('btn-default');
        $(this).closest('tr').find('.line-pay-bank').addClass('btn-success');
    });
}

function checkpaytype2(e){
    var val = e.attr('data-var');
        e.closest('tr').find('.line-payment-type').val(val);
        e.closest('tr').find('.line-pay-bank').removeClass('btn-success');
        e.closest('tr').find('.line-pay-bank').addClass('btn-default');
        e.removeClass('btn-default');
        e.addClass('btn-success');
}
function checkpaytype3(e){
    var val = e.attr('data-var');
        e.closest('tr').find('.line-payment-type').val(val);
        e.closest('tr').find('.line-pay-cash').removeClass('btn-success');
        e.closest('tr').find('.line-pay-cash').addClass('btn-default');
        e.removeClass('btn-default');
        e.addClass('btn-success');
}

function calpayment(){
    var pay_total = 0;
    var remain_amount = 0;
    $(".table-list tbody tr").each(function(){
         var x = $(this).closest('tr').find('.line-pay').val();
         var rem_amt = $(this).closest('tr').find('.line-remain-qty').val();
       
         if(x=='' || x== null){
             x=0;
         }
         pay_total += parseFloat(x);
         remain_amount += parseFloat(rem_amt);
    });
   // alert(pay_total);
    $(".table-list tfoot tr").find(".line-pay-remain").html(addCommas(parseFloat(remain_amount).toFixed(2)));
    $(".table-list tfoot tr").find(".line-pay-total").html(addCommas(parseFloat(pay_total).toFixed(2)));
}

function getpaymentrec(e){
    var ids = e.val();
    if(ids){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_get_receive_new",
              'data': {'customer_id': ids},
              'success': function(data) {
                  //  alert(data);
                   if(data != ''){
                       $(".show-save").show();
                       $(".show-button-payment").show();
                   }else{
                       $(".show-save").hide();
                       $(".show-button-payment").hide();
                   }
                   $(".table-list tbody").html(data);
                 }
              });
    }
    calpayment();
}
function getpaymentrecfromcar(e){
    var ids = e.val();
    if(ids){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_get_receive_new_car",
              'data': {'customer_id': ids},
              'success': function(data) {
                  //  alert(data);
                   if(data != ''){
                       $(".show-save").show();
                       $(".show-button-payment").show();
                   }else{
                       $(".show-save").hide();
                       $(".show-button-payment").hide();
                   }
                   $(".table-list tbody").html(data);
                 }
              });
    }
    calpayment();
}
function getpaymentrecold(e){
    var ids = e.val();
    if(ids){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_get_receive",
              'data': {'customer_id': ids},
              'success': function(data) {
                  //  alert(data);
                   if(data != ''){
                       $(".show-save").show();
                   }else{
                       $(".show-save").hide();
                   }
                   $(".table-list tbody").html(data);
                 }
              });
    }
    calpayment();
}
function checkpaytype(e){
    var type_ = e.val();
  //  alert(type_);
    if(type_ == 2){
         e.closest('tr').find('.line-doc').trigger('click');
    }
   
    
}

function cal_linenum() {
        var xline = 0;
        $("#table-list tbody tr").each(function () {
            xline += 1;
            $(this).closest("tr").find("td:eq(0)").text(xline);
        });
    }
    function removeline(e) {
        if (confirm("ต้องการลบรายการนี้ใช่หรือไม่?")) {
            if (e.parent().parent().attr("data-var") != '') {
                removelist.push(e.parent().parent().attr("data-var"));
                $(".remove-list").val(removelist);
            }
            // alert(removelist);

            if ($("#table-list tbody tr").length == 1) {
                $("#table-list tbody tr").each(function () {
                    $(this).find(":text").val("");
                   // $(this).find(".line-prod-photo").attr('src', '');
                    $(this).find(".line-price").val(0);
                    cal_num();
                });
            } else {
                e.parent().parent().remove();
            }
            cal_linenum();
           // cal_all();
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
