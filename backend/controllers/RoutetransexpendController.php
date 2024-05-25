<?php

namespace backend\controllers;

use backend\models\SequenceSearch;
use Yii;
use backend\models\Routetransexpend;
use backend\models\RoutetransexpendSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RoutetransexpendController implements the CRUD actions for Routetransexpend model.
 */
class RoutetransexpendController extends Controller
{
    public $enableCsrfValidation = false;

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
        ];
    }

    /**
     * Lists all Routetransexpend models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new RoutetransexpendSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['id' => SORT_DESC]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
        ]);
    }

    /**
     * Displays a single Routetransexpend model.
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
     * Creates a new Routetransexpend model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Routetransexpend();

        if ($model->load(Yii::$app->request->post())) {

            $trans_date = date('Y-m-d H:i:s');
            $exdate = explode('/', $model->trans_date);
            if ($exdate != null) {
                $trans_date = $exdate[2] . '/' . $exdate[1] . '/' . $exdate[0];


                $line_route_id = \Yii::$app->request->post('line_route_id');
                $line_deduct_1 = \Yii::$app->request->post('line_deduct1');
                $line_deduct_2 = \Yii::$app->request->post('line_deduct2');
                $line_deduct_3 = \Yii::$app->request->post('line_deduct3');
                $line_deduct_4 = \Yii::$app->request->post('line_deduct4');
                $line_deduct_5 = \Yii::$app->request->post('line_deduct5');
                $line_deduct_6 = \Yii::$app->request->post('line_deduct6');
                $line_deduct_7 = \Yii::$app->request->post('line_deduct7');
                $line_deduct_8 = \Yii::$app->request->post('line_deduct8');

                $model->trans_date = date('Y-m-d H:i:s', strtotime($trans_date));
                if ($model->save(false)) {
                    if ($line_route_id != null) {
                        foreach ($line_route_id as $key => $value) {
                            $model_line = new \common\models\RouteTransExpendDaily();
                            $model_line->route_trans_expend_id = $model->id;
                            $model_line->trans_date = date('Y-m-d H:i:s', strtotime($trans_date));
                            $model_line->route_id = $value;
                            $model_line->oil_amount = $line_deduct_1[$key] == null ? 0 : $line_deduct_1[$key];
                            $model_line->extra_amount = $line_deduct_2[$key] == null ? 0 : $line_deduct_2[$key];
                            $model_line->wator_amount = $line_deduct_3[$key] == null ? 0 : $line_deduct_3[$key];
                            $model_line->money_amount = $line_deduct_4[$key] == null ? 0 : $line_deduct_4[$key];
                            $model_line->deduct_amount = $line_deduct_5[$key] == null ? 0 : $line_deduct_5[$key];
                            $model_line->cash_transfer_amount = $line_deduct_6[$key] == null ? 0 : $line_deduct_6[$key];
                            $model_line->payment_transfer_amount = $line_deduct_7[$key] == null ? 0 : $line_deduct_7[$key];
                            $model_line->plus_amount = $line_deduct_8[$key] == null ? 0 : $line_deduct_8[$key];
                            $model_line->save(false);
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
     * Updates an existing Routetransexpend model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\RouteTransExpendDaily::find()->where(['route_trans_expend_id' => $id])->all();

        if ($model->load(Yii::$app->request->post())) {
            $trans_date = date('Y-m-d H:i:s');
            $exdate = explode('/', $model->trans_date);
            if ($exdate != null) {
                $trans_date = $exdate[2] . '/' . $exdate[1] . '/' . $exdate[0];


                $line_route_id = \Yii::$app->request->post('line_route_id');
                $line_deduct_1 = \Yii::$app->request->post('line_deduct1');
                $line_deduct_2 = \Yii::$app->request->post('line_deduct2');
                $line_deduct_3 = \Yii::$app->request->post('line_deduct3');
                $line_deduct_4 = \Yii::$app->request->post('line_deduct4');
                $line_deduct_5 = \Yii::$app->request->post('line_deduct5');
                $line_deduct_6 = \Yii::$app->request->post('line_deduct6');
                $line_deduct_7 = \Yii::$app->request->post('line_deduct7');
                $line_deduct_8 = \Yii::$app->request->post('line_deduct8');

            }
            $model->trans_date = date('Y-m-d H:i:s', strtotime($trans_date));
            if ($model->save(false)) {
                if ($line_route_id != null) {
                    foreach ($line_route_id as $key => $value) {
                        $model_update = \common\models\RouteTransExpendDaily::find()->where(['route_trans_expend_id' => $model->id, 'route_id' => $value])->one();
                        $model_update->trans_date = date('Y-m-d H:i:s', strtotime($trans_date));
                        $model_update->oil_amount = $line_deduct_1[$key] == null ? 0 : $line_deduct_1[$key];
                        $model_update->extra_amount = $line_deduct_2[$key] == null ? 0 : $line_deduct_2[$key];
                        $model_update->wator_amount = $line_deduct_3[$key] == null ? 0 : $line_deduct_3[$key];
                        $model_update->money_amount = $line_deduct_4[$key] == null ? 0 : $line_deduct_4[$key];
                        $model_update->deduct_amount = $line_deduct_5[$key] == null ? 0 : $line_deduct_5[$key];
                        $model_update->cash_transfer_amount = $line_deduct_6[$key] == null ? 0 : $line_deduct_6[$key];
                        $model_update->payment_transfer_amount = $line_deduct_7[$key] == null ? 0 : $line_deduct_7[$key];
                        $model_update->plus_amount = $line_deduct_8[$key] == null ? 0 : $line_deduct_8[$key];
                        $model_update->save(false);
                    }
                }

               return $this->redirect(['routetransexpend/index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }


    /**
     * Deletes an existing Routetransexpend model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (\common\models\RouteTransExpendDaily::deleteAll(['route_trans_expend_id' => $id])) {

        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Routetransexpend model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Routetransexpend the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = Routetransexpend::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
