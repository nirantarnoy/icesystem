<?php

namespace frontend\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;


class PaymentreceiveController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['POST'],
                    'paymentdaily' => ['POST'],
                    'addpay' => ['POST'],
                    'addpay2' => ['POST'],
                    'deletepay' => ['POST'],
                    'paymentcancel' => ['POST'],
                    'paymenthistory' => ['POST'],
                    'paymenthistorypos' => ['POST'],
                    'paymentcustomerlist' => ['POST'],
                    'poslist'=>['POST'],
                    'paymentposdaily' => ['POST'],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $customer_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];

        $data = [];
        $status = false;
        $model = null;
        if ($customer_id != null) {
            // $model = \common\models\JournalIssue::find()->one();

            // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $customer_id, 'sale_payment_method_id' => 2])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->all();

//            $sql = "select t1.id as order_id,t1.order_no,t1.order_date,sum(t2.line_total) AS remain_amt, t2.customer_id";
//            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t1.id = t2.order_id ";
//            $sql .= " WHERE  t2.customer_id =" . $customer_id;
//            $sql .= " AND t1.payment_status !=1";
//            $sql .= " AND t1.payment_method_id = 2";
//            $sql .= " AND t1.status != 3";
//            $sql .= " AND t1.sale_from_mobile = 1";
//            $sql .= " AND year(t1.order_date)>=2023";
//            $sql .= " AND month(t1.order_date)>=01";
//            $sql .= " GROUP BY t1.id";
//            $sql .= " ORDER BY t1.id DESC";

//            $sql = "SELECT t1.customer_id,t1.order_id,t1.order_date,t1.line_total,SUM(t2.payment_amount)as payment_amount, t1.line_total - SUM(t2.payment_amount) as remain_amount";
//            $sql .= " FROM query_sale_by_customer_car as t1 INNER JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
//            $sql .= " WHERE t1.customer_id=" . $cus_id;
//            $sql .= " AND t1.payment_method_id=2";
//            $sql .= " GROUP BY t1.customer_id,t1.order_id";
//            $sql .= " ORDER BY t1.order_id";


            $sql = "SELECT t1.customer_id,t1.order_id,t1.order_date,t1.line_total,SUM(t2.payment_amount)as payment_amount, t1.line_total - SUM(t2.payment_amount) as remain_amt";
            $sql .= " FROM query_sale_by_customer_car as t1 LEFT JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
            $sql .= " WHERE t1.customer_id=" . $customer_id;
            $sql .= " AND t1.payment_method_id=2";
            // $sql .= " AND t1.payment_status=0";
            $sql .= " AND date(t1.order_date) >='2022-06-01'";
            $sql .= " GROUP BY t1.customer_id,t1.order_id";
            $sql .= " ORDER BY t1.order_id";

            $sql_query = \Yii::$app->db->createCommand($sql);
            $model = $sql_query->queryAll();

            if ($model) {
                $status = true;
                for ($x=0;$x<=count($model)-1;$x++) {
                    //if($model[$x]['remain_amt'] <= 0 || $model[$x]['remain_amt'] == null)continue;
//                    if($model[$x]['remain_amt'] <= 0 )continue;
                    //     $xtotal = $value->payment_amount == null ? 0: $value->line_total;
                    $remain_new_amt = 0;
                    if($model[$x]['remain_amt'] == null && $model[$x]['payment_amount'] == null){
                        $remain_new_amt = $model[$x]['line_total'];
                    }else{
                        if($model[$x]['remain_amt'] <= 0 )continue;
                        $remain_new_amt = $model[$x]['remain_amt'];
                    }

                    array_push($data, [
                        'order_id' => $model[$x]['order_id'],
                        'order_no' => \backend\models\Orders::getNumber($model[$x]['order_id']),
                        'customer_id' => $model[$x]['customer_id'],
                        'customer_code' => \backend\models\Customer::findName($model[$x]['customer_id']),
                        'order_date' => $model[$x]['order_date'],
                        'line_total' => (float)$remain_new_amt,
                        //'payment_amount' => $value->payment_amount,
                        'remain_amount' => (float)$remain_new_amt,
                        //'pay_type' => $value->pay_type,
                    ]);
                }
//                foreach ($model as $value) {
//                    //     $xtotal = $value->payment_amount == null ? 0: $value->line_total;
//                    $remain_amt = $value->line_total;
//
//                    if ($value->remain_amount == null && $value->payment_amount != null) {
//                        $remain_amt = $value->line_total - $value->payment_amount;
//                    }
//                    array_push($data, [
//                        'order_id' => $value->order_id,
//                        'order_no' => \backend\models\Orders::getNumber($value->order_id),
//                        'customer_id' => $value->customer_id,
//                        'customer_code' => \backend\models\Customer::findName($value->customer_id),
//                        'order_date' => $value->order_date,
//                        'line_total' => (int)$value->line_total,
//                        //'payment_amount' => $value->payment_amount,
//                        'remain_amount' => (int)$remain_amt,
//                        //'pay_type' => $value->pay_type,
//                    ]);
//                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionPoslist()
    {
        $customer_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];

        $data = [];
        $status = false;
        $model = null;
        if ($customer_id != null) {

            $sql = "SELECT t1.customer_id,t1.order_id,t1.order_date,t1.line_total,SUM(t2.payment_amount)as payment_amount, t1.line_total - SUM(t2.payment_amount) as remain_amt";
            $sql .= " FROM query_sale_by_customer_pos as t1 LEFT JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
            $sql .= " WHERE t1.customer_id=" . $customer_id;
            $sql .= " AND t1.payment_method_id=2";
            $sql .= " AND (t1.payment_status=0 OR t1.payment_status is null )";
            $sql .= " AND date(t1.order_date) >='2023-08-01'";
            $sql .= " GROUP BY t1.customer_id,t1.order_id";
            $sql .= " ORDER BY t1.order_id";

            $sql_query = \Yii::$app->db->createCommand($sql);
            $model = $sql_query->queryAll();

            if ($model) {
                $status = true;
                for ($x=0;$x<=count($model)-1;$x++) {
                    //if($model[$x]['remain_amt'] <= 0 || $model[$x]['remain_amt'] == null)continue;
//                    if($model[$x]['remain_amt'] <= 0 )continue;
                    //     $xtotal = $value->payment_amount == null ? 0: $value->line_total;
                    $remain_new_amt = 0;
                    if($model[$x]['remain_amt'] == null && $model[$x]['payment_amount'] == null){
                        $remain_new_amt = $model[$x]['line_total'];
                    }else{
                        if($model[$x]['remain_amt'] <= 0 )continue;
                        $remain_new_amt = $model[$x]['remain_amt'];
                    }

                    array_push($data, [
                        'order_id' => $model[$x]['order_id'],
                        'order_no' => \backend\models\Orders::getNumber($model[$x]['order_id']),
                        'customer_id' => $model[$x]['customer_id'],
                        'customer_code' => \backend\models\Customer::findName($model[$x]['customer_id']),
                        'order_date' => $model[$x]['order_date'],
                        'line_total' => (float)$remain_new_amt,
                        //'payment_amount' => $value->payment_amount,
                        'remain_amount' => (float)$remain_new_amt,
                        //'pay_type' => $value->pay_type,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddpay()
    {
        $order_id = 0;
        $payment_channel_id = 0;
        $customer_id = 0;
        $pay_amount = 0;
        $pay_date = null;
        $company_id = 1;
        $branch_id = 1;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $order_id = $req_data['order_id'];
        $payment_channel_id = $req_data['payment_channel_id'];
        $customer_id = $req_data['customer_id'];
        $pay_amount = $req_data['pay_amount'];
        $pay_date = $req_data['pay_date'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $user_id = $req_data['user_id'];

        $xdate = explode('-', trim($pay_date));
        $t_date = date('Y-m-d');
        if (count($xdate) > 1) {
            $t_date = $xdate[2] . '/' . $xdate[1] . '/' . $xdate[0];
        }
        $data = [];
        $status = false;
        if ($customer_id && $order_id) {
            //  $t_date = date('Y-m-d');

            $xdate = explode('-', trim($pay_date));
            $t_date = date('Y-m-d');
            if (count($xdate) > 1) {
                $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
            }

            $check_record = $this->checkHasRecord($customer_id, $t_date);
            if ($check_record != null) {
                //if(count($check_record) > 0){
                $model_line = new \common\models\PaymentReceiveLine();
                $model_line->payment_receive_id = $check_record->id;
                $model_line->order_id = $order_id;
                $model_line->payment_amount = $pay_amount;
                $model_line->payment_channel_id = $payment_channel_id;
                $model_line->status = 1;
                if ($model_line->save(false)) {
                    $status = true;
                    \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$order_id]);
                    //$this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                }
                // }
            } else {
                $model = new \backend\models\Paymentreceive();
                $model->trans_date = date('Y-m-d H:i:s', strtotime($t_date . ' ' . date('H:i:s')));//date('Y-m-d H:i:s');
                $model->customer_id = $customer_id;
                $model->journal_no = $model->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
                $model->status = 1;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->crated_by = $user_id;

                if ($model->save()) {
                    $model_line = new \common\models\PaymentReceiveLine();
                    $model_line->payment_receive_id = $model->id;
                    $model_line->order_id = $order_id;
                    $model_line->payment_amount = $pay_amount;
                    $model_line->payment_channel_id = $payment_channel_id;
                    $model_line->status = 1;
                    if ($model_line->save(false)) {
                        $status = true;
                        \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$order_id]);
                        //$this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                    }
                }
            }

        }
        // array_push($data,['date'=>$t_date]);

        return ['status' => $status, 'data' => $data];
    }

    public function checkHasRecord($customer_id, $trans_date,$user_id)
    {
        $model = \common\models\PaymentReceive::find()->where(['date(trans_date)' => $trans_date, 'customer_id' => $customer_id,'crated_by'=>$user_id])->one();
        return $model;
    }

    public function updatePaymenttransline($customer_id, $order_id, $pay_amt, $pay_type)
    {
        if ($customer_id != null && $order_id != null && $pay_amt > 0) {
            //     $model = \backend\models\Paymenttransline::find()->where(['customer_id'=>$customer_id,'order_ref_id'=>$order_id])->andFilterWhere(['payment_method_id'=>2])->one();
            $model = \backend\models\Paymenttransline::find()->innerJoin('payment_method', 'payment_trans_line.payment_method_id=payment_method.id')->where(['payment_trans_line.customer_id' => $customer_id, 'payment_trans_line.order_ref_id' => $order_id])->andFilterWhere(['payment_method.pay_type' => 2])->one();
            if ($model) {
                if ($pay_type == 0) {
                    $model->payment_amount = ($model->payment_amount - (float)$pay_amt);
                } else {
                    $model->payment_amount = ($model->payment_amount + (float)$pay_amt);
                }

                $model->save(false);
            }
        }
    }


    public function actionAddpay2()
    {
        $order_id = 0;
        $payment_channel_id = 1;

        $pay_amount = 0;
        $pay_date = null;
        $company_id = 0;
        $branch_id = 0;
        $user_id = null;
        $data_list = null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $payment_channel_id = $req_data['payment_channel_id']; // 1 cash 2 transfer
        $pay_date = $req_data['pay_date'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $user_id = $req_data['user_id'];
        $data_list = $req_data['data'];


        $xdate = explode('-', trim($pay_date));
        $t_date = date('Y-m-d');
        if (count($xdate) > 1) {
            $t_date = $xdate[2] . '/' . $xdate[1] . '/' . $xdate[0];
        }
        $data = [];
        $status = false;

        if($user_id == null){
            $user_id = 1;// admin
        }

        if ($company_id != null && $branch_id != null && $pay_date != null && $data_list != null && $user_id != null) {
            if (count($data_list) > 0) {
                for ($i = 0; $i <= count($data_list) - 1; $i++) {
                    if ($data_list[$i]['order_id'] == null || $data_list[$i]['pay_amount'] == null || $data_list[$i]['pay_amount'] <= 0) continue;
                    $customer_id = 0;
                    $customer_id = $data_list[$i]['customer_id'];
                    $order_id = $data_list[$i]['order_id'];
                    $pay_amount = $data_list[$i]['pay_amount'];

//                    $check_has_order_pay = \common\models\PaymentReceiveLine::find()->where(['order_id'=>$data_list[$i]['order_id'],'payment_method_id'=>2,'payment_amount'=>$pay_amount])->one();
//                    if($check_has_order_pay)continue;

                    $check_record = $this->checkHasRecord($customer_id, $t_date,$user_id);
                    if ($check_record != null) {
                        //if(count($check_record) > 0){
                        $model_line = new \common\models\PaymentReceiveLine();
                        $model_line->payment_receive_id = $check_record->id;
                        $model_line->order_id = $order_id;
                        $model_line->payment_amount = $pay_amount;
                        $model_line->payment_channel_id = $payment_channel_id;
                        $model_line->payment_method_id = 2; // 2 เชื่อ
                        $model_line->status = 1;
                        if ($model_line->save(false)) {
                            $status = true;
                            // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                            $data = ['pay successfully'];
                            $check_record->trans_date = date('Y-m-d H:i:s');
                            $check_record->crated_by = $user_id;
                            $check_record->save(false);
                        }
                        // }
                    } else {
                        $model = new \backend\models\Paymentreceive();
                        $model->trans_date = date('Y-m-d H:i:s');// date('Y-m-d H:i:s', strtotime($t_date . ' ' . date('H:i:s')));//date('Y-m-d H:i:s');
                        $model->customer_id = $customer_id;
                        $model->journal_no = $model->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
                        $model->status = 1;
                        $model->company_id = $company_id;
                        $model->branch_id = $branch_id;
                        $model->created_at = time();
                        $model->crated_by = $user_id;
                        if ($model->save()) {
                            $model_line = new \common\models\PaymentReceiveLine();
                            $model_line->payment_receive_id = $model->id;
                            $model_line->order_id = $order_id;
                            $model_line->payment_amount = $pay_amount;
                            $model_line->payment_channel_id = $payment_channel_id;
                            $model_line->payment_method_id = 2; // 2 เชื่อ
                            $model_line->status = 1;
                            if ($model_line->save(false)) {
                                $status = true;
                                // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                                $data = ['pay successfully'];
                            }
                        }
                    }
                    \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$order_id]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionDeletepay()
    {
        $status = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $id = $req_data['id'];

        $data = [];
        if ($id) {
            if (\common\models\PaymentReceiveLine::deleteAll(['id' => $id])) {
                $status = true;
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionPaymentdaily()
    {
        $route_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];

        $data = [];
        $status = false;
        if ($route_id != null) {
            // $model = \common\models\JournalIssue::find()->one();
// old           $model = \common\models\QueryPaymentReceive::find()->where(['route_id' => $route_id, 'payment_method_id' => 2])->andFilterWhere(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['!=','status',100])->sum('payment_amount');
            $pay_amount = 0;

            $sql = "SELECT SUM(t1.payment_amount) as pay_amount";
            $sql .= " FROM query_payment_receive as t1 INNER JOIN customer as t2 ON t1.customer_id = t2.id";
            $sql .= " WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d') . "'" . " ";
            $sql .= " AND t1.payment_method_id = 2";
            $sql .= " AND t1.order_status = 1";
            $sql .= " AND t2.delivery_route_id=" . $route_id;
            $sql .= " GROUP BY t2.delivery_route_id";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $pay_amount = $model[$i]['pay_amount'];
                }
            }
            //return $pay_amount;



            $order_close_count = \backend\models\Orders::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'status' => 100])->count();
            array_push($data, [
                'payment_amount' => $pay_amount == null ? 0 : $pay_amount,
                'order_close_status' => 0, //$order_close_count,
            ]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionPaymentposdaily()
    {
        $user_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;
        if ($user_id != null) {
            // $model = \common\models\JournalIssue::find()->one();
// old           $model = \common\models\QueryPaymentReceive::find()->where(['route_id' => $route_id, 'payment_method_id' => 2])->andFilterWhere(['date(trans_date)' => date('Y-m-d')])->andFilterWhere(['!=','status',100])->sum('payment_amount');
            $pay_amount = 0;

            $sql = "SELECT SUM(t1.payment_amount) as pay_amount";
            $sql .= " FROM query_payment_receive as t1";
            $sql .= " WHERE  date(t1.trans_date) =" . "'" . date('Y-m-d') . "'" . " ";
            $sql .= " AND t1.payment_method_id = 2";
            $sql .= " AND t1.order_status = 1";
            $sql .= " AND t1.crated_by=" . $user_id;
            $sql .= " GROUP BY t1.crated_by";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $pay_amount = $model[$i]['pay_amount'];
                }
            }
            //return $pay_amount;


          //  $order_close_count = \backend\models\Orders::find()->where(['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'status' => 100])->count();
            array_push($data, [
                'payment_amount' => $pay_amount == null ? 0 : $pay_amount,
                'order_close_status' => 0, //$order_close_count,
            ]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionPaymenthistory()
    {
        $route_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];

        $data = [];
        $status = false;
        $total = 0;
        if ($route_id != null) {
            // $model = \common\models\JournalIssue::find()->one();
            $model = \common\models\QuerySaleCustomerPaySummary::find()->where(['route_id' => $route_id, 'status' => 1,'pay_type'=>2, 'date(payment_date)'=>date('Y-m-d')])->andFilterWhere(['>','payment_amount',0])->all();
          //  $model = \common\models\QuerySaleCustomerPaySummary::find()->where(['route_id' => 884, 'status' => 1])->andFilterWhere(['>','payment_amount',0])->limit(5)->all();
            //   $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $customer_id])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                   // $total+= $value->payment_amount;
                    array_push($data, [
                        'payment_id' => $value->payment_id,
                        'order_id' => $value->order_ref_id,
                        'customer_name' => $value->cus_name,
                        'customer_id' => $value->customer_id,
                        'journal_no' => $value->journal_no,
                        'journal_date' => $value->payment_date,
                        'amount' => (int)$value->payment_amount,
                        'status' => 1,
                        'order_no' =>\backend\models\Orders::getNumber($value->order_ref_id),
                        'order_date' => date('Y-m-d H:i:s', strtotime(\backend\models\Orders::getOrderdate($value->order_ref_id))),
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionPaymenthistorypos()
    {
        $user_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;
        $total = 0;
        if ($user_id != null) {
            // $model = \common\models\JournalIssue::find()->one();
            $model = \common\models\QuerySaleCustomerPaySummary::find()->where(['crated_by' => $user_id, 'status' => 1,'pay_type'=>2,'order_status'=>1, 'date(payment_date)'=>date('Y-m-d')])->andFilterWhere(['>','payment_amount',0])->all();
            //  $model = \common\models\QuerySaleCustomerPaySummary::find()->where(['route_id' => 884, 'status' => 1])->andFilterWhere(['>','payment_amount',0])->limit(5)->all();
            //   $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $customer_id])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $total+= $value->payment_amount;
                    array_push($data, [
                        'payment_id' => $value->payment_id,
                        'order_id' => $value->order_ref_id,
                        'customer_name' => $value->cus_name,
                        'customer_id' => $value->customer_id,
                        'journal_no' => $value->journal_no,
                        'journal_date' => $value->payment_date,
                        'amount' => (int)$value->payment_amount,
                        'status' => 1,
                        'order_no' =>\backend\models\Orders::getNumber($value->order_ref_id),
                        'order_date' => date('Y-m-d H:i:s', strtotime(\backend\models\Orders::getOrderdate($value->order_ref_id))),
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionPaymentcancel()
    {
        $status = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $id = $req_data['payment_id'];
        $order_id = $req_data['order_id'];
        $cancel_amount = $req_data['amount'];

        $data = [];
        if ($id && $order_id) {
            //if (\common\models\PaymentReceive::updateAll(['status'=>100],['id' => $id])) {
                $model = \common\models\PaymentReceiveLine::find()->where(['payment_receive_id'=>$id,'order_id'=>$order_id])->andFilterWhere(['>','payment_amount',0])->one();
                if($model){
                   // $model->payment_amount = ($model->payment_amount - $cancel_amount);
                    $model->payment_amount = 0;
                    $model->status = 3;
                    $model->save(false);
                }
                $status = true;
                array_push($data,['message'=>'Cancel completed']);
           // }
        }
        return ['status' => $status, 'data' => $data];
    }
    public function actionPaymentcustomerlist(){
        $route_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];

        $data = [];
        $status = false;
        if ($route_id != null) {
//            $sql = "select t2.customer_id, t3.name";
//            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t1.id = t2.order_id INNER JOIN customer as t3 ON t2.customer_id = t3.id";
//            $sql .= " WHERE  t1.order_channel_id =" . $route_id;
//            $sql .= " AND t1.payment_status !=1";
//            $sql .= " AND t1.payment_method_id = 2";
//            $sql .= " AND t1.status != 3";
//            $sql .= " AND t1.sale_from_mobile = 1";
//            $sql .= " AND year(t1.order_date)>=2022";
//            $sql .= " AND month(t1.order_date)>=01";
//            $sql .= " GROUP BY t2.customer_id";

//            $sql = "SELECT t1.customer_id,t1.order_id,t1.line_total,SUM(t2.payment_amount)as pay_amount";
//            $sql .= " FROM query_sale_by_customer_car as t1 INNER JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
//            $sql .= " WHERE t1.order_channel_id=" . $route_id;
//            $sql .= " AND t1.payment_method_id=2";
//            // $sql .= " AND t1.payment_status=0";
//            $sql .= " AND date(t1.order_date) >='2022-01-01'";
//           // $sql .= " AND t1.line_total > SUM(t2.payment_amount)";
//            $sql .= " GROUP BY t1.customer_id";
//            $sql .= " ORDER BY t1.customer_id";
//
//            $sql_query = \Yii::$app->db->createCommand($sql);
//            $model = $sql_query->queryAll();

            $sql = "SELECT t2.id,t2.customer_id, sum(t2.line_total_credit) as total_credit
             FROM query_sale_mobile_data_new2 as t2
             WHERE  date(t2.order_date) >='2023-01-01'
             AND t2.route_id =" . $route_id . " 
             AND t2.payment_method_id = 2";


            $sql .= " GROUP BY t2.customer_id";
            $sql .= " ORDER BY t2.customer_id";

            $sql_query = \Yii::$app->db->createCommand($sql);
            $model = $sql_query->queryAll();

            if ($model) {
                $status = true;
                for ($x = 0; $x <= count($model) - 1; $x++) {
                    $total_pay = $this->getPaytrans($model[$x]['id'], $model[$x]['customer_id']);

                    if ((double)$model[$x]['total_credit'] - (double)$total_pay <= 0) {
                        continue;
                    }
                        array_push($data, [
                            'customer_id' => $model[$x]['customer_id'],
                            'customer_name' => \backend\models\Customer::findName($model[$x]['customer_id']),
                            'remain' => 0,

                        ]);


                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }
    function getPaytrans($order_id, $customer_id)
    {
        $pay_total = 0;
        if ($order_id) {
            $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->sum('payment_amount');
            $pay_total = $model;
        }
        return $pay_total;
    }
}
