<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Session;

date_default_timezone_set('Asia/Bangkok');

class Customer extends \common\models\Customer
{
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
//            'timestampcompany'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'company_id',
//                ],
//                'value'=> isset($_SESSION['user_company_id'])? $_SESSION['user_company_id']:1,
//            ],
//            'timestampbranch'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'branch_id',
//                ],
//                'value'=> isset($_SESSION['user_branch_id'])? $_SESSION['user_branch_id']:1,
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

    public static function findCode($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->code : '';
    }

    public static function findName($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->name : '';
    }
    public static function findDescription($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->description : '';
    }
    public static function findType($id)
    {
        $name = '';
        $model = Customer::find()->where(['id' => $id])->one();
        $model_type = \backend\models\Customertype::findCode($model->customer_type_id);
        return $model_type != null ? $model_type : '';
    }

    public static function findRoute($customer_id)
    {
        $model = \common\models\QueryCustomerInfo::find()->where(['customer_id' => $customer_id])->one();
        return $model != null ? $model->route_code : '';
    }
    public static function findRouteId($customer_id)
    {
        $model = \common\models\QueryCustomerInfo::find()->where(['customer_id' => $customer_id])->one();
        return $model != null ? $model->rt_id : 0;
    }
    public static function getAddress($customer_id)
    {
        $model = \common\models\Customer::find()->where(['id' => $customer_id])->one();
        return $model != null ? $model->address : '';
    }
    public static function getPhone($customer_id)
    {
        $model = \common\models\Customer::find()->where(['id' => $customer_id])->one();
        return $model != null ? $model->phone : '';
    }

    public static function findPayMethod($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->payment_method_id : 0;
    }
    public static function findLocation($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->location_info : '';
    }

    public static function findPayTerm($id)
    {
        $model = Customer::find()->where(['id' => $id])->one();
        return $model != null ? $model->payment_term_id : 0;
    }
//    public function findName($id){
//        $model = Unit::find()->where(['id'=>$id])->one();
//        return count($model)>0?$model->name:'';
//    }
//    public function findUnitid($code){
//        $model = Unit::find()->where(['name'=>$code])->one();
//        return count($model)>0?$model->id:0;
//    }
//    public static function getLastNo($company_id, $branch_id)
//    {
//        //   $model = Orders::find()->MAX('order_no');
//        $model = Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('code');
//
//        $pre = "CU";
//        if ($branch_id == 2) {
//            $pre = "BT";
//            if ($model != null) {
////            $prefix = $pre.substr(date("Y"),2,2);
////            $cnum = substr((string)$model,4,strlen($model));
////            $len = strlen($cnum);
////            $clen = strlen($cnum + 1);
////            $loop = $len - $clen;
//                $prefix = $pre ;// substr(date("Y"), 2, 2);
//                $cnum = substr((string)$model, 2, strlen($model));
//                $len = strlen($cnum);
//                $clen = strlen($cnum + 1);
//                $loop = $len - $clen;
//                for ($i = 1; $i <= $loop; $i++) {
//                    $prefix .= "0";
//                }
//                $prefix .= $cnum + 1;
//                return $prefix;
//            } else {
//                $prefix = $pre ;// substr(date("Y"), 2, 2);
//                return $prefix . '0001';
//            }
//        }
//        if($branch_id==1){
//            if ($model != null) {
////            $prefix = $pre.substr(date("Y"),2,2);
////            $cnum = substr((string)$model,4,strlen($model));
////            $len = strlen($cnum);
////            $clen = strlen($cnum + 1);
////            $loop = $len - $clen;
//                $prefix = $pre . '-' . substr(date("Y"), 2, 2);
//                $cnum = substr((string)$model, 5, strlen($model));
//                $len = strlen($cnum);
//                $clen = strlen($cnum + 1);
//                $loop = $len - $clen;
//                for ($i = 1; $i <= $loop; $i++) {
//                    $prefix .= "0";
//                }
//                $prefix .= $cnum + 1;
//                return $prefix;
//            } else {
//                $prefix = $pre . '-' . substr(date("Y"), 2, 2);
//                return $prefix . '00001';
//            }
//        }
//
//    }
    public static function getLastNo($company_id, $branch_id)
    {
        //   $model = Orders::find()->MAX('order_no');
        $model = Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->MAX('code');

        $pre = "BP";

        //if($branch_id==1){
            if ($model != null) {
//            $prefix = $pre.substr(date("Y"),2,2);
//            $cnum = substr((string)$model,4,strlen($model));
//            $len = strlen($cnum);
//            $clen = strlen($cnum + 1);
//            $loop = $len - $clen;
             //   $prefix = $pre . '-' . substr(date("Y"), 2, 2);
                $prefix = $pre;
                 //  $cnum = substr((string)$model, 5, strlen($model)); // omnoi
                $cnum = substr((string)$model, 2, strlen($model)); // bkt

                $len = strlen($cnum);
                $clen = strlen($cnum + 1);
                $loop = $len - $clen;
                for ($i = 1; $i <= $loop; $i++) {
                    $prefix .= "0";
                }
                $prefix .= $cnum + 1;
                return $prefix;
            } else {
             //   $prefix = $pre . '-' . substr(date("Y"), 2, 2); // omnoi
                $prefix = $pre;
                return $prefix . '00001';
            }
       // }

    }

}
