<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Customertaxinvoice */
/* @var $form yii\widgets\ActiveForm */

$branch_id = 1;
$filename = "empty";
if (!empty(\Yii::$app->session->getFlash('msg-slip-tax'))) {
    $f_name = \Yii::$app->session->getFlash('msg-slip-tax');
    if ($branch_id == 1) {
        if (file_exists('../web/uploads/company1/sliptax/' . $f_name)) {
            $filename = "../web/uploads/company1/sliptax/" . $f_name;
        }
    } else if ($branch_id == 2) {
        if (file_exists('../web/uploads/company2/sliptax/' . $f_name)) {
            $filename = "../web/uploads/company2/sliptax/" . $f_name;
        }
    }
}
if (!empty(\Yii::$app->session->getFlash('msg-slip-tax-full'))) {
    $f_name = \Yii::$app->session->getFlash('msg-slip-tax-full');
    if ($branch_id == 1) {
        if (file_exists('../web/uploads/company1/sliptax/' . $f_name)) {
            $filename = "../web/uploads/company1/sliptax/" . $f_name;
        }
    } else if ($branch_id == 2) {
        if (file_exists('../web/uploads/company2/sliptax/' . $f_name)) {
            $filename = "../web/uploads/company2/sliptax/" . $f_name;
        }
    }
}
$unit_data = \backend\models\Unit::find()->all();

