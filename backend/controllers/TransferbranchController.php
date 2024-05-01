<?php

namespace backend\controllers;

use backend\models\UnitSearch;
use Yii;
use backend\models\Transferbranch;
use backend\models\TransferbranchSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransferbranchController implements the CRUD actions for Transferbranch model.
 */
class TransferbranchController extends Controller
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
        ];
    }

    /**
     * Lists all Transferbranch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $viewstatus = 1;

        if(\Yii::$app->request->get('viewstatus')!=null){
            $viewstatus = \Yii::$app->request->get('viewstatus');
        }

        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new TransferbranchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if($viewstatus ==1){
            $dataProvider->query->andFilterWhere(['status'=>$viewstatus]);
        }
        if($viewstatus == 2){
            $dataProvider->query->andFilterWhere(['status'=>0]);
        }

        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
            'viewstatus'=>$viewstatus,
        ]);
    }

    /**
     * Displays a single Transferbranch model.
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
     * Creates a new Transferbranch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transferbranch();

        if ($model->load(Yii::$app->request->post())) {
            $line_product = \Yii::$app->request->post('line_product_id');
            $line_price = \Yii::$app->request->post('line_price');
            if($model->save(false)){
               if($line_product != null){
                   for($x=0;$x<=count($line_product)-1;$x++){
                       $model_line = new \common\models\TransferBranchProductPrice();
                       $model_line->transfer_branch_id = $model->id;
                       $model_line->product_id = $line_product[$x];
                       $model_line->price = $line_price[$x] == null ? 0 :$line_price[$x];
                       $model_line->save(false);
                   }
               }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Transferbranch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\TransferBranchProductPrice::find()->where(['transfer_branch_id'=>$id])->all();

        if ($model->load(Yii::$app->request->post())) {
            $line_product = \Yii::$app->request->post('line_product_id');
            $line_price = \Yii::$app->request->post('line_price');
            if($model->save(false)){
                if($line_product != null){
                    for($x=0;$x<=count($line_product)-1;$x++){
                        $model_update =\common\models\TransferBranchProductPrice::find()->where(['transfer_branch_id'=>$model->id,'product_id'=>$line_product[$x]])->one();
                        if($model_update){
                            $model_update->price = $line_price[$x] == null ? 0 :$line_price[$x];
                            $model_update->save(false);
                        }else{
                            $model_line = new \common\models\TransferBranchProductPrice();
                            $model_line->transfer_branch_id = $model->id;
                            $model_line->product_id = $line_product[$x];
                            $model_line->price = $line_price[$x] == null ? 0 :$line_price[$x];
                            $model_line->save(false);
                        }

                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line,
        ]);
    }

    /**
     * Deletes an existing Transferbranch model.
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
     * Finds the Transferbranch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transferbranch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transferbranch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
