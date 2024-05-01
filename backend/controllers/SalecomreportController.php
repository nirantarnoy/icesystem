<?php

namespace backend\controllers;

use backend\models\SalereportSearch;
use \Yii;
use backend\models\SalecomSearch;
use yii\web\Controller;

class SalecomreportController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {

        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        //  $find_route_id = \Yii::$app->request->post('find_route_id');
        $find_emp_id = \Yii::$app->request->post('find_emp_id');
        return $this->render('_comsale_by_emp_daily', [ //_comsale_by_emp_daily_bt
       //return $this->render('_comsale_by_emp_daily', [ //
           'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'find_emp_id' => $find_emp_id
        ]);

    }
    public function actionIndex3()
    {

        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        //  $find_route_id = \Yii::$app->request->post('find_route_id');
        $find_emp_id = \Yii::$app->request->post('find_emp_id');
//        return $this->render('_comsale_by_emp_daily_bt', [ //_comsale_by_emp_daily
//            'from_date' => $from_date,
//            'to_date' => $to_date,
//            'company_id' => $company_id,
//            'branch_id' => $branch_id,
//            'find_emp_id' => $find_emp_id
//        ]);
        return $this->render('_comsale_by_emp_daily_summary_bt', [ //_comsale_by_emp_daily

            'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'find_emp_id' => $find_emp_id
        ]);
    }
//    public function actionComsale(){
//        $company_id = 0;
//        $branch_id = 0;
//
//        if (!empty(\Yii::$app->user->identity->company_id)) {
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if (!empty(\Yii::$app->user->identity->branch_id)) {
//            $branch_id = \Yii::$app->user->identity->branch_id;
//        }
//        $from_date = \Yii::$app->request->post('from_date');
//        $to_date = \Yii::$app->request->post('to_date');
//        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
//        $find_route_id = \Yii::$app->request->post('find_route_id');
//        return $this->render('_comsale', [
//            'from_date' => $from_date,
//            'to_date'=> $to_date,
//            'company_id' =>$company_id,
//            'branch_id'=>$branch_id,
//            'find_route_id'=>$find_route_id
//        ]);
//    }

    public function actionIndex2()
    {

        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        //  $find_route_id = \Yii::$app->request->post('find_route_id');
        $find_emp_id = \Yii::$app->request->post('find_emp_id');
        return $this->render('_comsale_by_emp_daily_update_bt', [ //_comsale_by_emp_daily_update_bt
            'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'find_emp_id' => $find_emp_id
        ]);
    }

    public function actionComdailycal()
    {

        $id = \Yii::$app->request->post('select_delivery_route');
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $car_id = 0;
        $emp_1 = 0;
        $emp_2 = 0;


        $total_com_all = 0;
        $total_qty_all = 0;
        $total_special_all = 0;
        $total_com_sum_all = 0;
        $total_amt_all = 0;
        $total_amt = 0;
        $xx = 0;

        $route_emp_count = 0;

        $model_order_mobile = \common\models\Orders::find()->select(['id', 'order_date', 'order_channel_id', 'car_ref_id', 'emp_1', 'emp_2', 'car_ref_id'])
            ->Where(['company_id' => $company_id, 'branch_id' => $branch_id, 'order_channel_id' => $id])
            ->andFilterWhere(['date(order_date)' => date('Y-m-d')])
            ->andFilterWhere(['sale_from_mobile' => 1])->all();
        // print_r($model_order_mobile);return;
        // echo count($model_order_mobile); return;
        foreach ($model_order_mobile as $value) {
            $xx++;
            $com_rate = 0;
            $route_emp_count = 0;

            $car_id = $value->car_ref_id;
            $emp_1 = $value->emp_1;
            $emp_2 = $value->emp_2;

            $route_total = null;
            $route_name = \backend\models\Deliveryroute::findName($value->order_channel_id);

            $order_data = null;
            if (substr($route_name, 0, 2) == 'CJ') {
                $order_data = $this->getOrderlineCJ($value->id, $company_id, $branch_id);
            } else {
                $order_data = $this->getOrderline($value->id, $company_id, $branch_id);
            }

            if ($order_data == null) continue;
            // print_r($order_data);return;


            $com_rate = 0;
            if ($emp_1 > 0) {
                $route_emp_count += 1;
            }
            if ($emp_2 > 0) {
                $route_emp_count += 1;
            }
//            if ((double)$order_data[0]['emp_1'] > 0) {
//                $route_emp_count += 1;
//            }
//            if ((double)$order_data[0]['emp_2'] > 0) {
//                $route_emp_count += 1;
//            }
            $com_rate = $this->getComRate($route_emp_count, $company_id, $branch_id);

            $total_qty_all = $total_qty_all + (double)$order_data[0]['total_qty'];
            $total_amt_all = $total_amt_all + (double)$order_data[0]['total_amt'];

            $line_com = 0;

            if (substr($route_name, 0, 2) == 'CJ') {
                if ($route_emp_count == 1) {
                    $line_com = (($order_data[0]['total_qty'] * $com_rate) * 1.75);
                } else {
//                  $line_com = $order_data[0]['total_qty'] * $com_rate;
                    $line_com = ($order_data[0]['total_qty'] * 0.50);
                }

            } else {
                $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
                $not_p2_qty = $order_data[0]['total_qty'];
                if ($order_data_p2 != null) {
                    $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];
                    $line_com = ($not_p2_qty * $com_rate) + ($order_data_p2[0]['total_qty'] * 1.75);

                } else {
                    $line_com = $order_data[0]['total_qty'] * $com_rate;
                }


                // $line_com = $order_data[0]['total_qty'] * $com_rate;
            }


            $total_amt = ($total_amt + $order_data[0]['total_amt']);

            $line_com_total = $line_com;

            $total_com_all = $total_com_all + $line_com_total;

        }

        //echo $xx;return;

        \common\models\ComDailyCal::deleteAll(['date(trans_date)' => date('Y-m-d'), 'route_id' => $id]);

        $line_special = 0;
        $extra_data = $this->getComspecial(date('Y-m-d'), date('Y-m-d'));

        if ($extra_data != null) {
            // echo $route_emp_count.' '. $total_amt.' '. $extra_data[0]['sale_price'];return;
            $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? (float)$extra_data[0]['com_extra'] : 0;
            $total_special_all = $total_special_all + $line_special;
            $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special);
        }

