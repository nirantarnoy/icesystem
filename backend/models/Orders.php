<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

date_default_timezone_set('Asia/Bangkok');


class Orders extends \common\models\Orders
{
    /**
     * @var mixed|null
     */


    /**
     * @var mixed|null
     */

    public function behaviors()
    {
        return [
            'timestampcdate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => time(),
            ],
            'timestampudate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                ],
                'value' => time(),
            ],
            'timestampcby' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                ],
                'value' => Yii::$app->user->id,
            ],
            'timestamuby' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'value' => Yii::$app->user->id,
            ],
//            'timestampcompany' => [
//                'class' => \yii\behaviors\AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => 'company_id',
//                ],
//                'value' => isset($_SESSION['user_company_id']) ? $_SESSION['user_company_id'] : 1,
//            ],
//            'timestampbranch' => [
//                'class' => \yii\behaviors\AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => 'branch_id',
//                ],
//                'value' => isset($_SESSION['user_branch_id']) ? $_SESSION['user_branch_id'] : 1,
//            ],
            'timestampupdate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => time(),
            ],
        ];
    }

    public static function getLastNo($date, $company_id, $branch_id)
    {
        $current_date = date('Y-m-d');
        //   $model = Orders::find()->MAX('order_no');
        //    $model = Orders::find()->where(['date(order_date)' => date('Y-m-d', strtotime($date))])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('order_no');
        $model = Orders::find()->where(['date(order_date)' => $current_date])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('order_no');

//        $model_seq = \backend\models\Sequence::find()->where(['module_id'=>4])->one();
//        //$pre = \backend\models\Sequence::find()->where(['module_id'=>15])->one();
//        $pre = '';
//        $prefix = '';
//        if($model_seq){
//            $pre =$model_seq->prefix;
//            if($model){
//                if($model_seq->use_year){
//                    $prefix = $pre.substr(date("Y"),2,2);
//                }
//                if($model_seq->use_month){
//                    $m = date('m');
//                    //if($m < 10){$m="0".$m;}
//                    $prefix = $prefix.$m;
//                }
//                if($model_seq->use_day){
//                    $d = date('d');
//                    //if($d < 10){$d="0".$d;}
//                    $prefix = $prefix.$d;
//                }
//
//                $seq_len = strlen($prefix);
//                $cnum = substr((string)$model,$seq_len,strlen($model));
//                $len = strlen($cnum);
//                $clen = strlen($cnum + 1);
//                $loop = $len - $clen;
//                for($i=1;$i<=$loop;$i++){
//                    $prefix.="0";
//                }
//                $prefix.=$cnum + 1;
//                return $prefix;
//            }else{
//                if($model_seq->use_year){
//                    $prefix = $pre.substr(date("Y"),2,2);
//                }
//                if($model_seq->use_month){
//                    $m = date('m');
//                   // if($m < 10){$m="0".$m;}
//                    $prefix = $prefix.$m;
//                }
//                if($model_seq->use_day){
//                    $d = date('d');
//                  ///  if($d < 10){$d="0".$d;}
//                    $prefix = $prefix.$d;
//                }
//                $seq_len = strlen($model_seq->maximum);
//                for($l=1;$l<=$seq_len-1;$l++){
//                    $prefix.="0";
//                }
//                return $prefix.'1';
//            }
//        }


        $pre = "SO";
        if ($model != null) {
//            $prefix = $pre.substr(date("Y"),2,2);
//            $cnum = substr((string)$model,4,strlen($model));
//            $len = strlen($cnum);
//            $clen = strlen($cnum + 1);
//            $loop = $len - $clen;
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            $cnum = substr((string)$model, 10, 4);
            $len = strlen($cnum);
            $clen = strlen($cnum + 1);
            $loop = $len - $clen;
            for ($i = 1; $i <= $loop; $i++) {
                $prefix .= "0";
            }
            $prefix .= $cnum + 1;
            return $prefix;
        } else {
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            return $prefix . '0001';
        }
    }

    public static function getLastNoPos($date, $company_id, $branch_id, $order_date)
    {
        $current_date = date('Y-m-d', strtotime($order_date));
        //   $model = Orders::find()->MAX('order_no');
        //    $model = Orders::find()->where(['date(order_date)' => date('Y-m-d', strtotime($date))])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('order_no');
        $model = Orders::find()->where(['date(order_date)' => $current_date])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('order_no');
        //$model = Orders::find()->select(['order_no'])->where(['date(order_date)' => $current_date])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['id'=>SORT_DESC])->one();

        $pre = "SO";
        if ($model != null) {
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            $cnum = substr((string)$model, 10, 4);
            $len = strlen($cnum);
            $clen = strlen($cnum + 1);
            $loop = $len - $clen;
            for ($i = 1; $i <= $loop; $i++) {
                $prefix .= "0";
            }
            $prefix .= $cnum + 1;
            return $prefix;
        } else {
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            return $prefix . '0001';
        }
    }

    public static function getLastNoCarPos($date, $company_id, $branch_id)
    {
        //   $model = Orders::find()->MAX('order_no');
        //    $model = Orders::find()->where(['date(order_date)' => date('Y-m-d', strtotime($date))])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('order_no');
        $model = Orders::find()->where(['date(order_date)' => date('Y-m-d')])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['like', 'order_no', 'SO'])->MAX('order_no');

