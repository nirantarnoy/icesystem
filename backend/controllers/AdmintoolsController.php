<?php

namespace backend\controllers;

use backend\models\Uploadfile;
use common\models\LoginForm;
use Yii;
use backend\models\Member;
use backend\models\MemberSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\UploadedFile;

class AdmintoolsController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $model_file = new Uploadfile();
        return $this->render('_index', [
            'model_file' => $model_file
        ]);
    }

    public function actionUpdatelogin()
    {
        $user_id = \Yii::$app->request->post('user_update_log');
        if($user_id){
         //   $max_id = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id])->andFilterWhere(['is', 'logout_date', new \yii\db\Expression('null')])->max('id');
            $max_id = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id])->max('id');
            if($max_id){
                $model = \common\models\LoginLogCal::find()->where(['<','id',$max_id])->orderBy(['id'=>SORT_DESC])->one();
                if($model){
                    $model->logout_date = null;
                    if($model->save(false)){
                        \common\models\LoginLogCal::deleteAll(['id'=>$max_id]);
                    }
                }
            }
        }
        return $this->redirect(['admintools/index']);
    }

    public function actionUpdateissuecancel(){
        $issue_id = \Yii::$app->request->post('issue_id');
        if($issue_id){
           $model = \backend\models\Journalissue::find()->where(['id'=>$issue_id,'status'=>200])->one();
           if($model){
               $model->status = 150; // 150 issue confirm
               $model->save(false);
           }
        }
        return $this->redirect(['admintools/index']);
    }

    public function actionFindlogintime(){
        $login_date = 'ccc';
        $user_id = \Yii::$app->request->post('user_id');
        if($user_id){
           // $max_id = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id])->andFilterWhere(['is', 'logout_date', new \yii\db\Expression('null')])->max('id');
            $max_id = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id])->max('id');
            if($max_id > 0 || $max_id !=null){
                //$login_date = $max_id;
                $model = \common\models\LoginLogCal::find()->where(['<','id',$max_id])->orderBy(['id'=>SORT_DESC])->one();
                if($model){
                  //  $login_date = 'niran';
                    $login_date = date('d-m-Y H:i:s', strtotime($model->login_date));
                }else{

                   // $login_date = date('d-m-Y H:i:s');
                    //$login_date = 'xxx';
                }
            }

        }
        echo $login_date;
    }

    public function actionRollbacksale(){
        $route_id = \Yii::$app->request->post('route_id');
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $res = 0;
     //   $route_id = 894;
        if($route_id > 0){

            $reprocess_wh = $this->findReprocesswh($company_id, $branch_id);
           // echo $reprocess_wh;return;
            if (\backend\models\Orders::updateAll(['status' => 1], ['order_channel_id' => $route_id, 'date(order_date)' => date('Y-m-d'), 'sale_from_mobile' => 1])) {
                $model = \backend\models\Stocktrans::find()->select(['qty','product_id'])->where(['trans_ref_id' => $route_id, 'date(trans_date)' => date('Y-m-d'), 'activity_type_id' => 7,'company_id'=>$company_id,'branch_id'=>$branch_id])->all();
                if ($model) {
                    foreach ($model as $value) {
                        $model_update = \backend\models\Stocksum::find()->where(['product_id' => $value->product_id, 'warehouse_id' => $reprocess_wh,'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
                        if ($model_update) {
                            if($model_update->qty > 0){
                                $model_update->qty = ($model_update->qty - $value->qty);
                                if ($model_update->save(false)) {
                                    $res += 1;
                                    // return qty to order stock

                                    // $model_return = \common\models\OrderStock::find()->where(['product_id'=>$value->product_id,'route_id'=>$route_id,'date(trans_date)'=>date('Y-m-d')])->max('id');
                                    $model_return = \common\models\OrderStock::find()->where(['product_id'=>$value->product_id,'route_id'=>$route_id])->one();
                                    if($model_return){
                                        $model_return->avl_qty = $value->qty;
                                        $model_return->save(false);
                                        //\common\models\OrderStock::updateAll(['avl_qty'=>$value->qty],['id'=>$model_return->id]);
                                    }
                                }
                            }else{
                                $model_return = \common\models\OrderStock::find()->where(['product_id'=>$value->product_id,'route_id'=>$route_id])->one();
                                if($model_return){
                                    $model_return->avl_qty = $value->qty;
                                    if($model_return->save(false)){
                                        $res += 1;
                                    }
                                }
                            }
                        }
                    }
                    if($res > 0){
                        \backend\models\Stocktrans::deleteAll(['trans_ref_id' => $route_id, 'date(trans_date)' => date('Y-m-d'), 'activity_type_id' => 7,'company_id'=>$company_id,'branch_id'=>$branch_id]);
                    }
                }
            }
            if($res > 0){
                echo "success";
            }else{
                echo "fail";
            }
        }
        return $this->redirect(['admintools/index']);
    }
    public function findReprocesswh($company_id, $branch_id)
    {
        $id = 0;
        if ($company_id && $branch_id) {
            $model = \backend\models\Warehouse::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_reprocess' => 1])->one();
            if ($model) {
                $id = $model->id;
            }
        }
        return $id;
    }

    public function actionPullrouteissue($id){
        $route_id = $id;
        $company_id = 1;
        $branch_id = 1;
        $check_route_type = 0;
        $status = 0;
        if ($route_id != null) {
            $issue_id = 0;
            $check_route_type = \backend\models\Deliveryroute::find()->select('type_id')->where(['id' => $route_id])->one();
            $model = \backend\models\Journalissue::find()->select(['id'])->where(['delivery_route_id'=>$route_id,'status'=>2])->andFilterWhere(['date(trans_date)'=>date('Y-m-d')])->all();
            if($model){
                foreach ($model as $value_model){
                    $issue_id = $value_model->id;
                    if($issue_id > 0){

                        $model_update_issue_status = \common\models\JournalIssue::find()->where(['id' => $issue_id])->one();
                        $model_issue_line = \backend\models\Journalissueline::find()->select(['id','product_id','qty','avl_qty','origin_qty'])->where(['issue_id' => $issue_id])->andFilterWhere(['>','origin_qty',0])->all();
                        if($model_issue_line){
                            foreach ($model_issue_line as $val2) {
                                // if ($val2->avl_qty <= 0 || $val2->avl_qty == null) continue;

                                // $old_stock = 0;

                                //$model_check_has_old_product = \common\models\OrderStock::find()->where(['product_id' => $val2->product_id, 'route_id' => $route_id])->orderBy(['id'=>SORT_DESC])->one();
                                $model_check_has_old_product = \common\models\OrderStock::find()->where(['product_id' => $val2->product_id, 'route_id' => $route_id])->andFilterWhere(['!=','date(trans_date)',date('Y-m-d')])->one();
                                if ($model_check_has_old_product) {
                                    $old_qty = $model_check_has_old_product->avl_qty;
                                    $new_qty = ($old_qty + $val2->origin_qty);
                                    $model_check_has_old_product->qty =  $val2->origin_qty;
                                    $model_check_has_old_product->avl_qty = $new_qty;
                                    $model_check_has_old_product->trans_date = date('Y-m-d H:i:s');
                                    if($model_check_has_old_product->save(false)){
                                        if ($model_update_issue_status) {
                                            if ($model_update_issue_status->status != 2) {
                                                $model_update_issue_status->status = 2;
                                                if ($model_update_issue_status->save(false)) {
                                                    $status = 1;
                                                }
                                            }
                                        }
                                        $this->updateStockCar($company_id, $branch_id, $val2->product_id, $route_id); // add new deduct stock from car warehouse
                                    }
                                }
                            }
                        }

                    }
                }
            }



            //}
        }
        echo "status is ". $status;
    }
    public function updateStockCar($company_id, $branch_id, $product_id, $route_id)
    {
        if ($product_id != null && $route_id != null && $company_id && $branch_id) {
            $car_warehouse = \backend\models\Warehouse::findWarehousecar($company_id, $branch_id);
            if ($car_warehouse) {
                $check_car_wh = \backend\models\Stocksum::find()->where(['route_id' => $route_id, 'product_id' => $product_id, 'warehouse_id' => $car_warehouse])->one();
                if ($check_car_wh) {
                    $check_car_wh->qty = 0; // reset stock
                    $check_car_wh->save(false);
                }
                return true;
            }
        }
        //   return false;
    }
}
