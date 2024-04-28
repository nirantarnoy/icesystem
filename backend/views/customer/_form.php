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

?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <input type="hidden" name="removelist" class="remove-list" value="">
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'readonly' => 'readonly', 'value' => $model->isNewRecord ? 'Draft' : $model->code]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'customer_group_id')->Widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customergroup::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกกลุ่มลูกค้า--'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'delivery_route_id')->Widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกเส้นทางขนส่ง--'
                ]
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'customer_type_id')->Widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Customertype::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->all(), 'id', function ($data) {
                    return $data->code . ' ' . $data->name;
                }),
                'options' => [
                    'placeholder' => '--เลือกประเภทลูกค้า--'
                ]
            ]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'location_info')->textInput(['maxlength' => true]) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'branch_no')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'active_date')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'payment_method_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Paymentmethod::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => '--วิธีชำระเงิน--'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'payment_term_id')->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(\backend\models\Paymentterm::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => '--เงื่อนไขชำระเงิน--'
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'address')->textarea(['maxlength' => true]) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-3">
            <?php //echo $form->field($model, 'shop_photo')->fileInput(['maxlength' => true]) ?>
            <br>
            <?php if ($model->shop_photo != ''): ?>
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <img src="../web/uploads/images/customer/<?= $model->shop_photo ?>"
                             width="100%"
                             alt="">
                    </div>
                    <div class="col-lg-4"></div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <div class="btn btn-danger btn-delete-photo"
                             data-var="<?= $model->id ?>">
                            ลบรูปภาพ
                        </div>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'shop_photo')->fileInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-3">
            <label for=""><?= $model->getAttributeLabel('is_distributor') ?></label>
            <?php echo $form->field($model, 'is_distributor')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label(false) ?>
        </div>
        <div class="col-lg-3">
            <label for=""><?= $model->getAttributeLabel('is_invoice_req') ?></label>
            <?php echo $form->field($model, 'is_invoice_req')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label(false) ?>
        </div>
        <div class="col-lg-3">
            <label for=""><?= $model->getAttributeLabel('status') ?></label>
            <?php echo $form->field($model, 'status')->widget(\toxor88\switchery\Switchery::className(), ['options' => ['label' => '', 'class' => 'form-control']])->label(false) ?>

        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-lg-12">
            <h3>
                รายการยืมถัง/กระสอบ</h3>
        </div>
        <table class="table table-bordered"
               id="table-list">
            <thead>
            <tr>
                <th style="text-align: center;width: 5%">
                    #
                </th>
                <th>
                    รหัสอุปกรณ์
                </th>
                <th>
                    ชื่ออุปกรณ์
                </th>
                <th>
                    จำนวน
                </th>
                <th>
                    วันที่เริ่ม
                </th>
                <th>
                    สถานะ
                </th>
                <th style="text-align: center;width: 5%">
                    -
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if ($model->isNewRecord): ?>
                <tr>
                    <td style="text-align: center">
                        #
                    </td>
                    <td>
                        <input type="hidden"
                               name="line_product_id[]"
                               class="line-product-id">
                        <input type="text"
                               class="form-control line-product-code"
                               name="line_product_code[]"
                               readonly>
                    </td>
                    <td>
                        <input type="text"
                               class="form-control line-prod-name"
                               name="line_product_name[]">
                    </td>
                    <td>
                        <input type="number" min="0"
                               class="form-control line-qty"
                               name="line_qty[]">
                    </td>
                    <td>
                        <input type="text"
                               class="form-control line-start-date"
                               name="line_start_date[]">
                    </td>
                    <td>
                        <div class="btn btn-info">
                            รายละเอียด
                        </div>
                    </td>
                    <td style="text-align: center">
                        <div class="btn btn-danger btn-sm"
                             onclick="removeline($(this))">
                            <i class="fa fa-trash"></i>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php if ($model_asset_list != null): ?>
                    <?php if (count($model_asset_list) > 0): ?>
                        <?php $i = 0; ?>
                        <?php foreach ($model_asset_list as $value): ?>
                            <?php $i += 1; ?>
                            <tr data-var="<?= $value->id ?>">
                                <td style="text-align: center">
                                    <?= $i ?>
                                </td>
                                <td>
                                    <input type="hidden"
                                           name="line_product_id[]"
                                           class="line-product-id" value="<?= $value->product_id ?>">
                                    <input type="text"
                                           class="form-control line-product-code"
                                           name="line_product_code[]"
                                           value="<?= \backend\models\Assetsitem::findCode($value->product_id) ?>"
                                           readonly>
                                </td>
                                <td>
                                    <input type="text"
                                           class="form-control line-prod-name"
                                           name="line_product_name[]"
                                           value="<?= \backend\models\Assetsitem::findName($value->product_id) ?>"
                                           readonly>
                                </td>
                                <td>
                                    <input type="number" min="0"
                                           class="form-control line-qty"
                                           name="line_qty[]" value="<?= $value->qty ?>">
                                </td>
                                <td>
                                    <input type="text"
                                           class="form-control line-start-date"
                                           name="line_start_date[]" value="<?= $value->start_date ?>">
                                </td>
                                <td>
                                    <div class="btn btn-info" data-var="<?=$value->product_id?>" onclick="showPhoto($(this))">
                                        รายละเอียด
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div class="btn btn-danger btn-sm"
                                         onclick="removeline($(this))">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td style="text-align: center">

                            </td>
                            <td>
                                <input type="hidden"
                                       name="line_product_id[]"
                                       class="line-product-id">
                                <input type="text"
                                       class="form-control line-product-code"
                                       name="line_product_code[]"
                                       readonly>
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control line-prod-name"
                                       name="line_product_name[]" readonly>
                            </td>
                            <td>
                                <input type="number" min="0"
                                       class="form-control line-qty"
                                       name="line_qty[]">
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control line-start-date"
                                       name="line_start_date[]">
                            </td>
                            <td>
                                <div class="btn btn-info">
                                    รายละเอียด
                                </div>
                            </td>
                            <td style="text-align: center">
                                <div class="btn btn-danger btn-sm"
                                     onclick="removeline($(this))">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                        <tr>
                            <td style="text-align: center">

                            </td>
                            <td>
                                <input type="hidden"
                                       name="line_product_id[]"
                                       class="line-product-id">
                                <input type="text"
                                       class="form-control line-product-code"
                                       name="line_product_code[]"
                                       readonly>
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control line-prod-name"
                                       name="line_product_name[]" readonly>
                            </td>
                            <td>
                                <input type="number" min="0"
                                       class="form-control line-qty"
                                       name="line_qty[]">
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control line-start-date"
                                       name="line_start_date[]">
                            </td>
                            <td>
                                <div class="btn btn-info">
                                    รายละเอียด
                                </div>
                            </td>
                            <td style="text-align: center">
                                <div class="btn btn-danger btn-sm"
                                     onclick="removeline($(this))">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </td>
                        </tr>

                <?php endif; ?>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td>
                    <div class="btn btn-primary" onclick="showfind($(this))">
                        <i class="fa fa-plus-circle"></i>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <hr/>
    <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

        </div>
        <div class="col-lg-4">

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<form id="form-delete-photo"
      action="<?= \yii\helpers\Url::to(['customer/deletephoto'], true) ?>"
      method="post">
    <input type="hidden"
           class="delete-photo-id"
           name="delete_id"
           value="">
