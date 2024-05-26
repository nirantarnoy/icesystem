<?php

namespace backend\controllers;

use backend\models\Transform;
use backend\models\TransformreservSearch;
use backend\models\TransformSearch;
use backend\models\WarehouseSearch;
use common\models\JournalIssue;
use Yii;
use backend\models\Unit;
use backend\models\UnitSearch;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class TransformController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
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
//                            $currentRoute = Yii::$app->controller->getRoute();
//                            if(Yii::$app->user->can($currentRoute)){
//                                return true;
//                            }
//                        }
//                    ]
//                ]
//            ],
        ];
    }


    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
//        $searchModel = new TransformSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider->query->andFilterWhere(['reason_id' => 4]);
//        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
//        $dataProvider->pagination->pageSize = $pageSize;

        $searchModel = new TransformreservSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Transform();
        $company_id = 0;
        $branch_id = 0;
        $user_id = \Yii::$app->user->id;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $default_warehouse = 0;
//        if ($company_id == 1 && $branch_id == 2) {
//            $default_warehouse = 5;
//        }
        $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
        if ($model->load(Yii::$app->request->post())) {

            $from_prod = \Yii::$app->request->post('line_from_product');
            $from_qty = \Yii::$app->request->post('line_from_qty');
            $to_prod = \Yii::$app->request->post('line_to_product');
            $to_qty = \Yii::$app->request->post('line_to_qty');

            // print_r($from_prod);return;

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }
//            $model->journal_no = $model->getLastNo($sale_date, $company_id, $branch_id);
//            $model->trans_date = date('Y-m-d', strtotime($sale_date));
//            $model->status = 1;
//            $model->company_id = $company_id;
//            $model->branch_id = $branch_id;
//            $model->reason_id = 4; // เบิกแปรสภาพ
//                       if ($model->save(false)) {

            if ($from_prod != null) {

                $model_reserv = new \backend\models\Transformreserv();
                $model_reserv->journal_no = $model_reserv::getLastNo(date('Y-m-d'),$company_id,$branch_id);
                $model_reserv->trans_date = date('Y-m-d H:i:s');
                $model_reserv->product_id = $from_prod;
                $model_reserv->qty = $from_qty;
                $model_reserv->status = 0;
                $model_reserv->user_id = $user_id;
                $model_reserv->company_id = $company_id;
                $model_reserv->branch_id = $branch_id;
                if ($model_reserv->save(false)) {

                    for ($i = 0; $i <= count($to_prod) - 1; $i++) {
                        if ($to_prod[$i] == '') continue;
                        $this->updateStockIn($to_prod[$i], $to_qty[$i], $default_warehouse, $model_reserv->id, $company_id, $branch_id);
                    }
                }
            }
            // echo "no";return;
