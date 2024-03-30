<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

date_default_timezone_set('Asia/Bangkok');

class Stocktrans extends \common\models\StockTrans
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
//            'timestampudate'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'updated_at',
//                ],
//                'value'=> time(),
//            ],
//            'timestampcby'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'created_by',
//                ],
//                'value'=> Yii::$app->user->identity->id,
//            ],
//            'timestamuby'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_UPDATE=>'updated_by',
//                ],
//                'value'=> Yii::$app->user->identity->id,
//            ],
//            'timestampupdate' => [
//                'class' => \yii\behaviors\AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
//                ],
//                'value' => time(),
//            ],
        ];
    }

//    public function findUnitname($id){
//        $model = Unit::find()->where(['id'=>$id])->one();
//        return count($model)>0?$model->name:'';
//    }
//    public function findName($id){
//        $model = Cartype::find()->where(['id'=>$id])->one();
//        return $model != null?$model->name:'';
//    }
//    public function findUnitid($code){
//        $model = Unit::find()->where(['name'=>$code])->one();
//        return count($model)>0?$model->id:0;
//    }

    public static function getLastNo($company_id,$branch_id)
    {
        $model = Stocktrans::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id])->MAX('journal_no');
        //   $model = Orders::find()->where(['date(order_date)' => date('Y-m-d', strtotime($date))])->MAX('order_no');

        $model_seq = \backend\models\Sequence::find()->where(['module_id' => 15])->one();
        //$pre = \backend\models\Sequence::find()->where(['module_id'=>15])->one();
        $pre = '';
        $prefix = '';
        if ($model_seq) {
            $pre = $model_seq->prefix;
            if ($model) {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    //if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    //if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }

                $seq_len = strlen($prefix);
                $cnum = substr((string)$model, $seq_len, strlen($model));
                $len = strlen($cnum);
                $clen = strlen($cnum + 1);
                $loop = $len - $clen;
                for ($i = 1; $i <= $loop; $i++) {
                    $prefix .= "0";
                }
                $prefix .= $cnum + 1;
                return $prefix;
            } else {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    // if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    ///  if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }
                $seq_len = strlen($model_seq->maximum);
                for ($l = 1; $l <= $seq_len - 1; $l++) {
                    $prefix .= "0";
                }
                return $prefix . '1';
            }
        }
    }
    public static function getReturnNo($company_id,$branch_id)
    {
        $model = Stocktrans::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id,'activity_type_id'=>7])->andFilterWhere(['date(trans_date)' => date('Y-m-d')])->MAX('journal_no');
        $model_seq = \backend\models\Sequence::find()->where(['module_id' => 7, 'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
        //$pre = \backend\models\Sequence::find()->where(['module_id'=>15])->one();
        $pre = '';
        $prefix = '';
        if ($model_seq) {
            $pre = $model_seq->prefix.$model_seq->symbol;
            if ($model) {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    //if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    //if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }
                $prefix = $prefix.'-';
                $seq_len = strlen($prefix);
                $cnum = substr((string)$model, $seq_len, strlen($model));
                $len = strlen($model_seq->maximum);// strlen($cnum);
                $clen = strlen($cnum + 1);
                $loop = $len - $clen;
                for ($i = 1; $i <= $loop; $i++) {
                    $prefix .= "0";
                }
                $prefix .= $cnum + 1;
                return $prefix;
            } else {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    // if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    ///  if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }
                $prefix = $prefix.'-';
                $seq_len = strlen($model_seq->maximum);
                for ($l = 1; $l <= $seq_len - 1; $l++) {
                    $prefix .= "0";
                }
                return $prefix . '1';
            }
        }
    }
    public static function getIssueReprocessCar($company_id,$branch_id)
    {
        $model = Stocktrans::find()->where(['company_id'=>$company_id,'branch_id'=>$branch_id,'activity_type_id'=>21])->andFilterWhere(['date(trans_date)' => date('Y-m-d')])->MAX('journal_no');
        $model_seq = \backend\models\Sequence::find()->where(['module_id' => 20, 'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
        //$pre = \backend\models\Sequence::find()->where(['module_id'=>15])->one();
        $pre = '';
        $prefix = '';
        if ($model_seq) {
            $pre = $model_seq->prefix.$model_seq->symbol;
            if ($model) {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    //if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    //if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }
                $prefix = $prefix.'-';
                $seq_len = strlen($prefix);
                $cnum = substr((string)$model, $seq_len, strlen($model));
                $len = strlen($model_seq->maximum);// strlen($cnum);
                $clen = strlen($cnum + 1);
                $loop = $len - $clen;
                for ($i = 1; $i <= $loop; $i++) {
                    $prefix .= "0";
                }
                $prefix .= $cnum + 1;
                return $prefix;
            } else {
                if ($model_seq->use_year) {
                    $prefix = $pre . substr(date("Y"), 2, 2);
                }
                if ($model_seq->use_month) {
                    $m = date('m');
                    // if($m < 10){$m="0".$m;}
                    $prefix = $prefix . $m;
                }
                if ($model_seq->use_day) {
                    $d = date('d');
                    ///  if($d < 10){$d="0".$d;}
                    $prefix = $prefix . $d;
                }
                $prefix = $prefix.'-';
                $seq_len = strlen($model_seq->maximum);
                for ($l = 1; $l <= $seq_len - 1; $l++) {
                    $prefix .= "0";
                }
                return $prefix . '1';
            }
        }
    }

    public static function findProdrecId($no, $company_id, $branch_id){
        $model = \common\models\StockTrans::find()->where(['journal_no' => $no,'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
        return $model != null ? $model->id : 0;
    }
    public static function findProdrecNo($id, $company_id, $branch_id){
        $model = \common\models\StockTrans::find()->where(['id' => $id,'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
        return $model != null ? $model->journal_no : '';
    }
    public static function findCancelqty($product_id,$from_date,$to_date,$company_id,$branch_id){
      $qty = 0;

        if($from_date !=null){
            $fx_datetime = explode(' ',$from_date);
            $tx_datetime = explode(' ',$to_date);

            $f_date = null;
            $f_time = null;
            $t_date = null;
            $t_time = null;

            $from_date_time = null;
            $to_date_time = null;

            if(count($fx_datetime) > 0){
                $f_date = $fx_datetime[0];
                $f_time = $fx_datetime[1];

                $x_date = explode('-', $f_date);
                $xx_date = date('Y-m-d');
                if (count($x_date) > 1) {
                    $xx_date = trim($x_date[1]) . '/' . trim($x_date[2]) . '/' . trim($x_date[0]);
                }
                $from_date_time = date('Y-m-d H:i:s',strtotime($xx_date.' '.$f_time));
                //$from_date_time = date('Y-m-d',strtotime($xx_date));
            }

            if(count($tx_datetime) > 0){
                $t_date = $tx_datetime[0];
                $t_time = $tx_datetime[1];

                $n_date = explode('-', $t_date);
                $nn_date = date('Y-m-d');
                if (count($n_date) > 1) {
                    $nn_date = trim($n_date[1]) . '/' . trim($n_date[2]) . '/' . trim($n_date[0]);
                }
                $to_date_time = date('Y-m-d H:i:s',strtotime($nn_date.' '.$t_time));

            }

            $qty =\backend\models\Stocktrans::find()->where(['product_id'=>$product_id,'activity_type_id'=>28,'company_id'=>$company_id,'branch_id'=>$branch_id])
                ->andFilterWhere(['>=','trans_date',$from_date_time])->andFilterWhere(['<=','trans_date',$to_date_time])->sum('qty');
        }else{
            $qty =\backend\models\Stocktrans::find()->where(['product_id'=>$product_id,'activity_type_id'=>28,'company_id'=>$company_id,'branch_id'=>$branch_id])
                ->andFilterWhere(['>=','date(trans_date)', date('Y-m-d')])->andFilterWhere(['<=','date(trans_date)',date('Y-m-d')])->sum('qty');
        }

        return $qty;
    }
    public static function findCancelqty2($product_id,$from_date,$to_date,$company_id,$branch_id){
        $qty = 0;

        if($from_date !=null){
            $fx_datetime = explode(' ',$from_date);
            $tx_datetime = explode(' ',$to_date);

            $f_date = null;
            $f_time = null;
            $t_date = null;
            $t_time = null;

            $from_date_time = null;
            $to_date_time = null;

            if(count($fx_datetime) > 0){
                $f_date = $fx_datetime[0];
                $f_time = $fx_datetime[1];

                $x_date = explode('-', $f_date);
                $xx_date = date('Y-m-d');
                if (count($x_date) > 1) {
                    $xx_date = trim($x_date[1]) . '/' . trim($x_date[2]) . '/' . trim($x_date[0]);
                }
                $from_date_time = date('Y-m-d H:i:s',strtotime($xx_date.' '.$f_time));
                //$from_date_time = date('Y-m-d',strtotime($xx_date));
            }

            if(count($tx_datetime) > 0){
                $t_date = $tx_datetime[0];
                $t_time = $tx_datetime[1];

                $n_date = explode('-', $t_date);
                $nn_date = date('Y-m-d');
                if (count($n_date) > 1) {
                    $nn_date = trim($n_date[1]) . '/' . trim($n_date[2]) . '/' . trim($n_date[0]);
                }
                $to_date_time = date('Y-m-d H:i:s',strtotime($nn_date.' '.$t_time));

            }

            $qty =\backend\models\Stocktrans::find()->where(['product_id'=>$product_id,'activity_type_id'=>28,'company_id'=>$company_id,'branch_id'=>$branch_id])
                ->andFilterWhere(['>=','trans_date',$from_date_time])->andFilterWhere(['<=','trans_date',$to_date_time])->sum('qty');
        }else{
            $qty =\backend\models\Stocktrans::find()->where(['product_id'=>$product_id,'activity_type_id'=>28,'company_id'=>$company_id,'branch_id'=>$branch_id])
                ->andFilterWhere(['>=','date(trans_date)', date('Y-m-d')])->andFilterWhere(['<=','date(trans_date)',date('Y-m-d')])->sum('qty');
        }

        return $qty;
    }

}