?>
<?php //echo \Yii::$app->getUrlManager()->baseUrl?>
<input type="hidden" class="slip-print" value="<?= $filename ?>">
<iframe id="iFramePdf" src="<?= $filename ?>" style="display:none;"></iframe>
<div class="customertaxinvoice-form">
    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" class="orderline-id-list" name="order_line_id_list" value="">
    <input type="hidden" class="order-id-list" name="order_id_list" value="">
    <input type="hidden" class="order-product-id-list" name="order_product_id_list" value="">
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'invoice_no')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'find_product_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Product::find()->where(['status' => 1])->all(),
                    'id', 'name'
                ),
                'options' => [
                    'class' => 'selected-customer-id',
                ]
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?php $model->invoice_date == null ? date('Y-m-d') : date('Y-m-d', strtotime($model->invoice_date)) ?>
            <?= $form->field($model, 'invoice_date')->widget(\kartik\date\DatePicker::className(), [
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true,
                    'todayBtn' => true
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'payment_term_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Paymentterm::find()->where(['status' => 1])->all(),
                    'id', 'name'
                )
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?php $model->payment_date == null ? date('Y-m-d') : date('Y-m-d', strtotime($model->payment_date)) ?>
            <?= $form->field($model, 'payment_date')->widget(\kartik\date\DatePicker::className(), [
                'pluginOptions' => [
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true,
                    'todayBtn' => true
                ]
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped table-bordered" id="table-list">
                <thead>
                <tr>
                    <th style="text-align: center">รายการ/รายละเอียด</th>
                    <th style="text-align: right">จำนวน</th>
                    <th style="text-align: center">หน่วยนับ</th>
                    <th style="text-align: right">หน่วยละ</th>
                    <th style="text-align: right">ส่วนลด</th>
                    <th style="text-align: right">รวมเงิน</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($model->isNewRecord): ?>
                    <tr>
                        <td>
                            <input type="hidden" class="line-product-group-id" name="line_product_group_id[]" value="">
                            <input type="text" class="form-control line-product-group-name"
                                   name="line_product_group_name[]" style="text-align: left;" readonly value="">
                        </td>
                        <td>
                            <input type="text" class="form-control line-qty" name="line_qty[]"
                                   style="text-align: right;" readonly value="">
                        </td>
                        <td>
                            <select id="" class="form-control" name="line_unit_id[]">
                                <?php foreach ($unit_data as $unit_value): ?>
                                    <option value="<?= $unit_value->id ?>"><?= $unit_value->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control line-price" name="line_price[]"
                                   style="text-align: right;" value="" onchange="caltaxinvoice2($(this))">
                        </td>
                        <td>
                            <input type="text" class="form-control line-discount" name="line_discount[]"
                                   style="text-align: right;" readonly value="">
                        </td>
                        <td>
                            <input type="text" class="form-control line-total" name="line_total[]"
                                   style="text-align: right;" readonly value="">
                        </td>
                        <td>
                            <input type="hidden" class="line-order-selected-id" value="">
                            <div class="btn btn-sm btn-danger" onclick="removeline($(this))"><i class="fa fa-trash"></i>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($model_line as $value): ?>
                        <tr>
                            <td>
                                <input type="hidden" class="line-product-group-id" name="line_product_group_id[]"
                                       value="<?= $value->product_group_id ?>">
                                <input type="text" class="form-control line-product-group-name"
                                       name="line_product_group_name[]" style="text-align: left;" readonly
                                       value="<?= \backend\models\Productgroup::findName($value->product_group_id) ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control line-qty" name="line_qty[]"
                                       style="text-align: right;" readonly value="<?= number_format($value->qty, 2) ?>">
                            </td>
                            <td>
                                <select id="" class="form-control" name="line_unit_id[]">
                                    <?php foreach ($unit_data as $unit_value): ?>
                                        <?php
                                        $selected = '';
                                        if ($unit_value->id == $value->unit_id) {
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?= $unit_value->id ?>" <?=$selected?>><?= $unit_value->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <input type="text" class="form-control line-price" name="line_price[]"
                                       style="text-align: right;" onchange="caltaxinvoice2($(this))"
                                       value="<?= number_format($value->price, 2) ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control line-discount" name="line_discount[]"
                                       style="text-align: right;" readonly
                                       value="<?= number_format($value->discount_amount, 2) ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control line-total" name="line_total[]"
                                       style="text-align: right;" readonly
                                       value="<?= number_format($value->line_total, 2) ?>">
                            </td>
                            <td>
                                <input type="hidden" class="line-order-selected-id" value="">
                                <div class="btn btn-sm btn-danger" onclick="removeline($(this))"><i
                                            class="fa fa-trash"></i></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-lg-4">
            <div class="btn btn-primary btn-select-product">เลือกสินค้า</div>
        </div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <?php //$model->total_amount = number_format($model->total_amount, 2); ?>
            <?= $form->field($model, 'total_amount')->textInput(['readonly' => 'readonly', 'style' => 'text-align: left', 'id' => 'total-amount']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <?php //$model->vat_amount = number_format($model->vat_amount, 2); ?>
            <?= $form->field($model, 'vat_amount')->textInput(['readonly' => 'readonly', 'style' => 'text-align: left', 'id' => 'vat-amount']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <?php //$model->net_amount = number_format($model->net_amount, 2); ?>
            <?= $form->field($model, 'net_amount')->textInput(['readonly' => 'readonly', 'style' => 'text-align: left', 'id' => 'net-amount']) ?>
        </div>
    </div>
    <div class="row">
        <?php //$model->total_text = numtothai(42.80); ?>
        <div class="col-lg-12"> <?= $form->field($model, 'total_text')->textInput(['readonly' => 'readonly', 'id' => 'amount-totext']) ?></div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-10"><?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <br>
    <div class="row">
        <div class="col-lg-10"><?php //print_r($order_line_list)?></div>
        <div class="col-lg-1">
            <?php if (!$model->isNewRecord): ?>
                <form action="<?= \yii\helpers\Url::to(['customertaxinvoice/printfull'], true) ?>"
                      method="post">
                    <input type="hidden" name="print_id" value="<?= $model->id ?>">
                    <button class="btn btn-warning">พิมพ์แบบเต็ม</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="col-lg-1">
            <?php if (!$model->isNewRecord): ?>
                <form action="<?= \yii\helpers\Url::to(['customertaxinvoice/printshort'], true) ?>"
                      method="post">
                    <input type="hidden" name="print_id" value="<?= $model->id ?>">
                    <button class="btn btn-info">พิมพ์แบบย่อ</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="findModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-lg-12">
                        <b>ค้นหารายการขาย <span class="checkseleted"></span></b>
                    </div>
                </div>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row" style="width: 100%">
                    <div class="col-lg-3">

                        <?php
                        echo \kartik\date\DatePicker::widget([
                            'value' => date('d/m/Y'),
                            'name' => 'search_from_date',
                            'options' => [
                                'id' => 'search-from-date',
                                'class' => 'form-control',
                            ],
                            'pluginOptions' => [
                                'format' => 'dd/mm/yyyy',
                                'todayHighlight' => true,
                                'todayBtn' => true
                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-lg-3">

                        <?php
                        echo \kartik\date\DatePicker::widget([
                            'value' => date('d/m/Y'),
                            'name' => 'search_to_date',
                            'options' => [
                                'id' => 'search-to-date',
                                'class' => 'form-control',
                            ],
                            'pluginOptions' => [
                                'format' => 'dd/mm/yyyy',
                                'todayHighlight' => true,
                                'todayBtn' => true
                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control find-price" name="find_price" value="">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary btn-search-submit">
                            <span class="fa fa-search"></span>
                        </button>
                    </div>
                    <div class="col-lg-2">
                        <!--                        <button type="button" class="close" data-dismiss="modal">&times;</button>-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" style="text-align: right">
                        <button class="btn btn-outline-success btn-emp-selected" data-dismiss="modalx" disabled><i
                                    class="fa fa-check"></i> ตกลง
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                    class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
                <div style="height: 10px;"></div>
                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list" width="100%">
                    <thead>
                    <tr>
                        <th style="text-align: center">เลือก</th>
                        <th>เลขที่บิล</th>
                        <th>วันที่</th>
                        <th>รหัสสินค้า</th>
                        <th>ลูกค้า</th>
                        <th>ประเภท</th>
                        <th style="text-align: right">จำนวน</th>
                        <th style="text-align: right">ราคา</th>
                        <th style="text-align: right">รวม</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <br/>
                <h6>รายการที่เลือกแล้ว</h6>
                <table class="table table-bordered table-striped table-after-list">
                    <thead>
                    <tr>
                        <th style="text-align: center;">รายการ</th>
                        <th style="text-align: right;">จำนวน</th>
                        <th style="text-align: right;">ราคา</th>
                        <th style="text-align: right;">ส่วนลด</th>
                        <th style="text-align: right;">รวม</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-success btn-emp-selected" data-dismiss="modalx" disabled><i
                            class="fa fa-check"></i> ตกลง
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>


<?php
$url_to_find_order = \yii\helpers\Url::to(['customertaxinvoice/findorder'], true);
$url_to_convertnumtotext = \yii\helpers\Url::to(['customertaxinvoice/convertnumtostring'], true);
$url_to_searchorder = \yii\helpers\Url::to(['customertaxinvoice/ordersearch'], true);
$url_to_delete_emp_item = '';
$js = <<<JS
var selecteditem = [];
var selectedorderlineid = [];
var selectedorderid = [];
var selectedlineorderid = [];
var selecteditemgroup = [];
var selectedorderproductid = [];
$(function(){
   $(".btn-select-product").click(function(){
      showfindorder();
      // $("#findModal").modal("show");
   }); 
   
   $(".btn-search-submit").click(function(){
       var customer_id = $('.selected-customer-id').val();
       var from_date = $("#search-from-date").val();
       var to_date = $("#search-to-date").val();
       var find_price = $(".find-price").val();
       
       
      // $(".checkseleted").html(selectedorderid);
   // alert(customer_id);
  //  alert(from_date);
  //  alert(to_date);
  
   // alert(selectedorderid);
    $.ajax({
      type: 'post',
      dataType: 'html',
      url:'$url_to_searchorder',
      async: false,
      data: {'customer_id': customer_id,'search_from_date': from_date,'search_to_date': to_date,'find_price':find_price,'selecteditem': selectedorderid},
      success: function(data){
        //  alert(data);
          $(".table-find-list tbody").html(data);
      },
      error: function(err){
          alert(err);
      }
      
    });
    
   });
   
    var xx = $(".slip-print").val();
     //   alert(xx);
     if(xx !="empty"){
         myPrint();
     }
});

function myPrint(){
        var getMyFrame = document.getElementById('iFramePdf');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
}

function showfindorder(){
    var customer_id = $('.selected-customer-id').val();
  // alert(customer_id);
    $.ajax({
      type: 'post',
      dataType: 'html',
      url:'$url_to_find_order',
      async: false,
      data: {'customer_id': customer_id},
      success: function(data){
       //   alert(data);
          $(".table-find-list tbody").html(data);
          $("#findModal").modal("show");
      },
      error: function(err){
          alert(err);
      }
      
    });
}

function addselecteditem(e) {
        var id = e.attr('data-var');
        var order_id = e.closest('tr').find('.line-find-order-id').val();
        var product_id = e.closest('tr').find('.line-find-product-id').val();
        
        ///// add new 
         var order_line_qty = e.closest('tr').find('.line-find-qty').val();
         var order_line_price = e.closest('tr').find('.line-find-price').val();
         var order_line_product_group_id = e.closest('tr').find('.line-find-product-group-id').val();
         var order_line_product_group_name = e.closest('tr').find('.line-find-product-group-name').val();
         var order_product_id = e.closest('tr').find('.line-find-product-id').val();
        ///////
        if (id) {
            // if(checkhasempdaily(id)){
            //     alert("คุณได้ทำการจัดรถให้พนักงานคนนี้ไปแล้ว");
            //     return false;
            // }
            if (e.hasClass('btn-outline-success')) {
                var obj = {};
                obj['id'] = id;
                obj['code'] = order_id;
                obj['product_id'] = order_product_id;
                obj['order_line_id'] = id;
                obj['product_group_id'] = order_line_product_group_id;
                obj['qty'] = order_line_qty;
                obj['price'] = order_line_price;
                obj['total'] = (order_line_qty * order_line_price);
                selecteditem.push(obj);
                selectedorderlineid.push(obj['id']);
                selectedorderid.push(obj['code']);
                selectedlineorderid.push(obj['code']);
                selectedorderproductid.push(obj['product_id']);
                    var obj_after = {};
                    obj_after['product_group_id'] = order_line_product_group_id;
                    obj_after['product_group_name'] = order_line_product_group_name;
                    obj_after['qty'] = order_line_qty;
                    obj_after['price'] = order_line_price;
                    obj_after['discount'] = 0;
                    obj_after['total'] = (order_line_qty * order_line_price);
                    
                    // alert(obj_after['product_group_id']);
                    // alert(obj_after['product_group_name']);
                    // alert(obj_after['qty']);
                    
                 var afterselected = '';
                if(selecteditemgroup.length == 0){
                    selecteditemgroup.push(obj_after);
                    
                    if(selecteditemgroup.length > 0){
                        for(var x=0;x<=selecteditemgroup.length-1;x++){
                            var line_total_amount =  parseFloat(selecteditemgroup[x]["qty"]) *  parseFloat(selecteditemgroup[x]["price"]);
                            afterselected +='<tr>';
                            afterselected +='<td><input type="hidden" class="product-group-line-id" value="'+ selecteditemgroup[x]['product_group_id'] +'">'+ selecteditemgroup[x]["product_group_name"]+'</td>';
                            afterselected +='<td style="text-align: right;">'+ selecteditemgroup[x]["qty"]+'</td>';
                            afterselected +='<td style="text-align: right;">'+ selecteditemgroup[x]["price"]+'</td>';
                            afterselected +='<td style="text-align: right;">'+ selecteditemgroup[x]["discount"]+'</td>';
                            afterselected +='<td style="text-align: right;">'+ line_total_amount+'</td>';
                            afterselected +='</tr>';
                        }
                        
                        $(".table-after-list tbody").html(afterselected);
                    }
                }else{
                    var check_has = 0;
                    for(var x=0;x<=selecteditemgroup.length-1;x++){
                        if(selecteditemgroup[x]["product_group_id"] == obj['product_group_id']){
                            //alert('มีรายการนี้แล้ว');
                            check_has = 1;
                            continue;
                        }
                    }
                    if(check_has == 0){ // not have group
                         selecteditemgroup.push(obj_after);
                         caltablecontent();
                    }else{ // have group must update qty
                        $.each(selecteditemgroup, function(i, el){
                           if(this.product_group_id == obj['product_group_id']){
                               this.qty = (parseFloat(this.qty) + parseFloat(obj['qty']));
                               this.total = (parseFloat(this.total) + parseFloat(obj['total']));
                           }
                       });
                        caltablecontent();
                    }
                   // $(".table-after-list tbody").append(afterselected);
                }
                
                e.removeClass('btn-outline-success');
                e.addClass('btn-success');
                disableselectitem();
                console.log(selecteditem);
            } else {
                //selecteditem.pop(id);
                $.each(selecteditem, function (i, el) {
                    if (this.id == id) {
                        var qty = this.qty;
                        var product_group_id = this.product_group_id;
                        selecteditem.splice(i, 1);
                        selectedorderlineid.splice(i,1);
                        deleteorderlineselected(product_group_id, qty); // update data in selected list
                        console.log(selecteditemgroup);
                        caltablecontent(); // refresh table below
                    }
                });
                e.removeClass('btn-success');
                e.addClass('btn-outline-success');
                
                disableselectitem();
                console.log(selecteditem);
                console.log(selectedorderlineid);
                console.log(selecteditemgroup);
            }
        }
        $(".orderline-id-list").val(selectedorderlineid);
        $(".order-id-list").val(selectedorderid);
        $(".order-product-id-list").val(selectedorderproductid);
        
    }
    function deleteorderlineselected(id, qty){
       $.each(selecteditemgroup, function(i, el){
           if(this.product_group_id == id && this.qty > 0){
               this.qty = (this.qty - qty);
               if(this.qty <= 0){
                   selecteditemgroup.splice(i,1);
               }
               
           }
       });
    }
    function caltablecontent(){
    var html = '';
    $.each(selecteditemgroup, function(i,el){
         html +='<tr>';
         html +='<td><input type="hidden" class="product-group-line-id" value="'+ this.product_group_id +'">'+ this.product_group_name +'</td>';
         html +='<td style="text-align: right;">'+ this.qty+'</td>';
         html +='<td style="text-align: right;">'+ this.price+'</td>';
         html +='<td style="text-align: right;">'+ this.discount+'</td>';
         html +='<td style="text-align: right;">'+ this.total+'</td>';
         html +='</tr>';
    });
              
      $(".table-after-list tbody").html(html);
    }
    function disableselectitem() {
        if (selecteditem.length > 0) {
            $(".btn-emp-selected").prop("disabled", "");
            $(".btn-emp-selected").removeClass('btn-outline-success');
            $(".btn-emp-selected").addClass('btn-success');
        } else {
            $(".btn-emp-selected").prop("disabled", "disabled");
            $(".btn-emp-selected").removeClass('btn-success');
            $(".btn-emp-selected").addClass('btn-outline-success');
        }
    }
    $(".btn-emp-selected").click(function () {
        var linenum = 0;
        var line_count = 0;
        var emp_qty = $(".selected-emp-qty").val();
        //alert(emp_qty);
        
        $("#table-list tbody tr").each(function () {
            if($(this).closest('tr').find('.line-car-emp-code').val()  != ''){
                // alert($(this).closest('tr').find('.line-car-emp-code').val());
             line_count += 1;   
            }
        });
      // alert(selecteditem.length + line_count);
      // alert(emp_qty);
       // if((line_count + selecteditem.length ) > emp_qty){
       // if((line_count + selecteditem.length ) > 2){
       //      alert('จำนวนพนักงานเกินกว่าที่กำหนด');
       //      return false;
       //  }
        
        if (selecteditemgroup.length > 0) {
            for (var i = 0; i <= selecteditemgroup.length - 1; i++) {
                var product_group_id = selecteditemgroup[i]['product_group_id'];
                var product_group_name = selecteditemgroup[i]['product_group_name'];
                var qty = selecteditemgroup[i]['qty'];
                var price = selecteditemgroup[i]['price'];
                var discount = selecteditemgroup[i]['discount'];
                var total = selecteditemgroup[i]['total'];
             
                //alert(line_prod_id);
                // if(check_dup(line_prod_id) == 1){
                //         alert("มีรายการสินและคำสั่งซื้อนี้ " +line_prod_code+ " มีในรายการแล้ว");
                //         return false;
                // }
                
                var tr = $("#table-list tbody tr:last");
                
                if (tr.closest("tr").find(".line-product-group-id").val() == "") {
                  //  alert(line_prod_code);
                    tr.closest("tr").find(".line-product-group-id").val(product_group_id);
                    tr.closest("tr").find(".line-product-group-name").val(product_group_name);
                    tr.closest("tr").find(".line-qty").val(qty);
                    tr.closest("tr").find(".line-price").val(price);
                    tr.closest("tr").find(".line-discount").val(discount);
                    tr.closest("tr").find(".line-total").val(total);
                    tr.closest("tr").find(".line-order-selected-id").val(selectedlineorderid);
                    //console.log(line_prod_code);
                } else {
                   // alert("dd");
                   // console.log(line_prod_code);
                    //tr.closest("tr").find(".line_code").css({'border-color': ''});

                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                    clone.closest("tr").find(".line-product-group-id").val(product_group_id);
                    clone.closest("tr").find(".line-product-group-name").val(product_group_name);
                    clone.closest("tr").find(".line-qty").val(qty);
                    clone.closest("tr").find(".line-price").val(price);
                    clone.closest("tr").find(".line-discount").val(discount);
                    clone.closest("tr").find(".line-total").val(total);
                    clone.closest("tr").find(".line-order-selected-id").val(selectedlineorderid);
                    
                    tr.after(clone);
                    //cal_num();
                }
            }
            caltaxinvoice();
        //  cal_num();
        }
        // $("#table-list tbody tr").each(function () {
        //    linenum += 1;
        //     $(this).closest("tr").find("td:eq(0)").text(linenum);
        //     // $(this).closest("tr").find(".line-prod-code").val(line_prod_code);
        // });
      
        selecteditem = [];
        selectedorderlineid = [];
        selectedlineorderid = [];
        selecteditemgroup = [];

        $("#table-find-list tbody tr").each(function () {
            $(this).closest("tr").find(".btn-line-select").removeClass('btn-success');
            $(this).closest("tr").find(".btn-line-select").addClass('btn-outline-success');
        });
        
       
        
        $(".btn-emp-selected").removeClass('btn-success');
        $(".btn-emp-selected").addClass('btn-outline-success');
        $("#findModal").modal('hide');
    });
  function caltaxinvoice(){
      var total_amt = 0;
      var vat_amt = 0;
      var grand_total = 0;
      $("#table-list tbody tr").each(function(){
          var line_total = $(this).closest("tr").find(".line-total").val();
          total_amt = parseFloat(total_amt) + parseFloat(line_total);
      });
      vat_amt = (parseFloat(total_amt) * 7)/100;
      grand_total = parseFloat(total_amt) + parseFloat(vat_amt);
      $("#total-amount").val(parseFloat(total_amt).toFixed(2));
      $("#vat-amount").val(parseFloat(vat_amt).toFixed(2));
      $("#net-amount").val(parseFloat(grand_total).toFixed(2));
      
      shownumtotext(parseFloat(grand_total).toFixed(2));
  }
  
   function caltaxinvoice2(e){
      var total_amt = 0;
      var vat_amt = 0;
      var grand_total = 0;
      $("#table-list tbody tr").each(function(){
          var line_total = parseFloat($(this).closest("tr").find(".line-price").val()) * parseFloat($(this).closest("tr").find(".line-qty").val());
          total_amt = parseFloat(total_amt) + parseFloat(line_total);
      });
      vat_amt = (parseFloat(total_amt) * 7)/100;
      grand_total = parseFloat(total_amt) + parseFloat(vat_amt);
      $("#total-amount").val(parseFloat(total_amt).toFixed(2));
      $("#vat-amount").val(parseFloat(vat_amt).toFixed(2));
      $("#net-amount").val(parseFloat(grand_total).toFixed(2));
      
      var new_line_total = parseFloat(e.closest("tr").find(".line-price").val()) * parseFloat(e.closest("tr").find(".line-qty").val());
      e.closest("tr").find(".line-total").val(new_line_total);
      
      shownumtotext(parseFloat(grand_total).toFixed(2));
  }
  function shownumtotext(nums){
    $.ajax({
      type: 'post',
      dataType: 'html',
      url:'$url_to_convertnumtotext',
      async: false,
      data: {'amount': nums},
      success: function(data){
         // alert(data);
          $("#amount-totext").val(data);
      },
      error: function(err){
          alert(err);
      }
      
    });
}
  function check_dup(prod_id){
      var _has = 0;
      $("#table-list tbody tr").each(function(){
          var p_id = $(this).closest('tr').find('.line-car-emp-id').val();
         // alert(p_id + " = " + prod_id);
          if(p_id == prod_id){
              _has = 1;
          }
      });
      return _has;
  }
  
  function removeline(e){
     
          if(confirm('ต้องการลบรายการนี้ใช่หรือไม่ ?')){
               if($("#table-list tbody tr").length == 1){
                   e.closest("tr").find(".line-product-group-id").val('');
                   e.closest("tr").find(".line-product-group-name").val('');
                   e.closest("tr").find(".line-qty").val('');
                   e.closest("tr").find(".line-price").val('');
                   e.closest("tr").find(".line-total").val('');
                   
                    $(".table-after-list tbody tr").each(function(){
                        $(this).closest("tr").find("td:eq(0)").html('');
                        $(this).closest("tr").find("td:eq(1)").html('');
                        $(this).closest("tr").find("td:eq(2)").html('');
                        $(this).closest("tr").find("td:eq(3)").html('');
                        $(this).closest("tr").find("td:eq(4)").html('');
                        $(this).closest("tr").find(".product-group-line-id").val('');
                    });
                   
                 }else{
                   e.parent().parent().remove();
               }
               
               var line_order_id = e.closest("tr").find(".line-order-selected-id").val();
               if(line_order_id != ''){
                   var result = line_order_id.split(',');
                   for(var x= 0; x<= result.length -1 ;x++){
                       for(var z=0;z<=selectedorderid.length-1;z++){
                           if(selectedorderid[z] == result[x]){
                                selectedorderid.splice(z, 1);
                           }
                       }
                   }
               }
              
               
          }
          
          caltaxinvoice();
          
      var linenum = 0;
       $("#table-list tbody tr").each(function () {
            linenum += 1;
       });
       //$(".selected-emp-qty").val(linenum);
  }  
  
  // function checkallreadyseleled(order_id){
  //     selectedorderid.forEach((item){
  //        if(item.code == order_id){
  //            alert();
  //        } 
  //     });
  // }

JS;

$this->registerJs($js, static::POS_END);
?>
