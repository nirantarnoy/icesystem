<?php

namespace backend\controllers;

use backend\models\LocationSearch;
use backend\models\Paymentreceive;
use Yii;
use backend\models\Customerinvoice;
use backend\models\CustomerinvoiceSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerinvoiceController implements the CRUD actions for Customerinvoice model.
 */
class CustomerinvoiceController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST', 'GET'],
                ],
            ],
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['login', 'error'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => [
//                           'getitem'
//                        ],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
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
     * Lists all Customerinvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
//        $pre_date ="2022-10-01 00:00:01";
//
//
//        $sql = "select t1.id as order_id,t1.order_date,sum(t2.line_total) AS remain_amt";
//        $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t2.order_id = t1.id ";
//        $sql .= " WHERE  t1.customer_id =" . 6113;
//        $sql .= " AND t1.payment_status != 1";
//        $sql .= " AND t1.payment_method_id = 2";
//        $sql .= " AND t1.status != 3";
////                $sql .= " AND year(t1.order_date)>=2022";
////                $sql .= " AND month(t1.order_date)>=9";
//        $sql .= " AND date(t1.order_dated) >='". date('Y-m-d',strtotime($pre_date))."'";
//        $sql .= " GROUP BY t1.id";
//
//        $sql_query = \Yii::$app->db->createCommand($sql);
//        $model = $sql_query->queryAll();

        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new CustomerinvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['customer_invoice.status' => 0,'invoice_type'=>1]);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
        ]);
    }

    /**
     * Displays a single Customerinvoice model.
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
     * Creates a new Customerinvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
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
        $model = new Customerinvoice();


        if ($model->load(Yii::$app->request->post())) {
            $xdate = explode('/', $model->trans_date);
            $t_date = date('Y-m-d H:i:s');
            if (count($xdate) > 1) {
                $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0] . ' ' . date('H:i:s');
            }

            $list_order = \Yii::$app->request->post('list_order');
            $list_note = \Yii::$app->request->post('note');

            //print_r($list_order);return;
         //   print_r(\Yii::$app->request->post());return;

            $model->journal_no = \backend\models\Customerinvoice::getLastNo($company_id, $branch_id);
            $model->trans_date = date('Y-m-d H:i:s', strtotime($t_date));
            $model->status = 0;
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            $model->invoice_type = 1;
            if ($model->save(false)) {
                $xlist = explode(',', $list_order);
                if ($xlist != null) {
                    for ($i = 0; $i <= count($xlist) - 1; $i++) {
                        $remain_amt = 0;
                        $check_is_cus_car = \backend\models\Customer::find()->select('is_show_pos')->where(['id' => $model->customer_id])->one();
                        if ($check_is_cus_car->is_show_pos == 0 || $check_is_cus_car->is_show_pos == null) { // route
                           // echo "this";return;
                            //  $customer_remain_amount = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $model->customer_id, 'order_id' => $xlist[$i]])->one();
                            //    $customer_remain_amount = \common\models\OrderLine::find()->where(['customer_id' => $model->customer_id, 'order_id' => $xlist[$i]])->sum('qty');
                          //  $customer_remain_amount = \common\models\OrderLine::find()->where(['order_id' => $xlist[$i], 'customer_id' => $model->customer_id])->sum('line_total');
                            $customer_remain_amount = \common\models\Orders::find()->select(['order_total_amt'])->where(['id' => $xlist[$i]])->one();
                            if ($customer_remain_amount) {
                                // $customer_remain_amount;
                                $model_line = new \common\models\CustomerInvoiceLine();
                                $model_line->customer_invoice_id = $model->id;
                                $model_line->order_id = $xlist[$i];
                                $model_line->amount = $customer_remain_amount->order_total_amt;
                                $model_line->remain_amount = $customer_remain_amount->order_total_amt;
                                $model_line->status = 1;
                            //    $model_line->note = $list_note[$i]==null?'':$list_note[$i];
                                if ($model_line->save(false)) {
                                    \common\models\Orders::updateAll(['create_invoice'=>1],['id'=>$xlist[$i]]);
                                    // update payment_status in order table

//                                    $model_update_order = \common\models\Orders::find()->where(['id'=>$xlist[$i]])->one();
//                                    if($model_update_order != null){
//                                        $model_update_order->payment_status = 1;
//                                        $model_update_order->save(false);
//                                    }

//                                    if(\common\models\Orders::updateAll(['create_invoice'=>1],['id'=>$xlist[$i]])){
//                                        $model_wait_pey = new \common\models\OrderWaitPayment();
//                                        $model_wait_pey->order_id = $xlist[$i];
//                                        $model_wait_pey->created_at = time();
//                                        $model_wait_pey->created_by = \Yii::$app->user->id;
//                                        $model_wait_pey->save(false);
//                                    }
                                }
                            }
                        } else {
//                            $customer_remain_amount = \common\models\Orders::find()->where(['customer_id' => $model->customer_id, 'id' => $xlist[$i]])->one();
                            $is_payment = 0;
//                            if ($customer_remain_amount) {
//                                $remain_amt = $customer_remain_amount->order_total_amt;
//                                $model_line = new \common\models\CustomerInvoiceLine();
//                                $model_line->customer_invoice_id = $model->id;
//                                $model_line->order_id = $xlist[$i];
//                                $model_line->amount = $remain_amt;
//                                $model_line->remain_amount = $remain_amt;
//                                $model_line->status = 1;
//                               // $model_line->note = $list_note[$i]==null?'':$list_note[$i];
//                                if ($model_line->save(false)) {
//                                    $is_payment += 1;
//
//                                    $model_wait_pey = new \common\models\OrderWaitPayment();
//                                    $model_wait_pey->order_id = $xlist[$i];
//                                    $model_wait_pey->created_at = time();
//                                    $model_wait_pey->created_by = \Yii::$app->user->id;
//                                    $model_wait_pey->save(false);
//                                }
//                            }
//                            if ($is_payment > 0) {
////                                $customer_remain_amount->payment_status = 1; // update payment status
////                                $customer_remain_amount->save(false);
//                            }

                            $sql = "select distinct(t1.order_total_amt) as order_total_amt";
                            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t2.order_id = t1.id ";
                            $sql .= " WHERE (t1.customer_id =" . $model->customer_id." OR t2.customer_id=".$model->customer_id.")";
                            $sql .= " AND t1.id =". $xlist[$i];

                            $sql_query = \Yii::$app->db->createCommand($sql);
                            $modelcheck = $sql_query->queryAll();
                            if($modelcheck){
                                for($x=0;$x<=count($modelcheck)-1;$x++){
                                    $remain_amt = $modelcheck[$x]['order_total_amt'];
                                    $model_line = new \common\models\CustomerInvoiceLine();
                                    $model_line->customer_invoice_id = $model->id;
                                    $model_line->order_id = $xlist[$i];
                                    $model_line->amount = $remain_amt;
                                    $model_line->remain_amount = $remain_amt;
                                    $model_line->status = 1;
                                    // $model_line->note = $list_note[$i]==null?'':$list_note[$i];
                                    if ($model_line->save(false)) {
                                        $is_payment += 1;

                                        \common\models\Orders::updateAll(['create_invoice'=>1],['id'=>$xlist[$i]]);

//                                        $model_wait_pey = new \common\models\OrderWaitPayment();
//                                        $model_wait_pey->order_id = $xlist[$i];
//                                        $model_wait_pey->created_at = time();
//                                        $model_wait_pey->created_by = \Yii::$app->user->id;
//                                        $model_wait_pey->save(false);
                                    }
                                }
                            }

                        }
                    }
                }
                //return $this->redirect(['view', 'id' => $model->id]);
                return $this->redirect(['printinvoice', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\CustomerInvoiceLine::find()->where(['customer_invoice_id' => $id, 'status' => 1])->all();

        if ($model->load(Yii::$app->request->post())) {
            $company_id = 0;
            $branch_id = 0;
            $user_id = 1;//
            if(\Yii::$app->user->id!=null){
                $user_id = \Yii::$app->user->id;
            }
            if (!empty(\Yii::$app->user->identity->company_id)) {
                $company_id = \Yii::$app->user->identity->company_id;
            }
            if (!empty(\Yii::$app->user->identity->branch_id)) {
                $branch_id = \Yii::$app->user->identity->branch_id;
            }

            $line_id = \Yii::$app->request->post('line_id');
            $line_pay_type = \Yii::$app->request->post('line_payment_type');
            $line_pay_status = \Yii::$app->request->post('line_pay_status');
            $model->status = 1; // close
            if ($model->save()) {
                if ($line_id != null) {
                    for ($i = 0; $i <= count($line_id) - 1; $i++) {
                        $model_update = \common\models\CustomerInvoiceLine::find()->where(['id' => $line_id[$i]])->one();
                        if ($model_update) {
                            $model_update->status = 100; // close
                            $model_update->emp_id = $user_id;
                            $model_update->updated_at = strtotime(date('Y-m-d H:i:s'));
                            $model_update->updated_by = $user_id;
                            if ($model_update->save(false)) {

                                $model_pay = new Paymentreceive();
                                $model_pay->trans_date = date('Y-m-d H:i:s');
                                $model_pay->journal_no = $model->getLastNo($company_id, $branch_id);
                                $model_pay->status = 1;
                                $model_pay->company_id = $company_id;
                                $model_pay->branch_id = $branch_id;
                                $model_pay->crated_by = $user_id;
                                if ($model_pay->save(false)) {
                                    $model_line_pay = new \common\models\PaymentReceiveLine();
                                    $model_line_pay->order_id = $model_update->order_id;
                                    $model_line_pay->payment_receive_id = $model_pay->id;
                                    $model_line_pay->payment_amount = $model_update->remain_amount;
                                    $model_line_pay->payment_channel_id = $line_pay_type[$i];
                                    $model_line_pay->payment_method_id = 2;
                                    $model_line_pay->status = 1;
                                    if($model_line_pay->save(false)){
                                      //  \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$model_update->order_id]);
                                    }
                                }
                            }
                        }
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line,
        ]);
    }

    /**
     * Deletes an existing Customerinvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //  $this->findModel($id)->delete();
        $model = \backend\models\Customerinvoice::find()->where(['id' => $id])->one();
        if ($model) {
            $model_line = \common\models\CustomerInvoiceLine::find()->where(['customer_invoice_id' => $id])->all();
            if ($model_line) {
                foreach ($model_line as $value){
                    \common\models\Orders::updateAll(['create_invoice'=>0],['id'=>$value->order_id]);
                }

            }
            if (\common\models\CustomerInvoiceLine::deleteAll(['customer_invoice_id' => $id])) {
                $this->findModel($id)->delete();
            }
            $this->findModel($id)->delete();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Customerinvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customerinvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customerinvoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetitem()
    {
        $cus_id = \Yii::$app->request->post('customer_id');
        $html = '';
        $total_amount = 0;
//        $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . "-3 month"));
        $pre_date ="2024-01-01 00:00:01";
        if ($cus_id) {
            // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['>', 'remain_amount', 0])->all();
           // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->andFilterWhere(['!=', 'payment_status', 1])->orderBy(['order_id' => SORT_DESC])->all();
         //   $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->andFilterWhere(['OR',['is', 'payment_status', new \yii\db\Expression('null')],['!=', 'payment_status', 1]])->orderBy(['order_id' => SORT_DESC])->all();
       //     $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->andFilterWhere(['OR',['is', 'payment_status', new \yii\db\Expression('null')],['!=', 'payment_status', 1]])->orderBy(['order_id' => SORT_DESC])->all();
           // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['!=', 'payment_status', 1])->orderBy(['order_id' => SORT_DESC])->all();

//            $sql = "select t1.id as order_id,t1.order_date,sum(t2.line_total) AS remain_amt";
//            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t1.id = t2.order_id ";
//            $sql .= " WHERE  (t1.customer_id =" . $cus_id. " OR t2.customer_id=".$cus_id.")";
//            $sql .= " AND (t1.payment_status !=1 OR ISNULL(t1.payment_status))";
//            $sql .= " AND t1.payment_method_id = 2";
//            $sql .= " AND t1.status != 3";


            $sql = "select t1.id as order_id,t1.order_date,sum(t2.line_total) AS remain_amt";
            $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t1.id = t2.order_id ";
            $sql .= " WHERE  (t1.customer_id =" . $cus_id. " OR t2.customer_id=".$cus_id.")";
            $sql .= " AND t1.payment_method_id = 2";
            $sql .= " AND t1.status != 3";
            $sql .= " AND (t1.create_invoice != 1 OR ISNULL(t1.create_invoice))";


//            $sql .= " AND year(t1.order_date)>=2022";
//            $sql .= " AND month(t1.order_date)>=10";
            $sql .= " AND date(t1.order_date) >='". date('Y-m-d',strtotime($pre_date))."'";
            $sql .= " GROUP BY t1.id";

            $sql_query = \Yii::$app->db->createCommand($sql);
            $model = $sql_query->queryAll();

            if ($model) {
//                $html = $cus_id;

                for ($i = 0; $i <= count($model) - 1; $i++) {

                    //   $total_amount = $total_amount + ($value->remain_amount == null ? 0 : $value->remain_amount);
//                    $remain_amt = $value->line_total;
//
//                    if ($value->remain_amount == null && $value->payment_amount != null) {
//                        $remain_amt = $value->line_total - $value->payment_amount;
//                    } else {
//                        $remain_amt = $value->remain_amount;
//                    }
                    if($model[$i]['remain_amt'] <= 0)continue;
                    //  $remain_amt = $value->remain_amount == null?$value->payment_amount:$value->remain_amount;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center"><input type="checkbox" data-var="' . $model[$i]['order_id'] . '" style="transform: scale(1.5)" onchange="showselectpayment($(this))"></td>';
                    $html .= '<td style="text-align: center">' . \backend\models\Orders::getNumber($model[$i]['order_id']) . '</td>';
                    $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($model[$i]['order_date'])) . '</td>';
//                    $html .= '<td style="text-align: center"><input type="file" class="form-control"></td>';
                    $html .= '<td>
                            <input type="text" class="form-control line-remain" style="text-align: right" name="line_remain[]" value="' . number_format($model[$i]['remain_amt'], 2) . '" readonly>
                            <input type="hidden" class="line-remain-qty" value="' . $model[$i]['remain_amt'] . '">
                            </td>';
                    $html .= '<td>
                                <input type="text" class="form-control" name="note[]" value="">
                            </td>';
                    $html .= '</tr>';

                }
                // $html .= '<tr><td colspan="4" style="text-align: right">รวม</td><td style="text-align: right;font-weight: bold">' . number_format($total_amount, 2) . '</td><td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td></tr>';
            } else {
              //  $model = \common\models\QuerySalePosPaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->andFilterWhere(['!=', 'status', 3])->andFilterWhere(['!=', 'payment_status', 1])->orderBy(['order_id' => SORT_DESC])->all();
               // $model = \common\models\QuerySalePosPaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['!=', 'status', 3])->andFilterWhere(['!=', 'payment_status', 1])->orderBy(['order_id' => SORT_DESC])->all();
               // $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . "-3 month"));
                $sql = "select t1.id as order_id,t1.order_date,sum(t2.line_total) AS remain_amt";
                $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t2.order_id = t1.id ";
                $sql .= " WHERE  t1.customer_id =" . $cus_id;
                $sql .= " AND t1.payment_status != 1";
                $sql .= " AND t1.payment_method_id = 2";
                $sql .= " AND t1.status != 3";
//                $sql .= " AND year(t1.order_date)>=2022";
//                $sql .= " AND month(t1.order_date)>=9";
                $sql .= " AND date(t1.order_date) >='". date('Y-m-d',strtotime($pre_date))."'";
                $sql .= " GROUP BY t1.id";

                $sql_query = \Yii::$app->db->createCommand($sql);
                $model = $sql_query->queryAll();
                if ($model) {
                   // echo "OK";
//                $html = $cus_id;

                    for ($i = 0; $i <= count($model) - 1; $i++) {
                       // if ($value->remain_amount == null || $value->remain_amount == 0) continue;

                        //   $total_amount = $total_amount + ($value->remain_amount == null ? 0 : $value->remain_amount);
//                        $remain_amt = $value->line_total;
//
//                        if ($value->remain_amount == null && $value->payment_amount != null) {
//                            $remain_amt = $value->line_total - $value->payment_amount;
//                        } else {
//                            $remain_amt = $value->remain_amount;
//                        }
//                        if($remain_amt < 1)continue;
                        //  $remain_amt = $value->remain_amount == null?$value->payment_amount:$value->remain_amount;
                        $html .= '<tr>';
                        $html .= '<td style="text-align: center"><input type="checkbox" data-var="' . $model[$i]['order_id'] . '" style="transform: scale(1.5)" onchange="showselectpayment($(this))"></td>';
                        $html .= '<td style="text-align: center">' . \backend\models\Orders::getNumber($model[$i]['order_id']) . '</td>';
                        $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($model[$i]['order_date'])) . '</td>';
//                    $html .= '<td style="text-align: center"><input type="file" class="form-control"></td>';
                        $html .= '<td>
                            <input type="text" class="form-control line-remain" style="text-align: right" name="line_remain[]" value="' . number_format($model[$i]['remain_amt'], 2) . '" readonly>
                            <input type="hidden" class="line-remain-qty" value="' . $model[$i]['remain_amt'] . '">
                            </td>';
                        $html .= '<td>
                                <input type="text" class="form-control" name="note[]" value="">
                            </td>';
                        $html .= '</tr>';

                    }
                    // $html .= '<tr><td colspan="4" style="text-align: right">รวม</td><td style="text-align: right;font-weight: bold">' . number_format($total_amount, 2) . '</td><td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td></tr>';
                }
            }
        }

        echo $html;
    }

    public function actionPrint($id)
    {
        if ($id) {
            $model_line = null;
            $model_min_max = [];
            $model = \backend\models\Customerinvoice::find()->where(['id' => $id])->one();
            if ($model) {
                $model_line = \common\models\CustomerInvoiceLine::find()->where(['customer_invoice_id' => $id])->orderBy(['order_id' => SORT_ASC])->all();
                if ($model_line) {
                    foreach ($model_line as $value) {
                        array_push($model_min_max, ['date' => date('Y-m-d', strtotime(\backend\models\Orders::getOrderdate($value->order_id)))]);
                    }
                }
            }
            return $this->render('_print3', [
                'model' => $model,
                'model_line' => $model_line,
                'model_min_max' => $model_min_max,
            ]);
        } else {
            return $this->redirect('customerinvoice/index');
        }
    }

    public function actionPrintinvoice($id)
    {
        if ($id) {
            $model_line = null;
            $model_min_max = [];
            $model = \backend\models\Customerinvoice::find()->where(['id' => $id])->one();
            if ($model) {
                $model_line = \common\models\CustomerInvoiceLine::find()->where(['customer_invoice_id' => $id])->orderBy(['order_id' => SORT_ASC])->all();
                if ($model_line) {
                    foreach ($model_line as $value) {
                        array_push($model_min_max, ['date' => date('Y-m-d', strtotime(\backend\models\Orders::getOrderdate($value->order_id)))]);
                    }
                }
            }
            return $this->render('_print3', [
                'model' => $model,
                'model_line' => $model_line,
                'model_min_max' => $model_min_max,
            ]);
        } else {
            return $this->redirect('customerinvoice/index');
        }

    }

    public function actionBillcycle(){
      return $this->render('_billlist');
    }

    public function actionCloseorderpayment(){
        $res = 0;
        $data = [];
       // $model = \common\models\OrderWaitPayment::find()->select(['order_id'])->limit(5)->all();
      //  $model = \common\models\OrderWaitPayment::find()->select(['order_id'])->limit(5)->orderBy(['id'=>SORT_ASC])->all();
        $model = \common\models\OrderWaitPayment::find()->select(['order_id'])->orderBy(['id'=>SORT_ASC])->all();
        if($model){
            foreach($model as $value){
              //  $model_update_order = \common\models\Orders::find()->where(['id'=>$value->order_id])->andFilterWhere(['!=','create_invoice',1])->one();
                $model_update_order = \common\models\Orders::find()->where(['id'=>$value->order_id])->one();
                if($model_update_order){
                    $model_update_order->create_invoice = 1;
                    if($model_update_order->save(false)){
                        if(\common\models\OrderWaitPayment::deleteAll(['order_id'=>$value->order_id])){
                            $res += 1;
                        }
                       // $res+=1;
                        array_push($data,['id'=>$value->order_id]);
                    }
                }

            }
        }
        if($res > 0){
           // $this->del
            echo "success ".$res.' records';
        }else{
            echo "not success";
        }
    }
    public function actionDeleteorderpayment(){
        $res = 0;
        if(\common\models\OrderWaitPayment::deleteAll()){
            $res = 1;
        }

        if($res > 0){
            echo "success";
        }else{
            echo "not success";
        }
    }
}
