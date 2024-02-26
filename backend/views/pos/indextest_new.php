<?php

use kartik\select2\Select2;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Session;

use chillerlan\QRCode\QRCode;
use yii\web\Response;
use yii2assets\printthis\PrintThis;

$filename = "empty";
$is_print_do = "";
$filename_do = "empty";
$filename_car_pos = "empty";
$order_do = "empty";


$company_id = 0;
$branch_id = 0;
$default_warehouse = 0; // 6
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
    $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
    $default_warehouse = $warehouse_primary;
}

$model = null;
$model_line = null;
//$order_id = 205665;
if ($order_id > 0 || $order_id != '') {
    $model = \backend\models\Orders::find()->select(['id', 'order_no', 'order_date', 'customer_id'])->where(['id' => $order_id])->one();
    $model_line = \backend\models\Orderline::find()->select(['id', 'product_id', 'qty', 'price', 'line_total'])->where(['order_id' => $order_id])->all();
   // echo "order id is ".$order_id;
}


$this->registerCss('
//@media print {
//    .noprint {display:none !important;}
//    a:link:after, a:visited:after {
//      display: none;
//      content: "";
//    }
//}
#print-slip{
   // page-break-after: avoid;
    page-break-before: avoid;
    margin-top: 0;  
    margin-bottom: 0;  
}
');




