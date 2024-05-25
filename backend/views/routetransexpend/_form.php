<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Routetransexpend */
/* @var $form yii\widgets\ActiveForm */

$model_route = \backend\models\Deliveryroute::find()->where(['>=','id',949])->andFilterWhere(['status'=>1])->orderBy(['id'=>SORT_ASC])->all();
?>

<div class="routetransexpend-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-lg-4">
            <?php $model->trans_date = $model->isNewRecord?date('d/m/Y'):date('d/m/Y',strtotime($model->trans_date))?>
            <?= $form->field($model, 'trans_date')->widget(\kartik\date\DatePicker::className(), [
                'value' => date('Y-m-d'),
                'options' => [
                    'format' => 'dd/mm/yyyy',
                  //  'onchange' => 'form.submit()',
                ],
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                ]
            ])->label('วันที่') ?>
        </div>
    </div>

    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                <tr>
                    <th>สายส่ง</th>
                    <th>หักน้ำมัน</th>
                    <th>หักเบี้ยเลี้ยง</th>
                    <th>หักค่าน้ำ</th>
                    <th>หักค่าเก็บเงิน</th>
                    <th>หัก</th>
                    <th>ขายสด(โอน)</th>
                    <th>ชำระหนี้(โอน)</th>
                    <th>บวก</th>
                    <!--                    <th>รวม</th>-->
                </tr>
                </thead>
                <tbody>
                  <?php foreach($model_route as $value):?>
                      <?php
                         $deduct_1 = 0;
                         $deduct_2 = 0;
                         $deduct_3 = 0;
                         $deduct_4 = 0;
                      $deduct_5 = 0;
                      $deduct_6 = 0;
                      $deduct_7 = 0;
                      $deduct_8 = 0;
                      $total = 0;
                       if($model_line != null) {
                           foreach ($model_line as $valuex) {
                               if ($valuex->route_id == $value->id) {
                                   $deduct_1 = $valuex->oil_amount;
                                   $deduct_2 = $valuex->extra_amount;
                                   $deduct_3 = $valuex->wator_amount;
                                   $deduct_4 = $valuex->money_amount;
                                   $deduct_5 = $valuex->deduct_amount;
                                   $deduct_6 = $valuex->cash_transfer_amount;
                                   $deduct_7 = $valuex->payment_transfer_amount;
                                   $deduct_8 = $valuex->plus_amount;
                                   //   $total = $valuex->total;
                               } else {

                               }
                           }
                       }
                      ?>
                  <tr>
                      <td>
                          <input type="hidden" name="line_route_id[]" value="<?=$value->id;?>">
                          <?= $value->name; ?></td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct1[]" step="0.1" value="<?=$deduct_1?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct2[]" step="0.1" value="<?=$deduct_2?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct3[]" step="0.1" value="<?=$deduct_3?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct4[]" step="0.1" value="<?=$deduct_4?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct5[]" step="0.1" value="<?=$deduct_5?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct6[]" step="0.1" value="<?=$deduct_6?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct7[]" step="0.1" value="<?=$deduct_7?>">
                      </td>
                      <td>
                          <input type="number" min="0" class="form-control" name="line_deduct8[]" step="0.1" value="<?=$deduct_8?>">
                      </td>
                      <!--                      <td>-->
<!--                          <input type="number" min="0" class="form-control" name="line_total[]" value="" readonly>-->
<!--                      </td>-->
                  </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
