<?php

namespace backend\controllers;

use common\models\QueryPaymentReceive;
use Yii;
use backend\models\Paymentreceive;
use backend\models\PaymentreceiveSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class PaymentreceiveController extends Controller
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
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new \backend\models\PaymentreceiveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->limit(300);

        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
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
        $company_id = 0;
        $branch_id = 0;
        $user_id = 1;
        if (!empty(\Yii::$app->user->id)) {
            if (\Yii::$app->user->id == null) {
                $user_id = 1;
            } else {
                $user_id = \Yii::$app->user->id;
            }

        }
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $model = new Paymentreceive();

        if ($model->load(Yii::$app->request->post())) {

            $line_order = \Yii::$app->request->post('line_order_id');
            $line_pay_type = \Yii::$app->request->post('line_pay_type');
            $line_pay = \Yii::$app->request->post('line_pay');
            $line_number = \Yii::$app->request->post('line_number');
            $payment_customer_id = \Yii::$app->request->post('customer_car_id');

//            echo count($line_order).'<br/>';
//            echo count($line_pay_type).'<br/>';
//            echo count($line_number).'<br/>';
//            echo count($line_pay);return;

//             print_r($line_pay);return;

            $uploaded_file = UploadedFile::getInstancesByName('line_doc');

            $xdate = explode('/', $model->trans_date);
            $t_date = date('Y-m-d H:i:s');
            if (count($xdate) > 1) {
                $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0] . ' ' . date('H:i:s');
            }

            $model->trans_date = date('Y-m-d H:i:s', strtotime($t_date));
            $model->journal_no = $model->getLastNo(date('Y-m-d'));
            $model->status = 1;
            if($payment_customer_id != null){
                $model->customer_id = $payment_customer_id;
            }

            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            $model->crated_by = $user_id;
            if ($model->save(false)) {
               // echo "ok";return;
                if ($line_order != null) {
                    if (count($line_order) > 0) {

                        for ($i = 0; $i <= count($line_order) - 1; $i++) {
                            if($i > count($line_pay)-1 ){continue;}
                            if ($line_pay[$i] == 0 || $line_pay[$i] == null)
                            {
                               // $i++;
                                continue;
                            }

                              // echo $line_pay[$i];return;
                            $model_line = new \common\models\PaymentReceiveLine();
                            $model_line->order_id = $line_order[$i];
                            $model_line->payment_receive_id = $model->id;
                            $model_line->payment_amount = $line_pay[$i];
                            $model_line->payment_channel_id = $line_pay_type[$i];
                            $model_line->payment_method_id = 2;
                            $model_line->status = 1;

                            if ($i == $line_number[$i]) {
                                if (!empty($uploaded_file)) {
                                    foreach ($uploaded_file as $files) {
                                        $file_name = time() . '.' . $files->getExtension();
                                        $files->saveAs(Yii::getAlias('@backend') . '/web/uploads/files/receive/' . $file_name);
                                        $model_line->doc = $file_name;
                                    }
                                }

                            }

                            if ($model_line->save(false)) {
                                \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$line_order[$i]]);
                                //$this->updatePaymenttransline($model->customer_id, $line_order[$i], $line_pay[$i], 1); // deprecate
                            }
                        }
                    }
                }
                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['index']);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function updatePaymenttransline($customer_id, $order_id, $pay_amt, $pay_type)
    {
        if ($customer_id != null && $order_id != null && $pay_amt > 0) {
            //     $model = \backend\models\Paymenttransline::find()->where(['customer_id'=>$customer_id,'order_ref_id'=>$order_id])->andFilterWhere(['payment_method_id'=>2])->one();
            $model = \backend\models\Paymenttransline::find()->innerJoin('payment_method', 'payment_trans_line.payment_method_id=payment_method.id')->where(['payment_trans_line.customer_id' => $customer_id, 'payment_trans_line.order_ref_id' => $order_id])->andFilterWhere(['payment_method.pay_type' => 2])->one();
            if ($model) {
                if ($pay_type == 0) {
                    $model->payment_amount = ($model->payment_amount - (float)$pay_amt);
                } else {
                    $model->payment_amount = ($model->payment_amount + (float)$pay_amt);
                }

                $model->save(false);
            }
        }
    }

    /**
     * Updates an existing Paymentreceive model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\PaymentReceiveLine::find()->where(['payment_receive_id' => $id])->all();

        if ($model->load(Yii::$app->request->post())) {

            $line_order = \Yii::$app->request->post('line_order_id');
            $line_pay_type = \Yii::$app->request->post('line_pay_type');
            $line_pay = \Yii::$app->request->post('line_pay');
            $line_id = \Yii::$app->request->post('line_id');
            $line_number = \Yii::$app->request->post('line_number');

            $uploaded_file = UploadedFile::getInstancesByName('line_doc');

            $xdate = explode('/', $model->trans_date);
            $t_date = date('Y-m-d H:i:s');
            if (count($xdate) > 1) {
                $t_date = $xdate[2] . '-' . $xdate[1] . '-' . $xdate[0] . ' ' . date('H:i:s');
            }

            $model->trans_date = date('Y-m-d H:i:s', strtotime($t_date));
            if ($model->save()) {
                if ($line_order != null) {
                    if (count($line_order) > 0) {
                        for ($i = 0; $i <= count($line_order) - 1; $i++) {

                            if ($line_id != null) {
                                $model_chk = \common\models\PaymentReceiveLine::find()->where(['id' => $line_id[$i]])->one();
                                if ($model_chk) {
                                    $model_chk->payment_channel_id = $line_pay_type[$i];
                                    $model_chk->payment_amount = $line_pay[$i];
                                }
                            } else {
                                $model_line = new \common\models\PaymentReceiveLine();
                                $model_line->order_id = $line_order[$i];
                                $model_line->payment_receive_id = $model->id;
                                $model_line->payment_amount = $line_pay[$i];
                                $model_line->payment_method_id = 2;
                                $model_line->status = 1;
                                if ($i == $line_number[$i]) {
                                    if (!empty($uploaded_file)) {
                                        foreach ($uploaded_file as $files) {
                                            $file_name = time() . '.' . $files->getExtension();
                                            $files->saveAs(Yii::getAlias('@backend') . '/web/uploads/files/receive/' . $file_name);
                                            $model_line->doc = $file_name;
                                        }
                                    }
                                }
                                $model_line->save();
                            }
                        }
                    }
                }
                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }

    /**
     * Deletes an existing Paymentreceive model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $customer_id = 0;
        $order_id = 0;
        $qty = 0;
        if ($id) {
            $model = \backend\models\Paymentreceive::find()->where(['id' => $id])->one();
            if ($model) {
                $customer_id = $model->customer_id;
                $model_line = \common\models\PaymentReceiveLine::find()->where(['payment_receive_id' => $id])->one();
                if ($model_line) {
                    $this->updatePaymenttransline($model->customer_id, $model_line->order_id, $model_line->payment_amount, 0);
                    if (\common\models\PaymentReceiveLine::deleteAll(['payment_receive_id' => $id])) {
                        \common\models\Orders::updateAll(['payment_status'=>0],['id'=>$model_line->order_id]);
                        $this->findModel($id)->delete();
                    }
                }else{
                    $this->findModel($id)->delete();
                }
            }

//            if(\common\models\PaymentReceiveLine::deleteAll(['payment_receive_id'=>$id])){
//                $this->findModel($id)->delete();
//            }
        }

        return $this->redirect(['index']);
    }

//    public function updatePaymenttransline($customer_id, $order_id, $qty){
//         if($customer_id && $order_id && $qty){
//             $model = \backend\models\Paymenttransline::find(['customer_id'=>$customer_id,'order_ref_id'=>$order_id])->one();
//             if($model){
//                 $old_q = $model->payment_amount;
//                 $model->payment_amount = ($old_q - $qty);
//                 $model->save(false);
//             }
//         }
//    }

    /**
     * Finds the Paymentreceive model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Paymentreceive the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Paymentreceive::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetitem()
    {
        $cus_id = \Yii::$app->request->post('customer_id');
        $html = '';
        $total_amount = 0;
        if ($cus_id) {
            // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['>', 'remain_amount', 0])->all();
            $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR', ['is', 'payment_amount', new \yii\db\Expression('null')], ['>', 'remain_amount', 0]])->all();
            if ($model) {
//                $html = $cus_id;
                $i = 0;
                foreach ($model as $value) {
                    $i += 1;
                    //   $total_amount = $total_amount + ($value->remain_amount == null ? 0 : $value->remain_amount);
                    $remain_amt = $value->line_total;

                    if ($value->remain_amount == null && $value->payment_amount != null) {
                        $remain_amt = $value->line_total - $value->payment_amount;
                    } else {
                        $remain_amt = $value->remain_amount;
                    }
                    //  $remain_amt = $value->remain_amount == null?$value->payment_amount:$value->remain_amount;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center">' . $i . '</td>';
                    $html .= '<td style="text-align: center">' . \backend\models\Orders::getNumber($value->order_id) . '</td>';
                    $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($value->order_date)) . '</td>';
                    $html .= '<td>
                            <select name="line_pay_type[]" id=""  class="form-control" onchange="checkpaytype($(this))">
                                <option value="1">เงินสด</option>
                                <option value="2">โอนธนาคาร</option>
                            </select>
                            <input type="file" class="line-doc" name="line_doc[]" style="display: none">
                            <input type="hidden" class="line-order-id" name="line_order_id[]" value="' . $value->order_id . '">
                            <input type="hidden" class="line-number" name="line_number[]" value="' . ($i - 1) . '">
                    </td>';
//                    $html .= '<td style="text-align: center"><input type="file" class="form-control"></td>';
                    $html .= '<td>
                            <input type="text" class="form-control line-remain" style="text-align: right" name="line_remain[]" value="' . number_format($remain_amt, 2) . '" readonly>
                            <input type="hidden" class="line-remain-qty" value="' . $remain_amt . '">
                            </td>';
                    $html .= '<td><input type="number" class="form-control line-pay" name="line_pay[]" value="0" min="0" onchange="linepaychange($(this))"></td>';
                    $html .= '</tr>';

                }
                // $html .= '<tr><td colspan="4" style="text-align: right">รวม</td><td style="text-align: right;font-weight: bold">' . number_format($total_amount, 2) . '</td><td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td></tr>';
            }
        }

        echo $html;
    }

    public function actionGetitemnew()
    {
        $cus_id = \Yii::$app->request->post('customer_id');
        $html = '';
        $total_amount = 0;
        if ($cus_id) {
            // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['>', 'remain_amount', 0])->all();
            //  $model = \common\models\QuerySalePosPaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR',['is','payment_amount',new \yii\db\Expression('null')],['>', 'remain_amount', 0]])->all();

            $sql = "SELECT t1.customer_id,t1.order_id,t1.order_date,t1.line_total,SUM(t2.payment_amount)as payment_amount, t1.line_total - SUM(t2.payment_amount) as remain_amount";
            $sql .= " FROM query_sale_by_customer_pos as t1 LEFT JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
            $sql .= " WHERE t1.customer_id=" . $cus_id;
            $sql .= " AND t1.payment_method_id=2";
           // $sql .= " AND t1.payment_status=0";
            $sql .= " GROUP BY t1.customer_id,t1.order_id";
            $sql .= " ORDER BY t1.order_id";
            //$sql.=" AND t1.payment"

            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();


            if ($model != null) {
//                $html = $cus_id;
                $i = 0;
                for ($x = 0; $x <= count($model) - 1; $x++) {
                    $i += 1;
                    //   $total_amount = $total_amount + ($value->remain_amount == null ? 0 : $value->remain_amount);
                    $remain_amt = $model[$x]['line_total'];

                    if ($model[$x]['remain_amount'] == null && $model[$x]['payment_amount'] != null) {
                        $remain_amt = $model[$x]['line_total'] - $model[$x]['payment_amount'];
                    } else {
                        if($model[$x]['remain_amount'] == null){
                            $remain_amt = $model[$x]['line_total'];
                        }else{
                            $remain_amt = $model[$x]['remain_amount'];
                        }

                    }
                    if ($remain_amt <= 0) continue;
                    //  $remain_amt = $value->remain_amount == null?$value->payment_amount:$value->remain_amount;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center">' . $i . '</td>';
                    $html .= '<td style="text-align: center">' . \backend\models\Orders::getNumber($model[$x]['order_id']) . '</td>';
                    $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($model[$x]['order_date'])) . '</td>';

//                    $html .= '<td>
//                            <select name="line_pay_type[]" id=""  class="form-control" onchange="checkpaytype($(this))">
//                                <option value="1">เงินสด</option>
//                                <option value="2">โอนธนาคาร</option>
//                            </select>
//                            <input type="file" class="line-doc" name="line_doc[]" style="display: none">
//                            <input type="hidden" class="line-order-id" name="line_order_id[]" value="' . $model[$x]['order_id'] . '">
//                            <input type="hidden" class="line-number" name="line_number[]" value="' . ($i - 1) . '">
//                    </td>';

                    $html .= '<td style="text-align: center;">
                            <div class="btn-group">
                                    <div class="btn btn-success line-pay-cash" data-var="1"
                                         onclick="checkpaytype2($(this))">เงินสด
                                    </div>
                                    <div class="btn btn-default line-pay-bank" data-var="2"
                                         onclick="checkpaytype3($(this))">โอนธนาคาร
                                    </div>

                                </div>
                                <input type="hidden" class="line-payment-type" name="line_pay_type[]" value="1">
                            <input type="file" class="line-doc" name="line_doc[]" style="display: none">
                            <input type="hidden" class="line-order-id" name="line_order_id[]" value="' . $model[$x]['order_id'] . '">
                            <input type="hidden" class="line-number" name="line_number[]" value="' . ($i - 1) . '">
                    </td>';


//                    $html .= '<td style="text-align: center"><input type="file" class="form-control"></td>';
                    $html .= '<td>
                            <input type="text" class="form-control line-remain" style="text-align: right" name="line_remain[]" value="' . number_format($remain_amt, 2) . '" readonly>
                            <input type="hidden" class="line-remain-qty" value="' . $remain_amt . '">
                            </td>';
                    $html .= '<td><input type="number" class="form-control line-pay" name="line_pay[]" value="0" min="0" step="any" onchange="linepaychange($(this))"></td>';
                    $html .= '</tr>';

                }
                // $html .= '<tr><td colspan="4" style="text-align: right">รวม</td><td style="text-align: right;font-weight: bold">' . number_format($total_amount, 2) . '</td><td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td></tr>';
            }
        }

        echo $html;
    }

    public function actionGetitemnewcar()
    {
        $cus_id = \Yii::$app->request->post('customer_id');
        $html = '';
        $total_amount = 0;
        if ($cus_id) {
            // $model = \common\models\QuerySalePaySummary::find()->where(['customer_id' => $cus_id])->andFilterWhere(['>', 'remain_amount', 0])->all();
            //  $model = \common\models\QuerySalePosPaySummary::find()->where(['customer_id' => $cus_id])->andfilterWhere(['OR',['is','payment_amount',new \yii\db\Expression('null')],['>', 'remain_amount', 0]])->all();

            $sql = "SELECT t1.customer_id,t1.order_id,t1.order_date,t1.line_total,CASE  WHEN SUM(t2.payment_amount) is null THEN 0 ELSE  SUM(t2.payment_amount) END  as payment_amount, t1.line_total - CASE  WHEN SUM(t2.payment_amount) is null THEN 0 ELSE  SUM(t2.payment_amount) END as remain_amount";
            $sql .= " FROM query_sale_by_customer_car as t1 LEFT JOIN query_sale_customer_pay_summary as t2 ON t2.order_ref_id=t1.order_id and t2.customer_id=t1.customer_id";
            $sql .= " WHERE t1.customer_id=" . $cus_id;
            $sql .= " AND t1.payment_method_id=2";
            $sql .= " GROUP BY t1.customer_id,t1.order_id";
            $sql .= " ORDER BY t1.order_id";
            //$sql.=" AND t1.payment"

            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();

            if ($model != null) {
//                $html = $cus_id;
                $i = 0;
                for ($x = 0; $x <= count($model) - 1; $x++) {
                    $i += 1;
                    //   $total_amount = $total_amount + ($value->remain_amount == null ? 0 : $value->remain_amount);
                    $remain_amt = $model[$x]['line_total'];

                    if ($model[$x]['remain_amount'] == null && $model[$x]['payment_amount'] != null) {
                        $remain_amt = $model[$x]['line_total'] - $model[$x]['payment_amount'];
                    } else {
                        $remain_amt = $model[$x]['remain_amount'];
                    }
                    if ($remain_amt <= 0) continue;
                    //  $remain_amt = $value->remain_amount == null?$value->payment_amount:$value->remain_amount;
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center">' . $i . '</td>';
                    $html .= '<td style="text-align: center">' . \backend\models\Orders::getNumber($model[$x]['order_id']) . '</td>';
                    $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($model[$x]['order_date'])) . '</td>';
//                    $html .= '<td>
//                            <select name="line_pay_type[]" id=""  class="form-control" onchange="checkpaytype($(this))">
//                                <option value="1">เงินสด</option>
//                                <option value="2">โอนธนาคาร</option>
//                            </select>
//                            <input type="file" class="line-doc" name="line_doc[]" style="display: none">
//                            <input type="hidden" class="line-order-id" name="line_order_id[]" value="' . $model[$x]['order_id'] . '">
//                            <input type="hidden" class="line-number" name="line_number[]" value="' . ($i - 1) . '">
//                    </td>';
                    $html .= '<td style="text-align: center;">
                            <div class="btn-group">
                                    <div class="btn btn-success line-pay-cash" data-var="1"
                                         onclick="checkpaytype2($(this))">เงินสด
                                    </div>
                                    <div class="btn btn-default line-pay-bank" data-var="2"
                                         onclick="checkpaytype3($(this))">โอนธนาคาร
                                    </div>

                                </div>
                                <input type="hidden" class="line-payment-type" name="line_pay_type[]" value="0">
                            <input type="file" class="line-doc" name="line_doc[]" style="display: none">
                            <input type="hidden" class="line-order-id" name="line_order_id[]" value="' . $model[$x]['order_id'] . '">
                            <input type="hidden" class="line-number" name="line_number[]" value="' . ($i - 1) . '">
                    </td>';



//                    $html .= '<td style="text-align: center"><input type="file" class="form-control"></td>';
                    $html .= '<td>
                            <input type="text" class="form-control line-remain" style="text-align: right" name="line_remain[]" value="' . number_format($remain_amt, 2) . '" readonly>
                            <input type="hidden" class="line-remain-qty" value="' . $remain_amt . '">
                            </td>';
                    $html .= '<td><input type="number" class="form-control line-pay" name="line_pay[]" value="0" min="0" step="any" onchange="linepaychange($(this))"></td>';
                    $html .= '</tr>';

                }
                // $html .= '<tr><td colspan="4" style="text-align: right">รวม</td><td style="text-align: right;font-weight: bold">' . number_format($total_amount, 2) . '</td><td style="text-align: right;font-weight: bold"><span class="line-pay-total">0</span></td></tr>';
            }
        }

        echo $html;
    }

    public function actionCustomerloan()
    {
        $searchModel = new \backend\models\SaleorderCustomerLoanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->limit(100);
        $dataProvider->query->andFilterWhere(['>', 'line_total', 'payment_amount']);
        $dataProvider->setSort([
            'defaultOrder' => ['customer_id' => SORT_ASC, 'order_date' => SORT_ASC]
        ]);

        return $this->render('_customerloan', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
//        return $this->render('_customerloan', [
//            'model' => null,
//        ]);
    }

    public function actionFindpayhistory()
    {
//        $company_id = 1;
//        $branch_id = 1;
//        if (!empty(\Yii::$app->user->identity->company_id)) {
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if (!empty(\Yii::$app->user->identity->branch_id)) {
//            $branch_id = \Yii::$app->user->identity->branch_id;
//        }

        $customer_id = \Yii::$app->request->post('customer_id');
        $order_id = \Yii::$app->request->post('order_id');
        $html = '';

        //  $model = \backend\models\Querypaymentreceive::find()->where(['order_id'=>$order_id,'company_id' => $company_id, 'branch_id' => $branch_id])->all();
        $model = \common\models\QueryPaymentReceive::find()->where(['order_id' => $order_id, 'customer_id' => $customer_id])->all();
        if ($model) {
            $total = 0;
            foreach ($model as $value) {
                $total = ($total + $value->payment_amount);
                $payment_channel = 'เงินสด';
                if ($value->payment_channel_id == 1) {
                    $payment_channel = 'เงินสด';
                } else if ($value->payment_channel_id == 2) {
                    $payment_channel = 'โอนธนาคาร';
                }
                $html .= '<tr>';
                $html .= '<td style="text-align: center">' . $value->journal_no . '</td>';
                $html .= '<td style="text-align: center">' . date('d/m/Y', strtotime($value->trans_date)) . '</td>';

                $html .= '<td style="text-align: right">' . number_format($value->payment_amount) . '</td>';
                $html .= '<td style="text-align: center">' . $payment_channel . '</td>';
                $html .= '</tr>';
            }
            $html .= '<tr>';
            $html .= '<td style="text-align: right" colspan="2"><h5 class="text-orange">รวมรับชำระ</h5></td>';
            $html .= '<td style="text-align: right"><b>' . number_format($total) . '</b></td>';
            $html .= '<td style="text-align: center"></td>';
            $html .= '</tr>';
        }

        echo $html;
    }

    public function actionFindcustomer()
    {
        $list = \Yii::$app->request->post('customer_list');
        $html = '';
        $data = [];
        if ($list != null) {
//            for($i=0;$i<=count($list)-1;$i++){
//                array_push($data,$list[$i]);
//            }
            //$ids = explode(',',$list);

            if (count($list) > 0) {
                $model = \common\models\QueryCustomerInfo::find()->where(['rt_id' => $list])->all();
                // $model = \common\models\QueryCustomerInfo::find()->all();
                if ($model) {
                    foreach ($model as $value) {
                        $html .= '<option id="' . $value->customer_id . '">';
                        $html .= $value->cus_name;
                        $html .= '</option>';
                    }
                }
            }
        }
        //echo $list[0];
        echo $html;
    }

    public function actionCustomerloanprint()
    {
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
        $is_find_date = \Yii::$app->request->post('is_find_date');
        //   echo $is_find_date;return;
        if ($from_date == null && $to_date == null) {
            $from_date = date('Y-m-d H:i');
            $to_date = date('Y-m-d H:i');
        }
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_customer_id = \Yii::$app->request->post('find_customer_id');
        return $this->render('_print_customer_loan_new', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'is_find_date' => $is_find_date,
            //    'find_sale_type'=>$find_sale_type,
            'find_customer_id' => $find_customer_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
        ]);
    }

    public function actionPossummaryupdate()
    {
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
        if ($from_date == null && $to_date == null) {
            $from_date = date('Y-m-d H:i');
            $to_date = date('Y-m-d H:i');
        }
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_customer_id = \Yii::$app->request->post('find_customer_id');
        return $this->render('_print_customer_loan_new_update', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            //    'find_sale_type'=>$find_sale_type,
            'find_customer_id' => $find_customer_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
        ]);
    }

    public function actionSaveupdate()
    {
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $order_id = \Yii::$app->request->post('order_id');
        $delivery_no = \Yii::$app->request->post('delivery_no');
        if ($order_id != null) {
            for ($i = 0; $i <= count($order_id) - 1; $i++) {
                if ($delivery_no[$i] == null) continue;
                $model = \backend\models\Orders::find()->where(['id' => $order_id[$i]])->one();
                if ($model) {
                    $model->customer_ref_no = $delivery_no[$i];
                    $model->save(false);
                }
            }
        }
        return $this->redirect(['paymentreceive/possummaryupdate']);
    }

    public function actionUpdatepaymentorder(){
        $model = \common\models\Orders::find()->select('id')->where(['payment_method_id'=>2,'sale_from_mobile'=>1])->andFilterWhere(['!=','payment_status',1])->andFilterWhere(['>=','id',129167])->limit(30)->all();
        if($model){
            foreach ($model as $value){
                $model_update = \common\models\PaymentReceiveLine::find()->select('id')->where(['order_id'=>$value->id])->one();
                if($model_update){
                   \common\models\Orders::updateAll(['payment_status'=>1],['id'=>$value->id]);
                }
            }
        }
        echo "success";
    }

}
