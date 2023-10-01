<?php

namespace backend\controllers;

use backend\models\ProducttypeSearch;
use Yii;
use backend\models\Salecomcon;
use backend\models\SalecomconSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalecomconController implements the CRUD actions for Salecomcon model.
 */
class SalecomconController extends Controller
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
     * Lists all Salecomcon models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new SalecomconSearch();
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
     * Displays a single Salecomcon model.
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
     * Creates a new Salecomcon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Salecomcon();
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        if ($model->load(Yii::$app->request->post())) {
            $fdate = date('Y-m-d');
            $tdate = date('Y-m-d');

            $xdate = explode('/',$model->from_date);
            $xdate2 = explode('/',$model->to_date);

            if($xdate != null){
                if(count($xdate) > 1){
                    $fdate = $xdate[2].'/'. $xdate[1].'/'.$xdate[0];
                }
            }
            if($xdate2 != null){
                if(count($xdate2) > 1){
                    $tdate = $xdate2[2].'/'. $xdate2[1].'/'.$xdate2[0];
                }
            }


            $model->from_date = date('Y-m-d', strtotime($fdate));
            $model->to_date = date('Y-m-d', strtotime($tdate));
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if($model->save(false)){
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Salecomcon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $fdate = date('Y-m-d');
            $tdate = date('Y-m-d');

            $xdate = explode('/',$model->from_date);
            $xdate2 = explode('/',$model->to_date);

            if($xdate != null){
                if(count($xdate) > 1){
                    $fdate = $xdate[2].'/'. $xdate[1].'/'.$xdate[0];
                }
            }
            if($xdate2 != null){
                if(count($xdate2) > 1){
                    $tdate = $xdate2[2].'/'. $xdate2[1].'/'.$xdate2[0];
                }
            }


            $model->from_date = date('Y-m-d', strtotime($fdate));
            $model->to_date = date('Y-m-d', strtotime($tdate));
            if($model->save(false)){

            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Salecomcon model.
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
     * Finds the Salecomcon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Salecomcon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Salecomcon::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
