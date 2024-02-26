<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


$prod_status = \backend\helpers\ProductStatus::asArrayObject();

$harf_select = [['id'=>1,'name'=>'Yes'],['id'=>0,'name'=>'No']];
?>

<div class="pricegroup-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" name="removelist" class="remove-list" value="">
    <input type="hidden" name="removelist2" class="remove-list2" value="">
    <div class="row">
        <div class="col-lg-1">

        </div>
        <div class="col-lg-10">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

            <label for=""><?= $model->getAttributeLabel('status') ?></label>
            <select name="status" class="form-control status" id=""
                    onchange="">
                <?php for ($i = 0; $i <= count($prod_status) - 1; $i++): ?>
                    <?php
                    $selected = '';
                    if ($prod_status[$i]['id'] == $model->status)
                        $selected = 'selected';
                    ?>
                    <option value="<?= $prod_status[$i]['id'] ?>" <?= $selected ?>><?= $prod_status[$i]['name'] ?></option>
                <?php endfor; ?>
            </select>
            <br>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php if (!$model->isNewRecord): ?>
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                           href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                           aria-selected="true">ผูกราคากับสินค้า</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-customer-tab" data-toggle="pill"
                           href="#custom-tabs-one-customer" role="tab" aria-controls="custom-tabs-one-customer"
                           aria-selected="false">ผูกราคากับประเภทลูกค้า</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                         aria-labelledby="custom-tabs-one-home-tab">
                        <br/>
                        <!--                    <div class="row">-->
                        <!--                        <div class="col-lg-6">-->
                        <!--                            <input type="text" class="form-control product-search" value=""-->
                        <!--                                   placeholder="เลือกรหัสสินค้า">-->
                        <!--                        </div>-->
                        <!--                    </div>-->
                        <!--                    <br/>-->
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="table-list" class="table table-bordered table-striped table-list">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;text-align: center">#</th>
                                        <th style="width: 20%">รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th style="width: 10%">ราคาขาย</th>
                                        <th style="width: 10%">คิดครึ่งถัง</th>
                                        <th style="width: 10%">ราคาครึ่งถัง</th>
                                        <th style="text-align: center">ลบ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($model->isNewRecord): ?>
                                        <tr>
                                            <td style="text-align: center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td>
                                                <input type="hidden" class="line-prod-id" name="line_prod_id[]"
                                                       value="">
                                                <input type="text" class="form-control line-prod-code"
                                                       name="line_prod_code[]" value=""
                                                       ondblclick="showfind($(this))"></td>
                                            <td><input type="text" class="form-control line-prod-name"
                                                       name="line_prod_name[]" value="" disabled>
                                            </td>
                                            <td><input type="number" class="form-control line-price" name="line_price[]"
                                                       style="text-align: right" value="0" min="0" step="0.01"></td>
                                            <td>
                                                <select name="line_haft_cal[]" class="form-control line-haft-cal" id="">
                                                    <?php for($b=0;$b<=count($harf_select)-1;$b++):?>
                                                    <option value="<?=$harf_select[$b]['id']?>"><?=$harf_select[$b]['name']?></option>
                                                    <?php endfor;?>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control line-haft-price" name="line_haft_price[]"
                                                       style="text-align: right" value="0" min="0" step="0.01"></td>
                                            <td style="text-align: center">
                                                <div class="btn btn-danger btn-sm" onclick="removeline($(this))"><i
                                                            class="fa fa-trash"></i></div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php if ($model_detail != null): ?>
                                            <?php $nums = 0 ?>
                                            <?php foreach ($model_detail as $value): ?>
                                                <?php $nums += 1; ?>
                                                <tr data-var="<?= $value->id ?>">
                                                    <td style="text-align: center">
                                                        <?= $nums ?>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="line_rec_id[]"
                                                               value="<?= $value->id ?>">
                                                        <input type="hidden" class="line-prod-id" name="line_prod_id[]"
                                                               value="<?= $value->product_id ?>">
                                                        <input type="text" class="form-control line-prod-code"
                                                               name="line_prod_code[]"
                                                               value="<?= \backend\models\Product::findCode($value->product_id) ?>"
                                                               ondblclick="showfind($(this))"></td>
                                                    <td><input type="text" class="form-control line-prod-name"
                                                               name="line_prod_name[]"
                                                               value="<?= \backend\models\Product::findName($value->product_id) ?>"
                                                               disabled>
                                                    </td>
                                                    <td><input type="number" class="form-control line-price"
                                                               name="line_price[]"
                                                               style="text-align: right"
                                                               value="<?= $value->sale_price ?>"
                                                               min="0" step="0.01"></td>
                                                    <td>
                                                        <select name="line_haft_cal[]" class="form-control line-haft-cal" id="">
                                                            <?php for($b=0;$b<=count($harf_select)-1;$b++):?>
                                                            <?php
                                                            $selected = '';
                                                            if($harf_select[$b]['id'] == $value->haft_cal)$selected = 'selected';
                                                            ?>
                                                                <option value="<?=$harf_select[$b]['id']?>" <?=$selected?>><?=$harf_select[$b]['name']?></option>
                                                            <?php endfor;?>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" class="form-control line-haft-price" name="line_haft_price[]"
                                                               style="text-align: right" value="<?= $value->sale_haft_price ?>" min="0" step="0.01"></td>
                                                    <td style="text-align: center">
                                                        <div class="btn btn-danger btn-sm"
                                                             onclick="removeline($(this))"><i
                                                                    class="fa fa-trash"></i></div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td style="text-align: center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="line-prod-id" name="line_prod_id[]"
                                                           value="">
                                                    <input type="text" class="form-control line-prod-code"
                                                           name="line_prod_code[]" value=""
                                                           ondblclick="showfind($(this))"></td>
                                                <td><input type="text" class="form-control line-prod-name"
                                                           name="line_prod_name[]" value="" disabled>
                                                </td>
                                                <td><input type="number" class="form-control line-price"
                                                           name="line_price[]"
                                                           style="text-align: right" value="0" min="0" step="0.01"></td>
                                                <td>
                                                    <select name="line_haft_cal[]" class="form-control line-haft-cal" id="">
                                                        <?php for($b=0;$b<=count($harf_select)-1;$b++):?>
                                                            <option value="<?=$harf_select[$b]['id']?>"><?=$harf_select[$b]['name']?></option>
                                                        <?php endfor;?>
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control line-haft-price" name="line_haft_price[]"
                                                           style="text-align: right" value="0" min="0" step="0.01"></td>
                                                <td style="text-align: center">
                                                    <div class="btn btn-danger btn-sm" onclick="removeline($(this))"><i
                                                                class="fa fa-trash"></i></div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>
                                            <div class="btn btn-primary" onclick="showfind($(this))"><i
                                                        class="fa fa-plus-circle"></i></div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="custom-tabs-one-customer" role="tabpanel"
                         aria-labelledby="custom-tabs-one-customer-tab">
                        <br/>
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="table-list2" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;text-align: center">#</th>
                                        <th style="width: 15%">รหัสประเภทลูกค้า</th>
                                        <th>ชื่อประเภท</th>
                                        <th style="width: 10%;text-align: center">ลบ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($model->isNewRecord): ?>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input type="hidden" name="line_type_id[]" class="line-type-id"
                                                       value="">
                                                <input type="text" class="form-control line-type-code"
                                                       name="line_type_code[]"
                                                       value="">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control line-type-name" disabled
                                                       name="line_type_name[]"
                                                       value="">
                                            </td>
                                            <td style="text-align: center">
                                                <div class="btn btn-danger btn-sm" onclick="removeline2($(this))"><i
                                                            class="fa fa-trash"></i></div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php if ($model_customer_type != null): ?>
                                            <?php $x = 0; ?>
                                            <?php foreach ($model_customer_type as $value): ?>
                                                <?php $x += 1; ?>
                                                <tr data-var="<?= $value->id ?>">
                                                    <td><?= $x ?></td>
                                                    <td>
                                                        <input type="hidden" name="line_type_id[]" class="line-type-id"
                                                               value="<?= $value->customer_type_id ?>">
                                                        <input type="text" class="form-control line-type-code"
                                                               name="line_type_code[]"
                                                               value="<?= \backend\models\Customertype::findCode($value->customer_type_id) ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control line-type-name" disabled
                                                               name="line_type_name[]"
                                                               value="<?= \backend\models\Customertype::findName($value->customer_type_id) ?>">
                                                    </td>
                                                    <td>
                                                        <div class="btn btn-danger btn-sm"
                                                             onclick="removeline2($(this))"><i
                                                                    class="fa fa-trash"></i></div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <input type="hidden" name="line_type_id[]" class="line-type-id"
                                                           value="">
                                                    <input type="text" class="form-control line-type-code"
                                                           name="line_type_code[]"
                                                           value="">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control line-type-name" disabled
                                                           name="line_type_name[]"
                                                           value="">
                                                </td>
                                                <td>
                                                    <div class="btn btn-danger btn-sm" onclick="removeline2($(this))"><i
                                                                class="fa fa-trash"></i></div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>
                                            <div class="btn btn-primary" onclick="showfind2($(this))"><i
                                                        class="fa fa-plus-circle"></i></div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-1"></div>
        </div>
    <?php endif; ?>
    <br/>
    <?php ActiveForm::end(); ?>

