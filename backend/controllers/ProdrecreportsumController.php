<?php

namespace backend\controllers;

use backend\models\ProdrecSearch;
use Yii;
use backend\models\Stocksum;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StocksumController implements the CRUD actions for Stocksum model.
 */
class ProdrecreportsumController extends Controller
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
     * Lists all Stocksum models.
     * @return mixed
     */

    public function actionIndex()
    {

        $from_date = null;
        $to_date = null;
        $created_by = null;

        if(!empty(\Yii::$app->request->queryParams['ProdrecSearch'])){

            $from_date = \Yii::$app->request->queryParams['ProdrecSearch']["from_date"];
            $to_date = \Yii::$app->request->queryParams['ProdrecSearch']["to_date"];
            $created_by = \Yii::$app->request->queryParams['ProdrecSearch']["created_by"];

        }

//        $searchModel = new ProdrecSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider->query->select(['product_id','company_id','branch_id','SUM(qty) as qty']);
//        $dataProvider->query->andFilterWhere(['activity_type_id'=>15]);
//        $dataProvider->query->andFilterWhere(['is not','product_id', new \yii\db\Expression('null')]);
//        $dataProvider->query->groupby(['product_id']);
//        $dataProvider->setSort( ['defaultOrder' => ['product_id' => SORT_ASC]]);
//        return $this->render('index_2', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//            'from_date'=> $from_date,
//            'to_date'=> $to_date,
//            'created_by'=>$created_by,
//        ]);

        $searchModel = new ProdrecSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->select(['product_id','company_id','branch_id']);
        $dataProvider->query->andFilterWhere(['activity_type_id'=>15]);
        $dataProvider->query->andFilterWhere(['is not','product_id', new \yii\db\Expression('null')]);
        $dataProvider->query->groupby(['product_id']);
        $dataProvider->setSort( ['defaultOrder' => ['product_id' => SORT_ASC]]);
        return $this->render('index_2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from_date'=> $from_date,
            'to_date'=> $to_date,
            'created_by'=>$created_by,
        ]);


    }

    /**
     * Displays a single Stocksum model.
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
     * Creates a new Stocksum model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stocksum();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stocksum model.
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
     * Deletes an existing Stocksum model.
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
     * Finds the Stocksum model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stocksum the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stocksum::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
