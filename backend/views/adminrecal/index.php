<?php

use kartik\daterange\DateRangePicker;

$company_id = 0;
$branch_id = 0;
$res = 0;
if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}

?>

    <div class="row">
        <div class="col-lg-12">
            <label for="">เลือกวันที่</label>
        </div>
    </div>
    <br/>
    <form action="<?= \yii\helpers\Url::to(['adminrecal/index'], true) ?>" method="post">
        <div class="row">
            <div class="col-lg-3">
                <?php
                echo DateRangePicker::widget([
                    'name' => 'find_date',
                    // 'value'=>'2015-10-19 12:00 AM',
                    'value' => $find_date != null ? date('Y-m-d', strtotime($find_date)) : date('Y-m-d'),
                    //    'useWithAddon'=>true,
                    'convertFormat' => true,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'วันที่',
                        //  'onchange' => 'this.form.submit();',
                        'autocomplete' => 'off',
                    ],
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'locale' => ['format' => 'Y-m-d'],
                        'singleDatePicker' => true,
                        'showDropdowns' => true,
                        'timePicker24Hour' => true
                    ]
                ]);
                ?>
            </div>
            <div class="col-lg-3">
                <input type="submit" class="btn btn-primary" value="ค้นหา">
            </div>
        </div>

    </form>
    <br/>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>พนักงานขาย</th>
                    <th>พนักงานฝ่ายผลิต</th>
                    <th>เข้าระบบ</th>
                    <th>ออกระบบ</th>
                    <th>-</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $user_login = findUserPos($find_date, $company_id, $branch_id);
                ?>
                <?php if ($user_login != null): ?>
                    <?php for ($i = 0; $i <= count($user_login) - 1; $i++): ?>
                        <?php
                        $second_user = '';
//                        $second_user_data = findUserSecond($user_login[$i]['id']);
//
//                        if (count($second_user_data) > 0) {
//                            for ($x = 0; $x <= count($second_user_data) - 1; $x++) {
//
//                                if ($x < count($second_user_data)) {
//                                    $second_user .= $second_user_data[$x] . ',';
//                                } else {
//                                    $second_user .= $second_user_data[$x];
//                                }
//                            }
//                        } else {
//                            continue;
//                        }
                        ?>
                        <tr>
                            <td><?= \backend\models\User::findName($user_login[$i]['user_id']) ?></td>
                            <td><?= $second_user ?></td>
                            <td><?= $user_login[$i]['login_date'] ?></td>
                            <td><?= $user_login[$i]['logout_date'] ?></td>
                            <td>
                                <input type="hidden" class="user-login-id" value="<?=$user_login[$i]['id'] ?>">
                                <input type="hidden" class="line-login-user" value="<?= $user_login[$i]['user_id'] ?>">
                                <input type="hidden" class="line-login-date"
                                       value="<?= $user_login[$i]['login_date'] ?>">
                                <input type="hidden" class="line-logout-date"
                                       value="<?= $user_login[$i]['logout_date'] ?>">
                                <div class="btn btn-warning" onclick="submitcal($(this))">คำนวนรายการ</div>
                            </td>
                        </tr>
                    <?php endfor; ?>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <form id="form-recal" action="<?= \yii\helpers\Url::to(['adminrecal/recalculate'], true) ?>" method="post">
        <input type="hidden" class="user-login-id" name="user_login_id" value="">
        <input type="hidden" class="find-user-id" name="find_user_id" value="">
        <input type="hidden" class="find-user-login" name="find_user_login" value="">
        <input type="hidden" class="find-user-logout" name="find_user_logout" value="">
    </form>

<?php
function findUserPos($t_date, $company_id, $branch_id)
{
    $data = [];
    if ($t_date != null) {
        $model = \common\models\LoginLogCal::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(login_date)' => date('Y-m-d', strtotime($t_date))])->all();
        if ($model) {
            foreach ($model as $value) {
                if ($value->user_id == 1) continue;
                array_push($data, [
                    'id' => $value->id,
                    'user_id' => $value->user_id,
                    'login_date' => $value->login_date,
                    'logout_date' => $value->logout_date,
                ]);
            }

        }
    }
    return $data;
}

function findUserSecond($login_id)
{
    $data = [];
    $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $login_id])->all();
    if ($model_user_ref) {
        foreach ($model_user_ref as $value) {
            array_push($data, \backend\models\Employee::findNameFromUserId($value->user_id));
        }
    }

    return $data;
}


$js = <<<JS
function submitcal(e){
   var user_login_id = e.closest("tr").find(".user-login-id").val();
   var find_user_id = e.closest("tr").find(".line-login-user").val();
   var find_login = e.closest("tr").find(".line-login-date").val();
   var find_logout = e.closest("tr").find(".line-logout-date").val();
   
   
   if(find_user_id != null){
       $(".find-user-id").val(find_user_id);
       $(".user-login-id").val(user_login_id);
       $(".find-user-login").val(find_login);
       $(".find-user-logout").val(find_logout);
       
       $("form#form-recal").submit();
   }
   
}
JS;

$this->registerJs($js, static::POS_END);

?>