</div>
<div id="findModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row" style="width: 100%">
                    <div class="col-lg-11">
                        <div class="input-group">
                            <input type="text" class="form-control search-item" placeholder="ค้นหาสินค้า">
                            <span class="input-group-addon">
                                        <button type="submit" class="btn btn-primary btn-search-submit">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">

                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list" width="100%">
                    <thead>
                    <tr>
                        <th style="text-align: center">เลือก</th>
                        <th>รหัสสินค้า</th>
                        <th>รายละเอียด</th>
                        <th>ต้นทุน</th>
                        <th>ราคาขาย</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-product-selected" data-dismiss="modalx" disabled><i
                            class="fa fa-check"></i> ตกลง
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>

<div id="findModal2" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row" style="width: 100%" >
                    <div class="col-lg-11">
                        <div class="input-group">
                            <input type="text" class="form-control search-item2" placeholder="ค้นหาประเภทลูกค้า">
                            <span class="input-group-addon">
                                        <button type="submit" class="btn btn-primary btn-search-submit2">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">

                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list2" width="100%">
                    <thead>
                    <tr>
                        <th style="text-align: center">เลือก</th>
                        <th>ประเภทลูกค้า</th>
                        <th>รายละเอียด</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-product-selected2" data-dismiss="modalx" disabled><i
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
$url_to_find_item = \yii\helpers\Url::to(['pricegroup/productdata'], true);
$url_to_find_customer_type = \yii\helpers\Url::to(['pricegroup/customertypedata'], true);
$js = <<<JS
  var removelist = [];
  var removelist2 = [];
  var selecteditem = [];
  var selecteditem2 = [];
  $(function(){
      $(".btn-search-submit").click(function (){
         var txt = $(".search-item").val();
         $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {txt_search: txt},
              'success': function(data) {
                  //  alert(data);
                   $(".table-find-list tbody").html(data);
                 //  $("#findModal").modal("show");
                 }
              });
      });
      
      $(".btn-search-submit2").click(function (){
         var txt = $(".search-item2").val();
         $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_customer_type",
              'data': {txt_search: txt},
              'success': function(data) {
                  //  alert(data);
                   $(".table-find-list2 tbody").html(data);
                 //  $("#findModal").modal("show");
                 }
              });
      });
  });
  
  function showfind(e){
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {},
              'success': function(data) {
                  //  alert(data);
                   $(".table-find-list tbody").html(data);
                   $("#findModal").modal("show");
                 }
              });
      
  }
  function showfind2(e){
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_customer_type",
              'data': {},
              'success': function(data) {
                  //  alert(data);
                   $(".table-find-list2 tbody").html(data);
                   $("#findModal2").modal("show");
                 }
              });
      
  }
  
  function addselecteditem(e) {
        var id = e.attr('data-var');
        var code = e.closest('tr').find('.line-find-code').val();
        var name = e.closest('tr').find('.line-find-name').val();
        var price = e.closest('tr').find('.line-find-price').val();
        if (id) {
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
    
    function check_dup(prod_id){
      var _has = 0;
      $("#table-list tbody tr").each(function(){
          var p_id = $(this).closest('tr').find('.line-prod-id').val();
         // alert(p_id + " = " + prod_id);
          if(p_id == prod_id){
              _has = 1;
          }
      });
      return _has;
    }
    function check_dup2(prod_id){
      var _has = 0;
      $("#table-list2 tbody tr").each(function(){
          var p_id = $(this).closest('tr').find('.line-type-id').val();
         // alert(p_id + " = " + prod_id);
          if(p_id == prod_id){
              _has = 1;
          }
      });
      return _has;
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
                
                 if(check_dup(line_prod_id) == 1){
                        alert("รายการสินค้า " +line_prod_code+ " มีในรายการแล้ว");
                        return false;
                    }
                
                var tr = $("#table-list tbody tr:last");
                
                if (tr.closest("tr").find(".line-prod-code").val() == "") {
                    tr.closest("tr").find(".line-prod-id").val(line_prod_id);
                    tr.closest("tr").find(".line-prod-code").val(line_prod_code);
                    tr.closest("tr").find(".line-prod-name").val(line_prod_name);
                    tr.closest("tr").find(".line-price").val(line_prod_price);

                    //cal_num();
                    console.log(line_prod_code);
                } else {
                   // alert("dd");
                    console.log(line_prod_code);
                    //tr.closest("tr").find(".line_code").css({'border-color': ''});

                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                    clone.find(".line-prod-id").val(line_prod_id);
                    clone.find(".line-prod-code").val(line_prod_code);
                    clone.find(".line-prod-name").val(line_prod_name);
                    clone.find(".line-price").val(line_prod_price);

                    clone.attr("data-var", "");
                    clone.find('.rec-id').val("");

                    clone.find(".line-price").on("keypress", function (event) {
                        $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
                        if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
                            event.preventDefault();
                        }
                    });

                    tr.after(clone);
                    //cal_num();
                }
            }
          cal_num();
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
   
  function cal_num(){
      $("#table-list tbody tr").each(function(){
          var x = $(this).closest('tr').find('.line-prod-id').val();
         // alert(x); 
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
            cal_all();
        }
    }

    function cal_linenum() {
        var xline = 0;
        $("#table-list tbody tr").each(function () {
            xline += 1;
            $(this).closest("tr").find("td:eq(0)").text(xline);
        });
    }
     function cal_linenum2() {
        var xline = 0;
        $("#table-list2 tbody tr").each(function () {
            xline += 1;
            $(this).closest("tr").find("td:eq(0)").text(xline);
        });
    }
    
    
    
    function addselecteditem2(e) {
        var id = e.attr('data-var');
        var code = e.closest('tr').find('.line-find-type-code').val();
        var name = e.closest('tr').find('.line-find-type-name').val();
        if (id) {
            if (e.hasClass('btn-outline-success')) {
                var obj = {};
                obj['id'] = id;
                obj['code'] = code;
                obj['name'] = name;
                selecteditem2.push(obj);
                
                e.removeClass('btn-outline-success');
                e.addClass('btn-success');
                disableselectitem2();
                console.log(selecteditem2);
            } else {
                //selecteditem.pop(id);
                $.each(selecteditem2, function (i, el) {
                    if (this.id == id) {
                        selecteditem2.splice(i, 1);
                    }
                });
                e.removeClass('btn-success');
                e.addClass('btn-outline-success');
                disableselectitem2();
                console.log(selecteditem2);
            }
        }
    }

    function disableselectitem2() {
        if (selecteditem2.length > 0) {
            $(".btn-product-selected2").prop("disabled", "");
            $(".btn-product-selected2").removeClass('btn-outline-success');
            $(".btn-product-selected2").addClass('btn-success');
        } else {
            $(".btn-product-selected2").prop("disabled", "disabled");
            $(".btn-product-selected2").removeClass('btn-success');
            $(".btn-product-selected2").addClass('btn-outline-success');
        }
    }
    $(".btn-product-selected2").click(function () {
        var linenum = 0;
        if (selecteditem2.length > 0) {
            for (var i = 0; i <= selecteditem2.length - 1; i++) {
                var line_prod_id = selecteditem2[i]['id'];
                var line_prod_code = selecteditem2[i]['code'];
                var line_prod_name = selecteditem2[i]['name'];
                
                if(check_dup2(line_prod_id) == 1){
                        alert("รายการสินค้า " +line_prod_code+ " มีในรายการแล้ว");
                        return false;
                    }
                
                var tr = $("#table-list2 tbody tr:last");
                
                if (tr.closest("tr").find(".line-type-code").val() == "") {
                    tr.closest("tr").find(".line-type-id").val(line_prod_id);
                    tr.closest("tr").find(".line-type-code").val(line_prod_code);
                    tr.closest("tr").find(".line-type-name").val(line_prod_name);
                    
                    //cal_num();
                    console.log(line_prod_code);
                } else {
                  //  alert("dd");
                    console.log(line_prod_code);
                    //tr.closest("tr").find(".line_code").css({'border-color': ''});

                    var clone = tr.clone();
                    //clone.find(":text").val("");
                    // clone.find("td:eq(1)").text("");
                    clone.find(".line-type-id").val(line_prod_id);
                    clone.find(".line-type-code").val(line_prod_code);
                    clone.find(".line-type-name").val(line_prod_name);

                    clone.attr("data-var", "");
                    clone.find('.rec-id').val("");

                    // clone.find(".line-price").on("keypress", function (event) {
                    //     $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
                    //     if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
                    //         event.preventDefault();
                    //     }
                    // });

                    tr.after(clone);
                    //cal_num();
                }
            }
            cal_num();
        }
        $("#table-list2 tbody tr").each(function () {
            linenum += 1;
            $(this).closest("tr").find("td:eq(0)").text(linenum);
            // $(this).closest("tr").find(".line-prod-code").val(line_prod_code);
        });
        selecteditem2.length = 0;

        $("#table-find-list2 tbody tr").each(function () {
            $(this).closest("tr").find(".btn-line-select").removeClass('btn-success');
            $(this).closest("tr").find(".btn-line-select").addClass('btn-outline-success');
        });
        $(".btn-product-selected2").removeClass('btn-success');
        $(".btn-product-selected2").addClass('btn-outline-success');
        $("#findModal2").modal('hide');
    });
  
  function removeline2(e) {
        if (confirm("ต้องการลบรายการนี้ใช่หรือไม่?")) {
            if (e.parent().parent().attr("data-var") != '') {
                removelist2.push(e.parent().parent().attr("data-var"));
                $(".remove-list2").val(removelist2);
            }
            // alert(removelist);

            if ($("#table-list2 tbody tr").length == 1) {
                $("#table-list2 tbody tr").each(function () {
                    $(this).find(":text").val("");
                   // $(this).find(".line-prod-photo").attr('src', '');
                
                    cal_num();
                });
            } else {
                e.parent().parent().remove();
            }
            cal_linenum2();
            cal_all();
        }
    }
 
JS;
$this->registerJs($js, static::POS_END);
?>
