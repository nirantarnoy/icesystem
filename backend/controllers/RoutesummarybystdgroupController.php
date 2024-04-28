<?php

namespace backend\controllers;

use backend\models\Customer;
use backend\models\Orders;
use backend\models\ProductgroupSearch;
use backend\models\WarehouseSearch;
use Yii;
use backend\models\Car;
use backend\models\CarSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * CarController implements the CRUD actions for Car model.
 */
class RoutesummarybystdgroupController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
//    public function behaviors()
//    {
////        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
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
//                            $currentRoute = \Yii::$app->controller->getRoute();
//                            if(\Yii::$app->user->can($currentRoute)){
//                                return true;
//                            }
//                        }
//                    ]
//                ]
//            ],
//        ];
//    }

    /**
     * Lists all Car models.
     * @return mixed
     */
    public function actionIndex()
    {
        $route_id = \Yii::$app->request->post('route_id');
        return $this->render('_summarybystdpricegroup',[
            'route_id' => $route_id,
        ]);
    }
}