?>
<!--<div id="PrintThis">-->
<!--    Your Html code here-->
<!--</div>-->
<div class="row">
    <div class="col-lg-6" style="border-right: 1px dashed gray ">
        <div class="row">
            <div class="col-lg-12">
                <h5><i class="fa fa-cubes"></i> รายการสินค้า TEST</h5>
            </div>
        </div>
        <hr style="border-top: 1px dashed gray">
        <div class="row">
            <div class="col-lg-8">
                <div class="btn btn-group group-customer-type">
                    <button class="btn btn-outline-secondary btn-sm" disabled>ประเภทลูกค้า</button>
                    <button id="btn-general-customer" class="btn btn-success btn-sm active">ขายสด</button>
                    <button id="btn-fix-customer" class="btn btn-outline-secondary btn-sm">ระบุลูกค้า</button>
                </div>
            </div>
            <div class="col-lg-4" style="text-align: right;">
                <span style="font-size: 20px;display: none;" class="text-price-type"><div
                            class="badge badge-primary badge-text-price-type"
                            style="vertical-align: middle"></div></span>
            </div>
        </div>
        <div class="row div-customer-search" style="display: none;">
            <div class="col-lg-8">
                <div class="input-group" style="margin-left: 10px;">
                    <!--                    <input type="text" class="form-control find-customer" value="">-->
                    <!--                    ->where(['sort_name' => null])->orFilterWhere(['sort_name'=>''])-->
                    <?php
                    $s_name = '';
                    echo Select2::widget([
                        'name' => 'customer_id',
                        // 'value' => 1,
                        'data' => ArrayHelper::map(\backend\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['is_show_pos' => 1])->andFilterWhere(['status' => 1])->all(), 'id', function ($data) {
                            return $data->code . ' ' . $data->name;
                        }),
                        'options' => [
                            'placeholder' => '--เลือกลูกค้า--',
                            'onchange' => 'getproduct_price($(this))'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]);
                    ?>
                    <!--                    <button class="btn btn-primary"><i class="fa fa-search"></i></button>-->
                </div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div id="sale-by-customer" style="display: none">
                <div class="col-lg-12" style="height: 800px;overflow-x: hidden">
                    <div class="row">
                        <?php $i = 0; ?>
                        <?php //$product_data = \backend\models\Product::find()->where(['IN','code',$list])->all(); ?>
                        <?php $product_data = \backend\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->orderBy(['item_pos_seq' => SORT_ASC])->all(); ?>
                        <?php foreach ($product_data as $value): ?>
                            <?php
                            $i += 1;
                            $product_onhand = \backend\models\Stocksum::findStock($value->id, $default_warehouse);
                            ?>
                            <!--                            <div class="col-lg-3 product-items">-->
                            <div class="product-items" style="margin: 5px;">
                                <!--                            <div class="card" style="heightc: 200px;" onclick="showadditemx($(this))">-->
                                <div class="card" style="height: 180px;">
                                    <!--                                <img class="card-img-top" src="../web/uploads/images/products/nologo.png" alt="">-->
                                    <!--                                <img class="card-img-top" src="../web/uploads/logo/Logo_head.jpg" alt="">-->
                                    <div class="card-body">
                                        <p class="card-text"
                                           style="font-size: 20px;text-align: center;font-weight: bold"><?= $value->code ?></p>
                                    </div>
                                    <div class="card-footer" style="width: 100%">
                                        <div class="row" style="width: 120%;text-align: center">
                                            <div class="col-lg-12">
                                                <div class="item-price"
                                                     style="color: red;font-weight: bold;"><?= $value->sale_price ?></div>
                                            </div>
                                        </div>
                                        <div style="height: 10px;"></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input type="hidden" class="list-item-product-id list-item-id-<?= $i ?>"
                                                       value="<?= $value->id ?>">
                                                <input type="hidden" class="list-item-code-<?= $i ?>"
                                                       value="<?= $value->code ?>">
                                                <input type="hidden" class="list-item-name-<?= $i ?>"
                                                       value="<?= $value->name ?>">
                                                <input type="hidden" class="list-item-price list-item-price-<?= $i ?>"
                                                       value="<?= $value->sale_price ?>">
                                                <input type="hidden"
                                                       class="list-item-onhand fix-list-item-onhand-<?= $i ?>"
                                                       value="<?= $product_onhand ?>">
                                                <div class="btn-group" style="width: 100%">
                                                    <div class="btn btn-outline-secondary btn-sm" data-var="<?= $i ?>"
                                                         onclick="reducecartdivcustomer($(this))"><i
                                                                class="fa fa-minus"></i>
                                                    </div>
                                                    <div class="btn btn-outline-primary btn-sm" data-var="<?= $i ?>"
                                                         onclick="addcartdivcustomer($(this))">
                                                        <i class="fa fa-plus"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div id="sale-by-original">
                <div class="col-lg-12" style="height: 800px;overflow-x: hidden">
                    <div class="row">
                        <?php $i = 0; ?>
                        <?php //$product_data = \backend\models\Product::find()->where(['IN','code',$list])->all(); ?>
                        <?php $product_data = \backend\models\Product::find()->where(['is_pos_item' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->orderBy(['item_pos_seq' => SORT_ASC])->all(); ?>
                        <?php foreach ($product_data as $value): ?>
                            <?php
                            $i += 1;
                            $product_onhand = \backend\models\Stocksum::findStock($value->id, $default_warehouse);
                            ?>
                            <div class="product-itemsx" style="margin: 5px;">
                                <!--                            <div class="card" style="heightc: 200px;" onclick="showadditemx($(this))">-->
                                <div class="card" style="height: 180px;">
                                    <!--                                <img class="card-img-top" src="../web/uploads/images/products/nologo.png" alt="">-->
                                    <!--                                <img class="card-img-top" src="../web/uploads/logo/Logo_head.jpg" alt="">-->
                                    <div class="card-body">
                                        <p class="card-text"
                                           style="font-size: 20px;text-align: center;font-weight: bold"><?= $value->code ?></p>
                                    </div>
                                    <div class="card-footer" style="width: 100%">
                                        <div class="row" style="width: 120%;text-align: center">
                                            <div class="col-lg-12">
                                                <div class="item-price"
                                                     style="color: red;font-weight: bold;"><?= $value->sale_price ?></div>
                                            </div>
                                        </div>
                                        <div style="height: 10px;"></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input type="hidden"
                                                       class="list-item-product-id fix-list-item-id-<?= $i ?>"
                                                       value="<?= $value->id ?>">
                                                <input type="hidden" class="fix-list-item-code-<?= $i ?>"
                                                       value="<?= $value->code ?>">
                                                <input type="hidden" class="fix-list-item-name-<?= $i ?>"
                                                       value="<?= $value->name ?>">
                                                <input type="hidden"
                                                       class="list-item-price fix-list-item-price-<?= $i ?>"
                                                       value="<?= $value->sale_price ?>">
                                                <input type="hidden"
                                                       class="list-item-onhand fix-list-item-onhand-<?= $i ?>"
                                                       value="<?= $product_onhand ?>">
                                                <div class="btn-group" style="width: 100%">
                                                    <div class="btn btn-outline-secondary btn-sm" data-var="<?= $i ?>"
                                                         data-val="<?= $value->id ?>"
                                                         onclick="reducecart2($(this))"><i class="fa fa-minus"></i>
                                                    </div>
                                                    <div class="btn btn-outline-primary btn-sm" data-var="<?= $i ?>"
                                                         data-val="<?= $product_onhand ?>" data-vax="<?= $value->id ?>"
                                                         onclick="addcart2($(this))">
                                                        <i class="fa fa-plus"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-lg-6">
        <form action="<?= \yii\helpers\Url::to(['pos/closesaletest'], true) ?>" id="form-close-sale" method="post">
            <input type="hidden" class="sale-customer-id" name="customer_id" value="">
            <input type="hidden" class="sale-total-amount" name="sale_total_amount" value="">
            <input type="hidden" class="sale-pay-amount" name="sale_pay_amount" value="">
            <input type="hidden" class="sale-pay-date" name="sale_pay_date" value="">
            <input type="hidden" class="sale-pay-change" name="sale_pay_change" value="">
            <input type="hidden" class="sale-pay-type" name="sale_pay_type" value="">
            <input type="hidden" class="print-type-doc" name="print_type_doc" value="">
            <input type="hidden" class="default-warehouse-id" name="default_warehouse_id"
                   value="<?= $default_warehouse ?>">

            <div class="row">
                <div class="col-lg-8" style="text-align: left">
                    <div class="btn-group">
                        <a id="modalButton" class="btn btn-primary"
                           href="<?= Url::to(['pos/createissue', 'id' => 'xx']); ?>">รับคำสั่งซื้อ(รถ)</a>
                        <a id="modalListissue" class="btn btn-success"
                           href="<?= Url::to(['pos/listissue']); ?>">ยืนยันคำสั่งซื้อ(รถ)</a>
                        <a href="index.php?r=pos/salehistory" class="btn btn-outline-info btn-history-cart"
                           style="display: nonex">
                            ประวัติการขาย
                        </a>
                        <a href="index.php?r=pos/dailysum" class="btn btn-outline-info btn-history-cart"
                           style="display: nonex">
                            สรุปยอดขายประจำวัน
                        </a>
                    </div>

                </div>
                <div class="col-lg-4" style="text-align: right">
                    <div class="btn btn-outline-secondary btn-cancel-cart" style="display: none">
                        ยกเลิกการขาย
                    </div>
                </div>
            </div>
            <hr>
            <div class="row div-payment" style="display: none">
                <div class="col-lg-12" style="text-align: center">
                    <div class="btn btn-group">
                        <div class="btn btn-success btn-lg btn-pay-cash">ชำระเงินสด</div>
                        <div class="btn btn-primary btn-lg btn-pay-credit">ชำระเงินเชื่อ</div>
                        <!--                        <div class="btn btn-outline-warning btn-lg btn-pay-credit-card">ชำระบัตรเครดิต</div>-->
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-4">
                    <h6 style="color: #258faf"><i class="fa fa-calendar"></i> <?= date('d/m/Y') ?> <span
                                class="c-time"><?= date('H:i') ?></span>
                    </h6>
                </div>
                <div class="col-lg-3">

                </div>
                <div class="col-lg-5" style="text-align: right">
                    <input type="hidden" class="total-value-top" value="0">
                    <h5> ยอดขาย <span style="color: red" class="total-text-top">0.00</span> <span> บาท</span></h5>
                </div>
            </div>
            <hr style="border-top: 1px dashed gray">
            <div class="row" style="height: 600px;overflow-x: hidden">
                <div class="col-lg-12">
                    <table class="table table-striped table-bordered table-cart">
                        <thead>
                        <tr style="background-color: #1aa67d;color: #e3e3e3">
                            <th style="text-align: center;width: 5%">#</th>
                            <th style="width: 15%;text-align: center">รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th style="text-align: right;width: 10%">จำนวน</th>
                            <th style="text-align: right;width: 10%">ราคา</th>
                            <th style="text-align: right;width: 15%">ราคารวม</th>
                            <th style="text-align: center">ลบ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="text-align: center;vertical-align: middle;width: 5%"></td>
                            <td style="vertical-align: middle;text-align: center"></td>
                            <td style="vertical-align: middle"></td>
                            <td style="text-align: right">
                                <input type="number" style="vertical-align: middle;text-align: right"
                                       class="form-control cart-qty" name="cart_qty[]" onchange="line_cal($(this))"
                                       value="" min="0" onclick="edit_qty($(this))">
                            </td>
                            <td style="text-align: right;vertical-align: middle"></td>
                            <td style="text-align: right;vertical-align: middle"></td>
                            <td style="text-align: center">
                                <input type="hidden" class="cart-product-id" name="cart_product_id[]" value="">
                                <input type="hidden" class="cart-price" name="cart_price[]" value="">
                                <input type="hidden" class="cart-total-price" name="cart_total_price[]" value="">
                                <input type="hidden" class="cart-product-onhand" name="cart_product_onhand[]" value="">
                                <div class="btn btn-danger btn-sm removecart-item" onclick="removecartitem($(this))"><i
                                            class="fa fa-minus"></i></div>
                            </td>
                        </tr>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;font-weight: bold">รวมทั้งหมด</td>
                            <td style="font-weight: bold;text-align: right"></td>
                            <td></td>
                            <td style="font-weight: bold;text-align: right"></td>
                            <td></td>
                        </tr>

                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
        <!--            <div class="footer-cart" style="height: 250px;position: fixed;bottom: 0px;">-->

        <!--            </div>-->

    </div>
</div>

<div id="posModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2b669a">
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span><h3 class="popup-product" style="color: white"></h3></span>
                        <input type="hidden" class="popup-product-id" value="">
                        <input type="hidden" class="popup-product-code" value="">
                    </div>
                </div>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4" style="text-align: center">
                        <h4>จำนวน</h4>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <input type="number" class="form-control popup-qty" value="" min="0"
                               style="font-size: 100px;height: 100px;text-align: center">
                    </div>
                    <div class="col-lg-3"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4" style="text-align: center">
                        <h4>ราคาขาย</h4>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <input type="number" class="form-control popup-price" min="0"
                               style="font-size: 100px;height: 100px;text-align: center">
                    </div>
                    <div class="col-lg-3"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-add-cart" data-dismiss="modalx" onclick="addcart($(this))"><i
                            class="fa fa-check"></i> บันทึกรายการ
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ยกเลิก
                </button>
            </div>
        </div>

    </div>
</div>

<div id="payModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-close-sale" style="background-color: #2b669a">
                <!--                <div class="modal-header modal-header-close-sale" style="background-color: white">-->
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span class="span-before-save"><h3 class="popup-payment" style="color: white"><i
                                        class="fa fa-shopping-cart"></i> บันทึกรับเงิน</h3></span>
                        <span class="span-saving" style="display:none;"><img
                                    src="<?= \Yii::$app->getUrlManager()->baseUrl . '/loading_gif.gif' ?>"
                                    style="width:10%" alt=""> <h3 class="popup-payment" style="color: #2b669a">กำลังบันทึกรายการ</h3></span>
                        <input type="hidden" class="popup-product-id" value="">
                        <input type="hidden" class="popup-product-code" value="">
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3" style="text-align: right">
                        <h4>วันที่ขาย</h4>
                    </div>
                    <div class="col-lg-8">
                        <?php
                        // $order_date = date('d/m/Y',strtotime($model->order_date));
                        echo \kartik\date\DatePicker::widget([
                            'name' => 'pos_date',
                            'value' => date('d/m/Y'),
                            'options' => [
                                'class' => 'pos-date',
                                'onchange' => '$(".sale-pay-date").val($(this).val());'
                                // 'readonly' => true,
                            ],
                            'pluginOptions' => [
                                'format' => 'dd/mm/yyyy',
                                'todayHighlight' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <hr>
                <!--                <hr style="border-top: 1px dashed gray">-->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4" style="text-align: center">
                                <h4>ยอดขาย</h4>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                        <div style="height: 10px;"></div>
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <input type="number" class="form-control pay-total-amount" value="" min="0"
                                       style="font-size: 50px;height: 60px;text-align: center" disabled>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4" style="text-align: center">
                                <h4>ชำระเงิน</h4>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                        <div style="height: 10px;"></div>
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <input type="number" class="form-control pay-amount" min="0"
                                       style="font-size: 50px;height: 60px;text-align: center" value="0">
                                <br>
                                <div class="alert alert-danger pay-alert" style="height: 50px;display: none;">
                                    <p>จำนวนเงินไม่พอสำหรับการซื้อนี้</p>
                                </div>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4" style="text-align: center">
                                <h4>เงินทอน</h4>
                            </div>
                            <div class="col-lg-4">

                            </div>
                        </div>
                        <div style="height: 10px;"></div>
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control pay-change" value="" readonly
                                       style="font-size: 50px;height: 60px;text-align: center">
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: center">
                                <h4>เหรียญหรือธนบัตร</h4>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="1"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">1
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="5"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">5
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="10"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">10
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="20"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">20
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="50"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">50
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="100"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">100
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="500"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">500
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn btn-outline-primary" data-var="1000"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">1000
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="btn btn-outline-danger" data-var="0"
                                     style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                                     onclick="calpayprice($(this))">Clear
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-pay-submit" data-dismiss="modalx">
                    <i class="fa fa-check"></i> จบการขาย
                </button>
                <button class="btn btn-outline-info btn-pay-submit-with-do" data-dismiss="modalx">
                    <i class="fa fa-check"></i> จบการขาย(พิมพ์ใบส่งของ)
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-ban text-danger"></i> ยกเลิก
                </button>
            </div>
        </div>

    </div>
</div>


<div id="paycreditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f9a123">
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span><h3 class="popup-payment" style="color: white"><i class="fa fa-shopping-cart"></i> บันทึกขายเงินเชื่อ</h3></span>
                        <input type="hidden" class="popup-product-id" value="">
                        <input type="hidden" class="popup-product-code" value="">
                    </div>
                </div>

            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3" style="text-align: right">
                        <h4>วันที่ขาย</h4>
                    </div>
                    <div class="col-lg-8">
                        <?php
                        // $order_date = date('d/m/Y',strtotime($model->order_date));
                        echo \kartik\date\DatePicker::widget([
                            'name' => 'pos_date',
                            'value' => date('d/m/Y'),
                            'options' => [
                                'class' => 'pos-date',
                                'onchange' => '$(".sale-pay-date").val($(this).val());'
                                // 'readonly' => true,
                            ],
                            'pluginOptions' => [
                                'format' => 'dd/mm/yyyy',
                                'todayHighlight' => true
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4" style="text-align: center">
                                <h4>ยอดขาย</h4>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                        <div style="height: 10px;"></div>
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <input type="number" class="form-control pay-total-amount" value="" min="0"
                                       style="font-size: 50px;height: 60px;text-align: center" disabled>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                        <input type="hidden" class="form-control pay-amount"
                               style="font-size: 50px;height: 60px;text-align: center" value="0">

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-success btn-pay-credit-submit" data-dismiss="modalx">
                    <i class="fa fa-check"></i> จบการขาย
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-ban text-danger"></i> ยกเลิก
                </button>
            </div>
        </div>

    </div>
</div>

<div id="historyModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2b669a">
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span><h3 class="popup-product" style="color: white"></h3></span>
                        <input type="hidden" class="popup-product-id" value="">
                        <input type="hidden" class="popup-product-code" value="">
                    </div>
                </div>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">

            </div>

            <!--            <div class="modal-footer">-->
            <!--                <button class="btn btn-outline-success btn-add-cart" data-dismiss="modalx" onclick="addcart($(this))"><i-->
            <!--                            class="fa fa-check"></i> บันทึกรายการ-->
            <!--                </button>-->
            <!--                <button type="button" class="btn btn-default" data-dismiss="modal"><i-->
            <!--                            class="fa fa-close text-danger"></i> ยกเลิก-->
            <!--                </button>-->
            <!--            </div>-->
        </div>

    </div>
</div>

<div id="editQtyModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2b669a">
                <div class="row" style="text-align: center;width: 100%;color: white">
                    <div class="col-lg-12">
                        <span><h3 class="popup-product" style="color: white"></h3></span>
                        <input type="hidden" class="line-edit-amount" value="">
                        <input type="hidden" class="line-edit-onhand" value="">
                    </div>
                </div>
            </div>
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto">-->
            <!--            <div class="modal-body" style="white-space:nowrap;overflow-y: auto;scrollbar-x-position: top">-->

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <input type="text" class="form-control edit-amount"
                               style="font-size: 50px;height: 60px;text-align: center" value="0"
                               onchange="checkonhand($(this))">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="1"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">1
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="2"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">2
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="3"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">3
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="4"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">4
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="5"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">5
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="6"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">6
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="7"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">7
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="8"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">8
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="9"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">9
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="0"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">0
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-primary" data-var="100"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))">.
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="btn btn-outline-danger" data-var="-1"
                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"
                             onclick="calpayprice2($(this))"> Clear
                        </div>
                    </div>
                    <!--                    <div class="col-lg-3">-->
                    <!--                        <div class="btn btn-outline-primary" data-var="8"-->
                    <!--                             style="width: 100%;height: 60px;font-weight: bold;font-size: 30px;"-->
                    <!--                             onclick="calpayprice($(this))">8-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                </div>
            </div>

            <div class="modal-footer">
                <div class="btn btn-outline-success btn-edit-cart-qty" data-dismiss="modalx"
                     onclick="sumitchangeqty($(this))">
                    <i class="fa fa-check"></i> ตกลง
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i
                            class="fa fa-close text-danger"></i> ยกเลิก
                </button>
            </div>
        </div>

    </div>
</div>


<form id="form-print-do" action="<?= \yii\helpers\Url::to(['pos/printdo'], true); ?>" method="post">
    <input type="hidden" class="order-do" name="order_id" value="<?= $order_do ?>">
</form>




    <div id="print-slip" style="display: ; height: 280px;">
        <input type="hidden" class="has-slip" value="<?= $model != null ? 1 : 0 ?>">
        <?php if ($model != null): ?>
            <table style="font-family: 'THSarabunNew';width: 250px;">
                <tr>
                    <td style="width: 40%">
                        <div style="height: 80px;width: 80px;">
                            <?php
                            $order_no = "ICE";
                            if ($model->order_no != '') {
                                $order_no = $model->order_no;
                            }
                            \Yii::$app->response->format = Response::FORMAT_HTML;
                            $data = $order_no;
                            $qr = new QRCode();
                            echo '<img style="width: 100%" src="' . $qr->render($data) . '" />';
                            ?>
                        </div>
                    </td>
                    <td style="width: 60%">
                        <table class="table-title">
                            <tr>
                                <td style="font-size: 20px;text-align: center;vertical-align: bottom">
                                    <h2>น้ำแข็ง</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 20px;text-align: center;vertical-align: top">
                                    <h2>ใบสั่งจ่าย</h2>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table-title" style="font-family: 'THSarabunNew';width: 250px;">
                <tr>
                    <td style="font-size: 18px;text-align: left;">เลขที่ <span><?php echo $model->order_no; ?></span>
                    </td>
                    <td style="font-size: 18px;text-align: left">
                        วันที่ <span><?= date('d/m/Y', strtotime($model->order_date)); ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 18px;text-align: left">
                        ลูกค้า <span><?= \backend\models\Customer::findName($model->customer_id); ?></span>
                    </td>
                    <td style="font-size: 18px;text-align: left">
                        เวลา <span><?= date('H:i:s', strtotime($model->order_date)); ?></span>
                    </td>
                </tr>
            </table>
            <table class="table-title" style="font-family: 'THSarabunNew';width: 250px;">
                <tr>
                    <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>รายการ</b></td>
                    <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>จำนวน</b>
                    </td>
                    <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>ราคา</b>
                    </td>
                    <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รวม</b></td>
                </tr>
                <?php
                $total_qty = 0;
                $total_amt = 0;
                $discount = 0;
                $change = 0;
                $items_cnt = 0;
                ?>
                <?php if ($model_line != null): ?>
                    <?php foreach ($model_line as $valuex): ?>
                        <?php
                        $total_qty = $total_qty + $valuex->qty;
                        $total_amt = $total_amt + ($valuex->qty * $valuex->price);
                        $change = 0;// $change_amount == null ? 0 : $change_amount;//$value->change_amount;
                        $items_cnt += 1;
                        ?>
                        <tr>
                            <td><?= \backend\models\Product::findName($valuex->product_id); ?></td>
                            <td style="text-align: center"><?= number_format($valuex->qty, 1) ?></td>
                            <td style="text-align: center"><?= number_format($valuex->price, 2) ?></td>
                            <td style="text-align: right"><?= number_format($valuex->qty * $valuex->price, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tfoot>
                <tr>
                    <td style="font-size: 18px;border-top: 1px dotted gray">จำนวนรายการ</td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"><?= number_format($items_cnt) ?></td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"></td>
                    <td style="font-size: 18px;border-top: 1px dotted gray;text-align: right"><?= number_format($total_amt, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">ส่วนลด</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"><?= number_format($discount, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">จำนวนสุทธิ</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"> <?= number_format($total_amt - $discount, 2) ?></td>
                </tr>
                <tr>
                    <td style="font-size: 18px;">ทอนเงิน</td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 18px;text-align: right"> <?= number_format($change, 2) ?></td>
                </tr>
                </tfoot>
            </table>
            <table class="table-header">
                <tr>
                    <td></td>
                </tr>
            </table>
            <table class="table-header" style="font-family: 'THSarabunNew';width: 250px; height: 50px;">
                <tr>
                    <td style="font-size: 18px;">แคชเชียร์ ........ <span
                                style="position: absolute;"><?= \backend\models\User::findName(\Yii::$app->user->id) ?></span>
                        .........
                    </td>
                </tr>
                <tr>
                    <!--            <td><p style="font-size: 15px;">Process time used <span>-->
                    <?php ////echo $model["time_used"]?><!--</span></p></td>-->
                </tr>
            </table>

            <input type="hidden" class="print-amount" value="2">
            <div id="print-slip-2" style="display: none; height: 280px;">
                <input type="hidden" class="has-slip" value="<?= $model != null ? 1 : 0 ?>">
                <?php if ($model != null): ?>
                    <table style="font-family: 'THSarabunNew';width: 250px;">
                        <tr>
                            <td style="width: 40%">
                                <div style="height: 80px;width: 80px;">
                                    <?php
                                    $order_no = "ICE";
                                    if ($model->order_no != '') {
                                        $order_no = $model->order_no;
                                    }
                                    \Yii::$app->response->format = Response::FORMAT_HTML;
                                    $data = $order_no;
                                    $qr = new QRCode();
                                    echo '<img style="width: 100%" src="' . $qr->render($data) . '" />';
                                    ?>
                                </div>
                            </td>
                            <td style="width: 60%">
                                <table class="table-title">
                                    <tr>
                                        <td style="font-size: 20px;text-align: center;vertical-align: bottom">
                                            <h2>น้ำแข็ง</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px;text-align: center;vertical-align: top">
                                            <h2>ใบสั่งจ่ายxxx</h2>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="table-title" style="font-family: 'THSarabunNew';width: 250px;">
                        <tr>
                            <td style="font-size: 18px;text-align: left;">เลขที่ <span><?php echo $model->order_no; ?></span>
                            </td>
                            <td style="font-size: 18px;text-align: left">
                                วันที่ <span><?= date('d/m/Y', strtotime($model->order_date)); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 18px;text-align: left">
                                ลูกค้า <span><?= \backend\models\Customer::findName($model->customer_id); ?></span>
                            </td>
                            <td style="font-size: 18px;text-align: left">
                                เวลา <span><?= date('H:i:s', strtotime($model->order_date)); ?></span>
                            </td>
                        </tr>
                    </table>
                    <table class="table-title" style="font-family: 'THSarabunNew';width: 250px;">
                        <tr>
                            <td style="border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>รายการ</b></td>
                            <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>จำนวน</b>
                            </td>
                            <td style="text-align: center;border-top: 1px dotted gray;border-bottom: 1px dotted gray;"><b>ราคา</b>
                            </td>
                            <td style="text-align: right;border-top: 1px dotted gray;border-bottom: 1px dotted gray"><b>รวม</b></td>
                        </tr>
                        <?php
                        $total_qty = 0;
                        $total_amt = 0;
                        $discount = 0;
                        $change = 0;
                        $items_cnt = 0;
                        ?>
                        <?php if ($model_line != null): ?>
                            <?php foreach ($model_line as $valuex): ?>
                                <?php
                                $total_qty = $total_qty + $valuex->qty;
                                $total_amt = $total_amt + ($valuex->qty * $valuex->price);
                                $change = 0;// $change_amount == null ? 0 : $change_amount;//$value->change_amount;
                                $items_cnt += 1;
                                ?>
                                <tr>
                                    <td><?= \backend\models\Product::findName($valuex->product_id); ?></td>
                                    <td style="text-align: center"><?= number_format($valuex->qty, 1) ?></td>
                                    <td style="text-align: center"><?= number_format($valuex->price, 2) ?></td>
                                    <td style="text-align: right"><?= number_format($valuex->qty * $valuex->price, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tfoot>
                        <tr>
                            <td style="font-size: 18px;border-top: 1px dotted gray">จำนวนรายการ</td>
                            <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"><?= number_format($items_cnt) ?></td>
                            <td style="font-size: 18px;border-top: 1px dotted gray;text-align: center"></td>
                            <td style="font-size: 18px;border-top: 1px dotted gray;text-align: right"><?= number_format($total_amt, 2) ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 18px;">ส่วนลด</td>
                            <td></td>
                            <td></td>
                            <td style="font-size: 18px;text-align: right"><?= number_format($discount, 2) ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 18px;">จำนวนสุทธิ</td>
                            <td></td>
                            <td></td>
                            <td style="font-size: 18px;text-align: right"> <?= number_format($total_amt - $discount, 2) ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 18px;">ทอนเงิน</td>
                            <td></td>
                            <td></td>
                            <td style="font-size: 18px;text-align: right"> <?= number_format($change, 2) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    <table class="table-header">
                        <tr>
                            <td></td>
                        </tr>
                    </table>
                    <table class="table-header" style="font-family: 'THSarabunNew';width: 250px; height: 50px;">
                        <tr>
                            <td style="font-size: 18px;">แคชเชียร์ ........ <span
                                        style="position: absolute;"><?= \backend\models\User::findName(\Yii::$app->user->id) ?></span>
                                .........
                            </td>
                        </tr>
                        <tr>
                            <!--            <td><p style="font-size: 15px;">Process time used <span>-->
                            <?php ////echo $model["time_used"]?><!--</span></p></td>-->
                        </tr>
                    </table>
                <?php endif;?>
            </div>
        <?php endif;?>
    </div>






<?php
//echo PrintThis::widget([
//    'htmlOptions' => [
//        'id' => 'print-slip',
//        'btnClass' => 'btn btn-info',
//        'btnId' => 'btnPrintThis',
//        'btnText' => 'พิมพ์หน้านี้',
//        'btnIcon' => 'fa fa-print'
//    ],
//    'options' => [
//        'debug' => false,
//        'importCSS' => true,
//        'importStyle' => false,
//        // 'loadCSS' => "path/to/my.css",
//        'pageTitle' => "",
//        'removeInline' => false,
//        'printDelay' => 333,
//        'header' => null,
//        'formValues' => true,
//        'onafterprint'=> 'alert()',
//    ]
//]);
?>
<?php




Modal::begin([
    'title' => 'บันทึกรายการใบเบิกสินค้า',
    'id' => 'modal-issue',
    'size' => 'modal-xl',
]);
echo "<div id='modalContent'></div>";
Modal::end();

Modal::begin([
    'title' => 'ยืนยันใบเบิกสินค้า',
    'id' => 'modal-issue-list',
    'size' => 'modal-xl',
]);
echo "<div id='modalContent'></div>";
Modal::end();


?>
<?php //endif;?>

<?php
$url_to_get_origin_price = \yii\helpers\Url::to(['pos/getoriginprice'], true);
$url_to_get_basic_price = \yii\helpers\Url::to(['pos/getbasicprice'], true);
$url_to_get_price = \yii\helpers\Url::to(['pos/getcustomerprice'], true);
$url_to_create_do = \yii\helpers\Url::to(['pos/printdo'], true);

$js = <<<JS
var loop_nums = 0;
 $(function(){
         $('#modalButton').click(function (){              
             //alert($(this).attr('href'));
            $.get($(this).attr('href'), function(data) {
              $('#modal-issue').modal('show').find('#modalContent').html(data);
              $.fn.modal.Constructor.prototype.enforceFocus = function() {};
           });
           return false;
        });
        $('#modalListissue').click(function (){               
            $.get($(this).attr('href'), function(data) {
              $('#modal-issue-list').modal('show').find('#modalContent').html(data)
           });
           return false;
        });
       
        
     setInterval(function (){
          var dt = new Date();
          var time = dt.getHours() + ":" + dt.getMinutes();
          $(".c-time").html(time);
     },60000);
        
        document.getElementById('btnFullscreen').addEventListener('click', function() {
            toggleFullscreen();
        });
        
     $(".customer-id").select2({
     dropdownAutoWidth : true
     });
     if($("#btn-general-customer").hasClass("active")){
         $(".sale-customer-id").val(3646); // 3646 & 1 & 210 bp
         //$(".sale-customer-id").val(210); // 3646 & 1 & 210 bp
         $(".div-customer-search").hide();
     }else{
         $(".text-price-type").show();
         $(".div-customer-search").show();
     }
     $(".btn-pay-cash").click(function(){
         $(".sale-pay-type").val(1);
         var sale_total_amt = $(".total-value-top").val()
             if(sale_total_amt > 0){
                 $(".pay-total-amount").val(sale_total_amt);
                 $(".sale-total-amount").val(sale_total_amt);
                 
                  $(".pay-amount").val(0);
                  $(".pay-change").val(0);
                  var c_pay = $(".pay-amount").val();
                  var sale_total = $(".pay-total-amount").val();
                    if(c_pay < sale_total){
                       // $(".pay-alert").fadeIn();
                        $(".btn-pay-submit").prop('disabled','disabled');
                    }else{
                       // $(".pay-alert").hide();
                        $(".btn-pay-submit").prop('disabled','');
                    }
                 $("#payModal").modal("show");
             }
     });
     
     $(".btn-pay-credit").click(function(){
         $(".sale-pay-type").val(2);
         var sale_total_amt = $(".total-value-top").val()
             if(sale_total_amt > 0){
                 $(".pay-total-amount").val(sale_total_amt);
                 $(".sale-total-amount").val(sale_total_amt);
                 
                  $(".pay-amount").val(0);
                  $(".pay-change").val(0);
                  var c_pay = $(".pay-amount").val();
                  var sale_total = $(".pay-total-amount").val();
                    if(c_pay < sale_total){
                       // $(".pay-alert").fadeIn();
                        $(".btn-pay-submit").prop('disabled','disabled');
                    }else{
                       // $(".pay-alert").hide();
                        $(".btn-pay-submit").prop('disabled','');
                    }
                 $("#paycreditModal").modal("show");
             }
     });
     
     $(".table-cart tbody tr").each(function (){
         var check_row = $(this).closest('tr').find('td:eq(1)').html();
         //alert(check_row);
        if( check_row == ''){
            $(this).closest('tr').find('.removecart-item').hide();
            $(this).closest('tr').find('.cart-qty').prop("disabled","disabled");
            $(".div-payment").hide();
        } 
     });
     
     $("#btn-fix-customer").click(function(){
           $("#sale-by-original").hide();
          $("#sale-by-customer").show();
        $(this).removeClass('btn-outline-secondary');
        $(this).addClass('btn-success');
        
        $("#btn-general-customer").removeClass('btn-success');
        $("#btn-general-customer").removeClass('active');
        $("#btn-general-customer").addClass('btn-outline-secondary');
        //$(".text-price-type").show();
        $(".div-customer-search").show();
        // $(".table-cart tbody tr").remove();
         $(".btn-cancel-cart").trigger("click");
     });
     
      $("#btn-general-customer").click(function(){
          $("#sale-by-original").show();
          $("#sale-by-customer").hide();
          // $(".sale-customer-id").val(3646); 
         $(".sale-customer-id").val(210); // bp
          
          //$(".table-cart tbody tr").remove();
       $(".btn-cancel-cart").trigger("click");
          
        $(this).removeClass('btn-outline-secondary');
        $(this).addClass('btn-success');
        
        $("#btn-fix-customer").removeClass('btn-success');
        $("#btn-fix-customer").removeClass('active');
        $("#btn-fix-customer").addClass('btn-outline-secondary');
        
        $(".div-customer-search").hide();
     });
      
     $(".btn-cancel-cart").click(function(){
         $(".table-cart tbody tr").each(function(){
             if($(".table-cart tbody>tr").length == 1){
                 $(".table-cart tbody tr").each(function(){
                     $(this).closest('tr').find('.cart-product-id').val('');
                     $(this).closest('tr').find('.cart-price').val('');
                     $(this).closest('tr').find('.cart-qty').val('');
                     $(this).closest('tr').find('.cart-qty').prop('disabled','disabled');
                     $(this).closest('tr').find('td:eq(0)').html('');
                     $(this).closest('tr').find('td:eq(1)').html('');
                     $(this).closest('tr').find('td:eq(2)').html('');
                     $(this).closest('tr').find('td:eq(4)').html('');
                     $(this).closest('tr').find('td:eq(5)').html('');
                     $(this).closest('tr').find('.removecart-item').hide();
                 });
                  $(".btn-cancel-cart").hide();
                  $(".div-payment").hide();
                  clearall();
             }else{
                 $(this).remove();
                  calall();
             }
         });  
        
     });
     
     $(".btn-pay-submit").click(function(){
         $(".print-type-doc").val(1);
         
         $(".modal-header-close-sale").css("background-color","white");
         $(".span-before-save").hide();
          $(".span-saving").show();
         $("form#form-close-sale").submit();
         
     });
     
     $(".btn-pay-submit-with-do").click(function(){
         $(".print-type-doc").val(2);
         $(".modal-header-close-sale").css("background-color","white");
          $(".span-before-save").hide();
         $(".span-saving").show();
         $("form#form-close-sale").submit();
     });
     
     $(".btn-pay-credit-submit").click(function(){
          $(".print-type-doc").val(2);
         $("form#form-close-sale").submit();
     });
     
      $(".btn-pay-credit-submit-with-do").click(function(){
           $(".print-type-doc").val(2);
         $("form#form-close-sale").submit();
     });
      
      var has_slipx = $(".has-slip").val();
     // alert(has_slipx);
      if(has_slipx > 1){
          
          if(loop_nums ==0){
             alert('xxx');
             var restorepage = document.body.innerHTML;
             var printcontent = document.getElementById('print-slip').innerHTML;
             document.body.innerHTML = printcontent;
             window.print();
          
             document.body.innerHTML = restorepage; 
             
             window.onafterprint = (event) => {
                 var xtime = $(".print-amount").val();
                 if(xtime == 2){
                     //alert(2);
                     
                     var printcontent2 = document.getElementById('print-slip-2').innerHTML;
                     document.body.innerHTML = printcontent2;
                     window.print();
                 }
              window.location.href = "http://localhost/icesystem/backend/web/index.php?r=pos%2Findextest&id=0";
            };
              
            //  printContent("print-slip");
           // $("#print-slip").show();
          //  $("#btnPrintThis").trigger('click');
           // window.addEventListener("afterprint", (event) => {
           //    alert("After print");
           //  });
           //  window.onafterprint = (event) => {
           //    alert("After print");
           //  };
            // if(window.onafterprint()){
            //     alert("after print"); 
            //     window.location.href = "http://localhost/icesystem/backend/web/index.php?r=pos%2Findextest&id=0";
            // }
          }
         
      }
      loop_nums +=1;
 });

function toggleFullscreen(elem) {
  elem = elem || document.documentElement;
  if (!document.fullscreenElement && !document.mozFullScreenElement &&
    !document.webkitFullscreenElement && !document.msFullscreenElement) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.msRequestFullscreen) {
      elem.msRequestFullscreen();
    } else if (elem.mozRequestFullScreen) {
      elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}



function calpayprice(e){
    var price_val = e.attr('data-var');
    if(price_val == 0){
        $(".pay-amount").val(0);
    }
    var c_pay = $(".pay-amount").val();
    var new_pay = parseFloat(price_val) + parseFloat(c_pay);

    var sale_total = $(".pay-total-amount").val();
    var price_change = parseFloat(new_pay) - parseFloat(sale_total);
    if(price_val == 0){ price_change = 0;}
    $(".pay-amount").val(new_pay);
    $(".pay-change").val(price_change);
    
    //alert($(".pos-date").val());
    
    $(".sale-pay-date").val($(".pos-date").val());
    
    $(".sale-pay-amount").val(new_pay);
    $(".sale-pay-change").val(price_change);
    
    if(new_pay < sale_total){
        $(".pay-alert").fadeIn();
        $(".btn-pay-submit").prop('disabled','disabled');
    }else{
        $(".pay-alert").hide();
        $(".btn-pay-submit").prop('disabled','');
    }
}
function calpayprice2(e){
    var price_val = e.attr('data-var');
    var c_pay = "";
    if(price_val == "-1"){
        $(".edit-amount").val(0);
    }else{
        if($(".edit-amount").val() == "0"){
            if(price_val == "100"){
                 c_pay = '0.';
            }else{
                 c_pay = price_val;
            }
           
        }else if($(".edit-amount").val() == "0."){
            c_pay = '0.'+ price_val;
           
        }else if(price_val == "100"){
            
            c_pay = $(".edit-amount").val()+".";
        }else{
          //  alert('>0');
            if(price_val == "100"){
                price_val = ".";
                 c_pay = price_val;
            }else{
                c_pay = $(".edit-amount").val();
                c_pay = c_pay + price_val;
            }
         //   c_pay = ''+$(".edit-amount").val()+".";
          
        }
    }
    
    $(".edit-amount").val(c_pay);
}
function getproduct_price(e){
   
    var ids = e.val();
    if(ids > 0){
      //  alert(ids);
        $(".sale-customer-id").val(ids);
        $("div.product-items").each(function(){
         // alert();
         var _this = $(this);
         var line_product_id = $(this).find(".list-item-product-id").val();
          $.ajax({
                   type: "post",
                   dataType: "json",
                   url: "$url_to_get_basic_price",
                   data: {'product_id': line_product_id, 'customer_id': ids},
                   success: function(data){
                       if(data.length > 0){
                           //alert(data.length);
                              if(data[0]['sale_price'] != null){
                                   _this.find(".card").css("background-color","#66CCFF");
                                   _this.find(".list-item-price").val(data[0]['sale_price']);
                                   _this.find(".item-price").html(data[0]['sale_price']); 
                              }else{
                                   _this.find(".card").css("background-color","white");
                                   _this.find(".list-item-price").val(data[0]['basic_price']);
                                   _this.find(".item-price").html(data[0]['basic_price']); 
                              }
                       }
                   }
          });                               
     });
    }
}
function getproduct_price2(e){
    var ids = e.val();
    if(ids > 0){
     //  alert(ids);
        $(".sale-customer-id").val(ids);
         $.ajax({
              type: "post",
              dataType: "json",
              async: true,
              url: "$url_to_get_price",
              data: {customer_id: ids},
              success: function(data){
                  if(data.length > 0){
                          var i = -1;
                          var price_group_name = '';
                          if(data[0][0] != null){
                              loop_item_price(data);
                              //alert($("div.product-items").length);
//                              $("div.product-items").each(function(){
//                                // alert();
//                                     i++;
//                                     // var line_product_id = $(this).find(".list-item-product-id").val();
//                                     // alert(line_product_id);
//                                        // if(data[0][i]!= null){
//                                            // alert(data[0].length);
//                                            //  for(var x =0;x<= data[0].length -1;x++){
//                                            //      alert('product = '+ data[0][x]['product_id']);
//                                            //      if(parseInt(line_product_id) == parseInt(data[0][x]['product_id'])){
//                                            //          alert("OKKK");
//                                            //      }
//                                            //  }
//                                             
//                                             // alert('line_id= '+line_product_id + ' AND ' +data[0][i]['product_id']);
//                                             //     if(parseInt(line_product_id) == parseInt(data[0][i]['product_id'])){
//                                             //         alert("equal");
//                                             //         $(this).find(".card").css("background-color","#66CCFF");
//                                             //         $(this).find(".list-item-price").val(data[0][i]['sale_price']);
//                                             //         $(this).find(".item-price").html(data[0][i]['sale_price']);
//                                             //     }
//                                              price_group_name = data[0][i]['price_name'];
//                                             //alert(line_product_id);
//                                         // }else{
//                                         //         $(this).find(".card").css("background-color","white"); 
//                                         //         $(this).find(".list-item-price").val(data[1][i]['sale_price']);
//                                         //         $(this).find(".item-price").html(data[1][i]['sale_price']); 
//                                         // }
//                               
//                              });
                          }else{
                              $(".product-items").each(function(){
                                     i+=1;
                                     var line_product_id = $(this).find(".list-item-product-id").val();
                                     if(line_product_id == data[1][i]['product_id']){
                                         $(".card").css("background-color","white"); 
                                         $(this).find(".list-item-price").val(data[1][i]['sale_price']);
                                         $(this).find(".item-price").html(data[1][i]['sale_price']);
                                     }
                              });
                          }
                          if(price_group_name !=''){
                              $(".text-price-type").show();
                              $(".badge-text-price-type").html(price_group_name);
                          }else{
                               $(".text-price-type").hide();
                              $(".badge-text-price-type").html('');
                          }
                            
                  }else{
                      alert('no price');
                  }
               },
               error: function(err) {
                  alert('eror');
               }
             });
    }
    // $(".product-items").each(function(){
    //   // console.log($(this).find(".list-item-price").val());
    //    $(".popup-price").val($(this).find(".list-item-price").val());
    // });
}

function loop_item_price(data){
     //alert($("div.product-items").length);
     var i = -1;
     var price_group_name = '';
     $("div.product-items").each(function(){
         // alert();
         var _this = $(this);
         i++;
         var line_product_id = $(this).find(".list-item-product-id").val();
         //alert(line_product_id);
         if(data[0][0]!= null){
             //alert(data[0].length);
             for(var x =0;x<= data[0].length -1;x++){
               //  alert('product = '+ data[0][x]['product_id']);
                if(parseInt(line_product_id) == parseInt(data[0][x]['product_id'])){
                    //alert("OKKK");
                             $(this).find(".card").css("background-color","#66CCFF");
                             $(this).find(".list-item-price").val(data[0][x]['sale_price']);
                             $(this).find(".item-price").html(data[0][x]['sale_price']);
               }else{
                             $(this).find(".card").css("background-color","white");
                             $.ajax({
                                      type: "post",
                                      dataType: "json",
                                      async: true,
                                      url: "$url_to_get_basic_price",
                                      data: {id: line_product_id},
                                      success: function(data){
                                          if(data.length > 0){
                                               _this.find(".list-item-price").val(data[0]['sale_price']);
                                               _this.find(".item-price").html(data[0]['sale_price']); 
                                          }
                                         
                                      }
                             });
               }
             }
         }
                               
     });
}

function showadditem(e){
    var c_prod_id = e.find('.list-item-id').val();
    var c_prod_code = e.find('.list-item-code').val();
    var c_prod_name = e.find('.list-item-name').val();
    var c_prod_price = e.find('.list-item-price').val();
   //alert(c_prod_price);
    if(c_prod_id > 0){
        $(".popup-product-id").val(c_prod_id);
        $(".popup-product-code").val(c_prod_code);
        $(".popup-product").html(c_prod_name);
        $(".popup-qty").val(1);
        $(".popup-price").val(c_prod_price);
        $("#posModal").modal("show");
    }
}
function check_dup(prod_id){
    var has_id = 0;
     $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            has_id =1;
        }
     });
    return has_id;
}

function addcart(e){
    var prod_id = $(".popup-product-id").val();
    var prod_code = $(".popup-product-code").val();
    var prod_name = $(".popup-product").html();
    var qty = $(".popup-qty").val();
    var price = $(".popup-price").val();
    var tr = $(".table-cart tbody tr:last");
     // alert(prod_code);
    var check_old = check_dup(prod_id);
    if(check_old == 1){
        $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            var old_qty = $(this).closest('tr').find('.cart-qty').val();
            var new_qty = parseFloat(old_qty) + parseFloat(qty);
            $(this).closest('tr').find('.cart-qty').val(new_qty);
            line_cal($(this));
        }
     });
    }else{
        if(tr.closest('tr').find('.cart-product-id').val() == ''){
            tr.closest('tr').find('.cart-product-id').val(prod_id);
           
            tr.closest('tr').find('.cart-qty').val(qty);
            tr.closest('tr').find('.cart-price').val(price);
            tr.closest('tr').find('td:eq(1)').html(prod_code);
            tr.closest('tr').find('td:eq(2)').html(prod_name);
            tr.closest('tr').find('td:eq(4)').html(price);

            tr.closest('tr').find('.cart-qty').prop("disabled","");
            tr.closest('tr').find('.removecart-item').show();
            $(".div-payment").show();
            line_cal(tr);
        }else{
            var clone = tr.clone();
            clone.find(".cart-product-id").val(prod_id);
            clone.find('.cart-qty').val(qty);
            clone.find('.cart-price').val(price);
            clone.find('td:eq(1)').html(prod_code);
            clone.find('td:eq(2)').html(prod_name);
            clone.find('td:eq(4)').html(price);
            tr.after(clone);
            line_cal(clone);
        }
    }
    cal_linenum();
    calall();
    $("#posModal").modal('hide');
}

function addcart2(e){
    var ids = e.attr('data-var');
    
    var prod_id = e.attr('data-vax');//$(".fix-list-item-id-"+ids).val();
    var prod_code = $(".fix-list-item-code-"+ids).val();
    var prod_name = $(".fix-list-item-name-"+ids).val();
    // alert(prod_id);
    var qty = 1;
    var price =$(".fix-list-item-price-"+ids).val();
    //var onhand =$(".fix-list-item-onhand-"+ids).val(); 
    // e.closest("tr").find('.list-item-onhand').val();
    var onhand = e.attr('data-val');
    var tr = $(".table-cart tbody tr:last");
     
    if(parseFloat(onhand)<=0){
         alert('จำนวนสินค้าในสต๊อกไม่เพียงพอ');
                return false;
    }
  //  alert(onhand);
  
    var check_old = check_dup(prod_id);
    if(check_old == 1){
        $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            var old_qty = $(this).closest('tr').find('.cart-qty').val();
            var new_qty = parseFloat(old_qty) + parseFloat(qty);
            if(parseFloat(new_qty) > parseFloat(onhand)){
                //alert(onhand);
                alert('จำนวนสินค้าในสต๊อกไม่เพียงพอ');
                return false;
            }
            $(this).closest('tr').find('.cart-qty').val(new_qty);
            line_cal($(this));
        }
     });
    }else{
        if(tr.closest('tr').find('.cart-product-id').val() == ''){
            // alert('has');
            tr.closest('tr').find('.cart-product-id').val(prod_id);
             tr.closest('tr').find('.cart-product-onhand').val(onhand);
            tr.closest('tr').find('.cart-qty').val(qty);
            tr.closest('tr').find('.cart-price').val(price);
            tr.closest('tr').find('td:eq(1)').html(prod_code);
            tr.closest('tr').find('td:eq(2)').html(prod_name);
            tr.closest('tr').find('td:eq(4)').html(price);

            tr.closest('tr').find('.cart-qty').prop("disabled","");
            tr.closest('tr').find('.removecart-item').show();
            $(".div-payment").show();
            line_cal(tr);
        }else{
            var clone = tr.clone();
            clone.find(".cart-product-id").val(prod_id);
            clone.find('.cart-qty').val(qty);
            clone.find('.cart-price').val(price);
            clone.find('td:eq(1)').html(prod_code);
            clone.find('td:eq(2)').html(prod_name);
            clone.find('td:eq(4)').html(price);
            tr.after(clone);
            line_cal(clone);
        }
    }
    cal_linenum();
    calall();
    $(".btn-cancel-cart").show();
   // $("#posModal").modal('hide');
}
function reducecart2(e){
    var ids = e.attr('data-var');
    var prod_id = e.attr('data-val');//$("#sale-by-original").find(".list-item-id-"+ids).val();
    var prod_code = $(".list-item-code-"+ids).val();
    var prod_name = $(".list-item-name-"+ids).val();
    // alert(prod_id);
    var qty = -1;
    var price = $(".list-item-price-"+ids).val();
    var onhand = $(".list-item-onhand-"+ids).val();
    var tr = $(".table-cart tbody tr:last");
     
    var check_old = check_dup(prod_id);
    if(check_old == 1){
        $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            var old_qty = $(this).closest('tr').find('.cart-qty').val();
            var new_qty = parseFloat(old_qty) + parseFloat(qty);
            if(new_qty < 0){return false;}
            $(this).closest('tr').find('.cart-qty').val(new_qty);
            line_cal($(this));
            
        }
     });
    }
    cal_linenum();
    calall();
   // $("#posModal").modal('hide');
}

