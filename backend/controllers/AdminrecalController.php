<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CarController implements the CRUD actions for Car model.
 */
class AdminrecalController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $find_date = \Yii::$app->request->post('find_date');
        return $this->render('index', [
            'find_date' => $find_date
        ]);
    }

    public function actionRecalculate()
    {
        $user_login_id = \Yii::$app->request->post('user_login_id');
        $user_id = \Yii::$app->request->post('find_user_id');
        $user_login_datetime = date('Y-m-d H:i:s', strtotime(\Yii::$app->request->post('find_user_login')));
        $t_date = date('Y-m-d H:i:s', strtotime(\Yii::$app->request->post('find_user_logout')));

        // echo $user_login_id;return;
//
//        echo $user_id . " <br />";
//        echo $user_login_datetime . " <br />";
//        echo $t_date . " <br />";
//        return;


        $company_id = 0;
        $branch_id = 0;
        $res = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }


        $line_prod_id = [];
        $line_production_qty = [];
        $line_cash_qty = [];
        $line_credit_qty = [];
        $line_balance_in = [];
        $line_balance_out = [];
        $line_repack_qty = []; // เบิกแปรสภาพ
        $line_refill_qty = [];
        $line_transfer_qty = [];
        $line_reprocess_car_qty = []; // แปรสภาพรถ
        $issue_reprocess_qty = [];
        $line_scrap_qty = [];
        $line_stock_count = [];


        $model = \common\models\QuerySalePosData::find()->select([
            'product_id'
        ])->where(['BETWEEN', 'order_date', $user_login_datetime, $t_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                array_push($line_prod_id, $value->product_id);
                array_push($line_production_qty, $this->getProdDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id, $user_login_id));
                array_push($line_cash_qty, $this->getOrderCashQty($value->product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id));
                array_push($line_credit_qty, $this->getOrderCreditQty($value->product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id));
                array_push($line_balance_in, $this->getBalanceinnew($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id));
                array_push($line_repack_qty, $this->getProdRepackDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id)); // เบิกแปรสภาพ
                array_push($line_refill_qty, $this->getIssueRefillDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id));
                array_push($line_transfer_qty, $this->getProdTransferDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id));
                array_push($line_reprocess_car_qty, $this->getProdReprocessCarDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id)); // รับแปรสภาพรถ
                // $issue_reprocess_qty = $this->getIssueReprocessDaily($value->product_id, $user_login_datetime, $t_date, $default_wh);
                array_push($line_scrap_qty, $this->getScrapDaily($value->product_id, $user_login_datetime, $t_date, $company_id, $branch_id, $user_login_id));
                array_push($line_stock_count, $this->getDailycount($value->product_id, $company_id, $branch_id, $user_login_datetime,$t_date, $user_id));
            }
        }

        // print_r($line_repack_qty);
        if ($this->saveTransactionsalepos($line_prod_id, $line_balance_in, 0, $line_production_qty, $line_cash_qty, $line_credit_qty, $line_repack_qty, $line_refill_qty, $line_scrap_qty, $line_stock_count, $user_login_datetime, $line_transfer_qty, $t_date, $user_id, $line_reprocess_car_qty)) {
            $session = \Yii::$app->session;
            $session->setFlash('after-save', true);
            $session->setFlash('msg', 'บันทึกจบรายการเรียบร้อย');

            // return $this->redirect(['site/logout']);
        }
        echo "ok";
    }

    function saveTransactionsalepos($line_prod_id, $line_balance_in, $line_balance_out, $line_production_qty, $line_cash_qty, $line_credit_qty, $line_repack_qty, $line_refill_qty, $line_scrap_qty, $line_stock_count, $login_date, $line_transfer_qty, $logout_date, $user_id,$line_reprocess_car_qty)
    {
        $company_id = 0;
        $branch_id = 0;
        $res = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        // $user_id = \Yii::$app->user->id;
        // $cal_date = date('Y-m-d',strtotime("2022/06/22"));

      //  $cal_date = date('Y-m-d', strtotime($login_date));
        $cal_date = date('Y-m-d', strtotime($logout_date));


        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionPosSaleSum::deleteAll(['date(trans_date)' => $cal_date, 'user_id' => $user_id]);

        // $user_login_datetime = date('Y-m-d H:i:s', strtotime('2022-06-25 07:30:39'));

        $cur_shift = $this->getTransShift($company_id, $branch_id);
        if ($line_prod_id != null) {
            if (count($line_prod_id)) {
                for ($i = 0; $i <= count($line_prod_id) - 1; $i++) {
                    $model_trans = new \common\models\TransactionPosSaleSum();
                    $model_trans->trans_date = $cal_date;
                    $model_trans->product_id = $line_prod_id[$i];
                    $model_trans->cash_qty = $line_cash_qty[$i];
                    $model_trans->credit_qty = $line_credit_qty[$i];
                    $model_trans->free_qty = 0;
                    $model_trans->balance_in_qty = $line_balance_in[$i];
                    $model_trans->balance_out_qty = 0;
                    $model_trans->prodrec_qty = $line_production_qty[$i];
                    $model_trans->reprocess_qty = $line_repack_qty[$i];
                    $model_trans->return_qty = $line_reprocess_car_qty[$i];//$this->getReturnCarReprocess($line_prod_id[$i], $login_date, $company_id, $branch_id);
                    $model_trans->issue_car_qty = $this->getIssueCarQty($line_prod_id[$i], $user_id, $login_date, $logout_date, $company_id, $branch_id);
                    $model_trans->issue_transfer_qty = $line_transfer_qty[$i];// $this->getTransferout($value->product_id, $cal_date);
                    $model_trans->issue_refill_qty = $line_refill_qty[$i];
                    $model_trans->scrap_qty = $line_scrap_qty[$i];//$this->getScrapDaily($value->product_id, $user_login_datetime, $cal_date);
                    $model_trans->counting_qty = $line_stock_count[$i];
                    $model_trans->shift = $cur_shift;//$this->checkDailyShift($cal_date);
                    $model_trans->company_id = $company_id;
                    $model_trans->branch_id = $branch_id;
                    $model_trans->user_id = $user_id;
                    $model_trans->login_datetime = $login_date;
                    $model_trans->logout_datetime = $logout_date;
                    $model_trans->user_second_id = 10000;
                    if ($model_trans->save(false)) {
                        $res += 1;
                    }
                }
            }
        }

        return $res;
    }

    public function getTransShift($company_id, $branch_id)
    {
        $nums = 1;
        $model = \common\models\TransactionPosSaleSum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->max('shift');
        if ($model) {
            $nums = $model + 1;
        }
        return $nums;
    }

    function getReturnCarReprocess($product_id, $login_date, $company_id, $branch_id)
    {
        $good_qty = 0;
        $model = \backend\models\Stocktrans::find()->where(['product_id' => $product_id, 'activity_type_id' => 26])->
        andFilterWhere(['>=', 'date(trans_date)', date('Y-m-d', strtotime($login_date))])->
        andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        if ($model) {
            $good_qty = $model;
        }
        return $good_qty;
    }

    function getBalancein($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $model = \common\models\BalanceDaily::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
                $qty = $model->balance_qty;
            }
        }

        return $qty;
    }

    function getBalanceinnew($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $new_shift = 0;
            $cur_shift = $this->getTransShift($company_id, $branch_id);
            if($cur_shift){
                $new_shift = ($cur_shift -1);
            }
         //   $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id,'shift'=>$new_shift])->one();
            $model = \common\models\TransactionPosSaleSum::find()->where(['product_id' => $product_id,'date(trans_date)'=>date('Y-m-d',strtotime($user_login_datetime))])->andFilterWhere(['<','shift',$cur_shift])->orderBy(['id'=>SORT_DESC])->one();
            if ($model) {
                $qty = $model->counting_qty;
            }
        }

        return $qty;
    }

    function getProdDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id, $login_id)
    {
        $qty = 0;
        $cancel_qty = 0;
        $second_user_id = [];
        if ($product_id != null) {

//            $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_DESC])->one();
//            if ($model_login) {
            //  $second_user_id = $model_login->second_user_id;
            $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $login_id])->all();
            if ($model_user_ref) {
                foreach ($model_user_ref as $value) {
                    array_push($second_user_id, $value->user_id);
                }
            }
