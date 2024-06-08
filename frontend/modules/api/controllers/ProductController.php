<?php

namespace frontend\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;


class ProductController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['POST'],
                    'listall' => ['POST'],
                    'itemcodelist' => ['POST'],
                    'warehouselist' => ['POST'],
                    'findreprocessstock' => ['POST'], // เบิกคืนจากรถ
                    'issuelist' => ['POST'],
                    'issuelist2' => ['POST'],
                    'findcustpriceoffline' => ['POST'],
                    'listproductall' => ['GET'],
                    'adddailycount' => ['POST'],
                    'addtftransfer' => ['POST'],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];

        $data = [];
        $status = false;

        if ($customer_id) {
            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    $product_info = \backend\models\Product::findInfo($value->product_id);
                    $new_price = $value->sale_price;
                    if($value->haft_cal == 1){
                        $new_price = $value->sale_haft_price;
                    }
                    if($product_info != null){
                        array_push($data, [
                            'id' => $value->product_id,
                            //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                            'image' => '', // 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                            'code' => $product_info->code,
                            'name' => $product_info->name,
                            'sale_price' => $new_price,
                            'haft_cal' => $value->haft_cal,
                        ]);
                    }

                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionListall()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];

        $data = [];
        $status = false;

        if ($customer_id) {
            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    $product_info = \backend\models\Product::findInfo($value->product_id);
                    $new_price = $value->sale_price;
                    if($value->haft_cal == 1){
                        $new_price = $value->sale_haft_price;
                    }
                    array_push($data, [
                        'id' => $value->product_id,
                        //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        'code' => $product_info->code,
                        'name' => $product_info->name,
                        'sale_price' => $new_price,
                        'haft_cal' => $value->haft_cal,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionListproductall()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();

        $data = [];
        $status = false;

        $model = \common\models\Product::find()->where(['status' =>1])->all();
        // $model = \common\models\QueryCustomerPrice::find()->all();
        if ($model) {
            $status = true;
            foreach ($model as $value) {

                array_push($data, [
                    'id' => $value->id,
                    //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                    'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $value->photo,
                    'code' => $value->code,
                    'name' => $value->name,
                    'sale_price' => 0,
                    'onhand_qty' => $this->getstockqty($value->id),
                ]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function getstockqty($product_id){
        $qty  = 0;
        if($product_id){
            $qty = \backend\models\Stocksum::find()->where(['product_id'=>$product_id,'warehouse_id'=>1])->sum('qty');
            if($qty == null){
                $qty = 0;
            }
        }
        return (float)$qty;
    }

    public function actionItemcodelist()
    {
        $code = 0;
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $code = $req_data['item_code'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($code) {
            $model = \common\models\Product::find()->where(['code' => $code, 'company_id' => $company_id, 'branch_id' => $branch_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        //'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        'code' => $value->code,
                        'name' => $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionWarehouselist()
    {
        $code = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $code = $req_data['item_code'];

        $data = [];
        $status = false;

        if ($code) {
            $model = \common\models\Warehouse::find()->where(['code' => $code])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        //'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        'code' => $value->code,
                        'name' => $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionIssuelist()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];
        $route_id = $req_data['route_id'];
        $issue_date = $req_data['issue_date'];

        $data = [];
        $status = false;

        if ($route_id != null && $customer_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;
            $exp_order_date = explode(' ', $issue_date);
            if ($exp_order_date != null) {
                if (count($exp_order_date) > 1) {
                    $x_date = explode('-', $exp_order_date[0]);
                    if (count($x_date) > 1) {
                        $t_date = $x_date[0] . "/" . $x_date[1] . "/" . $x_date[2];
                    }
                }
            }
            if ($t_date != null) {
                $trans_date = $t_date;
            }
            $model_issue = \common\models\JournalIssue::find()->where(['delivery_route_id' => $route_id, 'date(trans_date)' => $trans_date])->andFilterWhere(['<=', 'status', 2])->one();
            if ($model_issue) {
                $model = \common\models\QuerySaleIssueProductPrice::find()->where(['cus_id' => $customer_id, 'delivery_route_id' => $route_id, 'issue_id' => $model_issue->id])->all();
                // $model = \common\models\QueryCustomerPrice::find()->all();
                if ($model) {
                    $status = true;
                    foreach ($model as $value) {
                        if ($value->qty == null || $value->qty <= 0) continue;
                        array_push($data, [
                            'id' => $value->product_id,
                            //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                            'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $value->photo,
                            'code' => $value->code,
                            'name' => $value->name,
                            'sale_price' => $value->sale_price,
                            'issue_id' => $value->issue_id,
                            'onhand' => $value->avl_qty
                        ]);
                    }
                }
            }
        }


        return ['status' => $status, 'data' => $data];
    }

    public function actionIssuelist2()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];
        $route_id = $req_data['route_id'];
        $issue_date = $req_data['issue_date'];

        $data = [];
        $status = false;

        if ($route_id != null && $customer_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;
            $exp_order_date = explode(' ', $issue_date);
            if ($exp_order_date != null) {
                if (count($exp_order_date) > 1) {
                    $x_date = explode('-', $exp_order_date[0]);
                    if (count($x_date) > 1) {
                        $t_date = $x_date[0] . "/" . $x_date[1] . "/" . $x_date[2];
                    }
                }
            }
            if ($t_date != null) {
                $trans_date = $t_date;
            }

            $model = null;

//            $model = \common\models\OrderStock::find()->select(['product_id', 'SUM(qty) as qty', 'SUM(avl_qty) as avl_qty'])->where(['route_id' => $route_id, 'date(trans_date)' => date('Y-m-d', strtotime($trans_date))])->groupBy(['product_id'])->all();
//            if(!$model){
            $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -2 day"));
            // $model = \common\models\OrderStock::find()->select(['product_id', 'SUM(qty) as qty', 'SUM(avl_qty) as avl_qty'])->where(['route_id' => $route_id])->andFilterWhere(['BETWEEN', 'date(trans_date)', $pre_date, date('Y-m-d', strtotime($trans_date))])->groupBy(['product_id'])->all();
            $model = \common\models\OrderStock::find()->select(['product_id', 'SUM(qty) as qty', 'SUM(avl_qty) as avl_qty'])->where(['route_id' => $route_id])->groupBy(['product_id'])->all();
            // }

            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    if ($value->qty == null || $value->qty <= 0) continue;
                    array_push($data, [
                        'id' => $value->product_id,
                        'image' => '',
                        'code' => \backend\models\Product::findCode($value->product_id),
                        'name' => \backend\models\Product::findName($value->product_id),
                        'sale_price' => $this->findCustomerprice($customer_id, $value->product_id),
                        //     'price_group_id' => $this->findCustomerpricegroup($customer_id, $value->product_id, $route_id),
                        'price_group_id' => 0, // not include price group
                        'issue_id' => 0,
                        'onhand' => $value->avl_qty
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function findCustomerprice($customer_id, $product_id)
    {
        $price = 0;
        if ($customer_id && $product_id) {
            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id, 'product_id' => $product_id])->one();
            if ($model) {
                $price = $model->sale_price;
            }
        }
        return $price;
    }

    public function findCustomerpricegroup($customer_id, $product_id, $route_id)
    {
        $id = 0;
        if ($customer_id && $product_id && $route_id) {
            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id, 'product_id' => $product_id, 'delivery_route_id' => $route_id])->one();
            if ($model) {
                $id = $model->id;
            }
        }
        return $id;
    }

    public function actionFindreprocessstock()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $itemcode = $req_data['item_code'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;

            $model = \common\models\QueryReprocessStock::find()->where(['LIKE', 'product_name', $itemcode])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    if ($value->qty == null || $value->qty <= 0) continue;
                    array_push($data, [
                        'id' => $value->product_id,
                        'warehouse_id' => $value->warehouse_id,
                        'warehouse_name' => $value->warehouse_name,
                        'product_id' => $value->product_id,
                        'product_code' => $value->product_code,
                        'product_name' => $value->product_name,
                        'qty' => $value->qty
                    ]);
                }

            }
        }
        return ['status' => $status, 'data' => $data];
    }

//    public function findCustomerprice($customer_id, $product_id)
//    {
//        $price = 0;
//        if ($customer_id && $product_id) {
//            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id, 'product_id' => $product_id])->one();
//            if ($model) {
//                $price = $model->sale_price;
//
//            }
//        }
//        return $price;
//    }

    public function actionFindcustpriceoffline()
    {
        $route_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];


        $data = [];
        $status = false;
        if ($route_id) {
            //     $model = \common\models\QueryCustomerPrice::find()->where(['delivery_route_id' => $route_id])->limit(50)->all();
            $model = \common\models\QueryCustomerPrice::find()->where(['delivery_route_id' => $route_id])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
//                   $sale_price_new = 0;
//                   $index = strpos($value->sale_price,'.');
//                   if(!$index){
//                       $sale_price_new = $value->sale_price.'.0';
//                   }else{
//                       $sale_price_new = $value->sale_price;
//                   }
                    array_push($data, [
                        'cus_id' => $value->cus_id == null ? 0 : $value->cus_id,
                        'route_id' => $value->delivery_route_id == null ? 0 : $value->delivery_route_id,
                        'product_id' => $value->product_id,
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'cus_code' => $value->cus_code,
                        'cus_name' => $value->cus_name,
                        'sale_price' => (float)$value->sale_price,
                        'price_group_id' => $value->customer_type_id == null ? 0 : $value->customer_type_id,
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }
    public function actionAdddailycount()
    {
        $company_id = 0;
        $branch_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $datalist = null;
        $rep_no = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $datalist = $req_data['data'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = 0;
        $journal_no = '';


        if ($datalist != null) {

            \common\models\DailyCountStock::deleteAll(['company_id' => $company_id, 'branch_id' => $branch_id,'date(trans_date)'=>date('Y-m-d'), 'status' => 0]); // clear before save

            $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            for($i=0;$i<=count($datalist)-1;$i++){
                $model_journal = new \common\models\DailyCountStock();

                $model_journal->trans_date = date('Y-m-d H:i:s');
                $model_journal->journal_no = 'COUNTED';
                $model_journal->product_id = $datalist[$i]['product_id'];
                $model_journal->qty = (float)$datalist[$i]['qty'];
                $model_journal->warehouse_id = $main_warehouse;
                $model_journal->company_id = $company_id;
                $model_journal->branch_id = $branch_id;
                $model_journal->status = 0;
                $model_journal->user_id = $user_id;
                if ($model_journal->save(false)) {
                    $status = 1;
                    array_push($data, ['id' => $model_journal->id]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddtftransfer()
    {
        $company_id = 0;
        $branch_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $datalist = null;
        $rep_no = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $datalist = $req_data['data'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = 0;
        $journal_no = '';


        if ($datalist != null) {
            $trans_date = date('Y/m/d');
            $journal_no = \backend\models\Journaltransfer::getLastNo2($trans_date, $company_id, $branch_id);

            $model = new \backend\models\Journaltransfer();
            $model->journal_no = $journal_no;
            $model->trans_date = date('Y-m-d H:i:s');
            $model->order_ref_id = 0;
            $model->order_target_id = 0;
            $model->from_car_id = 0;
            $model->to_car_id = 0;
            $model->from_route_id = 0;
            $model->to_route_id = 0;
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            //  $model->created_by = $user_id;
            $model->status = 1;
            if ($model->save(false)) {
                for ($i = 0; $i <= count($datalist) - 1; $i++) {
                    $modelx = new \backend\models\Stocktrans();
                    $modelx->journal_no = $journal_no;
                    $modelx->trans_date = date('Y-m-d H:i:s');
                    $modelx->product_id = $datalist[$i]['product_id'];
                    $modelx->qty = $datalist[$i]['qty'];
                    $modelx->warehouse_id = 1;
                    $modelx->stock_type = 1; // 1 in 2 out
                    $modelx->created_by = $user_id;
                    $modelx->activity_type_id = 5; // transfer
                    $modelx->production_type = 5;
                    $modelx->status = 0;
                    $modelx->company_id = $company_id;
                    $modelx->branch_id = $branch_id;
                    if ($modelx->save(false)) {
                        $model_update_stock = \backend\models\Stocksum::find()->where(['product_id' => $datalist[$i]['product_id'], 'warehouse_id' => 1])->one();
                        if ($model_update_stock) {
                            $model_update_stock->qty = ($model_update_stock->qty + $datalist[$i]['qty']);
                            $model_update_stock->save(false);
                        }
                        $status = 1;
                        array_push($data, ['id' => $model->id]);
                    }
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

}