function addcartdivcustomer(e){
    var ids = e.attr('data-var');
    
    var prod_id = $(".list-item-id-"+ids).val();
    var prod_code = $(".list-item-code-"+ids).val();
    var prod_name = $(".list-item-name-"+ids).val();
   //  alert(prod_id);
    var qty = 1;
    var price =$(".list-item-price-"+ids).val();
   // var onhand =$(".list-item-onhand"+ids).val();
   var onhand =$(".fix-list-item-onhand-"+ids).val();
    var tr = $(".table-cart tbody tr:last");
    
   //  alert(onhand);
      if(parseFloat(onhand)<=0){
         alert('จำนวนสินค้าในสต๊อกไม่เพียงพอ');
                return false;
    }
     
    var check_old = check_dup(prod_id);
    if(check_old == 1){
        $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            var old_qty = $(this).closest('tr').find('.cart-qty').val();
            var new_qty = parseFloat(old_qty) + parseFloat(qty);
            $(this).closest('tr').find('.cart-qty').val(new_qty);
            line_cal($(this));
        }
     });
    }else{
        if(tr.closest('tr').find('.cart-product-id').val() == ''){
            // alert('has');
            tr.closest('tr').find('.cart-product-id').val(prod_id);
            tr.closest('tr').find('.cart-qty').val(qty);
            tr.closest('tr').find('.cart-price').val(price);
            tr.closest('tr').find('td:eq(1)').html(prod_code);
            tr.closest('tr').find('td:eq(2)').html(prod_name);
            tr.closest('tr').find('td:eq(4)').html(price);

            tr.closest('tr').find('.cart-qty').prop("disabled","");
            tr.closest('tr').find('.removecart-item').show();
            $(".div-payment").show();
            line_cal(tr);
        }else{
            var clone = tr.clone();
            clone.find(".cart-product-id").val(prod_id);
            clone.find('.cart-qty').val(qty);
            clone.find('.cart-price').val(price);
            clone.find('td:eq(1)').html(prod_code);
            clone.find('td:eq(2)').html(prod_name);
            clone.find('td:eq(4)').html(price);
            tr.after(clone);
            line_cal(clone);
        }
    }
    cal_linenum();
    calall();
    $(".btn-cancel-cart").show();
   // $("#posModal").modal('hide');
}
function reducecartdivcustomer(e){
    var ids = e.attr('data-var');
    var prod_id = $(".list-item-id-"+ids).val();
    var prod_code = $(".list-item-code-"+ids).val();
    var prod_name = $(".list-item-name-"+ids).val();
    // alert(prod_id);
    var qty = -1;
    var price = $(".list-item-price-"+ids).val();
    var onhand = $(".list-item-onhand-"+ids).val();
    var tr = $(".table-cart tbody tr:last");

    
    var check_old = check_dup(prod_id);
    if(check_old == 1){
        $(".table-cart tbody tr").each(function(){
        var id = $(this).closest('tr').find('.cart-product-id').val();
        if(id == prod_id){
            var old_qty = $(this).closest('tr').find('.cart-qty').val();
            var new_qty = parseFloat(old_qty) + parseFloat(qty);
            if(new_qty < 0){return false;}
            $(this).closest('tr').find('.cart-qty').val(new_qty);
            line_cal($(this));
            
        }
     });
    }
    cal_linenum();
    calall();
   // $("#posModal").modal('hide');
}

