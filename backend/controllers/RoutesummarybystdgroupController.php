<?php

namespace backend\controllers;

use backend\models\Customer;
use backend\models\Deliveryroute;
use backend\models\Orders;
use backend\models\ProductgroupSearch;
use backend\models\WarehouseSearch;
use Yii;
use backend\models\Car;
use backend\models\CarSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * CarController implements the CRUD actions for Car model.
 */
class RoutesummarybystdgroupController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
////        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//            'access'=>[
//                'class'=>AccessControl::className(),
//                'denyCallback' => function ($rule, $action) {
//                    throw new ForbiddenHttpException('คุณไม่ได้รับอนุญาติให้เข้าใช้งาน!');
//                },
//                'rules'=>[
//                    [
//                        'allow'=>true,
//                        'roles'=>['@'],
//                        'matchCallback'=>function($rule,$action){
//                            $currentRoute = \Yii::$app->controller->getRoute();
//                            if(\Yii::$app->user->can($currentRoute)){
//                                return true;
//                            }
//                        }
//                    ]
//                ]
//            ],
//        ];
//    }

    /**
     * Lists all Car models.
     * @return mixed
     */
    public function actionIndex()
    {
        $route_id = \Yii::$app->request->post('route_id');
        $search_date = \Yii::$app->request->post('search_date');
        return $this->render('_summarybystdpricegroup', [
            'route_id' => $route_id,
            'search_date' => $search_date,
        ]);
    }

    public function actionIndex2()
    {
        $route_id = \Yii::$app->request->post('route_id');
        $search_date = \Yii::$app->request->post('search_date');
        $search_to_date = \Yii::$app->request->post('search_to_date');
        return $this->render('_summarybystdpricegroup2', [
            'route_id' => $route_id,
            'search_date' => $search_date,
            'search_to_date' => $search_to_date,
        ]);
    }

    public function actionDailycal()
    {

//        $date=date_create("2013-03-15");
//        echo date_format($date,"Y/m/d");

       // $cal_date = date('Y-m-d', strtotime("-1 day"));
        $cal_date = date('Y-m-d');
        $model_product = \backend\models\Product::find()->select(['id'])->where(['status' => 1, 'branch_id' => 2])->all();
        $model_transfer_branch = \backend\models\Transferbrach::find()->select(['id'])->where(['status' => 1])->all();
        $model_stdgroup = \backend\models\Stdpricegroup::find()->select(['price', 'type_id'])->groupBy('seq_no')->orderBy(['type_id' => SORT_ASC, 'seq_no' => SORT_ASC])->all();


        $route_id = 949;
        $model_route_data = \backend\models\Deliveryroute::find()->select(['id'])->where(['status' => 1, 'branch_id' => 2,'id'=>956])->all();
        if ($model_route_data) {
            foreach ($model_route_data as $value_route) {
                \common\models\RouteIssueDailyCal::deleteAll(['route_id' => $value_route->id, 'date(trans_date)' => date('Y-m-d', strtotime($cal_date))]);
                \common\models\RouteTransPriceCal::deleteAll(['route_id' => $value_route->id, 'date(trans_date)' => date('Y-m-d', strtotime($cal_date))]);

                foreach ($model_product as $value) {
                    // save normal issue
                    $issue_normal_qty = $this->getIssuecar($value_route->id, $cal_date, $value->id);
                    $model_insert = new \common\models\RouteIssueDailyCal();
                    $model_insert->trans_date = date('Y-m-d H:i:s', strtotime($cal_date));
                    $model_insert->issue_trans_type = 1; // normal issue type
                    $model_insert->transfer_branch_id = 8888; // normal branch car issue
                    $model_insert->route_id = $value_route->id;
                    $model_insert->product_id = $value->id;
                    $model_insert->qty = $issue_normal_qty;
                    $model_insert->total_amount = 0;
                    $model_insert->save(false);
                    // end save normal issue

                    // save transfer from other branch
                    foreach ($model_transfer_branch as $value_transfer) {
                        $transfer_qty = $this->getReceiveTransfer($value_route->id, $cal_date, $value->id, $value_transfer->id);
                        $product_branch_price = $this->caltransferbrachtotalprice($value->id, $value_transfer->id);

                        $model_insert2 = new \common\models\RouteIssueDailyCal();
                        $model_insert2->trans_date = date('Y-m-d H:i:s', strtotime($cal_date));
                        $model_insert2->issue_trans_type = 2; // normal issue type
                        $model_insert2->transfer_branch_id = $value_transfer->id; // transfer branch id
                        $model_insert2->route_id = $value_route->id;
                        $model_insert2->product_id = $value->id;
                        $model_insert2->qty = $transfer_qty;
                        $model_insert2->total_amount = ($transfer_qty * $product_branch_price);
                        $model_insert2->save(false);
                    }
                    // end transfer from other branch

                    // save return car
                    $return_qty = $this->getReturnCar($value_route->id, $value->id, $cal_date);
                    $model_insert3 = new \common\models\RouteIssueDailyCal();
                    $model_insert3->trans_date = date('Y-m-d H:i:s', strtotime($cal_date));
                    $model_insert3->issue_trans_type = 3; // normal issue type
                    $model_insert3->transfer_branch_id = 9999; // normal branch car issue
                    $model_insert3->route_id = $value_route->id;
                    $model_insert3->product_id = $value->id;
                    $model_insert3->qty = $return_qty;
                    $model_insert3->total_amount = 0;
                    $model_insert3->save(false);
                    // end save return car

                    foreach ($model_stdgroup as $value_std) {
                        $line_product_price_qty = $this->getQtyByPrice($value_route->id, $value_std->price, $value_std->type_id, $cal_date, $value->id);
                        $model_insert4 = new \common\models\RouteTransPriceCal();
                        $model_insert4->trans_date = date('Y-m-d H:i:s', strtotime($cal_date));
                        $model_insert4->std_price_type = $value_std->type_id;
                        $model_insert4->route_id = $value_route->id;
                        $model_insert4->product_id = $value->id;
                        $model_insert4->qty = $line_product_price_qty;
                        $model_insert4->price = $value_std->price;
                        $model_insert4->line_total = ($value_std->price * $line_product_price_qty);
                        $model_insert4->save(false);
                    }

                }
            }
        }

        return $this->redirect(['routesummarybystdgroup/index2']);
    }

    function getIssuecar($route_id, $order_date, $product_id)
    {
        $sale_qty = 0;

        if ($route_id > 0) {
            $sql = "SELECT SUM(qty) as qty";
            $sql .= " FROM query_issue_none_transfer";
            $sql .= " WHERE product_id =" . $product_id;
            // $sql .= " AND not isnull(t1.delivery_route_id)";
            $sql .= " AND date(trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
            $sql .= " AND delivery_route_id=" . $route_id;
            $sql .= " GROUP BY product_id";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $sale_qty = $model[$i]['qty'];
                }
            }
        }
        return $sale_qty;
    }

    function getReceiveTransfer($route_id, $order_date, $product_id, $transfer_from_id)
    {
        $sale_qty = 0;

        if ($route_id > 0) {
            $sql = "SELECT SUM(qty) as qty";
            $sql .= " FROM query_issue_from_transfer";
            $sql .= " WHERE product_id =" . $product_id;
            // $sql .= " AND not isnull(t1.delivery_route_id)";
            $sql .= " AND date(trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
            if ($route_id != null) {
                $sql .= " AND delivery_route_id=" . $route_id;
            }
            if ($transfer_from_id != null) {
                $sql .= " AND transfer_branch_id=" . $transfer_from_id;
            }
            $sql .= " GROUP BY product_id";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $sale_qty = $model[$i]['qty'];
                }
            }
        }
        return $sale_qty;
    }

    function getReturnCar($route_id, $product_id, $order_date)
    {
        $return_qty = 0;
        $sql = "SELECT SUM(t1.qty) as qty";
        $sql .= " FROM stock_trans as t1 ";
        $sql .= " WHERE  t1.product_id =" . $product_id;
        $sql .= " AND t1.activity_type_id in (7,26)";
        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
        if ($route_id != null) {
            $sql .= " AND t1.trans_ref_id=" . $route_id;
        }

        $sql .= " GROUP BY t1.product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $return_qty = $model[$i]['qty'];
            }
        }
        return $return_qty;
    }

    function getQtyByPrice($route_id, $price, $sale_type, $order_date, $product_id)
    {
        $sale_qty = 0;

        if ($route_id > 0) {
            $sql = "SELECT SUM(t2.qty) as qty";
            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t2.order_id = t1.id";
            $sql .= " WHERE t2.product_id =" . $product_id;
            // $sql .= " AND not isnull(t1.delivery_route_id)";
            $sql .= " AND t1.status <> 3";
            $sql .= " AND t2.status not in (3,500)";
            $sql .= " AND t2.price = " . $price;
            $sql .= " AND t2.sale_payment_method_id = " . $sale_type;
            $sql .= " AND date(t1.order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
            if ($route_id != null) {
                $sql .= " AND t1.order_channel_id=" . $route_id;
            }
            $sql .= " GROUP BY t2.price";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $sale_qty = $model[$i]['qty'];
                }
            }
        }


        return $sale_qty;
    }

    function caltransferbrachtotalprice($product_id, $transfer_from_id)
    {
        $price = 0;
        $model = \common\models\TransferBranchProductPrice::find()->select(['price'])->where(['transfer_branch_id' => $transfer_from_id, 'product_id' => $product_id])->one();
        if ($model) {
            $price = $model->price;
        }
        return $price;
    }
}
