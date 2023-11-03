<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class AdminreportController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionCardaily(){

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
        return $this->render('_cardaily', [
            'from_date' => $from_date,
            'to_date'=> $to_date,
            'company_id' =>$company_id,
            'branch_id'=>$branch_id,
            'find_emp_id'=>$find_emp_id
        ]);
    }
    public function actionCardailyamount(){

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
        return $this->render('_cardailyamount', [
            'from_date' => $from_date,
            'to_date'=> $to_date,
            'company_id' =>$company_id,
            'branch_id'=>$branch_id,
            'find_emp_id'=>$find_emp_id
        ]);
    }

    public function actionPosdaily(){

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
        return $this->render('_posdailyqty2', [
            'from_date' => $from_date,
            'to_date'=> $to_date,
            'company_id' =>$company_id,
            'branch_id'=>$branch_id,
            'find_emp_id'=>$find_emp_id
        ]);
    }
}