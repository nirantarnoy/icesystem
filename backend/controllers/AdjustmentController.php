<?php

namespace backend\controllers;

use backend\models\BranchSearch;
use Yii;
use backend\models\Adjustment;
use backend\models\AdjustmentSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdjustmentController implements the CRUD actions for Adjustment model.
 */
class AdjustmentController extends Controller
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

    /**
     * Lists all Adjustment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new AdjustmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
        ]);
    }

    /**
     * Displays a single Adjustment model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Adjustment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $company_id = 1;
        $branch_id = 1;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $model = new Adjustment();

        if ($model->load(Yii::$app->request->post())) {
            $product = \Yii::$app->request->post('line_product_id');
            $warehouse = \Yii::$app->request->post('line_warehouse_id');
            $qty = \Yii::$app->request->post('line_qty');
            $stock_type = \Yii::$app->request->post('line_stock_type');

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }

            $model->journal_no = $model->getLastNo($company_id, $branch_id);
            $model->trans_date = date('Y-m-d', strtotime($sale_date));
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if ($model->save()) {
                if ($product != null) {
                    for ($i = 0; $i <= count($product) - 1; $i++) {
                        $model_line = new \backend\models\Stocktrans();
                        $model_line->journal_no = $model->journal_no;
                        $model_line->journal_stock_id = $model->id;
                        $model_line->trans_date = date('Y-m-d H:i:s');
                        $model_line->product_id = $product[$i];
                        $model_line->warehouse_id = $warehouse[$i];
                        $model_line->qty = (float)$qty[$i];
                        $model_line->stock_type = $stock_type[$i];
                        $model_line->activity_type_id = 11;
                        $model_line->company_id = $company_id;
                        $model_line->branch_id = $branch_id;
                        if ($model_line->save(false)) {
                            $model_stock = \backend\models\Stocksum::find()->where(['warehouse_id' => $warehouse[$i], 'product_id' => $product[$i]])->one();
                            if ($model_stock) {
//                                echo "ok";
//                                return;
                                if ($stock_type[$i] == 1) {
                                    $model_stock->qty = (float)$model_stock->qty + (float)$qty[$i];
                                } else if ($stock_type[$i] == 2) {
                                    $model_stock->qty = (float)$model_stock->qty - (float)$qty[$i];
                                }
                                $model_stock->save(false);
                            }
                        }
                    }
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Adjustment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Adjustment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (\backend\models\Stocktrans::deleteAll(['journal_stock_id' => $id])) {
            $this->findModel($id)->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Adjustment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Adjustment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Adjustment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionRoutestock()
    {
        return $this->render('_routestock');
    }

    public function actionUpdatestock()
    {
        $id = \Yii::$app->request->post('line_id');
        $company_id = \Yii::$app->request->post('line_company_id');
        $branch_id = \Yii::$app->request->post('line_branch_id');
        $route_id = \Yii::$app->request->post('line_route_id');
        $product_id = \Yii::$app->request->post('line_product_id');
        $qty = \Yii::$app->request->post('line_qty');
        if ($product_id) {
            for ($ix = 0; $ix <= count($product_id) - 1; $ix++) {
                \common\models\OrderStock::updateAll(['qty' => 0, 'avl_qty' => 0], ['product_id' => $product_id[$ix], 'route_id' => $route_id[$ix]]);
//                $model = \common\models\OrderStock::find()->where(['product_id'=>$product_id[$ix],'route_id'=>$route_id[$ix]])->one();
//                if($model){
//                    $model->qty = 0;
//                    $model->avl_qty = 0;
//                    $model->save(false);
//                }
            }
            for ($i = 0; $i <= count($product_id) - 1; $i++) {
                $model = new \common\models\OrderStock();
                $model->product_id = $product_id[$i];
                $model->route_id = $route_id[$i];
                $model->trans_date = date('Y-m-d H:i:s');
                $model->company_id = $company_id[$i];
                $model->branch_id = $branch_id[$i];
                $model->qty = $qty[$i];
                $model->avl_qty = $qty[$i];
                $model->save(false);
            }
        }
        return $this->redirect(['adjustment/routestock']);
    }

    public function actionGetstockitemOld()
    {
        $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
        $html = '';
        $id = \Yii::$app->request->post('route_id');
        if ($id) {
            $model = \common\models\Product::find()->where(['route_id' => $id, 'date(trans_date)' => date('Y-m-d')])->groupBy(['product_id'])->all();
            if ($model) {
                $i = 0;
                foreach ($model as $value) {
                    $i += 1;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center;">' . $i . '</td>';
                    $html .= '<td>' . \backend\models\Product::findName($value->product_id) . '</td>';
                    $html .= '<td><input type="hidden" class="line-id" name="line_id[]" value="' . $value->id . '" /><input type="hidden" class="line-route-id" name="line_route_id[]" value="' . $value->route_id . '" /><input type="hidden" class="line-company-id" name="line_company_id[]" value="' . $value->company_id . '" /><input type="hidden" class="line-branch-id" name="line_branch_id[]" value="' . $value->branch_id . '" /><input type="hidden" class="line-product-id" name="line_product_id[]" value="' . $value->product_id . '" /><input type="text" class="form-control" name="line_qty[]" value="0" /> </td>';
                    $html .= '</tr>';
                }
            } else {
                $model = \common\models\OrderStock::find()->where(['route_id' => $id, 'date(trans_date)' => $pre_date])->groupBy(['product_id'])->all();
                if ($model) {
                    $i = 0;
                    foreach ($model as $value) {
                        $i += 1;
                        $html .= '<tr>';
                        $html .= '<td style="text-align: center;">' . $i . '</td>';
                        $html .= '<td>' . \backend\models\Product::findName($value->product_id) . '</td>';
                        $html .= '<td><input type="hidden" class="line-id" name="line_id[]" value="' . $value->id . '" /><input type="hidden" class="line-route-id" name="line_route_id[]" value="' . $value->route_id . '" /><input type="hidden" class="line-company-id" name="line_company_id[]" value="' . $value->company_id . '" /><input type="hidden" class="line-branch-id" name="line_branch_id[]" value="' . $value->branch_id . '" /><input type="hidden" class="line-product-id" name="line_product_id[]" value="' . $value->product_id . '" /><input type="text" class="form-control" name="line_qty[]" value="0" /> </td>';
                        $html .= '</tr>';
                    }
                }
            }
        }

        return $html;
    }

    public function actionGetstockitem()
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        // $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
        $html = '';
        $id = \Yii::$app->request->post('route_id');
        if ($id) {
            $model = \common\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->orderBy(['item_pos_seq' => SORT_ASC])->all();
            if ($model) {
                $i = 0;
                foreach ($model as $value) {
                    $i += 1;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center;">' . $i . '</td>';
                    $html .= '<td>' . $value->name . '</td>';
                    $html .= '<td><input type="hidden" class="line-id" name="line_id[]" value="' . $value->id . '" /><input type="hidden" class="line-route-id" name="line_route_id[]" value="' . $id . '" /><input type="hidden" class="line-company-id" name="line_company_id[]" value="' . $value->company_id . '" /><input type="hidden" class="line-branch-id" name="line_branch_id[]" value="' . $value->branch_id . '" /><input type="hidden" class="line-product-id" name="line_product_id[]" value="' . $value->id . '" /><input type="text" class="form-control" name="line_qty[]" value="0" /> </td>';
                    $html .= '</tr>';
                }
            }
        }

        return $html;
    }


}