//                if ($from_prod != null) {
//
//                    $model_line = new \backend\models\Journalissueline();
//                    $model_line->issue_id = $model->id;
//                    $model_line->product_id = $from_prod;
//                    $model_line->qty = $from_qty;
//                    $model_line->avl_qty = 0;
//                    $model_line->sale_price = 0;
//                    $model_line->status = 1;
//                    if ($model_line->save(false)) {
//
//                        if ($from_prod != null && $from_qty > 0) {
//                            $model_trans = new \backend\models\Stocktrans();
//                            $model_trans->journal_no = $model->journal_no;
//                            $model_trans->trans_date = date('Y-m-d H:i:s');
//                            $model_trans->product_id = $from_prod;
//                            $model_trans->qty = $from_qty;
//                            $model_trans->warehouse_id = $default_warehouse;
//                            $model_trans->stock_type = 2; // 1 in 2 out
//                            $model_trans->activity_type_id = 20; // 20 issue reprocess
//                            $model_trans->company_id = $company_id;
//                            $model_trans->branch_id = $branch_id;
//                            if ($model_trans->save(false)) {
//                                $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $default_warehouse, 'product_id' => $from_prod])->one();
//                                if ($model_sum) {
//                                    $model_sum->qty = (int)$model_sum->qty - (int)$from_qty;
//                                    $model_sum->save(false);
//                                }
//                            }
//                        }else{
//                            echo "no";return;
//                        }
//                        //$this->updateStock($from_prod, $from_qty, $default_warehouse, $model->journal_no, $company_id, $branch_id);
//                        for ($i = 0; $i <= count($to_prod) - 1; $i++) {
//                            if ($to_prod[$i] == '') continue;
//                            $this->updateStockIn($to_prod[$i], $to_qty[$i], $default_warehouse, $model->journal_no, $company_id, $branch_id);
//                        }
//                    }
//                }

            $session = Yii::$app->session;
            $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
            return $this->redirect(['index']);
            //         }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function updateStock($product_id, $qty, $wh_id, $journal_no, $company_id, $branch_id)
    {
        //   echo "OK";return;
        if ($product_id != null && $qty > 0) {
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $journal_no;
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $wh_id;
            $model_trans->stock_type = 2; // 1 in 2 out
            $model_trans->activity_type_id = 20; // 6 issue cars
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            if ($model_trans->save(false)) {
                $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                if ($model_sum) {
                    $model_sum->qty = $model_sum->qty - (int)$qty;
                    $model_sum->save(false);
                }
            }
        }
    }

    public function updateStockIn($product_id, $qty, $wh_id, $journal_id, $company_id, $branch_id)
    {
        if ($product_id != null && $qty > 0) {
            $user_id = \Yii::$app->user->id;
            $model_journal = new \backend\models\Stockjournal();
            $model_journal->journal_no = $model_journal->getLastNoReprocess($company_id, $branch_id);
            $model_journal->trans_date = date('Y-m-d H:i:s');
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = 27;
            if ($model_journal->save(false)) {
                $model_trans = new \backend\models\Stocktrans();
                $model_trans->journal_no = $model_journal->journal_no;
                $model_trans->trans_date = date('Y-m-d H:i:s');
                $model_trans->product_id = $product_id;
                $model_trans->qty = $qty;
                $model_trans->warehouse_id = $wh_id;
                $model_trans->stock_type = 1; // 1 in 2 out
                $model_trans->activity_type_id = 27; // 27 receive reprocess
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->created_by = $user_id;
                $model_trans->trans_ref_id = $journal_id;
                $model_trans->status = 0;
                if ($model_trans->save(false)) {
                    $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                    if ($model_sum) {
                        $model_sum->qty = (int)$model_sum->qty + (int)$qty;
                        $model_sum->save(false);
                    }

                    //$model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
                    // $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

                    // $session = \Yii::$app->session;
                    // $session->setFlash('msg-index', 'slip_prodrec.pdf');
                    // $session->setFlash('after-save', true);

                    // $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
                }
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\StockTrans::find()->where(['activity_type_id'=>27,'trans_ref_id'=>$id])->all();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $session = Yii::$app->session;
        $session->setFlash('msg', 'ดำเนินการเรียบร้อย');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Transform::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionUpdatedailyboots(){
        return $this->render('_bootbalance');
    }
    public function actionUpdatebalance(){
        $route_id = \Yii::$app->request->post('route_id');
        $product_id = \Yii::$app->request->post('product_id');
        $qty = \Yii::$app->request->post('qty');

        $company_id = 0;
        $branch_id = 0;
        $user_id = \Yii::$app->user->id;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        if($route_id){
            if($product_id != null){
                for($i=0;$i<=count($product_id)-1;$i++){
                    if($qty[$i] == null || $qty[$i]== 0)continue;
                    $model = \common\models\SaleRouteDailyClose::find()->where(['product_id'=>$product_id[$i],'route_id'=>$route_id])->orderBy(['id'=>SORT_DESC])->one();
                    if($model){
                        $model->qty = $qty[$i];
                        $model->save(false);
                    }else{
                        $model = new \common\models\SaleRouteDailyClose();
                        $model->trans_date = date('Y-m-d H:i:s');
                        $model->product_id = $product_id[$i];
                        $model->route_id = $route_id;
                        $model->qty = $qty[$i];
                        $model->order_shift = 1;
                        $model->company_id = 1;
                        $model->branch_id = 2;
                        $model->save(false);
                    }

                    \common\models\OrderStock::updateAll(['avl_qty' => 0], ['route_id' => $route_id, 'product_id' => $product_id[$i]]);

                    $model_stock = new \common\models\OrderStock();
                    $model_stock->issue_id = 0;
                    $model_stock->product_id = $product_id[$i];
                    $model_stock->qty = $qty[$i];
                    $model_stock->avl_qty = $qty[$i];
                    $model_stock->route_id = $route_id;
                    $model_stock->company_id = $company_id;
                    $model_stock->branch_id = $branch_id;
                    $model_stock->order_id = 0;
                    $model_stock->trans_date = date('Y-m-d H:i:s');
                    $model_stock->save(false);

//                    $model_update_order_stock = \common\models\OrderStock::find()->where(['route_id'=>$route_id,'product_id'=>$product_id[$i],'date(trans_date)'=>date('Y-m-d')])->one();
//                    if($model_update_order_stock){
////                        \common\models\OrderStock::updateAll(['avl_qty'=>0],['route_id'=>$route_id,'product_id'=>$product_id[$i],'date(trans_date)'=>date('Y-m-d')]);
////                        $model_update_order_stock->avl_qty = $qty[$i];
////                        $model_update_order_stock->save(false);
//                    }else {
//                        $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
//                        $model_update_order_stock2 = \common\models\OrderStock::find()->where(['route_id' => $route_id, 'product_id' => $product_id[$i], 'date(trans_date)' => $pre_date])->one();
//                        if ($model_update_order_stock2) {
//                            \common\models\OrderStock::updateAll(['avl_qty' => 0], ['route_id' => $route_id, 'product_id' => $product_id[$i],'date(trans_date)'=>$pre_date]);
//                            $model_update_order_stock2->avl_qty = $qty[$i];
//                            $model_update_order_stock2->save(false);
//
//                        }
//                    }
                }
            }
        }

        return $this->render('_bootbalance');
    }

    public function actionGettransformdata()
    {
        $id = \Yii::$app->request->post('id');
        $product_id = \Yii::$app->request->post('product_id');
        $qty = \Yii::$app->request->post('qty');
        $html = '';
        if ($id) {

                $html .= '<tr>';
                $html .= '<td colspan="2"> แปรสภาพจากสินค้า <span style="color: red;font-weight: bold">' . \backend\models\Product::findName($product_id) . '</span> จำนวน <span style="color: red;font-weight: bold">' . number_format($qty==null?0:$qty,2) . '</span></td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>สินค้า</td>';
                $html .= '<td>จำนวน</td>';
                $html .= '</tr>';
                $model = \common\models\StockTrans::find()->select(['product_id', 'qty'])->where(['activity_type_id' => 27, 'trans_ref_id' => $id])->all();
                if ($model) {
                    foreach ($model as $value) {
                        $html .= '<tr>';
                        $html .= '<td>' . \backend\models\Product::findName($value->product_id) . '</td>';
                        $html .= '<td>' . number_format($value->qty,2) . '</td>';
                        $html .= '</tr>';
                    }
                }


        }
        echo $html;
    }
}