//        $model_seq = \backend\models\Sequence::find()->where(['module_id'=>4])->one();
//        //$pre = \backend\models\Sequence::find()->where(['module_id'=>15])->one();
//        $pre = '';
//        $prefix = '';
//        if($model_seq){
//            $pre =$model_seq->prefix;
//            if($model){
//                if($model_seq->use_year){
//                    $prefix = $pre.substr(date("Y"),2,2);
//                }
//                if($model_seq->use_month){
//                    $m = date('m');
//                    //if($m < 10){$m="0".$m;}
//                    $prefix = $prefix.$m;
//                }
//                if($model_seq->use_day){
//                    $d = date('d');
//                    //if($d < 10){$d="0".$d;}
//                    $prefix = $prefix.$d;
//                }
//
//                $seq_len = strlen($prefix);
//                $cnum = substr((string)$model,$seq_len,strlen($model));
//                $len = strlen($cnum);
//                $clen = strlen($cnum + 1);
//                $loop = $len - $clen;
//                for($i=1;$i<=$loop;$i++){
//                    $prefix.="0";
//                }
//                $prefix.=$cnum + 1;
//                return $prefix;
//            }else{
//                if($model_seq->use_year){
//                    $prefix = $pre.substr(date("Y"),2,2);
//                }
//                if($model_seq->use_month){
//                    $m = date('m');
//                   // if($m < 10){$m="0".$m;}
//                    $prefix = $prefix.$m;
//                }
//                if($model_seq->use_day){
//                    $d = date('d');
//                  ///  if($d < 10){$d="0".$d;}
//                    $prefix = $prefix.$d;
//                }
//                $seq_len = strlen($model_seq->maximum);
//                for($l=1;$l<=$seq_len-1;$l++){
//                    $prefix.="0";
//                }
//                return $prefix.'1';
//            }
//        }


        $pre = "SO";
        if ($model != null) {
//            $prefix = $pre.substr(date("Y"),2,2);
//            $cnum = substr((string)$model,4,strlen($model));
//            $len = strlen($cnum);
//            $clen = strlen($cnum + 1);
//            $loop = $len - $clen;
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            $cnum = substr((string)$model, 10, 4);
            $len = strlen($cnum);
            $clen = strlen($cnum + 1);
            $loop = $len - $clen;
            for ($i = 1; $i <= $loop; $i++) {
                $prefix .= "0";
            }
            $prefix .= $cnum + 1;
            return $prefix;
        } else {
            $prefix = $pre . '-' . substr(date("Y"), 2, 2) . date('m', strtotime($date)) . date('d', strtotime($date)) . '-';
            return $prefix . '0001';
        }
    }

    public static function findOrderemp($id)
    {
        $html = '';
        if ($id) {
//            $x_date = explode('/', $trans_date);
//            $t_date = date('Y-m-d');
//            if (count($x_date) > 1) {
//                $t_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
//            }

            $model_o = \backend\models\Orders::find()->where(['id' => $id])->one();
            if ($model_o) {
                $t_date = date('Y-m-d', strtotime($model_o->order_date));

                $model = \backend\models\Cardaily::find()->where(['car_id' => $model_o->car_ref_id, 'date(trans_date)' => $t_date])->all();
                $i = 0;
                foreach ($model as $value) {
                    $i += 1;
                    $emp_code = \backend\models\Employee::findCode($value->employee_id);
                    $emp_fullname = \backend\models\Employee::findFullName($value->employee_id);

                    $html .= $emp_fullname . ' , ';

                }
            }

        }

        return $html;
    }

    public static function findordercash($order_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) {
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 1])->sum('line_total');
            $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['id' => $order_id, 'sale_payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->line_total;
                }
            }
        } else {
            $model = \backend\models\Orderline::find()->select(['qty', 'price', 'customer_id'])->where(['order_id' => $order_id])->all();
            if ($model) {
                foreach ($model as $value) {
                    $cus_pay_method = \backend\models\Customer::findPayMethod($value->customer_id);
                    $paymethod_id = \backend\models\Paymentmethod::find()->select('pay_type')->where(['id' => $cus_pay_method])->one();
                    if ($paymethod_id) {
                        if ($paymethod_id->pay_type == 1) {
                            $total = $total + ($value->qty * $value->price);
                        }
                    }
//                $paymethod_name = \backend\models\Paymentmethod::findName($cus_pay_method);
//                if($paymethod_name == 'เงินสด'){
//                    $total = $total + ($value->qty * $value->price);
//                }
                }

            }
        }
        return $total;
    }

    public static function getlinesum($order_id)
    {
        $total = 0;
        $model = \backend\models\Orderline::find()->where(['order_id' => $order_id])->all();
        if ($model) {
            foreach ($model as $value) {
                $total = $total + ($value->qty * $value->price);
//                $cus_pay_method = \backend\models\Customer::findPayMethod($value->customer_id);
//                $paymethod_id = \backend\models\Paymentmethod::find()->where(['id' => $cus_pay_method])->one();
//                if ($paymethod_id) {
//                    if ($paymethod_id->pay_type == 2) {
//                        $total = $total + ($value->qty * $value->price);
//                    }
//                }
            }

        }
        return $total;
    }

    public static function getlinesumcredit($order_id)
    {
        $total = 0;
        $model = \backend\models\Orderline::find()->where(['order_id' => $order_id, 'sale_payment_method_id' => 2])->all();
        if ($model) {
            foreach ($model as $value) {
                $total = $total + ($value->qty * $value->price);
            }

        }
        return $total;
    }

    public static function getlineremainsum($order_id, $customer_id)
    {
        $total = 0;
        $model = \common\models\QuerySalePaySummary::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->one();
        if ($model) {
            $total = $model->remain_amount;
        } else {
            $model = \common\models\QuerySalePosPaySummary::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->one();
            if ($model) {
                $total = $model->remain_amount;
            }
        }
        return $total;
    }