function removecartitem(e){
   // if(confirm('ต้องการลบข้อมูลนี้ใช่หรือไม่?')){
        
        if($('.table-cart tbody tr').length == 1){
            var tr = $('.table-cart tbody tr:last');
             tr.find('.cart-product-id').val('');
             tr.find('.cart-price').val('');
             tr.find('.cart-qty').val('');
             tr.find('.cart-qty').prop('disabled','disabled');
             tr.find('td:eq(0)').html('');
             tr.find('td:eq(1)').html('');
             tr.find('td:eq(2)').html('');
             tr.find('td:eq(4)').html('');
             tr.find('td:eq(5)').html('');
             tr.find('.removecart-item').hide();
             clearall();
            $(".btn-cancel-cart").hide();
            $(".div-payment").hide();
        }else{
             e.parent().parent().remove();
              cal_linenum();
              calall();
        }
        
        
  //  }
}
function cal_linenum() {
        var xline = 0;
        $(".table-cart tbody tr").each(function () {
            xline += 1;
            var ids = $(this).closest('tr').find('.cart-product-id').val();
            if(ids !=''){
               // alert()
                $(this).closest("tr").find("td:eq(0)").text(xline);
            }
            
        });
}

function line_cal(e){
    var line_total = 0;
  //  $(".table-cart tbody tr").each(function(){
          var qty = e.closest('tr').find('.cart-qty').val();
          var price = e.closest('tr').find('.cart-price').val();
          line_total = parseFloat(qty) * parseFloat(price);
        //  alert(price);
   // });
    e.closest('tr').find('.cart-total-price').val(parseFloat(line_total));
    e.closest('tr').find('td:eq(5)').html(addCommas(parseFloat(line_total).toFixed(2)));
    calall();
}
function calall(){
      var total_qty = 0;
      var total_price = 0;

      $(".table-cart tbody tr").each(function(){
          var qty = $(this).closest('tr').find('.cart-qty').val();
          var price = $(this).closest('tr').find('.cart-total-price').val();
          
         if(qty == '' || qty == null){
             qty = 0;
         }
         if(price == '' || price == null){
             price = 0;
         }
         
        // alert("qty "+qty+" price "+price);
          
          total_qty =  parseFloat(total_qty) + parseFloat(qty);
          total_price = total_price + parseFloat(price);
          // alert(total_price);
      });

      $(".table-cart tfoot tr").find('td:eq(1)').html(total_qty);
      $(".table-cart tfoot tr").find('td:eq(3)').html(addCommas(parseFloat(total_price).toFixed(2)));
      $(".total-text-top").html(addCommas(parseFloat(total_price).toFixed(2)));
      $(".total-value-top").val(total_price);

}
function clearall(){
      $(".table-cart tfoot tr").find('td:eq(1)').html(0);
      $(".table-cart tfoot tr").find('td:eq(3)').html(addCommas(0));
      $(".total-text-top").html(addCommas(0));
      $(".total-value-top").val(0.00);
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

function myPrint(){
        var getMyFrame = document.getElementById('iFramePdf');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
}
function myPrint2(){
    var has_print_do = $(".has-print-do").attr("data-var");
   // alert(has_print_do);
    if(has_print_do != "" || has_print_do != null){
        var getMyFrame = document.getElementById('iFramePdfDo');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
    }
    
}
function myPrint3(){
    var has_print_car_pos = $(".has-print-car-pos").attr("data-var");
   // alert(has_print_do);
    if(has_print_car_pos != "" || has_print_car_pos != null){
        var getMyFrame = document.getElementById('iFramePdfCarPos');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
    }
   
}
function myPrint3Copy(){
    var has_print_car_pos = $(".has-print-car-pos2").attr("data-var");
   // alert(has_print_do);
    if(has_print_car_pos != "" || has_print_car_pos != null){
        var getMyFrame = document.getElementById('iFramePdfCarPos2');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
    }
    
}

function edit_qty(e){
   // alert();
    var line_product_id = e.closest("tr").find(".cart-product-id").val();
    var line_product_onhand = e.closest("tr").find(".cart-product-onhand").val();
    $(".line-edit-amount").val(line_product_id);
    $(".line-edit-onhand").val(line_product_onhand);
    $(".edit-amount").val(0);
    $("#editQtyModal").modal("show");
}
function checkonhand(e){
    var c_val = e.val();
    var line_product_onhand = $(".line-edit-onhand").val();
   // alert(line_product_onhand);
    if(parseFloat(c_val) > parseFloat(line_product_onhand)){
        e.val(0);
        alert('จำนวนไม่พอสำหรับการขาย');
        return false;
    }
}

function sumitchangeqty(e){
    
    var c_val = $(".edit-amount").val();
    var line_product_onhand = $(".line-edit-onhand").val();
   // alert(line_product_onhand);
    if(parseFloat(c_val) > parseFloat(line_product_onhand)){
        $(".edit-amount").val(0);
        alert('จำนวนไม่พอสำหรับการขาย');
        return false;
    }
    
     var new_amt = $(".edit-amount").val();
     var update_product_line = $(".line-edit-amount").val();
     $("table.table-cart tbody tr").each(function(){
         var p_line = $(this).closest('tr').find('.cart-product-id').val();
         if(p_line == update_product_line){
             $(this).closest('tr').find('.cart-qty').val(new_amt).change();
         }
     });
     //alert(new_amt);
     $("#editQtyModal").modal("hide");
}
function printContent(el)
      {
         // $("#print-slip").show();
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
         
         
     }

JS;
$this->registerJs($js, static::POS_END);
?>
