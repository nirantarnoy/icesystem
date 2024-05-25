<?php

namespace backend\controllers;

use common\models\QueryPaymentReceive;
use Yii;
use backend\models\PaymenttermSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymenttermController implements the CRUD actions for Paymentterm model.
 */
class PaymentrechistoryController extends Controller
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
     * Lists all Paymentterm models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new \backend\models\QuerypaymentreceiveSearch();
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
     * Displays a single Paymentterm model.
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


    protected function findModel($id)
    {
        if (($model = QueryPaymentReceive::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPrintcarpayment(){
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_user_id = \Yii::$app->request->post('find_user_id');
        $find_customer_id = \Yii::$app->request->post('find_customer_id');
        return $this->render('_print_car_payment', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            //    'find_sale_type'=>$find_sale_type,
            'find_user_id' => $find_user_id,
            'find_customer_id' => $find_customer_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
        ]);
    }
}
