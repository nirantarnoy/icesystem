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
?>

<div class="customerinvoice-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" class="selected-list" name="list_order" value="">
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'journal_no')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <div class="col-lg-4">
            <?php $model->trans_date = $model->isNewRecord ? $t_date : date('d/m/Y', strtotime($model->trans_date)); ?>
            <?= $form->field($model, 'trans_date')->widget(\kartik\date\DatePicker::className(), [
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'todayHighlight' => true
                ]
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?php
            $disabled = false;
            if(!$model->isNewRecord){

            }
            ?>

            <?= $form->field($model, 'customer_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customer::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกลูกค้า--',
                    'onchange' => 'getpaymentrec($(this));'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4"><?= $form->field($model, 'status')->textInput() ?></div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4"></div>
    </div>
    <div class="row">
        <div class="col-lg-10">
            <h5>รายการค้างชำระ</h5>
        </div>
        <div class="col-lg-2" style="text-align: right">
<!--            <div class="btn btn-primary btn-payment" style="display: none"><span class="count-selected"></span>บันทึกชำระเงิน-->
            </div>
        </div>
    </div>
    <hr>
    <?php if($model->isNewRecord):?>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped table-bordered table-list-2" id="table-list-2">
                    <thead>
                    <tr>
                        <th style="text-align: center" width="5%"><input type="checkbox" onchange="showselectpaymentall($(this))" class="selected-all-item"></th>
                        <th style="text-align: center">เลขที่</th>
                        <th style="text-align: center">วันที่</th>
                        <th style="text-align: center">ค้างชำระ</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right">รวม</td>
                        <td style="text-align: right;font-weight: bold"><span class="line-pay-remain">0</span></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
<?php else:?>
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
                            $i += 1;
                            $order_date = \backend\models\Orders::getOrderdate($value->order_id);
                            ?>
                            <tr data-id="<?= $value->id ?>">
                                <td style="text-align: center"><?= $i ?></td>
                                <td style="text-align: center"><?= \backend\models\Orders::getNumber($value->order_id) ?></td>
                                <td style="text-align: center"><?= date('d/m/Y', strtotime($order_date)) ?></td>
                                <td style="text-align: center">
<!--                                    <select name="line_pay_type[]" id="" class="form-control"-->
<!--                                            onchange="checkpaytype($(this))">-->
<!--                                        <option value="0">เงินสด</option>-->
<!--                                        <option value="1">โอนธนาคาร</option>-->
<!--                                    </select>-->
                                    <div class="btn-group">
                                        <div class="btn btn-success" data-var="0" onclick="checkpaytype($(this))">เงินสด</div>
                                        <div class="btn btn-default" data-var="1" onclick="checkpaytype($(this))">โอนธนาคาร</div>
                                    </div>
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
                                    <input type="hidden" class="line-remain-qty" value="<?= $value->remain_amount?>">
                                </td>
                                <td>
                                    <input type="number" class="form-control line-pay" name="line_pay[]" value="0" min="0"
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
<?php endif;?>

   <div class="row">
       <div class="col-lg-12">
           <b><span class="count-selected"></span></b>
       </div>
   </div>
    <div class="form-group">

        <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-payment']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$url_to_get_receive = \yii\helpers\Url::to(['customerinvoice/getitem'], true);
$js = <<<JS
  var removelist = [];
  var selecteditem = [];
  var checkeditem = [];
  var current_row = 0;
  var payment_remove_list = [];
$(function(){
   
});

function linepaychange(e){
   // alert();
    var remain_amount = e.closest('tr').find('.line-remain-qty').val();
    var pay = e.val();
    
    if( parseFloat(pay) > parseFloat(remain_amount)){
        alert('ชำระเงินมากกว่ายอดค้างชำระ');
        e.val(remain_amount);
        e.focus();
        return false;
    }
   calpayment();
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
function calpayment2(){
    var pay_total = 0;
    var remain_amount = 0;
    $(".table-list-2 tbody tr").each(function(){
         var x = $(this).closest('tr').find('.line-pay').val();
         var rem_amt = $(this).closest('tr').find('.line-remain-qty').val();
       
         if(x=='' || x== null){
             x=0;
         }
         pay_total += parseFloat(x);
         remain_amount += parseFloat(rem_amt);
    });
   // alert(pay_total);
    $(".table-list-2 tfoot tr").find(".line-pay-remain").html(addCommas(parseFloat(remain_amount).toFixed(2)));
}

function getpaymentrec(e){
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
                   $(".table-list-2 tbody").html(data);
                 }
              });
    }
    calpayment2();
}
function checkpaytype(e){
    var type_ = e.val();
    var type_ = e.val();
    if(type_ == 1){
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
     
     
     function showselectpayment(e){
        var table_id = e.parent().parent().parent().parent().attr('id');
        var cnt = 0;
        var cnt_selected = 0;
        var cur_id = e.attr('data-var');
       //alert(table_id);
        if(typeof(table_id) != 'undefined'){
               var all_checkbox = $("#"+table_id+ " tbody input[type=checkbox]").length;
               var cnt_selected = $("#"+table_id+" tbody input:checked").length;
               if(all_checkbox == cnt_selected){
                   $(".selected-all-item").prop('checked',true);
                   $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                      $(this).prop('checked',true);
                   //   checkeditem.push($(this).attr('data-var'));
                   });
               }else{
                   // $(".selected-all-item").prop('checked',false);
                   // $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                   //    $(this).prop('checked',false);
                   //    var index = checkeditem.indexOf($(this).attr('data-var'));
                   //    if (index !== -1) {
                   //        checkeditem.splice(index, 1);
                   //    }
                   // });
               }
               
               if(cnt_selected > 0){
                 $('.count-selected').html("เลือก "+cnt_selected+" รายการ");   
               }else{
                 $('.count-selected').html("");   
               }
               
               console.log(checkeditem);
               checkeditem = [];
               $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                    if(this.checked){
                       // if(cur_id != $(this).attr('data-var')){
                          //   alert($(this).attr('data-var'));
                            checkeditem.push($(this).attr('data-var'));
                       // }
                    }else{
                        // var index = checkeditem.indexOf($(this).attr('data-var'));
                        // if (index !== -1) {
                        //     checkeditem.splice(index, 1);
                        // }
                    }
                          
                });
               if(checkeditem.length > 0){
                   $('.btn-payment').show();
               }else{
                     $('.btn-payment').hide();
               }
           
        }
        $('.selected-list').val(checkeditem);
    }
    
    function showselectpaymentall(e){
        var table_id = e.parent().parent().parent().parent().attr('id');
        var cnt = 0;
        var cnt_selected = 0;
        if(typeof(table_id) != 'undefined'){
            var all_checkbox = $("#"+table_id+ " tbody input[type=checkbox]").length;
            var cnt_selected = $("#"+table_id+" tbody input:checked").length;
            if(cnt_selected > 0){
                if(all_checkbox != cnt_selected){
                    //alert();
                     $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                        $(this).prop('checked',true);
                     });
                }else{
                     $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                        $(this).prop('checked',false);
                     });
                }
               
            }else{
                $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
                      $(this).prop('checked',true);
                });
            }
        }
        
        $("#"+table_id+ " tbody input[type=checkbox]").each(function(){
            if(this.checked){
                // alert($(this).attr('data-var'));
                checkeditem.push($(this).attr('data-var'));
            }else{
                var index = checkeditem.indexOf($(this).attr('data-var'));
                if (index !== -1) {
                    checkeditem.splice(index, 1);
                }
            }
                  
        });
        
        if(checkeditem.length > 0){
          //  $('.count-selected').html("["+checkeditem.length+"] ");
             $('.count-selected').html("เลือก "+checkeditem.length+" รายการ");   
            $('.btn-payment').show();
        }else{
            $('.count-selected').html("");   
            $('.btn-payment').hide();
        }
        $('.selected-list').val(checkeditem);
    }
     
JS;

$this->registerJs($js, static::POS_END);
?>