</form>

<div id="findModal"
     class="modal fade"
     role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row"
                     style="width: 100%">
                    <div class="col-lg-11">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control search-item"
                                   placeholder="ค้นหาสินค้า">
                            <span class="input-group-addon">
                                        <button type="submit"
                                                class="btn btn-primary btn-search-submit">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <button type="button"
                                class="close"
                                data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">

                <input type="hidden"
                       name="line_qc_product"
                       class="line_qc_product"
                       value="">
                <table class="table table-bordered table-striped table-find-list"
                       width="100%">
                    <thead>
                    <tr>
                        <th style="text-align: center">
                            เลือก
                        </th>
                        <th>
                            รหัสสินค้า
                        </th>
                        <th>
                            รายละเอียด
                        </th>
                        <th>
                            ต้นทุน
                        </th>
                        <th>
                            ราคาขาย
                        </th>
                        <th>
                            จำนวนคงเหลือ
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-product-selected"
                        data-dismiss="modalx"
                        disabled>
                    <i
                            class="fa fa-check"></i>
                    ตกลง
                </button>
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i
                            class="fa fa-close text-danger"></i>
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>

<div id="photoModal"
     class="modal fade"
     role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4>รูปภาพ</h4>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
              <div class="show-asset-photo"></div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i
                            class="fa fa-close text-danger"></i>
                    ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>

<?php
$url_to_find_item = \yii\helpers\Url::to(['assetsitem/get-item'], true);
$url_to_get_price_group = \yii\helpers\Url::to(['journalissue/find-pricegroup'], true);
$url_to_get_standard_qty = \yii\helpers\Url::to(['journalissue/standardcal'], true);
$url_to_find_asset_photo = \yii\helpers\Url::to(['assetsitem/getassetphoto'], true);
$js = <<<JS
  var removelist = [];
  var selecteditem = [];
$(function (){
$(".line-start-date").datepicker({'format': 'dd-mm-yyyy'});
$(".btn-delete-photo").click(function (){
        var prodid = $(this).attr('data-var');
       //alert(prodid);
      swal({
                title: "ต้องการทำรายการนี้ใช่หรือไม่",
                text: "",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true,
                showLoaderOnConfirm: true
               }, function () {
                  $(".delete-photo-id").val(prodid);
                  $("#form-delete-photo").submit();
         });
     });
$(".btn-search-submit").click(function (){
    var txt = $(".search-item").val();
    showfindwithsearch(txt);
});
});
function showfind(e){
   
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {'txt':''},
              'success': function(data) {
                  // alert(data);
                   $(".table-find-list tbody").html(data);
                   $("#findModal").modal("show");
                 }
              });
      
  }
