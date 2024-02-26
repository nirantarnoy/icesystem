<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class AdminreportreturnController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex(){

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
        $find_emp_id = \Yii::$app->request->post('find_emp_id');
        return $this->render('_reprocesscar', [
            'from_date' => $from_date,
            'to_date'=> $to_date,
            'company_id' =>$company_id,
            'branch_id'=>$branch_id,
            'find_emp_id'=>$find_emp_id
        ]);
    }
    public function actionIndex2(){

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
        $find_emp_id = \Yii::$app->request->post('find_route_id');
        return $this->render('_reprocesscardetail', [
            'from_date' => $from_date,
            'to_date'=> $to_date,
            'company_id' =>$company_id,
            'branch_id'=>$branch_id,
            'find_route_id'=>$find_emp_id
        ]);
    }
}