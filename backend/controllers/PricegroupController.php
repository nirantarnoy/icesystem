<?php

namespace backend\controllers;

use backend\models\CustomerSearch;
use backend\models\UserSearch;
use Yii;
use backend\models\Pricegroup;
use backend\models\PricegroupSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PricegroupController implements the CRUD actions for Pricegroup model.
 */
class PricegroupController extends Controller
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

    public function actionIndex()
    {
        $pageSize = 50;
        $viewstatus = 1;

        if(\Yii::$app->request->get('viewstatus')!=null){
            $viewstatus = \Yii::$app->request->get('viewstatus');
        }

        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new PricegroupSearch();
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

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

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

        $model = new Pricegroup();

        if ($model->load(Yii::$app->request->post())) {
//            $prod_id = \Yii::$app->request->post('line_prod_id');
//            $prod_price = \Yii::$app->request->post('line_price');

            //print_r($prod_price);return;
            $model->status = 1;
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if ($model->save()) {
//                if (count($prod_id)) {
//                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
//                        $model_line = new \common\models\PriceGroupLine();
//                        $model_line->price_group_id = $model->id;
//                        $model_line->product_id = $prod_id[$i];
//                        $model_line->sale_price = $prod_price[$i] == null ? 0 : $prod_price[$i];
//                        $model_line->status = 1;
//                        $model_line->save();
//                    }
//                }
                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['update', 'id' => $model->id]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_detail = \common\models\PriceGroupLine::find()->where(['price_group_id' => $id])->all();
        $model_customer_type = \common\models\PriceCustomerType::find()->where(['price_group_id' => $id])->all();

        if ($model->load(Yii::$app->request->post())) {
            $recid = \Yii::$app->request->post('line_rec_id');
            $status = \Yii::$app->request->post('status');
            $prod_id = \Yii::$app->request->post('line_prod_id');
            $prod_price = \Yii::$app->request->post('line_price');
            $removelist = \Yii::$app->request->post('removelist');

            $recid2 = \Yii::$app->request->post('line_rec_type_id');
            $customer_type_id = \Yii::$app->request->post('line_type_id');
            $removelist2 = \Yii::$app->request->post('removelist2');
            $line_haft_cal = \Yii::$app->request->post('line_haft_cal');

            // print_r($removelist2);return;
            $model->status = $status;
            if ($model->save()) {
                if (count($prod_id) > 0) {
                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
                        if ($prod_id[$i] == '') {
                            continue;
                        }
                        $model_update = \common\models\PriceGroupLine::find()->where(['product_id' => $prod_id[$i], 'price_group_id' => $model->id])->one();
                        if ($model_update) {
                            $model_update->sale_price = $prod_price[$i] == null ? 0 : $prod_price[$i];
                            $model_update->haft_cal = $line_haft_cal[$i];
                            $model_update->save(false);
                        } else {
                            $model_line = new \common\models\PriceGroupLine();
                            $model_line->price_group_id = $model->id;
                            $model_line->product_id = $prod_id[$i];
                            $model_line->sale_price = $prod_price[$i] == null ? 0 : $prod_price[$i];
                            $model_line->status = 1;
                            $model_line->haft_cal = $line_haft_cal[$i];
                            $model_line->save(false);
                        }
                    }
                }

                if (count($customer_type_id) > 0) {
                    for ($i = 0; $i <= count($customer_type_id) - 1; $i++) {
                        if ($customer_type_id[$i] == '') {
                            continue;
                        }
                        $model_update = \common\models\PriceCustomerType::find()->where(['customer_type_id' => $customer_type_id[$i], 'price_group_id' => $model->id])->one();
                        if ($model_update) {

                        } else {
                            $model_line = new \common\models\PriceCustomerType();
                            $model_line->price_group_id = $model->id;
                            $model_line->customer_type_id = $customer_type_id[$i];
                            $model_line->status = 1;
                            $model_line->save();
                        }
                    }
                }

                if ($removelist != '') {
                    $x = explode(',', $removelist);
                    if (count($x) > 0) {
                        for ($m = 0; $m <= count($x) - 1; $m++) {
                            \common\models\PriceGroupLine::deleteAll(['id' => $x[$m]]);
                        }
                    }

                }
                if ($removelist2 != '') {
                    $x = explode(',', $removelist2);
                    if (count($x) > 0) {
                        for ($m = 0; $m <= count($x) - 1; $m++) {
                            \common\models\PriceCustomerType::deleteAll(['id' => $x[$m]]);
                        }
                    }
                }

                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_detail' => $model_detail,
            'model_customer_type' => $model_customer_type
        ]);
    }

    public function actionDelete($id)
    {
        \common\models\PriceGroupLine::deleteAll(['price_group_id' => $id]);

        $this->findModel($id)->delete();
        $session = Yii::$app->session;
        $session->setFlash('msg', 'ดำเนินการเรียบร้อย');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Pricegroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionProductdata()
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $txt = \Yii::$app->request->post('txt_search');
        $html = '';
        $model = null;
        if ($txt != '') {
            $model = \backend\models\Product::find()->where(['OR', ['LIKE', 'code', $txt], ['LIKE', 'name', $txt]])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'status'=>1])->all();
        } else {
            $model = \backend\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status'=>1])->all();
        }
        foreach ($model as $value) {
            $prod_stock = $this->getStock($value->id);
            $html .= '<tr>';
            $html .= '<td style="text-align: center">
                        <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $value->id . '">เลือก</div>
                        <input type="hidden" class="line-find-code" value="' . $value->code . '">
                        <input type="hidden" class="line-find-name" value="' . $value->name . '">
                        <input type="hidden" class="line-find-price" value="' . $value->sale_price . '">
                        <input type="hidden" class="line-onhand" value="' . $prod_stock . '">
                       </td>';
            $html .= '<td>' . $value->code . '</td>';
            $html .= '<td>' . $value->name . '</td>';
            $html .= '<td>' . number_format($value->std_cost) . '</td>';
            $html .= '<td>' . number_format($value->sale_price) . '</td>';
            $html .= '<td>' . number_format($prod_stock) . '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    public function actionProductdata2()
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $company = \Yii::$app->request->post('company');
        $branch = \Yii::$app->request->post('branch');
        $warehouse = \Yii::$app->request->post('warehouse');

        $html = '';
        $model = null;
//        if($company !=''){
//            $model = \backend\models\Product::find()->where(['OR',['LIKE','code',$txt],['LIKE','name',$txt]])->all();
//        }else{
        $model = \backend\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
        // }
        foreach ($model as $value) {
            $prod_stock = $this->getStock2($company, $branch, $warehouse, $value->id);
            //$prod_stock = 0;
            $html .= '<tr>';
            $html .= '<td style="text-align: center">
                        <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $value->id . '">เลือก</div>
                        <input type="hidden" class="line-find-code" value="' . $value->code . '">
                        <input type="hidden" class="line-find-name" value="' . $value->name . '">
                        <input type="hidden" class="line-find-price" value="' . $value->sale_price . '">
                        <input type="hidden" class="line-onhand" value="' . $prod_stock . '">
                       </td>';
            $html .= '<td>' . $value->code . '</td>';
            $html .= '<td>' . $value->name . '</td>';
            $html .= '<td>' . number_format($value->std_cost) . '</td>';
            $html .= '<td>' . number_format($value->sale_price) . '</td>';
            $html .= '<td style="text-align: right">' . number_format($prod_stock) . '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    public function getStock($prod_id)
    {
        $default_wh = 6;
        if (!empty(\Yii::$app->user->identity->branch_id)) {
           if(\Yii::$app->user->identity->branch_id == 2){
               $default_wh = 5;
           }
        }
        $qty = 0;
        if ($prod_id != null) {
            $model = \backend\models\Stocksum::find()->where(['product_id' => $prod_id, 'warehouse_id' => $default_wh])->one();
            if ($model) {
                $qty = $model->qty;
            }
        }
        return $qty;
    }

    public function getStock2($company, $branch, $warehouse, $product_id)
    {
        $qty = 0;
        if ($company != null && $branch != null && $warehouse != null && $product_id != null) {
            $model = \backend\models\Stocksum::find()->where(['product_id' => $product_id, 'warehouse_id' => $warehouse, 'company_id' => $company, 'branch_id' => $branch])->one();
            if ($model) {
                $qty = $model->qty;
            }
        }
        return $qty;
    }

    public function actionCustomertypedata()
    {
        $company_id = 1;
        $branch_id = 1;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $txt = \Yii::$app->request->post('txt_search');
        $html = '';
        $model = null;
        if ($txt != '') {
            $model = \backend\models\Customertype::find()->where(['OR', ['LIKE', 'code', $txt], ['LIKE', 'name', $txt]])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
        } else {
            $model = \backend\models\Customertype::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
        }
        foreach ($model as $value) {
            $html .= '<tr>';
            $html .= '<td style="text-align: center">
                        <div class="btn btn-outline-success btn-sm" onclick="addselecteditem2($(this))" data-var="' . $value->id . '">เลือก</div>
                        <input type="hidden" class="line-find-type-code" value="' . $value->code . '">
                        <input type="hidden" class="line-find-type-name" value="' . $value->name . '">
                       </td>';
            $html .= '<td>' . $value->code . '</td>';
            $html .= '<td>' . $value->name . '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

}
