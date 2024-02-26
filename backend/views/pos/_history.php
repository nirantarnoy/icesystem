<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

$this->title = 'ประวัติการขาย POS';

$company_id = 1;
$branch_id = 1;
$default_warehouse = 0; // 6
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
    $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
    $default_warehouse = $warehouse_primary;
}

$filename = "empty";

if (!empty(\Yii::$app->session->getFlash('msg-index')) && !empty(\Yii::$app->session->getFlash('after-print'))) {
    $f_name = \Yii::$app->session->getFlash('msg-index');
//    echo $f_name;
//    if (file_exists('../web/uploads/slip/' . $f_name)) {
//        $filename = "../web/uploads/slip/" . $f_name;
//    }
    if ($branch_id == 1) {
        if (file_exists('../web/uploads/company1/slip/' . $f_name)) {
            $filename = "../web/uploads/company1/slip/" . $f_name;
        }
    } else if ($branch_id == 2) {
        if (file_exists('../web/uploads/company2/slip/' . $f_name)) {
            $filename = "../web/uploads/company2/slip/" . $f_name;
        }
    }
}
//unlink('../web/uploads/slip/slip.pdf');

?>
<input type="hidden" class="slip-print" value="<?= $filename ?>">
<div class="row">
    <div class="col-lg-12">
        <a href="index.php?r=pos/indextest&id=0" class="btn btn-primary"><i class="fa fa-arrow-left"></i> กลับหน้า POS </a>
    </div>
</div>
<div style="height: 5px;"></div>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'emptyCell' => '-',
            'layout' => "{items}\n{summary}\n<div class='text-center'>{pager}</div>",
            'summary' => "แสดง {begin} - {end} ของทั้งหมด {totalCount} รายการ",
            'showOnEmpty' => false,
            //    'bordered' => true,
            //     'striped' => false,
            //    'hover' => true,
            'id' => 'product-grid',
            //'tableOptions' => ['class' => 'table table-hover'],
            'emptyText' => '<div style="color: red;text-align: center;"> <b>ไม่พบรายการไดๆ</b> <span> เพิ่มรายการโดยการคลิกที่ปุ่ม </span><span class="text-success">"สร้างใหม่"</span></div>',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                    'attribute' => 'order_no',
                    'format' => 'raw',
                    'value' => function ($data) {
                       return '<div onclick="showdetail('.$data->id.')">'.$data->order_no.'</div>';
                    }
                ],
                [
                    'attribute' => 'order_date',
                    'value' => function ($data) {
                        return date('d/m/Y H:i:s', strtotime($data->order_date));
                    }
                ],
                [
                    'attribute' => 'customer_id',
                    'value' => function ($data) {
                        $cus_name = '';
                        if ($data->customer_id != null) {
                            $cus_name = \backend\models\Customer::findName($data->customer_id);
                        } else {
                            $cus_name = \backend\models\Deliveryroute::findName($data->order_channel_id);
                        }
                        return $cus_name;
                    }
                ],
