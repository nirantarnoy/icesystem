<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$company_id = 1;
$branch_id = 1;
if (isset($_SESSION['user_company_id'])) {
    $company_id = $_SESSION['user_company_id'];
}
if (isset($_SESSION['user_branch_id'])) {
    $branch_id = $_SESSION['user_branch_id'];
}

$default_warehouse = 0;
if ($company_id != null && $branch_id != null) {
    $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
}

$prod_data = \backend\models\Product::find()->where(['status' => 1])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
function getStock($prod_id, $warehouse)
{
    $qty = 0;
    if ($prod_id != null) {
        $model = \backend\models\Stocksum::find()->where(['product_id' => $prod_id, 'warehouse_id' => $warehouse])->one();
        if ($model) {
            $qty = $model->qty;
        }
    }
    return $qty;
}


?>

<div class="journalissue-form">

    <?php $form = ActiveForm::begin(); ?>
    <input type="hidden" name="removelist" class="remove-list" value="">
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'journal_no')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <div class="col-lg-3">
            <?php $model->trans_date = $model->isNewRecord ? date('d/m/Y') : date('d/m/Y', strtotime($model->trans_date)); ?>
            <?= $form->field($model, 'trans_date')->widget(\kartik\date\DatePicker::className(),
                [
                    'pluginOptions' => [
                        'format' => 'dd/mm/yyyy',
                        'todayHighlight' => true
                    ],
                ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status')->textInput(['readonly' => 'readonly', 'value' => $model->isNewRecord ? 'Open' : \backend\helpers\IssueStatus::getTypeById($model->status)]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <select name="line_from_product" class="form-control line-from-product" id="" required>
                <?php foreach ($prod_data as $val): ?>
                    <option value="<?= $val->id ?>"><?= $val->code . ' ' . $val->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-3">
            <input type="number" class="form-control line-from-qty" name="line_from_qty" step=".01"
                   novalidate="novalidate" min="1" required>
        </div>
    </div>
    <br>
    <h5>รายละเอียด</h5>
    <!--    <hr>-->
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered" id="table-list">
                <thead>
                <tr>
                    <th style="text-align: center;width: 5%">#</th>
                    <th>เป็นสินค้า</th>
                    <th style="width: 15%;text-align: right">จำนวน</th>
                    <th style="width: 10%"></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($model_line != null): ?>
                    <?php $i = 0; ?>
                    <?php foreach ($model_line as $value): ?>
                        <?php $i += 1; ?>
                        <tr>
                            <td style="text-align: center;width: 5%"><?= $i ?></td>
                            <td>
                                <select name="line_to_product[]" class="form-control line-to-product" id="">
                                    <option value="-1">--เลือกสินค้า--</option>
                                    <?php foreach ($prod_data as $val): ?>
                                        <?php
                                        $selected = '';
                                        if ($val->id == $value->product_id) {
                                            $selected = "selected";
                                        }
                                        ?>
                                        <option value="<?= $val->id ?>" <?=$selected?>><?= $val->code . ' ' . $val->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="text-align: right">
                                <input type="number" class="form-control line-to-qty" name="line_to_qty[]" step=".01" min="1" value="<?=$value->qty?>" required>
                            </td>
                            <td>
                                <div class="btn btn-danger btn-sm" onclick="removeline($(this))"><i
                                            class="fa fa-trash"></i></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td style="text-align: center;width: 5%">#</td>
                        <td>
                            <select name="line_to_product[]" class="form-control line-to-product" id="">
                                <option value="-1">--เลือกสินค้า--</option>
                                <?php foreach ($prod_data as $val): ?>
                                    <option value="<?= $val->id ?>"><?= $val->code . ' ' . $val->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td style="text-align: right">
                            <input type="number" class="form-control line-to-qty" name="line_to_qty[]" step=".01" min="1" required>
                        </td>
                        <td>
                            <div class="btn btn-danger btn-sm" onclick="removeline($(this))"><i
                                        class="fa fa-trash"></i></div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <?php if($model->isNewRecord):?>
                <tr>
                    <td>
                        <div class="btn btn-primary" onclick="addline($(this))"><i
                                    class="fa fa-plus-circle"></i></div>
                    </td>
                </tr>
                <?php endif;?>
                </tfoot>
            </table>
        </div>
    </div>
    <br>
    <div class="form-group">
        <?php if($model->isNewRecord):?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php endif;?>
    </div>

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
                        <th>จำนวนคงเหลือ</th>
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
<?php
$url_to_find_item = \yii\helpers\Url::to(['pricegroup/productdata'], true);
$url_to_get_price_group = \yii\helpers\Url::to(['journalissue/find-pricegroup'], true);
$url_to_get_standard_qty = \yii\helpers\Url::to(['journalissue/standardcal'], true);
$js = <<<JS
  var removelist = [];
  var selecteditem = [];
$(function (){
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
});
function showfind(e){
    //alert();
      $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_item",
              'data': {},
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
    
    function addline(e){
      var tr = $("#table-list tbody tr:last");
       var clone = tr.clone();
       
                    // clone.find(".line-prod-id").val(line_prod_id);
                    // clone.find(".line-prod-code").val(line_prod_code);
                    // clone.find(".line-prod-name").val(line_prod_name);
                    // clone.find(".line-price").val(line_prod_price);
                    // clone.find(".line-avl-qty").val(line_onhand);

                    clone.attr("data-var", "");
                    clone.find('.rec-id').val("");

                    clone.find(".line-from-qty,.line-to-qty").on("keypress", function (event) {
                        $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
                        if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
                            event.preventDefault();
                        }
                    });

                    tr.after(clone);
                    cal_linenum();
    }
  

JS;
$this->registerJs($js, static::POS_END);
?>