//
    public static function findordercredit($order_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) { // 1 sale from mobile
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 2])->sum('line_total');
            $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['id' => $order_id, 'sale_payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->line_total;
                }
            }
        } else {
            $model = \backend\models\Orderline::find()->select(['qty', 'price', 'customer_id'])->where(['order_id' => $order_id])->all();
            if ($model) {
                foreach ($model as $value) {
                    $cus_pay_method = \backend\models\Customer::findPayMethod($value->customer_id);
                    $paymethod_id = \backend\models\Paymentmethod::find()->select('pay_type')->where(['id' => $cus_pay_method])->one();
                    if ($paymethod_id) {
                        if ($paymethod_id->pay_type == 2) {
                            $total = $total + ($value->qty * $value->price);
                        }
                    }
                }
            }
        }

//        $model = \common\models\QuerySaleCustomerPaySummary::find()->where(['order_ref_id'=>$order_id])->sum('payment_amount');
//        if($model){
//            $total = $model;
//        }
        return $total;
    }

    public static function findordercredit2($route_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) { // 1 sale from mobile
            $x = null;
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 2])->sum('line_total');
            $order_shift = \common\models\QueryApiOrderDailySummaryNew::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_status', 500])->max('order_shift');
            if ($order_shift) {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2, 'order_shift' => $order_shift])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            } else {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            }

            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->line_total;
                }
            }
        }
        return $total;
    }

    public static function findordercash2($route_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) {
            $x = null;
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 1])->sum('line_total');
            $order_shift = \common\models\QueryApiOrderDailySummaryNew::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_status', 500])->max('order_shift');
            if ($order_shift) {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1, 'order_shift' => $order_shift])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            } else {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('line_total')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            }

            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->line_total;
                }
            }
        }
        return $total;
    }

    public static function findordercreditdiscount($route_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) { // 1 sale from mobile
            $x = null;
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 2])->sum('line_total');
            $order_shift = \common\models\QueryApiOrderDailySummaryNew::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_status', 500])->max('order_shift');
            if ($order_shift) {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('discount_amt')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2, 'order_shift' => $order_shift])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            } else {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('discount_amt')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 2])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            }

            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->discount_amt;
                }
            }
        }
        return $total;
    }

    public static function findordercashdiscount($route_id, $sale_type)
    {
        $total = 0;
        if ($sale_type == 1) {
            $x = null;
            //  $total = \common\models\QueryApiOrderDailySummary::find()->where(['id' => $order_id, 'sale_payment_method_id' => 1])->sum('line_total');
            $order_shift = \common\models\QueryApiOrderDailySummaryNew::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_status', 500])->max('order_shift');
            if ($order_shift) {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('discount_amt')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1, 'order_shift' => $order_shift])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            } else {
                $x = \common\models\QueryApiOrderDailySummaryNew::find()->select('discount_amt')->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_payment_method_id' => 1])->andFilterWhere(['!=', 'order_line_status', 500])->all();
            }

            if ($x) {
                foreach ($x as $value) {
                    $total = $total + $value->discount_amt;
                }
            }
        }
        return $total;
    }


    public static function getNumber($id)
    {
        $model = Orders::find()->where(['id' => $id])->one();
        return $model != null ? $model->order_no : '';
    }

    public static function getCustomerrefno($id)
    {
        $model = Orders::find()->where(['id' => $id])->one();
        return $model != null ? $model->customer_ref_no : '';
    }

    public static function findId($order_no)
    {
        $model = Orders::find()->where(['order_no' => $order_no])->one();
        return $model != null ? $model->id : 0;
    }

    public static function getOrderdate($id)
    {
        $model = Orders::find()->where(['id' => $id])->one();
        return $model != null ? $model->order_date : null;
    }