//            'customer_type',
//            'customer_name',
//                [
//                    'attribute' => 'order_channel_id',
//                    'value' => function ($data) {
//                        return \backend\models\Deliveryroute::findName($data->order_channel_id);
//                    }
//                ],
                [
                    'label' => 'เครดิต/เชื่อ',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'value' => function ($data) {
                        // return number_format(\backend\models\Orders::findordercredit($data->id));
                        if ($data->payment_method_id == 2) {
                            return number_format(\backend\models\Orders::getlinesum($data->id), 2);
                        } else {
                            return 0;
                        }
                    }
                ],
                [
                    'label' => 'สด',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'value' => function ($data) {
                        if ($data->payment_method_id == 1) {
                            return number_format(\backend\models\Orders::getlinesum($data->id), 2);
                        } else {
                            return 0;
                        }

                    }
                ],
                [
                    'attribute' => 'order_total_amt',
                    'headerOptions' => ['style' => 'text-align: right'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'value' => function ($data) {
                        return number_format($data->order_total_amt, 2);
                    }
                ],
                //'vat_per',
                //'order_total_amt',
                //'emp_sale_id',
                //'car_ref_id',
                //'order_channel_id',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'text-align: center'],
                    'contentOptions' => ['style' => 'text-align: center'],
                    'value' => function ($data) {
                        if ($data->status == 1) {
                            return '<div class="badge badge-success">Open</div>';
                        } else if ($data->status == 3) {
                            return '<div class="badge badge-warning">Cencel</div>';
                        } else {
                            return '<div class="badge badge-secondary">Closed</div>';
                        }
                    }
                ],
                //'company_id',
                //'branch_id',
                //'created_at',
                //'updated_at',
                [
                    'attribute' => 'created_by',
                    'label' => 'พนักงาน',
                    'value' => function ($data) {
                        return \backend\models\User::findName($data->created_by);
                    }
                ],
                //'updated_by',

                [

                    'header' => 'ตัวเลือก',
                    'headerOptions' => ['style' => 'text-align:center;', 'class' => 'activity-view-link',],
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'template' => '{print}{cancelorder}',
                    'buttons' => [
                        'print' => function ($url, $data, $index) {
                            $options = [
                                'title' => Yii::t('yii', 'Print'),
                                'aria-label' => Yii::t('yii', 'Print'),
                                'data-pjax' => '0',
                                'data-id' => $data->id,
//                                'target' => '_blank',
                            ];
                            return Html::a(
                                '<span class="fas fa-list-alt btn btn-xs btn-default"></span>', $url, $options);
                        },
//                        'update' => function ($url, $data, $index) {
//                            $options = array_merge([
//                                'title' => Yii::t('yii', 'Update'),
//                                'aria-label' => Yii::t('yii', 'Update'),
//                                'data-pjax' => '0',
//                                'id' => 'modaledit',
//                            ]);
//                            return Html::a(
//                                '<span class="fas fa-edit btn btn-xs btn-default"></span>', null, [
//                                'id' => 'activity-view-link',
//                                //'data-toggle' => 'modal',
//                                // 'data-target' => '#modal',
//                                'data-id' => $data->id,
//                                'data-pjax' => '0',
//                                'onclick' => 'showorderedit($(this))'
//                                // 'style'=>['float'=>'rigth'],
//                            ]);
//                        },
                        'cancelorder' => function ($url, $data, $index) {
                            $options = [
                                'title' => Yii::t('yii', 'Cancel'),
                                'aria-label' => Yii::t('yii', 'Cancel'),
                                'data-pjax' => '0',
                                'data-id' => $data->id,
//                                'target' => '_blank',
                            ];
                            return Html::a(
                                '<span class="fas fa-trash-alt btn btn-xs btn-default"></span>', $url, $options);
                        },
//                        'delete' => function ($url, $data, $index) {
//                            $options = array_merge([
//                                'title' => Yii::t('yii', 'Delete'),
//                                'aria-label' => Yii::t('yii', 'Delete'),
//                                //'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
//                                //'data-method' => 'post',
//                                //'data-pjax' => '0',
//                                'data-url' => $url,
//                                'data-var' => $data->id,
//                                'onclick' => 'recDelete($(this));'
//                            ]);
//                            return Html::a('<span class="fas fa-trash-alt btn btn-xs btn-default"></span>', 'javascript:void(0)', $options);
//                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>

<div id="poseditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="form-pos-edit" action="<?= \yii\helpers\Url::to(['pos/posupdate'], true); ?>" method="post">
                <div class="modal-header" style="background-color: #2b669a">
                    <div class="row" style="text-align: center;width: 100%;color: white">
                        <div class="col-lg-12">
                            <span><h3 class="popup-payment" style="color: white"><i class="fa fa-shopping-cart"></i> แก้ไขรายการขาย</h3></span>
                            <input type="hidden" class="order-id" name="order_id" value="">
                        </div>
                    </div>

                </div>
                <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
                <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>เลขที่ขาย <small>
                                    <div class="badge badge-warning txt-order-no"></div>
                                </small></h5>
                        </div>
                        <div class="col-lg-6" style="border-left: 1px dashed black">
                            <h5>ลูกค้า <small>
                                    <div class="badge badge-warning txt-customer-name">ป้าไพ</div>
                                </small></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>วันที่ <small>
                                    <div class="badge badge-warning txt-order-date">26/02/2021</div>
                                </small></h5>
                        </div>
                        <div class="col-lg-6" style="border-left: 1px dashed black">
                            <h5>วิธีชำระเงิน <small>
                                    <div class="badge badge-success txt-payment-method">เงินสด</div>
                                </small></h5>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered table-striped table-order-history">
                                <thead>
                                <tr>
                                    <th style="text-align: center;width: 10%">รหัสสินค้า</th>
                                    <th style="text-align: center;">ชื่อสินค้า</th>
                                    <th style="text-align: right;width: 15%">จำนวน</th>
                                    <th style="text-align: right;width: 15%">ราคา</th>
                                    <th style="text-align: right;width: 15%">ยอดรวม</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right"><b>ยอดรวม</b></td>
                                    <td style="text-align: right"><b><span class="total-all"></span></b></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-success btn-pos-edit-submit" data-dismiss="modalx"><i
                                class="fa fa-check"></i> ตกลง
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i
                                class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="findModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4>รายละเอียดคำสั่งซื้อ</h4>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <input type="hidden" name="line_qc_product" class="line_qc_product" value="">
                <table class="table table-bordered table-striped table-find-list" width="100%">
                    <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th style="text-align: right">จำนวน</th>
                        <th style="text-align: right">ราคา</th>
                        <th style="text-align: right">รวม</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ปิดหน้าต่าง
                </button>
            </div>
        </div>

    </div>
</div>
<iframe id="iFramePdf" src="<?= $filename ?>" style="display:none;"></iframe>
<?php
$url_to_find_item = \yii\helpers\Url::to(['pos/orderedit'], true);
$url_to_find_order_detail = Url::to(['orders/getorderdetail'], true);
$js = <<<JS
 $(function(){
      //$(".btn-find-data").trigger('click');
      var xx = $(".slip-print").val();
        //alert(xx);
        if(xx !="empty"){
           myPrint();
         }
 });
function myPrint(){
        var getMyFrame = document.getElementById('iFramePdf');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
}
function showorderedit(e){
    var ids = e.attr("data-id");
    if(ids){
        $.ajax({
              'type':'post',
              'dataType': 'json',
              'url': "$url_to_find_item",
              'data': {'order_id': ids},
              'success': function(data) {
                  //  alert(data);
                   if(data.length > 0){
                        //alert();
                        $(".order-id").val(ids);
                        $(".txt-order-no").html(data[0]['order_no']);
                        $(".txt-order-date").html(data[0]['order_date']);
                        $(".txt-customer-name").html(data[0]['customer_name']);
                        $(".txt-payment-method").html(data[0]['payment_data']);
                        $(".table-order-history tbody").html(data[0]['html']);
                        
                        var total_all = 0;
                        $(".table-order-history tbody tr").each(function(){
                            var line_total = $(this).closest('tr').find('.line-total').val();
                            total_all = total_all + parseFloat(line_total);
                        });
                        $(".total-all").html(parseFloat(total_all));
                        $("#poseditModal").modal("show");
                   }
                 }
              });
        
    }
    
    $(".btn-pos-edit-submit").click(function(){
       if(confirm('คุณมั่นใจที่จะทำรายการนี้ใช่หรือใม่')){
           $("form#form-pos-edit").submit();
       } 
    });
}
function calline(e){
    var qty = e.closest('tr').find('.line-qty').val();
    var price = e.closest('tr').find('.line-price').val();
    var line_total = parseFloat(qty) * parseFloat(price);
    
    e.closest('tr').find('.line-total').val(parseFloat(line_total));
    e.closest('tr').find('td:eq(4)').html(addCommas(parseFloat(line_total)));
  calall();
}
function calall(){
   var total_all = 0;
    $(".table-order-history tbody tr").each(function(){
          var qty = $(this).closest('tr').find('.line-qty').val();
          var price = $(this).closest('tr').find('.line-price').val();
         total_all = total_all + (parseFloat(qty) * parseFloat(price));
    });
     $(".total-all").html(addCommas(parseFloat(total_all)));
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
 function showdetail(e){
    var order_id = e
    //alert(order_id);
    if(order_id!=null){
        $.ajax({
              'type':'post',
              'dataType': 'html',
              'async': false,
              'url': "$url_to_find_order_detail",
              'data': {'id': order_id},
              'success': function(data) {
                    //alert(data);
                   $(".table-find-list tbody").html(data);
                   $("#findModal").modal("show");
                 }
              });   
    }
    
}     
JS;
$this->registerJs($js, static::POS_END);
?>
