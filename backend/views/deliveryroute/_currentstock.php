<?php
$this->title = 'สินค้าคงเหลือสายส่ง :'. \backend\models\Deliveryroute::findName($route_id);
?>
<form action="<?=\yii\helpers\Url::to(['deliveryroute/updateroutestock'],true)?>" method="post">
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th style="width: 5%;text-align: center;">#</th>
                <th>สินค้า</th>
                <th>จำนวน</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;?>
            <?php foreach ($model as $value):?>
                <?php $i +=1;?>
            <tr>
                <td style="text-align: center;"><?=$i?></td>
                <td><?=\backend\models\Product::findName($value->product_id)?></td>
                <td>
                    <input type="hidden" name="line_id[]" value="<?=$value->id;?>">
                    <input type="number" class="form-control" name="line_qty[]" min="0" step="any" value="<?=$value->avl_qty?>">
                </td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
    <div class="row">
        <div class="col-lg-3">
            <button class="btn btn-success">บันทึก</button>
        </div>
    </div>
</form>