//            }

            if (count($second_user_id) > 0) {
                $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s', strtotime($t_date))]])->sum('qty');
            } else {
                $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s', strtotime($t_date))]])->sum('qty');
            }

        }

        return $qty - $cancel_qty; // ลบยอดยกเลิกผลิต
        //return $cancel_qty; // ลบยอดยกเลิกผลิต
    }

    function getProdRepackDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [27]])
                ->andFilterWhere(['product_id' => $product_id])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }


    function getProdReprocessCarDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26]])->andFilterWhere(['product_id' => $product_id])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }

    function getProdTransferDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['production_type' => 5])
                ->andFilterWhere(['product_id' => $product_id])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }

    function getIssueRefillDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 18])
                ->andFilterWhere(['product_id' => $product_id])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }

    function getIssueReprocessDaily($product_id, $user_login_datetime, $t_date, $default_wh, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 20])
                ->andFilterWhere(['product_id' => $product_id, 'warehouse_id' => $default_wh])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }

    function getScrapDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id, $user_login_id)
    {
//        $qty = 0;
//        if ($product_id != null) {
//            $data = [];
//            $model = \common\models\LoginUserRef::find()->where(['login_log_cal_id' => $user_login_id])->all();
//            if ($model) {
//                foreach ($model as $value) {
//                    array_push($data, $value->user_id); // second user
//                }
//            }
//            if (count($data) > 0) {
//                $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id])
//                    ->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
//                    ->andFilterWhere(['scrap.company_id' => $company_id, 'scrap.branch_id' => $branch_id])->andFilterWhere(['in', 'scrap.created_by', $data])->sum('scrap_line.qty');
//            }
//
//        }
//
//        return $qty;
        $qty = 0;
        $second_user_id = [];
        if ($product_id != null) {
            $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_login_id])->orderBy(['id' => SORT_DESC])->one();
            if ($model_login) {
                //  $second_user_id = $model_login->second_user_id;
                $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $model_login->id])->all();
                if ($model_user_ref) {
                    foreach ($model_user_ref as $value) {
                        array_push($second_user_id, $value->user_id);
                    }
                }
            }
            if(count($second_user_id) >0){
                $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id,'created_by'=>$second_user_id])->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('scrap_line.qty');
            }else{
                $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id])->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('scrap_line.qty');
            }

        }

        return $qty;
    }