//    public function findUnitid($code){
//        $model = Unit::find()->where(['name'=>$code])->one();
//        return count($model)>0?$model->id:0;
//    }

    public static function findordercreditPos($user_id)
    {
        $total = 0;
        //if ($user_id > 0) {
        $sql = "SELECT SUM(line_total) as line_total";
        $sql .= " FROM query_order_data";
        $sql .= " WHERE  created_by =" . $user_id;
        $sql .= " AND  sale_payment_method_id = 2";
        $sql .= " AND  date(order_date) = " . "'" . date('Y-m-d') . "'";
        $sql .= " AND  order_status in(1,100)";
        $sql .= " AND  sale_channel_id = 2";
        $query = \Yii::$app->db->createCommand($sql);
        $modelx = $query->queryAll();
        if ($modelx) {
            $total = $modelx[0]['line_total'];
        }
        //}
        return $total;
    }

    public static function findordercashPos($user_id)
    {
        $total = 0;
        //if ($user_id > 0) {
        $sql = "SELECT SUM(line_total) as line_total";
        $sql .= " FROM query_order_data";
        $sql .= " WHERE  created_by =" . $user_id;
        $sql .= " AND  sale_payment_method_id = 1";
        $sql .= " AND  date(order_date) = " . "'" . date('Y-m-d') . "'";
        $sql .= " AND  order_status in(1,100)";
        $sql .= " AND  sale_channel_id = 2";
        $query = \Yii::$app->db->createCommand($sql);
        $modelx = $query->queryAll();
        if ($modelx) {
            $total = $modelx[0]['line_total'];
        }
        // }
        return $total;
    }

}