function showfindwithsearch(txt){
   
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {'txt': txt},
              'success': function(data) {
                  // alert(data);
                   $(".table-find-list tbody").html(data);
                   $("#findModal").modal("show");
                 }
              });
      
}  
  function addselecteditem(e) {
        var id = e.attr('data-var');
        var code = e.closest('tr').find('.line-find-code').val();
        var name = e.closest('tr').find('.line-find-name').val();
        var price = e.closest('tr').find('.line-find-price').val();
        if (id) {
           // alert(id);
            if (e.hasClass('btn-outline-success')) {
                var obj = {};
                obj['id'] = id;
                obj['code'] = code;
                obj['name'] = name;
                obj['price'] = price;
                selecteditem.push(obj);
                
                e.removeClass('btn-outline-success');
                e.addClass('btn-success');
                disableselectitem();
                console.log(selecteditem);
            } else {
                //selecteditem.pop(id);
                $.each(selecteditem, function (i, el) {
                    if (this.id == id) {
                        selecteditem.splice(i, 1);
                    }
                });
                e.removeClass('btn-success');
                e.addClass('btn-outline-success');
                disableselectitem();
                console.log(selecteditem);
            }
        }
    }
     function disableselectitem() {
        if (selecteditem.length > 0) {
            $(".btn-product-selected").prop("disabled", "");
            $(".btn-product-selected").removeClass('btn-outline-success');
            $(".btn-product-selected").addClass('btn-success');
        } else {
            $(".btn-product-selected").prop("disabled", "disabled");
            $(".btn-product-selected").removeClass('btn-success');
            $(".btn-product-selected").addClass('btn-outline-success');
        }
    }
    $(".btn-product-selected").click(function () {
        var linenum = 0;
        if (selecteditem.length > 0) {
            for (var i = 0; i <= selecteditem.length - 1; i++) {
                var line_prod_id = selecteditem[i]['id'];
                var line_prod_code = selecteditem[i]['code'];
                var line_prod_name = selecteditem[i]['name'];
                var line_prod_price = selecteditem[i]['price'];
                
                 // if(check_dup(line_prod_id) == 1){
                 //        alert("รายการสินค้า " +line_prod_code+ " มีในรายการแล้ว");
                 //        return false;
                 //    }
                
              //  alert(line_prod_id);
                var tr = $("#table-list tbody tr:last");
                
                if (tr.closest("tr").find(".line-product-code").val() == "") {
                    tr.closest("tr").find(".line-product-id").val(line_prod_id);
                    tr.closest("tr").find(".line-product-code").val(line_prod_code);
                    tr.closest("tr").find(".line-prod-name").val(line_prod_name);
                   

                    //cal_num();
                    console.log(line_prod_code);
                } else {
                   // alert("dd");
                    console.log(line_prod_code);
                    //tr.closest("tr").find(".line_code").css({'border-color': ''});

                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                    clone.find(".line-product-id").val(line_prod_id);
                    clone.find(".line-product-code").val(line_prod_code);
                    clone.find(".line-prod-name").val(line_prod_name);
                   
                    clone.attr("data-var", "");
                    clone.find('.rec-id').val("");
                    clone.find('.line-start-date').datepicker({'format': 'dd-mm-yyyy'});
//                    clone.find(".line-price").on("keypress", function (event) {
//                        $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
//                        if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
//                            event.preventDefault();
//                        }
//                    });

                    tr.after(clone);
                    //cal_num();
                }
            }
        
        }
        $("#table-list tbody tr").each(function () {
            linenum += 1;
            $(this).closest("tr").find("td:eq(0)").text(linenum);
            // $(this).closest("tr").find(".line-prod-code").val(line_prod_code);
        });
        selecteditem.length = 0;

        $("#table-find-list tbody tr").each(function () {
            $(this).closest("tr").find(".btn-line-select").removeClass('btn-success');
            $(this).closest("tr").find(".btn-line-select").addClass('btn-outline-success');
        });
        $(".btn-product-selected").removeClass('btn-success');
        $(".btn-product-selected").addClass('btn-outline-success');
        $("#findModal").modal('hide');
    });
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
            
        }
    }
  function check_dup(prod_id){
      var _has = 0;
      $("#table-list tbody tr").each(function(){
          var p_id = $(this).closest('tr').find('.line-product-id').val();
         // alert(p_id + " = " + prod_id);
          if(p_id == prod_id){
              _has = 1;
          }
      });
      return _has;
    }
    
    
    function showPhoto(e){
        var id = e.attr("data-var");
         $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_asset_photo",
              'data': {'id': id},
              'success': function(data) {
                  // alert(data);
                   $(".show-asset-photo").html(data);
                   $("#photoModal").modal("show");
                 }
              });
    }
JS;

$this->registerJs($js, static::POS_END);

?>