//function getOrderCashQty($product_id, $user_id, $user_login_datetime, $t_date)
//{
//    $qty = 0;
//    if ($user_id != null) {
//        $qty = \common\models\QuerySaleDataSummary::find()->where(['created_by' => $user_id, 'product_id' => $product_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['LIKE', 'name', 'สด'])->sum('qty');
//    }
//
//    return $qty;
//}
    function getOrderCashQty($product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($user_id != null) {
            $qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id, 'product_id' => $product_id])
                ->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('line_qty_cash');
        }
        return $qty;
    }

    function getOrderCreditQty($product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($user_id != null) {
            $qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id, 'product_id' => $product_id])
                ->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'payment_method_id'=>2])->andFilterWhere(['order_channel_id'=>0])->sum('line_qty_credit');
           // ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'payment_method_id'=>2])->andFilterWhere(['is', 'order_channel_id', new \yii\db\Expression('null')])->sum('line_qty_credit');
        }
        return $qty;
    }

    function getIssueCarQty($product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($user_id != null) {
            $qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id, 'product_id' => $product_id])
                ->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'payment_method_id'=>2])->andFilterWhere(['>', 'order_channel_id', 0])->sum('line_qty_credit');
           // ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'payment_method_id'=>2])->andFilterWhere(['is not', 'order_channel_id', new \yii\db\Expression('null')])->sum('line_qty_credit');
        }
        return $qty;
    }


//function getBalancein($product_id)
//{
//    $data = [];
//    if ($product_id != null) {
//        $model = \common\models\SaleBalanceOut::find()->where(['product_id' => $product_id, 'status' => 1])->one();
//        if ($model) {
//            array_push($data, ['id' => $model->id, 'qty' => $model->balance_out]);
//        }
//    }
//    return $data;
//}

    function getDailycount($product_id, $company_id, $branch_id,$user_login_datetime, $t_date, $user_login_id)
    {
        $qty = 0;
        if ($product_id != null) {
//            $data = [];
//            $model = \common\models\LoginUserRef::find()->where(['login_log_cal_id' => $user_login_id])->all();
//            if ($model) {
//                foreach ($model as $value) {
//                    array_push($data, $value->user_id); // second user
//                }
//            }
//            if (count($data) > 0) {
                $model = $model = \common\models\DailyCountStock::find()->where(['product_id' => $product_id, 'user_id' => $user_login_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->one();
           // }
            if($model){
                $qty = $model->qty;
            }

        }

        return $qty;
//        $qty = 0;
//        if ($product_id != null && $company_id != null && $branch_id != null) {
//            $model = \common\models\DailyCountStock::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['date(trans_date)' => date('Y-m-d', strtotime($t_date))])->all();
//            if ($model) {
//                foreach ($model as $value) {
////                $production_bill_id = \backend\models\Stockjournal::findProdrecId($value->journal_no,$company_id,$branch_id);
////                $model_sale_after_count_qty = \common\models\IssueStockTemp::find()->where(['prodrec_id'=>$production_bill_id,'product_id'=>$product_id])->sum('qty');
////                $qty += ($value->qty - $model_sale_after_count_qty);
//
//                    $qty += $value->qty;
//                }
//
//            }
//        }
//        return $qty;
    }
}