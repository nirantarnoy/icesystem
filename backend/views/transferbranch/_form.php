<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Transferbranch */
/* @var $form yii\widgets\ActiveForm */

$product_data = \common\models\Product::find()->where(['status' => 1])->orderBy(['item_pos_seq' => SORT_ASC])->all();
?>

<div class="transferbranch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->widget(\toxor88\switchery\Switchery::className())->label(false) ?>


    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped table-bordered" style="width: 100%">
                <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>ราคา</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($model->isNewRecord): ?>
                    <?php foreach ($product_data as $value): ?>
                        <tr>
                            <td>
                                <input type="hidden" class="line-product-id" name="line_product_id[]"
                                       value="<?= $value->id ?>">
                                <input type="text" class="form-control line-product-name" name="line_product_name[]"
                                       value="<?= $value->name; ?>" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control line-price" min="0" step="0.1"
                                       name="line_price[]">
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($product_data as $valuex): ?>
                        <?php if ($model_line != null): ?>
                            <?php foreach ($model_line as $value_line): ?>
                                <?php if ($valuex->id == $value_line->product_id): ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" class="line-product-id" name="line_product_id[]"
                                                   value="<?= $value_line->product_id ?>">
                                            <input type="text" class="form-control line-product-name"
                                                   name="line_product_name[]"
                                                   value="<?= \backend\models\Product::findName($value_line->product_id) ?>"
                                                   readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control line-price" min="0" step="0.1"
                                                   name="line_price[]" value="<?= $value_line->price ?>">
                                        </td>

                                    </tr>
                                <?php else: ?>

                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>
                                    <input type="hidden" class="line-product-id" name="line_product_id[]"
                                           value="<?= $valuex->id ?>">
                                    <input type="text" class="form-control line-product-name" name="line_product_name[]"
                                           value="<?= $valuex->name; ?>" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control line-price" min="0" step="0.1"
                                           name="line_price[]">
                                </td>

                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
