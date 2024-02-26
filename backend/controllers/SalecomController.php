<?php

namespace backend\controllers;

use backend\models\ProducttypeSearch;
use Yii;
use backend\models\Salecom;
use backend\models\SalecomSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalecomController implements the CRUD actions for Salecom model.
 */
class SalecomController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access'=>[
                'class'=>AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException('คุณไม่ได้รับอนุญาติให้เข้าใช้งาน!');
                },
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['@'],
                        'matchCallback'=>function($rule,$action){
                            $currentRoute = \Yii::$app->controller->getRoute();
                            if(\Yii::$app->user->can($currentRoute)){
                                return true;
                            }
                        }
                    ]
                ]
            ],
        ];
    }

    /**
     * Lists all Salecom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new SalecomSearch();
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
     * Displays a single Salecom model.
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
     * Creates a new Salecom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Salecom();
        $company_id = 1;
        $branch_id = 1;
        $default_warehouse = 6;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            if ($branch_id == 2) {
                $default_warehouse = 5;
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            $f_date = date('Y-m-d');
            $t_date = date('Y-m-d');

            $x_date = explode('-', $model->from_date);
            if (count($x_date) > 1) {
                $f_date = $x_date[2] . '-' . $x_date[1] . '-' . $x_date[0];
            }

            $xx_date = explode('-', $model->to_date);
            if (count($xx_date) > 1) {
                $t_date = $xx_date[2] . '-' . $xx_date[1] . '-' . $xx_date[0];
            }
            $model->from_date = date('Y-m-d',strtotime($f_date));
            $model->to_date = date('Y-m-d',strtotime($t_date));
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Salecom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $f_date = date('Y-m-d');
            $t_date = date('Y-m-d');

            $x_date = explode('-', $model->from_date);
            if (count($x_date) > 1) {
                $f_date = $x_date[2] . '-' . $x_date[1] . '-' . $x_date[0];
            }

            $xx_date = explode('-', $model->to_date);
            if (count($xx_date) > 1) {
                $t_date = $xx_date[2] . '-' . $xx_date[1] . '-' . $xx_date[0];
            }

           //echo date('Y-m-d',strtotime($f_date));return;
            $model->from_date = date('Y-m-d',strtotime($f_date));
            $model->to_date = date('Y-m-d',strtotime($t_date));

            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Salecom model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Salecom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Salecom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Salecom::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
