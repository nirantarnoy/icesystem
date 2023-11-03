<?php

namespace frontend\modules\api\controllers;

use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;

date_default_timezone_set('Asia/Bangkok');

class DailysummaryController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'getbalancein' => ['POST'],
                    'getprodrec' => ['POST'],
                    'getcounting' => ['POST'],
                    'getscrap' => ['POST'],
                    'getcashqty' => ['POST'],
                    'getcreditqty' => ['POST'],
                    'calcloseshift' => ['POST'],
                    'gettransferqty' => ['POST'],
                    'getrepackqty' => ['POST'],
                    'getrefillqty' => ['POST'],
                    'getreprocesscarqty' => ['POST'],
                ],
            ],
        ];
    }

    public function actionGetbalancein()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];
        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\BalanceDaily::find()->orderBy(['product_id' => SORT_ASC])->all();
            if ($model) {
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'qty' => $value->balance_qty,
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionGetprodrec()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getProdDaily($value->id, $login_time, $company_id, $branch_id, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getProdDaily($product_id, $user_login_datetime, $company_id, $branch_id, $user_id)
    {
        $qty = 0;
        $cancel_qty = 0;
        $second_user_id = [];
        if ($product_id != null) {

            $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_DESC])->one();
            if ($model_login) {
                //  $second_user_id = $model_login->second_user_id;
                $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $model_login->id])->all();
                if ($model_user_ref) {
                    foreach ($model_user_ref as $value) {
                        array_push($second_user_id, $value->user_id);
                    }
                }
            }

            if (count($second_user_id) > 0) {
                $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');
                // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s')]])->sum('qty');
            } else {
                $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'created_by' => $user_id, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');
                // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
                $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'created_by' => $user_id, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s')]])->sum('qty');
            }

        }

        return $qty - $cancel_qty; // ลบยอดยกเลิกผลิต
        //return $cancel_qty; // ลบยอดยกเลิกผลิต
    }

    public function actionGetscrap()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getScrapDaily($value->id, $login_time, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getScrapDaily($product_id, $user_login_datetime, $user_id)
    {
        $qty = 0;
        $second_user_id = [];
        if ($product_id != null) {
            $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_DESC])->one();
            if ($model_login) {
                //  $second_user_id = $model_login->second_user_id;
                $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $model_login->id])->all();
                if ($model_user_ref) {
                    foreach ($model_user_ref as $value) {
                        array_push($second_user_id, $value->user_id);
                    }
                }
            }
            if (count($second_user_id) > 0) {
                $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id, 'created_by' => $second_user_id])->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('scrap_line.qty');
            } else {
                $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id, 'created_by' => $user_id])->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('scrap_line.qty');
            }

        }
        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }

    public function actionGetcounting()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getDailycount($value->id, $company_id, $branch_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getDailycount($product_id, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null && $company_id != null && $branch_id != null) {
            $model = \common\models\DailyCountStock::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 0])->andFilterWhere(['date(trans_date)' => date('Y-m-d')])->all();
            if ($model) {
                foreach ($model as $value) {
                    $qty += $value->qty;
                }

            }
        }
        return $qty;
    }

    public function actionGetcashqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' =>$this->getOrderCashQty($value->id, $user_id, $login_time),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionGetcreditqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getOrderCreditQty($value->id, $user_id, $login_time),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getOrderCashQty($product_id, $user_id, $user_login_datetime)
    {
        $qty = 0;
        if ($user_id != null) {
       //     $model = \common\models\SalePosCloseCashQty::find()->select('qty')->where(['user_id' => $user_id, 'product_id' => $product_id])->andFilterWhere(['between', 'start_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->one();
            $model = \common\models\SalePosCloseCashQty::find()->select('qty')->where(['user_id' => $user_id, 'product_id' => $product_id])->andFilterWhere(['>=', 'start_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))])->andFilterWhere(['<=','start_date',date('Y-m-d H:i:s')])->one();
            if ($model) {
                $qty = $model->qty;
            }
        }
        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }

    function getOrderCreditQty($product_id, $user_id, $user_login_datetime)
    {
        $qty = 0;
        $qty2 = 0;
        if ($user_id != null) {
            $model = \common\models\SalePosCloseCreditQty::find()->select('qty')->where(['product_id' => $product_id])->andFilterWhere(['between', 'start_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->one();
            if ($model) {
                $qty = $model->qty;
            }
            $model2 = \common\models\SalePosCloseIssueCarQty::find()->select('qty')->where(['product_id' => $product_id])->andFilterWhere(['between', 'start_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->one();
            if ($model2) {
                $qty2 = $model2->qty;
            }
        }
        return ($qty + $qty2);
    }

    public function actionCalcloseshift()
    {

        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];

        $user_login_datetime = \backend\models\User::findLogintime($user_id);
        $t_date = \Yii::$app->request->post('t_date');

        if ($user_id != null && $company_id && $branch_id) {

            \common\models\SalePosCloseCashQty::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCreditQty::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCashAmount::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCreditAmount::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseIssueCarQty::deleteAll(['user_id' => $user_id]);

            $sql = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_cash";
            $sql .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql .= " AND orders.payment_method_id = 1";
            $sql .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            // $sql .= " AND orders.created_by=181";
            $sql .= " AND orders.created_by=" . $user_id;
            $sql .= " GROUP BY order_line.product_id";

            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $product_id = $model[$i]['product_id'];
                    $qty = $model[$i]['line_total_cash'];

                    if ($product_id != null) {
                        $model_x = new \common\models\SalePosCloseCashQty();
                        $model_x->product_id = $product_id;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }


            $sql2 = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_credit";
            $sql2 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql2 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql2 .= " AND orders.payment_method_id = 2";
            $sql2 .= " AND orders.order_channel_id = 0";
            $sql2 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql2 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            //$sql .= " AND orders.created_by=181";
            $sql2 .= " AND orders.created_by=" . $user_id;
            $sql2 .= " GROUP BY order_line.product_id";

            $query2 = \Yii::$app->db->createCommand($sql2);
            $model2 = $query2->queryAll();
            if ($model2) {
                for ($i = 0; $i <= count($model2) - 1; $i++) {
                    $product_id2 = $model2[$i]['product_id'];
                    $qty2 = $model2[$i]['line_total_credit'];

                    if ($product_id2 != null) {
                        $model_x = new \common\models\SalePosCloseCreditQty();
                        $model_x->product_id = $product_id2;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty2;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }

            $sql20 = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_credit";
            $sql20 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql20 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql20 .= " AND orders.order_channel_id > 0";
            $sql20 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql20 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            $sql20 .= " AND orders.created_by=" . $user_id;
            $sql20 .= " GROUP BY order_line.product_id";

            $query20 = \Yii::$app->db->createCommand($sql20);
            $model20 = $query20->queryAll();
            if ($model20) {
                for ($i = 0; $i <= count($model20) - 1; $i++) {
                    $product_id20 = $model20[$i]['product_id'];
                    $qty20 = $model20[$i]['line_total_credit'];

                    if ($product_id20 != null) {
                        $model_x = new \common\models\SalePosCloseIssueCarQty();
                        $model_x->product_id = $product_id20;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->ent_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty20;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }


            $sql3 = "SELECT order_line.product_id, SUM(order_line.line_total) as line_total_cash";
            $sql3 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql3 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql3 .= " AND orders.payment_method_id = 1";
            $sql3 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql3 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            //$sql .= " AND orders.created_by=181";
            // $sql3 .= " AND orders.created_by=" . $user_id;
            $sql3 .= " GROUP BY order_line.product_id";

            $query3 = \Yii::$app->db->createCommand($sql3);
            $model3 = $query3->queryAll();
            if ($model3) {
                for ($i = 0; $i <= count($model3) - 1; $i++) {
                    $product_id3 = $model3[$i]['product_id'];
                    $amount3 = $model3[$i]['line_total_cash'];

                    if ($product_id3 != null) {
                        $model_x = new \common\models\SalePosCloseCashAmount();
                        $model_x->product_id = $product_id3;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $amount3;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }

            $sql4 = "SELECT order_line.product_id, SUM(order_line.line_total) as line_total_credit";
            $sql4 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql4 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql4 .= " AND orders.payment_method_id = 2";
            $sql4 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql4 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            // $sql .= " AND orders.created_by=181";
            $sql4 .= " AND orders.created_by=" . $user_id;
            $sql4 .= " GROUP BY order_line.product_id";

            $query4 = \Yii::$app->db->createCommand($sql4);
            $model4 = $query4->queryAll();
            if ($model4) {
                for ($i = 0; $i <= count($model4) - 1; $i++) {
                    $product_id4 = $model4[$i]['product_id'];
                    $amount4 = $model4[$i]['line_total_credit'];

                    if ($product_id4 != null) {
                        $model_x = new \common\models\SalePosCloseCreditAmount();
                        $model_x->product_id = $product_id4;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $amount4;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }
            $status = 1;
            array_push($data, ['message' => 'success']);
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionGettransferqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                foreach ($model as $value) {
                    $login_time = \backend\models\User::findLogintime($user_id);
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getProdTransferDaily($value->id, $login_time, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getProdTransferDaily($product_id, $user_login_datetime, $user_id)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['production_type' => 5])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');
        }
        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }

    public function actionGetrepackqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getProdRepackDaily($value->id, $login_time, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getProdRepackDaily($product_id, $user_login_datetime, $user_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [27]])->andFilterWhere(['product_id' => $product_id, 'created_by' => $user_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');
        }

        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }

    public function actionGetreprocesscarqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                foreach ($model as $value) {
                    $login_time = \backend\models\User::findLogintime($user_id);
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getProdReprocessCarDaily($value->id, $login_time, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getProdReprocessCarDaily($product_id, $user_login_datetime, $user_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');

        }
        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }

    public function actionGetrefillqty()
    {
        $company_id = 0;
        $branch_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
            $company_id = $req_data['company_id'];
            $branch_id = $req_data['branch_id'];

        }
        $status = 0;
        $data = [];
        if ($company_id && $branch_id) {
            $model = \common\models\Product::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->all();
            if ($model) {
                $login_time = \backend\models\User::findLogintime($user_id);
                foreach ($model as $value) {
                    array_push($data, [
                        'product_id' => $value->id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $this->getIssueRefillDaily($value->id, $login_time, $user_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    function getIssueRefillDaily($product_id, $user_login_datetime, $user_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 18])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s')])->sum('qty');
        }
        if ($qty == null) {
            $qty = 0;
        }
        return $qty;
    }
}

?>