//        $line_special = $total_amt >= 3500 && $route_emp_count == 1 ? 30 : 0;
//        $total_special_all = $total_special_all + $line_special;
//        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special);

        $model_com_daily = new \common\models\ComDailyCal();
        $model_com_daily->trans_date = date('Y-m-d H:i:s');
        $model_com_daily->emp_1 = $emp_1;
        $model_com_daily->emp_2 = $emp_2;
        $model_com_daily->total_qty = $total_qty_all;
        $model_com_daily->total_amt = $total_amt;
        $model_com_daily->line_com_amt = $total_com_all;
        $model_com_daily->line_com_special_amt = $total_special_all;
        $model_com_daily->line_total_amt = $total_com_sum_all;
        $model_com_daily->created_at = time();
        $model_com_daily->route_id = $id;
        $model_com_daily->car_id = $car_id;
        $model_com_daily->company_id = $company_id;
        $model_com_daily->branch_id = $branch_id;
        $model_com_daily->save(false);


        return $this->redirect(['salecomreport/index']);
    }

    public function actionComdailycalbkt()
    {

        $id = \Yii::$app->request->post('select_delivery_route');
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $car_id = 0;
        $emp_1 = 0;
        $emp_2 = 0;


        $total_com_all = 0;
        $total_com_all_2 = 0;
        $total_qty_all = 0;
        $total_special_all = 0;
        $total_com_sum_all = 0;
        $total_amt_all = 0;
        $total_amt = 0;
        $xx = 0;

        $route_emp_count = 0;

        $model_order_mobile = \common\models\Orders::find()->select(['id', 'order_date', 'order_channel_id', 'car_ref_id', 'emp_1', 'emp_2', 'car_ref_id'])
            ->Where(['company_id' => $company_id, 'branch_id' => $branch_id, 'order_channel_id' => $id])
            ->andFilterWhere(['date(order_date)' => date('Y-m-d')])
            ->andFilterWhere(['sale_from_mobile' => 1])->all();
        // print_r($model_order_mobile);return;
        // echo count($model_order_mobile); return;
        foreach ($model_order_mobile as $value) {
            $xx++;
            $com_rate = 0;
            $route_emp_count = 0;

            $car_id = $value->car_ref_id;
            $emp_1 = $value->emp_1;
            $emp_2 = $value->emp_2;

            $route_total = null;
            $route_name = \backend\models\Deliveryroute::findName($value->order_channel_id);

            $order_data = null;
            if (substr($route_name, 0, 2) == 'CJ') {
                $order_data = $this->getOrderlineCJ($value->id, $company_id, $branch_id);
            } else {
                $order_data = $this->getOrderline($value->id, $company_id, $branch_id);
            }

            if ($order_data == null) continue;
            // print_r($order_data);return;


            $com_rate = 0;
            if ($emp_1 > 0) {
                $route_emp_count += 1;
            }
            if ($emp_2 > 0) {
                $route_emp_count += 1;
            }
//            if ((double)$order_data[0]['emp_1'] > 0) {
//                $route_emp_count += 1;
//            }
//            if ((double)$order_data[0]['emp_2'] > 0) {
//                $route_emp_count += 1;
//            }
            $com_rate = $this->getComRateBkt($route_emp_count, $company_id, $branch_id);

            $total_qty_all = $total_qty_all + (double)$order_data[0]['total_qty'];
            $total_amt_all = $total_amt_all + (double)$order_data[0]['total_amt'];

            $line_com = 0;
            $line_com_2 = 0;

            if ($com_rate != null) {
                if (substr($route_name, 0, 2) == 'CJ') {
//                if ($route_emp_count == 1) {
                    $line_com = (($order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate']) * 1.75);
//                } else {
//                  $line_com = $order_data[0]['total_qty'] * $com_rate;
                    if ($route_emp_count == 2) {
                        //    $line_com_2 = ($order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate']);
                        $line_com_2 = ($order_data[0]['total_qty'] * 0.5);
                    }

                } else {
                    $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
                    $not_p2_qty = $order_data[0]['total_qty'];
                    if ($order_data_p2 != null) {
                        $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];

                        $line_com = ($not_p2_qty * $com_rate[0]['emp_1_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
                        if ($route_emp_count == 2) {
                            $line_com_2 = ($not_p2_qty * $com_rate[0]['emp_2_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
                        }
                    } else {
                        $line_com = $order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate'];
                        if ($route_emp_count == 2) {
                            $line_com_2 = $order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate'];
                        }
                    }

                    // $line_com = $order_data[0]['total_qty'] * $com_rate;
                }
            }

            $total_amt = ($total_amt + $order_data[0]['total_amt']);

            $line_com_total = $line_com;
            $line_com_total_2 = $line_com_2;

            $total_com_all = $total_com_all + $line_com_total;
            $total_com_all_2 = $total_com_all_2 + $line_com_total_2;

        }

        //echo $xx;return;

        \common\models\ComDailyCal::deleteAll(['date(trans_date)' => date('Y-m-d'), 'route_id' => $id]);

        $line_special = 0;
        $extra_data = $this->getComspecial(date('Y-m-d'), date('Y-m-d'));

        if ($extra_data != null) {
            // echo $route_emp_count.' '. $total_amt.' '. $extra_data[0]['sale_price'];return;
            $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? (float)$extra_data[0]['com_extra'] : 0;
            $total_special_all = $total_special_all + $line_special;
            $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special + $total_com_all_2);
        } else {
            $total_com_sum_all = $total_com_sum_all + ($total_com_all + $total_com_all_2);
        }

//        $line_special = $total_amt >= 3500 && $route_emp_count == 1 ? 30 : 0;
//        $total_special_all = $total_special_all + $line_special;
//        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special);

        $model_com_daily = new \common\models\ComDailyCal();
        $model_com_daily->trans_date = date('Y-m-d H:i:s');
        $model_com_daily->emp_1 = $emp_1;
        $model_com_daily->emp_2 = $emp_2;
        $model_com_daily->total_qty = $total_qty_all;
        $model_com_daily->total_amt = $total_amt;
        $model_com_daily->line_com_amt = $total_com_all;
        $model_com_daily->line_com_amt_2 = $total_com_all_2;
        $model_com_daily->line_com_special_amt = $total_special_all;
        $model_com_daily->line_total_amt = $total_com_sum_all;
        $model_com_daily->created_at = time();
        $model_com_daily->route_id = $id;
        $model_com_daily->car_id = $car_id;
        $model_com_daily->company_id = $company_id;
        $model_com_daily->branch_id = $branch_id;
        $model_com_daily->save(false);


        return $this->redirect(['salecomreport/index']);
    }

    public
    function actionComdailycalprev()
    {

        $date_cal = \Yii::$app->request->post('cal_date');
        $id = \Yii::$app->request->post('select_delivery_route');
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }


        $xdate = explode('/', $date_cal);
        $t_date = date('Y-m-d H:i:s');
        if (count($xdate) > 1) {
            $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
        }

       //  echo $t_date;return;


        $model_route_data = \common\models\Orders::find()->select(['order_channel_id'])->where(['sale_from_mobile' => 1, 'date(order_date)' => date('Y-m-d', strtotime($t_date))])->groupBy(['order_channel_id'])->all();

        if ($model_route_data) {
           // echo count($model_route_data);return;
            foreach ($model_route_data as $val) {

                $car_id = 0;
                $emp_1 = 0;
                $emp_2 = 0;


                $total_com_all = 0;
                $total_qty_all = 0;
                $total_special_all = 0;
                $total_com_sum_all = 0;
                $total_amt_all = 0;
                $total_amt = 0;
                $xx = 0;

                $route_emp_count = 0;


                $model_order_mobile = \common\models\Orders::find()->select(['id', 'order_date', 'order_channel_id', 'car_ref_id', 'emp_1', 'emp_2', 'car_ref_id'])
                    ->Where(['company_id' => $company_id, 'branch_id' => $branch_id, 'order_channel_id' => $val->order_channel_id])
                    ->andFilterWhere(['date(order_date)' => date('Y-m-d', strtotime($t_date))])
                    ->andFilterWhere(['sale_from_mobile' => 1])->all();
                // print_r($model_order_mobile);return;
                // echo count($model_order_mobile); return;
                foreach ($model_order_mobile as $value) {
                    $xx++;
                    $com_rate = 0;
                    $route_emp_count = 0;

                    $car_id = $value->car_ref_id;
                    $emp_1 = $value->emp_1;
                    $emp_2 = $value->emp_2;

                    $route_total = null;
                    $route_name = \backend\models\Deliveryroute::findName($value->order_channel_id);

                    $order_data = null;
                    if (substr($route_name, 0, 2) == 'CJ') {
                        $order_data = $this->getOrderlineCJ($value->id, $company_id, $branch_id);
                    } else {
                        $order_data = $this->getOrderline($value->id, $company_id, $branch_id);
                    }

                    if ($order_data == null) continue;
                    // print_r($order_data);return;


                    $com_rate = 0;
                    if ($emp_1 > 0) {
                        $route_emp_count += 1;
                    }
                    if ($emp_2 > 0) {
                        $route_emp_count += 1;
                    }
//            if ((double)$order_data[0]['emp_1'] > 0) {
//                $route_emp_count += 1;
//            }
//            if ((double)$order_data[0]['emp_2'] > 0) {
//                $route_emp_count += 1;
//            }
                    $com_rate = $this->getComRate($route_emp_count, $company_id, $branch_id);

                    $total_qty_all = $total_qty_all + (double)$order_data[0]['total_qty'];
                    $total_amt_all = $total_amt_all + (double)$order_data[0]['total_amt'];

                    $line_com = 0;

                    if (substr($route_name, 0, 2) == 'CJ') {
                        if ($route_emp_count == 1) {
                            $line_com = (($order_data[0]['total_qty'] * $com_rate) * 1.75);
                        } else {
//                  $line_com = $order_data[0]['total_qty'] * $com_rate;
                            $line_com = ($order_data[0]['total_qty'] * 0.50); // cj 2 คน
                        }

                    } else {
                        $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
                        $not_p2_qty = $order_data[0]['total_qty'];
                        if ($order_data_p2 != null) {
                            $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];

                            $line_com = ($not_p2_qty * $com_rate) + ($order_data_p2[0]['total_qty'] * 1.75);

                        } else {
                            $line_com = $order_data[0]['total_qty'] * $com_rate;
                        }


                        // $line_com = $order_data[0]['total_qty'] * $com_rate;
                    }


                    $total_amt = ($total_amt + $order_data[0]['total_amt']);

                    $line_com_total = $line_com;

                    $total_com_all = $total_com_all + $line_com_total;

                }

                //echo $xx;return;

//                $line_special = 0;

                \common\models\ComDailyCal::deleteAll(['date(trans_date)' => date('Y-m-d', strtotime($t_date)), 'route_id' => $val->order_channel_id]);

                $extra_data = $this->getComspecial($t_date, $t_date);

                if ($extra_data != null) {
                    $line_special = $total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? (float)$extra_data[0]['com_extra'] : 0;
                    $total_special_all = $total_special_all + $line_special;
                    $total_com_sum_all = $total_com_sum_all + ($line_com + $line_special);
                }

//                $line_special = $total_amt >= 3500 && $route_emp_count == 1 ? 30 : 0;
//                $total_special_all = $total_special_all + $line_special;
//                $total_com_sum_all = $total_com_sum_all + ($line_com + $line_special);

                $model_com_daily = new \common\models\ComDailyCal();
                $model_com_daily->trans_date = date('Y-m-d', strtotime($t_date));
                $model_com_daily->emp_1 = $emp_1;
                $model_com_daily->emp_2 = $emp_2;
                $model_com_daily->total_qty = $total_qty_all;
                $model_com_daily->total_amt = $total_amt;
                $model_com_daily->line_com_amt = $total_com_all;
                $model_com_daily->line_com_special_amt = $total_special_all;
                $model_com_daily->line_total_amt = $total_com_sum_all;
                $model_com_daily->created_at = time();
                $model_com_daily->route_id = $val->order_channel_id;
                $model_com_daily->car_id = $car_id;
                $model_com_daily->company_id = $company_id;
                $model_com_daily->branch_id = $branch_id;
                $model_com_daily->save(false);

            }
        }


        return $this->redirect(['salecomreport/index3']);
    }

    public
    function actionComdailycalprevbt()
    {

        $date_cal = \Yii::$app->request->post('cal_date');
        // $id = \Yii::$app->request->post('select_delivery_route');
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }


        $xdate = explode('/', $date_cal);
        $t_date = date('Y-m-d H:i:s');
        if (count($xdate) > 1) {
            $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
        }

        // echo date('Y-m-d',$t_date);return;
        $model_route_data = [];
        $counted = 0;
        $loop = 0;
        //$model_route_all = \common\models\DeliveryRoute::find()->select(['id'])->where(['status'=>1])->all();
        $model_route_all = \common\models\Orders::find()->select(['distinct(order_channel_id)'])->where(['sale_from_mobile' => 1, 'date(order_date)' => date('Y-m-d', strtotime($t_date))])->groupBy(['order_channel_id'])->all();
        if($model_route_all){

            foreach ($model_route_all as $valuexx){
//                //$model_already_cal = \common\models\ComDailyCal::find()->where(['date(trans_date)'=>date('Y-m-d',strtotime($t_date))])->andFilterWhere(['route_idx'=>$valuexx->id])->count();
//                $model_already_cal = \common\models\ComDailyCal::find()->Where(['route_id'=>$valuexx->order_channel_id,'date(trans_date)'=>date('Y-m-d',strtotime($t_date))])->count();
//                if($model_already_cal == 0){
//                    array_push($model_route_data,$valuexx->order_channel_id);
//                    $loop+=1;
//
//                }else{
//                    $counted +=1;
//                }
//
//                if($loop == 8)break;
                array_push($model_route_data,$valuexx->order_channel_id);
            }
//            if(count($model_route_all) == $counted){
//                $session = Yii::$app->session;
//                $session->setFlash('msg-already', 'คำนวนครบทุกสายส่งแล้ว ');
//                return $this->redirect(['salecomreport/index3']);
//            }

        }





       // $model_route_data = \common\models\Orders::find()->select(['distinct(order_channel_id)'])->where(['sale_from_mobile' => 1, 'date(order_date)' => date('Y-m-d', strtotime($t_date))])->groupBy(['order_channel_id'])->all();
        if ($model_route_data != null) {
            if(count($model_route_data) >0){
                for($a=0;$a<=count($model_route_data)-1;$a++) {

                    $car_id = 0;
                    $emp_1 = 0;
                    $emp_2 = 0;


                    $total_com_all = 0;
                    $total_com_all_2 = 0;
                    $total_qty_all = 0;
                    $total_special_all = 0;
                    $total_com_sum_all = 0;
                    $total_amt_all = 0;
                    $total_amt = 0;
                    $xx = 0;

                    $route_emp_count = 0;

                    $model_order_mobile = \common\models\Orders::find()->select(['id', 'order_date', 'order_channel_id', 'car_ref_id', 'emp_1', 'emp_2', 'car_ref_id'])
                        ->Where(['company_id' => $company_id, 'branch_id' => $branch_id, 'order_channel_id' => $model_route_data[$a]])
                        ->andFilterWhere(['date(order_date)' => date('Y-m-d', strtotime($t_date))])
                        ->andFilterWhere(['>', 'order_total_amt', 0])
                        ->andFilterWhere(['sale_from_mobile' => 1])->all();
                    //print_r($model_order_mobile);return;
                    // echo count($model_order_mobile); return;
                    if ($model_order_mobile) {
                        foreach ($model_order_mobile as $value) {
                            $xx++;
                            $com_rate = null;
                            $route_emp_count = 0;

                            $car_id = $value->car_ref_id;
                            $emp_1 = $value->emp_1;
                            $emp_2 = $value->emp_2;

                            if ((int)$emp_1 > 0) {
                                $route_emp_count += 1;
                            }
                            if ((int)$emp_2 > 0) {
                                $route_emp_count += 1;
                            }

                            $route_total = null;
                            $route_name = \backend\models\Deliveryroute::findName($value->order_channel_id);

                            $order_data = null;
                            if (substr($route_name, 0, 2) == 'CJ') {
                                $order_data = $this->getOrderlineCJ($value->id, $company_id, $branch_id);
                            } else {
                                $order_data = $this->getOrderline($value->id, $company_id, $branch_id);
                            }

                            if ($order_data == null) continue 1;
                            // print_r($order_data);return;


                            $com_rate = $this->getComRateBktPrev($route_emp_count, $company_id, $branch_id, $t_date);

                            $total_qty_all = $total_qty_all + (double)$order_data[0]['total_qty'];
                            $total_amt_all = $total_amt_all + (double)$order_data[0]['total_amt'];

                            $line_com = 0;
                            $line_com_2 = 0;

                            if ($com_rate != null) {
                                if (substr($route_name, 0, 2) == 'CJ') {

                                    $com_pack2_rate = $this->getComRateBktPrevPack2($route_emp_count, $company_id, $branch_id, $t_date);
//                if ($route_emp_count == 1) {
                                    if ($route_emp_count == 1) {
                                        $line_com = (($order_data[0]['total_qty']  * $com_pack2_rate[0]['emp_1_rate']));
                                    }

//                } else {
//                  $line_com = $order_data[0]['total_qty'] * $com_rate;
                                    if ($route_emp_count == 2) {
                                        $line_com = ($order_data[0]['total_qty'] * $com_pack2_rate[0]['emp_1_rate']);
                                        $line_com_2 = ($order_data[0]['total_qty'] * $com_pack2_rate[0]['emp_2_rate']);
                                    }

                                } else {
                                    // Other

                                    $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
                                    $not_p2_qty = $order_data[0]['total_qty'];
                                    if ($order_data_p2 != null) {
                                        $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];

                                        $com_pack2_rate = $this->getComRateBktPrevPack2($route_emp_count, $company_id, $branch_id, $t_date);
                                        if ($route_emp_count == 1) {
                                            $line_com = ($not_p2_qty * $com_rate[0]['emp_1_rate']) + ($order_data_p2[0]['total_qty'] * $com_pack2_rate[0]['emp_1_rate']);
                                        }
                                        if ($route_emp_count == 2) {
                                            $line_com_2 = ($not_p2_qty * $com_rate[0]['emp_2_rate']) + ($order_data_p2[0]['total_qty'] * $com_pack2_rate[0]['emp_2_rate']);
                                        }
                                    } else {
                                        $line_com = $order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate'];
                                        if ($route_emp_count == 2) {
                                            $line_com_2 = $order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate'];
                                        }
                                    }

                                    /// NKY

//                                $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
//                                $not_p2_qty = $order_data[0]['total_qty'];
//                                if ($order_data_p2 != null) {
//                                    $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];
//
//                                    $line_com = ($not_p2_qty * $com_rate[0]['emp_1_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
//                                    if ($route_emp_count == 2) {
//                                        $line_com_2 = ($not_p2_qty * $com_rate[0]['emp_2_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
//                                    }
//                                } else {
//                                    $line_com = $order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate'];
//                                    if ($route_emp_count == 2) {
//                                        $line_com_2 = $order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate'];
//                                    }
//                                }

                                    // $line_com = $order_data[0]['total_qty'] * $com_rate;
                                }
                            }

                            $total_amt = ($total_amt + $order_data[0]['total_amt']);

                            $line_com_total = $line_com;
                            $line_com_total_2 = $line_com_2;

                            $total_com_all = $total_com_all + $line_com_total;
                            $total_com_all_2 = $total_com_all_2 + $line_com_total_2;

                        }
                    } else {
                        //echo $val->order_channel_id;
                    }

                    //echo $xx;return;

                    \common\models\ComDailyCal::deleteAll(['date(trans_date)' => date('Y-m-d', strtotime($t_date)), 'route_id' => $model_route_data[$a]]);

                    $line_special = 0;
                    $extra_data = $this->getComspecial(date('Y-m-d', strtotime($t_date)), date('Y-m-d', strtotime($t_date)));

                    if ($extra_data != null) {
                        // echo $route_emp_count.' '. $total_amt.' '. $extra_data[0]['sale_price'];return;
                        $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? (float)$extra_data[0]['com_extra'] : 0;
                        //  $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? $route_emp_count : 0;
                        $total_special_all = $total_special_all + $line_special;
                        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special + $total_com_all_2);
                    } else {
                        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $total_com_all_2);
                    }

//        $line_special = $total_amt >= 3500 && $route_emp_count == 1 ? 30 : 0;
//        $total_special_all = $total_special_all + $line_special;
//        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special);

                    if ($emp_1 == 0 || $total_qty_all <= 0) continue;

                    $model_com_daily = new \common\models\ComDailyCal();
                    $model_com_daily->trans_date = date('Y-m-d H:i:s', strtotime($t_date));
                    $model_com_daily->emp_1 = $emp_1;
                    $model_com_daily->emp_2 = $emp_2;
                    $model_com_daily->total_qty = $total_qty_all;
                    $model_com_daily->total_amt = $total_amt;
                    $model_com_daily->line_com_amt = $total_com_all;
                    $model_com_daily->line_com_amt_2 = $total_com_all_2;
                    $model_com_daily->line_com_special_amt = $total_special_all;
                    $model_com_daily->line_total_amt = $total_com_sum_all;
                    $model_com_daily->created_at = time();
                    $model_com_daily->route_id = $model_route_data[$a];
                    $model_com_daily->car_id = $car_id;
                    $model_com_daily->company_id = $company_id;
                    $model_com_daily->branch_id = $branch_id;
                    $model_com_daily->save(false);

                }
            }

        }


        return $this->redirect(['salecomreport/index3']);
    }

    public
    function actionComdailycalprevbtauto()
    {
        $company_id = 1;
        $branch_id = 2;

        $t_date = date('Y-m-d H:i:s',strtotime('-1 day'));
        //$t_date = date('Y-m-d H:i:s');

        $model_route_data = [];
        $counted = 0;
        $loop = 0;
        //$model_route_all = \common\models\DeliveryRoute::find()->select(['id'])->where(['status'=>1])->all();
        $model_route_all = \common\models\Orders::find()->select(['distinct(order_channel_id)'])->where(['sale_from_mobile' => 1, 'date(order_date)' => date('Y-m-d', strtotime($t_date))])->groupBy(['order_channel_id'])->all();
        if($model_route_all){
            $count_trans_already = \common\models\ComDailyCal::find()->select(['distinct(route_id)'])->Where(['date(trans_date)'=>date('Y-m-d',strtotime($t_date))])->groupBy(['route_id'])->all();
            if($count_trans_already){
                if(count($model_route_all) == count($count_trans_already)){
                    $this->notifymessageorderclose($company_id,$branch_id);
                    return;
                }
            }

            foreach ($model_route_all as $valuexx){
                //$model_already_cal = \common\models\ComDailyCal::find()->where(['date(trans_date)'=>date('Y-m-d',strtotime($t_date))])->andFilterWhere(['route_idx'=>$valuexx->id])->count();
                $model_already_cal = \common\models\ComDailyCal::find()->Where(['route_id'=>$valuexx->order_channel_id,'date(trans_date)'=>date('Y-m-d',strtotime($t_date))])->count();
                if($model_already_cal == 0){
                    array_push($model_route_data,$valuexx->order_channel_id);
                    $loop+=1;

                }else{
                    $counted +=1;
                }

                if($loop == 8)break;
            }
            if(count($model_route_all) == $counted){
                $this->notifymessageorderclose($company_id,$branch_id);
            }

        }

        // $model_route_data = \common\models\Orders::find()->select(['distinct(order_channel_id)'])->where(['sale_from_mobile' => 1, 'date(order_date)' => date('Y-m-d', strtotime($t_date))])->groupBy(['order_channel_id'])->all();
        if ($model_route_data != null) {
            if(count($model_route_data) >0){
                for($a=0;$a<=count($model_route_data)-1;$a++) {

                    $car_id = 0;
                    $emp_1 = 0;
                    $emp_2 = 0;


                    $total_com_all = 0;
                    $total_com_all_2 = 0;
                    $total_qty_all = 0;
                    $total_special_all = 0;
                    $total_com_sum_all = 0;
                    $total_amt_all = 0;
                    $total_amt = 0;
                    $xx = 0;

                    $route_emp_count = 0;

                    $model_order_mobile = \common\models\Orders::find()->select(['id', 'order_date', 'order_channel_id', 'car_ref_id', 'emp_1', 'emp_2', 'car_ref_id'])
                        ->Where(['company_id' => $company_id, 'branch_id' => $branch_id, 'order_channel_id' => $model_route_data[$a]])
                        ->andFilterWhere(['date(order_date)' => date('Y-m-d', strtotime($t_date))])
                        ->andFilterWhere(['>', 'order_total_amt', 0])
                        ->andFilterWhere(['sale_from_mobile' => 1])->all();
                    //print_r($model_order_mobile);return;
                    // echo count($model_order_mobile); return;
                    if ($model_order_mobile) {
                        foreach ($model_order_mobile as $value) {
                            $xx++;
                            $com_rate = null;
                            $route_emp_count = 0;

                            $car_id = $value->car_ref_id;
                            $emp_1 = $value->emp_1;
                            $emp_2 = $value->emp_2;

                            if ((int)$emp_1 > 0) {
                                $route_emp_count += 1;
                            }
                            if ((int)$emp_2 > 0) {
                                $route_emp_count += 1;
                            }

                            $route_total = null;
                            $route_name = \backend\models\Deliveryroute::findName($value->order_channel_id);

                            $order_data = null;
                            if (substr($route_name, 0, 2) == 'CJ') {
                                $order_data = $this->getOrderlineCJ($value->id, $company_id, $branch_id);
                            } else {
                                $order_data = $this->getOrderline($value->id, $company_id, $branch_id);
                            }

                            if ($order_data == null) continue 1;
                            // print_r($order_data);return;


                            $com_rate = $this->getComRateBktPrev($route_emp_count, $company_id, $branch_id, $t_date);

                            $total_qty_all = $total_qty_all + (double)$order_data[0]['total_qty'];
                            $total_amt_all = $total_amt_all + (double)$order_data[0]['total_amt'];

                            $line_com = 0;
                            $line_com_2 = 0;

                            if ($com_rate != null) {
                                if (substr($route_name, 0, 2) == 'CJ') {

                                    $com_pack2_rate = $this->getComRateBktPrevPack2($route_emp_count, $company_id, $branch_id, $t_date);
//                if ($route_emp_count == 1) {
                                    if ($route_emp_count == 1) {
                                        $line_com = (($order_data[0]['total_qty']  * $com_pack2_rate[0]['emp_1_rate']));
                                    }

//                } else {
//                  $line_com = $order_data[0]['total_qty'] * $com_rate;
                                    if ($route_emp_count == 2) {
                                        $line_com = ($order_data[0]['total_qty'] * $com_pack2_rate[0]['emp_1_rate']);
                                        $line_com_2 = ($order_data[0]['total_qty'] * $com_pack2_rate[0]['emp_2_rate']);
                                    }

                                } else {
                                    // Other

                                    $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
                                    $not_p2_qty = $order_data[0]['total_qty'];
                                    if ($order_data_p2 != null) {
                                        $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];

                                        $com_pack2_rate = $this->getComRateBktPrevPack2($route_emp_count, $company_id, $branch_id, $t_date);
                                        if ($route_emp_count == 1) {
                                            $line_com = ($not_p2_qty * $com_rate[0]['emp_1_rate']) + ($order_data_p2[0]['total_qty'] * $com_pack2_rate[0]['emp_1_rate']);
                                        }
                                        if ($route_emp_count == 2) {
                                            $line_com_2 = ($not_p2_qty * $com_rate[0]['emp_2_rate']) + ($order_data_p2[0]['total_qty'] * $com_pack2_rate[0]['emp_2_rate']);
                                        }
                                    } else {
                                        $line_com = $order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate'];
                                        if ($route_emp_count == 2) {
                                            $line_com_2 = $order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate'];
                                        }
                                    }

                                    /// NKY

//                                $order_data_p2 = $this->getOrderlineP2($value->id, $company_id, $branch_id);
//                                $not_p2_qty = $order_data[0]['total_qty'];
//                                if ($order_data_p2 != null) {
//                                    $not_p2_qty = $order_data[0]['total_qty'] - $order_data_p2[0]['total_qty'];
//
//                                    $line_com = ($not_p2_qty * $com_rate[0]['emp_1_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
//                                    if ($route_emp_count == 2) {
//                                        $line_com_2 = ($not_p2_qty * $com_rate[0]['emp_2_rate']) + ($order_data_p2[0]['total_qty'] * 1.75);
//                                    }
//                                } else {
//                                    $line_com = $order_data[0]['total_qty'] * $com_rate[0]['emp_1_rate'];
//                                    if ($route_emp_count == 2) {
//                                        $line_com_2 = $order_data[0]['total_qty'] * $com_rate[0]['emp_2_rate'];
//                                    }
//                                }

                                    // $line_com = $order_data[0]['total_qty'] * $com_rate;
                                }
                            }

                            $total_amt = ($total_amt + $order_data[0]['total_amt']);

                            $line_com_total = $line_com;
                            $line_com_total_2 = $line_com_2;

                            $total_com_all = $total_com_all + $line_com_total;
                            $total_com_all_2 = $total_com_all_2 + $line_com_total_2;

                        }
                    } else {
                        //echo $val->order_channel_id;
                    }

                    //echo $xx;return;

                    \common\models\ComDailyCal::deleteAll(['date(trans_date)' => date('Y-m-d', strtotime($t_date)), 'route_id' => $model_route_data[$a]]);

                    $line_special = 0;
                    $extra_data = $this->getComspecial(date('Y-m-d', strtotime($t_date)), date('Y-m-d', strtotime($t_date)));

                    if ($extra_data != null) {
                        // echo $route_emp_count.' '. $total_amt.' '. $extra_data[0]['sale_price'];return;
                        $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? (float)$extra_data[0]['com_extra'] : 0;
                        //  $line_special = (float)$total_amt >= (float)$extra_data[0]['sale_price'] && $route_emp_count == 1 ? $route_emp_count : 0;
                        $total_special_all = $total_special_all + $line_special;
                        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special + $total_com_all_2);
                    } else {
                        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $total_com_all_2);
                    }

//        $line_special = $total_amt >= 3500 && $route_emp_count == 1 ? 30 : 0;
//        $total_special_all = $total_special_all + $line_special;
//        $total_com_sum_all = $total_com_sum_all + ($total_com_all + $line_special);

                    if ($emp_1 == 0 || $total_qty_all <= 0) continue;

                    $model_com_daily = new \common\models\ComDailyCal();
                    $model_com_daily->trans_date = date('Y-m-d H:i:s', strtotime($t_date));
                    $model_com_daily->emp_1 = $emp_1;
                    $model_com_daily->emp_2 = $emp_2;
                    $model_com_daily->total_qty = $total_qty_all;
                    $model_com_daily->total_amt = $total_amt;
                    $model_com_daily->line_com_amt = $total_com_all;
                    $model_com_daily->line_com_amt_2 = $total_com_all_2;
                    $model_com_daily->line_com_special_amt = $total_special_all;
                    $model_com_daily->line_total_amt = $total_com_sum_all;
                    $model_com_daily->created_at = time();
                    $model_com_daily->route_id = $model_route_data[$a];
                    $model_com_daily->car_id = $car_id;
                    $model_com_daily->company_id = $company_id;
                    $model_com_daily->branch_id = $branch_id;
                    $model_com_daily->save(false);

                }
            }

        }

    }

    public function notifymessageorderclose($company_id, $branch_id)
    {
        //$message = "This is test send request from camel paperless";
        $line_api = 'https://notify-api.line.me/api/notify';
        $line_token = '';

        //   6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE   token omnoi
        if ($company_id == 1 && $branch_id == 1) {
            //  $line_token = 'ZMqo4ZqwBGafMOXKVht2Liq9dCGswp4IRofT2EbdRNN'; // vorapat
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            //   $line_token = '6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE'; // omnoi
            $line_token = trim($b_token);
        } else if ($company_id == 1 && $branch_id == 2) {
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            $line_token = trim($b_token);
            //   $line_token = 'TxAUAOScIROaBexBWXaYrVcbjBItIKUwGzFpoFy3Jrx'; // BKT
        }



        $message = '' . "\n";
        $message .= 'BKT:'. "\n";
        $message .= 'คำนวนค่าคอมสายส่งสำเร็จทุกสายแล้ว' . "\n"; // bkt


        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData, '', '&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Authorization: Bearer " . $line_token . "\r\n"
                    . "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData
            )
        );
        $context = stream_context_create($headerOptions);
        $result = file_get_contents($line_api, FALSE, $context);
        $res = json_decode($result);
        return $res;
    }

    public
    function getRouteSum($route_id, $f_date, $t_date, $company_id, $branch_id)
    {

        $data = [];
        if ($route_id != null) {
//        $sql = "SELECT t2.order_no, SUM(t3.qty) as total_qty, SUM(t3.line_total) as total_amt
//              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id
//             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
//             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . "
//             AND t2.order_channel_id =" . $route_id . "
//             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql = "SELECT t2.order_no, SUM(t3.qty) as total_qty, SUM(t3.line_total) as total_amt
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id
             WHERE  (t2.order_date BETWEEN " . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . "
             AND " . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "')" . "
             AND t2.order_channel_id =" . $route_id . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.order_channel_id";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {

                    array_push($data, [
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                    ]);
                }
            }
        }

        return $data;
    }


    public
    function getRouteSumCJ($route_id, $f_date, $t_date, $company_id, $branch_id)
    {

        $data = [];
        if ($route_id != null) {
            $sql = "SELECT t2.order_no, SUM(t3.qty / t4.nw) as total_qty, SUM(t3.line_total) as total_amt 
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id INNER JOIN product as t4 ON t3.product_id = t4.id
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . " 
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t2.order_channel_id =" . $route_id . " 
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY t2.order_channel_id";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {

                for ($i = 0; $i <= count($model) - 1; $i++) {

                    array_push($data, [
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getComRate($count, $company_id, $branch_id)
    {
        $rate = 0;
        $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model) {
            $rate = $model->com_extra;
        }
        return $rate;
    }

    public
    function getComRateBkt($count, $company_id, $branch_id)
    {

        $rate = [];
        $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['<=', 'date(from_date)', date('Y-m-d')])->andFilterWhere(['>=', 'date(to_date)', date('Y-m-d')])->one();
        // $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model) {
            if ($count == 1) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => 0]);
            } else if ($count == 2) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => $model->second_emp]);
            }

        }
        return $rate;
    }

    function getComRateBktPrev($count, $company_id, $branch_id, $for_cal_date)
    {

        $rate = [];
        //   $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id,'emp_qty'=>$count])->andFilterWhere(['<=', 'date(from_date)', date('Y-m-d',strtotime($for_cal_date))])->andFilterWhere(['>=', 'date(to_date)', date('Y-m-d',strtotime($for_cal_date))])->one();
        $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id, 'emp_qty' => $count,'product_id'=>0])->andFilterWhere(['>=', 'date(to_date)', date('Y-m-d', strtotime($for_cal_date))])->one();
        // $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model) {
            if ($count == 1) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => 0]);
            } else if ($count == 2) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => $model->second_emp]);
            }

        }
        return $rate;
    }

    function getComRateBktPrevPack2($count, $company_id, $branch_id, $for_cal_date)
    {

        $rate = [];
        //   $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id,'emp_qty'=>$count])->andFilterWhere(['<=', 'date(from_date)', date('Y-m-d',strtotime($for_cal_date))])->andFilterWhere(['>=', 'date(to_date)', date('Y-m-d',strtotime($for_cal_date))])->one();
        $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id, 'emp_qty' => $count,'product_id'=>4])->andFilterWhere(['>=', 'date(to_date)', date('Y-m-d', strtotime($for_cal_date))])->one();
        // $model = \backend\models\Salecom::find()->where(['emp_qty' => $count, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model) {
            if ($count == 1) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => 0]);
            } else if ($count == 2) {
                array_push($rate, ['emp_1_rate' => $model->com_extra, 'emp_2_rate' => $model->second_emp]);
            }

        }
        return $rate;
    }


    public
    function getRouteSumAll($order_id, $company_id, $branch_id, $route_name)
    {

        $data = [];
        $sql = '';
        if ($order_id != null) {
            if ($route_name == 'CJ') {
                $sql = "SELECT  SUM(t3.qty / t4.nw) as total_qty, SUM(t3.line_total) as total_amt 
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id INNER JOIN product as t4 ON t3.product_id = t4.id
             WHERE  t2.id=" . $order_id . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

                $sql .= " GROUP BY t2.order_channel_id";
            } else {
                $sql = "SELECT SUM(t3.qty) as total_qty, SUM(t3.line_total) as total_amt
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id
             WHERE  t2.id=" . $order_id . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

                $sql .= " GROUP BY t2.order_channel_id";
            }

            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    array_push($data, [
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getRouteComSum($route_id, $from_date, $to_date, $company_id, $branch_id, $com_rate, $route_emp_count)
    {
        $data = [];
        $total_com_sum_all = 0;
        $total_special_all = 0;
        $order_data = getOrderline($route_id, $from_date, $company_id, $branch_id);

        $sale_target = 0;
        $com_extra = 0;
        $extra_data = $this->getComspecial($from_date, $to_date);
        if ($extra_data != null) {
            $sale_target = (float)$extra_data[0]['sale_price'];
            $com_extra = (float)$extra_data[0]['com_extra'];
        }

        for ($m = 0; $m <= count($order_data) - 1; $m++) {
            // $line_special = $order_data[$m]['total_amt'] >= 3500 && $route_emp_count == 1 ? 30 : 0;
            $line_special = $order_data[$m]['total_amt'] >= $sale_target && $route_emp_count == 1 ? $com_extra : 0;
            $total_special_all = $total_special_all + $line_special;
        }
        array_push($data, ['special' => $total_special_all]);
        return $data;
    }

    public
    function getOrderCJ($route_id, $f_date, $t_date, $company_id, $branch_id)
    {

        $data = [];
        if ($route_id != null) {
            $sql = "SELECT date(t2.order_date) as order_date, SUM(t3.qty / t4.nw) as total_qty, SUM(t3.line_total) as total_amt 
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id INNER JOIN product as t4 ON t3.product_id = t4.id
             WHERE  t2.order_date >=" . "'" . date('Y-m-d H:i:s', strtotime($f_date)) . "'" . " 
             AND t2.order_date <=" . "'" . date('Y-m-d H:i:s', strtotime($t_date)) . "'" . " 
             AND t2.order_channel_id =" . $route_id . " 
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY date(t2.order_date)";
            $sql .= " ORDER BY date(t2.order_date) ASC";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {

                    array_push($data, [
                        //   'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],

                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getOrderlineCJ($order_id, $company_id, $branch_id)
    {

        $data = [];
        if ($order_id != null) {
            $sql = "SELECT date(t2.order_date) as order_date, SUM(t3.qty / t4.nw) as total_qty, SUM(t3.line_total) as total_amt , t2.emp_1,t2.emp_2
              FROM orders as t2 INNER  JOIN order_line as t3 ON t3.order_id = t2.id INNER JOIN product as t4 ON t3.product_id = t4.id
             WHERE  t2.id=" . $order_id ;
           //  AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY date(t2.order_date)";
            $sql .= " ORDER BY date(t2.order_date) ASC";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {

                    array_push($data, [
                        //   'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                        'emp_1' => $model[$i]['emp_1'],
                        'emp_2' => $model[$i]['emp_2'],
                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getOrderlineOld($order_id, $company_id, $branch_id)
    {
        $data = [];
        if ($order_id != null) {
            $sql = "SELECT date(t2.order_date) as order_date, SUM(t2.qty/t2.nw) as total_qty, SUM(t2.line_total) as total_amt, t2.emp_1,t2.emp_2
              FROM query_sale_mobile_data_new as t2
             WHERE  t2.id=" . $order_id ;
            //  AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY date(t2.order_date)";
            $sql .= " ORDER BY date(t2.order_date) ASC";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    array_push($data, [
                        //   'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                        'emp_1' => $model[$i]['emp_1'],
                        'emp_2' => $model[$i]['emp_2'],
                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getOrderline($order_id, $company_id, $branch_id)
    {
        $data = [];
        if ($order_id != null) {
            $sql = "SELECT date(t2.order_date) as order_date, SUM(t2.qty/t2.nw) as total_qty, SUM(t2.line_total) as total_amt, t2.emp_1,t2.emp_2
              FROM query_sale_mobile_data_new as t2
             WHERE  t2.id=" . $order_id ;
            //  AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY date(t2.order_date)";
            $sql .= " ORDER BY date(t2.order_date) ASC";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    array_push($data, [
                        //   'order_no' => $model[$i]['order_no'],
                        'order_date' => $model[$i]['order_date'],
                        'total_qty' => $model[$i]['total_qty'],
                        'total_amt' => $model[$i]['total_amt'],
                        'emp_1' => $model[$i]['emp_1'],
                        'emp_2' => $model[$i]['emp_2'],
                    ]);
                }
            }
        }

        return $data;
    }

    public
    function getOrderlineP2($order_id, $company_id, $branch_id)
    {
        $data = [];
        if ($order_id != null) {
            $sql = "SELECT date(t2.order_date) as order_date, SUM((t2.qty)/t4.nw) as total_qty, SUM(t2.line_total) as total_amt, t3.emp_1,t3.emp_2
              FROM query_sale_mobile_data_new as t2 INNER  JOIN  orders as t3 ON t2.id = t3.id INNER JOIN product as t4 ON t2.product_id = t4.id
             WHERE  t2.id=" . $order_id . "
             AND t2.qty > 0  
             AND t2.product_id = 4  
              AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

            $sql .= " GROUP BY date(t2.order_date)";
            $sql .= " ORDER BY date(t2.order_date) ASC";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    array_push($data, [
                        //   'order_no' => $model[$i]['order_no'],
                        'total_qty' => $model[$i]['total_qty'],

                    ]);
                }
            }
        }

        return $data;
    }

    function getComspecial($from_date, $to_date)
    {
        $com_amt = [];
        if ($from_date != null && $to_date != null) {
            $model = \common\models\SaleComSummary::find()->select(['id', 'com_extra', 'sale_price'])->where(['<=', 'date(from_date)', date('Y-m-d', strtotime($from_date))])->andFilterWhere(['company_id' => 1, 'branch_id' => 1])->orderBy(['id' => SORT_DESC])->one();
            // $model = \common\models\SaleComSummary::find()->select(['id', 'com_extra', 'sale_price'])->where(['<=', 'date(from_date)', date('Y-m-d', strtotime($from_date))])->andFilterWhere(['company_id' => 1, 'branch_id' => 1])->orderBy(['id' => SORT_DESC])->one();
            if ($model) {

                array_push($com_amt, ['sale_price' => $model->sale_price, 'com_extra' => $model->com_extra]);

            }
        }
        return $com_amt;
    }

    public function actionDeletecal(){
        $cal = \Yii::$app->request->post('delete_cal');
        $xdate = explode('/', $cal);
        $t_date = date('Y-m-d H:i:s');
        if (count($xdate) > 1) {
            $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0];
        }

        \common\models\ComDailyCal::deleteAll(['date(trans_date)'=>date('Y-m-d',strtotime($t_date))]);
        return $this->redirect(['salecomreport/index2']);
    }
}
