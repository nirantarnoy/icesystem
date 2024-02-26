<?php

namespace backend\controllers;

use backend\models\IssuesummarySearch;
use backend\models\LocationSearch;
use Yii;
use backend\models\Journalissue;
use backend\models\JournalissueSearch;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JournalissueController implements the CRUD actions for Journalissue model.
 */
class JournalissueController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST', 'GET'],
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

    /**
     * Lists all Journalissue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new JournalissueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
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

        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id) ;
//        if($company_id == 1 && $branch_id ==2){
//            $default_warehouse = 5;
//        }

        $model = new Journalissue();

        if ($model->load(Yii::$app->request->post())) {
            $prod_id = \Yii::$app->request->post('line_prod_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_issue_price = \Yii::$app->request->post('line_issue_line_price');

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }
            $c_time = date('H:i:s');

            $model->journal_no = $model->getLastNo($sale_date, $company_id, $branch_id);
            $model->trans_date = date('Y-m-d H:i:s', strtotime($sale_date.' '.$c_time));
            $model->status = 1;
            $model->reason_id = 1; // เบิกขาย
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            $model->created_by = \Yii::$app->user->id;
            if ($model->save(false)) {
                if ($prod_id != null) {
                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
                        if ($prod_id[$i] == '') continue;
                        $model_line = new \backend\models\Journalissueline();
                        $model_line->issue_id = $model->id;
                        $model_line->product_id = $prod_id[$i];
                        $model_line->qty = $line_qty[$i];
                        $model_line->avl_qty = $line_qty[$i];
                        $model_line->sale_price = $line_issue_price[$i];
                        $model_line->status = 1;
                        if ($model_line->save()) {
                          //  $this->updateStock($prod_id[$i], $line_qty[$i], $default_warehouse, $model->journal_no, $company_id, $branch_id);
                        }
                    }
                }
                $session = \Yii::$app->session;
                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function updateStock($product_id, $qty, $wh_id, $journal_no,$company_id, $branch_id)
    {
        if ($product_id != null && $qty > 0) {
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $journal_no;
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $wh_id;
            $model_trans->stock_type = 2; // 1 in 2 out
            $model_trans->activity_type_id = 6; // 6 issue cars
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            if ($model_trans->save(false)) {
                $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                if ($model) {
                    $model->qty = $model->qty - (int)$qty;
                    $model->save(false);
                }
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \backend\models\Journalissueline::find()->where(['issue_id' => $id])->all();

        if ($model->load(Yii::$app->request->post())) {
            $prod_id = \Yii::$app->request->post('line_prod_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_issue_price = \Yii::$app->request->post('line_issue_line_price');

            $removelist = \Yii::$app->request->post('removelist');

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }

            $model->trans_date = date('Y-m-d', strtotime($sale_date));
            $model->status = 1;
            if ($model->save()) {

                if ($prod_id != null) {
                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
                        if ($prod_id[$i] == '') continue;

                        $model_chk = \backend\models\Journalissueline::find()->where(['issue_id' => $id, 'product_id' => $prod_id[$i]])->one();
                        if ($model_chk) {
                           // echo 'ok';return;
                            $model_chk->qty = $line_qty[$i];
                            $model_chk->save(false);
                        }
//                        else {
//                            $model_line = new \backend\models\Journalissueline();
//                            $model_line->issue_id = $model->id;
//                            $model_line->product_id = $prod_id[$i];
//                            $model_line->qty = $line_qty[$i];
//                            $model_line->sale_price = $line_issue_price[$i];
//                            $model_line->status = 1;
//                            $model_line->save(false);
//                        }
                    }
                }

                if ($removelist != '') {
                    $x = explode(',', $removelist);
                    if (count($x) > 0) {
                        for ($m = 0; $m <= count($x) - 1; $m++) {
                            \common\models\Journalissueline::deleteAll(['id' => $x[$m]]);
                        }
                    }
                }
                $session = \Yii::$app->session;
                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            \backend\models\Journalissueline::deleteAll(['issue_id' => $id]);
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Journalissue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionFindPricegroup()
    {
        $html = '';
        $route_id = \Yii::$app->request->post('route_id');
        $price_group_list = [];

        if ($route_id > 0) {
//            $model = \backend\models\Customer::find()->select(['delivery_route_id'])->where(['delivery_route_id' => $route_id])->groupBy('delivery_route_id')->one();
//            if ($model) {
                $model_prod_price = \common\models\QueryCategoryPrice::find()->where(['delivery_route_id' => $route_id])->groupBy('product_id')->orderBy(['product_id' => SORT_ASC])->all();
                if ($model_prod_price) {
                    $i=0;
                    foreach ($model_prod_price as $value) {
                        $i+=1;
                        $prod_stock = $this->getStock($value->product_id);
                        $is_stock_on_car = $this->checkoncar($value->product_id);
                        $html .= '<tr>';
                        $html .='<td style="text-align: center">'.$i.'</td>';
                        $html .= '<td>
                                <input type="hidden" class="line-prod-id" name="line_prod_id[]"
                                       value="' . $value->product_id . '">
                                ' . $value->code . '
                            </td>';
                        $html .= ' <td>' . $value->name . '</td>';
                      //  $html .= ' <td>' . $value->price_group_name . '</td>';
                        $html .= ' <td>' . $prod_stock . '</td>';
                        $html .= '
                                <td>
                                <input type="hidden" class="line-avl-qty" name="line_avl_qty[]" value="' . $prod_stock . '">
                                <input type="hidden" class="line-stock-on-car" name="line_stock_on_car[]" value="' . $is_stock_on_car . '">
                                <input type="hidden" class="line-issue-sale-price" name="line_issue_line_price[]" value="' . $value->std_cost . '">
                                <input type="number" class="line-qty form-control" name="line_qty[]" value="0" min="0" step="0.10" onchange="checkstock($(this))">
                                </td>
                                <td style="text-align: center">
                                    <div class="btn btn-danger btn-sm" onclick="removeline($(this))"><i
                                                class="fa fa-trash"></i>
                                    </div>
                                </td>
                                ';
                        $html .= '</tr>';
                    }
                }
           // }
        }

        return $html;
    }
    public function checkoncar($product_id){
        $res = 0;
        if($product_id){
            $model = \backend\models\Product::find()->where(['id'=>$product_id])->one();
            if($model){
                $res = $model->stock_on_car;
            }
        }
        return $res;
    }
    public function actionStandardcal(){
        $product_id = \Yii::$app->request->post('product_id');
        $qty = \Yii::$app->request->post('qty');
        $res = 0;
        if($product_id && $qty){
            $model = \backend\models\Product::find()->where(['id'=>$product_id])->one();
            if($model){
                if($model->code == "K"){
                    $std = \common\models\ProductKukStd::find()->one();
                    $item = ($qty / $std->level4_qty);
                    $items = ($item / $std->level2_qty);
                    $res = $items;
                }else if($model->code == "M"){

                }
            }
        }
        echo $res;
    }
    public function getStock($prod_id)
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $default_warehouse = \backend\models\Warehouse::findPrimary($company_id,$branch_id);
//        if ($company_id == 1 && $branch_id == 2) {
//            $default_warehouse = 5;
//        }
        $qty = 0;
        if ($prod_id != null) {
            $model = \backend\models\Stocksum::find()->where(['product_id' => $prod_id, 'warehouse_id' => $default_warehouse])->one();
            if ($model) {
                $qty = $model->qty;
            }
        }
        return $qty;
    }
    public function actionPrint($id){
        if($id){
           // echo $id;return;
            $model = \backend\models\Journalissue::find()->where(['id'=>$id])->one();

            $model_line = \backend\models\Journalissueline::find()->where(['issue_id' => $model->id])->all();
            $this->renderPartial('_printissue', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0, 'branch_id'=> $model->branch_id]);
            $session = \Yii::$app->session;
            $session->setFlash('msg-index-car-pos', 'slip.pdf');
            $session->setFlash('after-save', true);
          //  $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
            return $this->redirect(['journalissue/index']);
        }

    }

    public function actionIssuebyroute(){
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new IssuesummarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['>','delivery_route_id', 0]);
        $dataProvider->query->andFilterWhere(['>','qty', 0]);
        $dataProvider->setSort(['defaultOrder' => ['delivery_route_id' => SORT_ASC]]);
        $dataProvider->pagination->pageSize = 1500;

        $model_bottom = $dataProvider->getModels();

        return $this->render('routesummary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
            'model_bottom' => $model_bottom,
        ]);
    }

}
