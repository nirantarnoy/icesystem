<?php

namespace backend\controllers;

use backend\models\LocationSearch;
use Yii;
use backend\models\Customertaxinvoice;
use backend\models\CustomertaxinvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomertaxinvoiceController implements the CRUD actions for Customertaxinvoice model.
 */
class CustomertaxinvoiceController extends Controller
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
     * Lists all Customertaxinvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new CustomertaxinvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        // if($pageSize != '10000'){
        $dataProvider->pagination->pageSize = $pageSize;
//        }else{
//            $dataProvider->pagination->pageSize = $pageSize;
//        }

        //echo $pageSize;return ;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
            'from_date' => null,
            'to_date' => null,
        ]);

        //  return $this->render('_printshortpreview');
    }

    /**
     * Displays a single Customertaxinvoice model.
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
     * Creates a new Customertaxinvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customertaxinvoice();

        if ($model->load(Yii::$app->request->post())) {

            $order_line_id_list = \Yii::$app->request->post('order_line_id_list');
            $order_id_list = \Yii::$app->request->post('order_id_list');
            $order_product_id_list = \Yii::$app->request->post('order_product_id_list');
            $line_product_group_id = \Yii::$app->request->post('line_product_group_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_price = \Yii::$app->request->post('line_price');
            $line_unit = \Yii::$app->request->post('line_unit_id');
            $line_discount = \Yii::$app->request->post('line_discount');
            $line_total = \Yii::$app->request->post('line_total');

            $inv_date = date('Y-m-d');
            $pay_date = date('Y-m-d');

            $x1 = explode('-', $model->invoice_date);
            $x2 = explode('-', $model->payment_date);
            if ($x1 != null && count($x1) > 1) {
                $inv_date = $x1[2] . '-' . $x1[1] . '-' . $x1[0];
            }
            if ($x2 != null && count($x2) > 1) {
                $pay_date = $x2[2] . '-' . $x2[1] . '-' . $x2[0];
            }

            $find_product_group_id = \backend\models\Product::findGroupId($model->find_product_id);

            $model->invoice_date = date('Y-m-d', strtotime($inv_date));
            $model->payment_date = date('Y-m-d', strtotime($pay_date));
            // $model->total_text = '';
            if ($model->save(false)) {
                if ($line_product_group_id != null) {
                    for ($i = 0; $i <= count($line_product_group_id) - 1; $i++) {
                        $modelline = new \common\models\CustomerTaxInvoiceLine();
                        $modelline->tax_invoice_id = $model->id;
                        $modelline->product_group_id = $line_product_group_id[$i];
                        $modelline->qty = $line_qty[$i];
                        $modelline->unit_id = $line_unit[$i];
                        $modelline->price = $line_price[$i];
                        $modelline->discount_amount = $line_discount[$i];
                        $modelline->line_total = $line_total[$i];
                        $modelline->save(false);
                    }
                }
//                if($order_line_id_list != null){
//                    $arr = explode(',',$order_line_id_list);
//                    if($arr != null){
//                        for($x=0;$x<=count($arr)-1;$x++){
//                            $model_x = new \common\models\CustomerTaxInvoiceDetail();
//                            $model_x->customer_tax_invoice_id = $model->id;
//                            $model_x->order_line_id = $arr[$x];
//                            if($model_x->save(false)){
//                                $model_update_line = \common\models\OrderLine::find()->where(['id'=>$arr[$x]])->one();
//                                if($model_update_line != null){
//                                    $model_update_line->tax_status = 1;
//                                    $model_update_line->save(false);
//                                }
//                            }
//                        }
//                    }
//                }
                if ($order_id_list != null) {
                    $arr = explode(',', $order_id_list);
                    $arr_product = explode(',', $order_product_id_list);
                    if ($arr != null) {
                        for ($x = 0; $x <= count($arr) - 1; $x++) {
                            $model_x = new \common\models\OrderTaxTemp();
                            $model_x->order_id = $arr[$x];
                            $model_x->tax_invoice_id = $model->id;
                            $model_x->product_group_id = $find_product_group_id;
                            $model_x->product_id = $arr_product[$x];
                            if ($model_x->save(false)) {

                            }
                        }
                    }
                }
            }
         //   return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['customertaxinvoice/index']);
        }

        return $this->render('create', [
            'model' => $model,
            'model_line' => null,
        ]);
    }

    /**
     * Updates an existing Customertaxinvoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_line = \common\models\CustomerTaxInvoiceLine::find()->where(['tax_invoice_id' => $id])->all();
        $model_order_line = \common\models\CustomerTaxInvoiceDetail::find()->select('order_line_id')->where(['customer_tax_invoice_id' => $id])->all();
        $order_list = [];
        if ($model_order_line) {
            foreach ($model_order_line as $value) {
                array_push($order_list, $value->order_line_id);
            }
        }


        if ($model->load(Yii::$app->request->post())) {
            $line_product_group_id = \Yii::$app->request->post('line_product_group_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_price = \Yii::$app->request->post('line_price');
            $line_unit = \Yii::$app->request->post('line_unit_id');
            $line_discount = \Yii::$app->request->post('line_discount');
            $line_total = \Yii::$app->request->post('line_total');

            if($model->save(false)){
                for ($i = 0; $i <= count($line_product_group_id) - 1; $i++) {

                    $model_check = \common\models\CustomerTaxInvoiceLine::find()->where(['product_group_id'=>$line_product_group_id[$i],'price'=>$line_price[$i]])->one();
                    if($model_check){
                        $model_check->qty = $line_qty[$i];
                        $model_check->unit_id = $line_unit[$i];
                        $model_check->line_total = $line_total[$i];
                        $model_check->save(false);
                    }else{
                        $modelline = new \common\models\CustomerTaxInvoiceLine();
                        $modelline->tax_invoice_id = $model->id;
                        $modelline->product_group_id = $line_product_group_id[$i];
                        $modelline->qty = $line_qty[$i];
                        $modelline->unit_id = $line_unit[$i];
                        $modelline->price = $line_price[$i];
                        $modelline->discount_amount = $line_discount[$i];
                        $modelline->line_total = $line_total[$i];
                        $modelline->save(false);
                    }

                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'model_line' => $model_line,
            'order_line_list' => $order_list,
        ]);
    }

    /**
     * Deletes an existing Customertaxinvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        \common\models\OrderTaxTemp::deleteAll(['tax_invoice_id' => $id]);
        \common\models\CustomerTaxInvoiceLine::deleteAll(['tax_invoice_id' => $id]);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Customertaxinvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customertaxinvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customertaxinvoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionFindorder()
    {
        $customer_id = 210;// \Yii::$app->request->post('customer_id');
        // $customer_id =  \Yii::$app->request->post('customer_id');
        $html = '';
//        if ($customer_id > 0) {
//            $model = \backend\models\Orders::find()->select(['id', 'order_date'])->where(['customer_id' => $customer_id])->limit(50)->all();
//            if ($model) {
//                foreach ($model as $x_value) {
//                    //$html .= $x_value->id."<br />";
//                    $modelline = \backend\models\Orderline::find()->where(['order_id' => $x_value->id])->andFilterWhere(['is','tax_status',new \yii\db\Expression('null')])->all();
//                    if ($modelline) {
//                        foreach ($modelline as $value) {
//                            $product_group_name = \backend\models\Product::findGroupName($value->product_id);
//                            $html .= '<tr>';
//                            $html .= '<td style="text-align: center">
//                            <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $value->id . '">เลือก</div>
//                            <input type="hidden" class="line-find-order-id" value="' . $value->order_id . '">
//                            <input type="hidden" class="line-find-product-id" value="' . $value->product_id . '">
//                            <input type="hidden" class="line-find-qty" value="' . $value->qty . '">
//                            <input type="hidden" class="line-find-price" value="' . $value->price . '">
//                            <input type="hidden" class="line-find-product-group-id" value="' . \backend\models\Product::findGroupId($value->product_id) . '">
//                            <input type="hidden" class="line-find-product-group-name" value="' . $product_group_name . '">
//                           </td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Orders::getNumber($value->order_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . date('d-m-Y', strtotime($x_value->order_date)) . '</td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Product::findCode($value->product_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Product::findName($value->product_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . $product_group_name . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->qty, 1) . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->price, 1) . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->line_total, 1) . '</td>';
//                            $html .= '</tr>';
//                        }
//                    }
//                }
//
//            }
//        }
        echo $html;
    }

//    public function actionOrdersearch(){
//        $from_date = \Yii::$app->request->post('search_from_date');
//        $to_date = \Yii::$app->request->post('search_to_date');
//        $customer_id = \Yii::$app->request->post('customer_id');
//        //  $customer_id =  \Yii::$app->request->post('customer_id');
//        $html = '';
//
//        if ($customer_id != 0 && $from_date != null && $to_date) {
//
//            $search_from_date = null;
//            $search_to_date = null;
//
//            $x1 = explode('/',$from_date);
//            $x2 = explode('/',$to_date);
//            if($x1!=null && count($x1)>1){
//                $search_from_date = $x1[2].'-'.$x1[1].'-'.$x1[0];
//            }
//            if($x2!=null && count($x2)>1){
//                $search_to_date = $x2[2].'-'.$x2[1].'-'.$x2[0];
//            }
//
//          //  $html.= $search_from_date. ' and '.$search_to_date;
//
//            $model = \backend\models\Orders::find()->select(['id', 'order_date'])->where(['customer_id' => $customer_id])->andFilterWhere(['>=','date(order_date)',date('Y-m-d',strtotime($search_from_date))])->andFilterWhere(['<=','date(order_date)',date('Y-m-d',strtotime($search_to_date))])->limit(25)->all();
//            if ($model) {
//                foreach ($model as $x_value) {
////                    $html .= $x_value->id;
//                    $modelline = \backend\models\Orderline::find()->where(['order_id' => $x_value->id])->andFilterWhere(['is','tax_status',new \yii\db\Expression('null')])->all();
//                    if ($modelline) {
//                        foreach ($modelline as $value) {
//                            $product_group_name = \backend\models\Product::findGroupName($value->product_id);
//                            $html .= '<tr>';
//                            $html .= '<td style="text-align: center">
//                            <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $value->id . '">เลือก</div>
//                            <input type="hidden" class="line-find-order-id" value="' . $value->order_id . '">
//                            <input type="hidden" class="line-find-product-id" value="' . $value->product_id . '">
//                            <input type="hidden" class="line-find-qty" value="' . $value->qty . '">
//                            <input type="hidden" class="line-find-price" value="' . $value->price . '">
//                            <input type="hidden" class="line-find-product-group-id" value="' . \backend\models\Product::findGroupId($value->product_id) . '">
//                            <input type="hidden" class="line-find-product-group-name" value="' . $product_group_name . '">
//                           </td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Orders::getNumber($value->order_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . date('d-m-Y', strtotime($x_value->order_date)) . '</td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Product::findCode($value->product_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . \backend\models\Product::findName($value->product_id) . '</td>';
//                            $html .= '<td style="text-align: left">' . $product_group_name . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->qty, 1) . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->price, 1) . '</td>';
//                            $html .= '<td style="text-align: right">' . number_format($value->line_total, 1) . '</td>';
//                            $html .= '</tr>';
//                        }
//                    }
//                }
//            }else{
//                $html.='<tr>';
//                $html.='<td colspan="9" style="text-align: center;color: red;">';
//                $html.='ไม่พบข้อมูล inner';
//                $html.='</td>';
//                $html.='</tr>';
//            }
//        }else{
//            $html.='<tr>';
//            $html.='<td colspan="9" style="text-align: center;color: red;">';
//            $html.='ไม่พบข้อมูล outer';
//            $html.='</td>';
//            $html.='</tr>';
//        }
//        echo $html;
//    }

    public function actionOrdersearch()
    {
        $from_date = \Yii::$app->request->post('search_from_date');
        $to_date = \Yii::$app->request->post('search_to_date');
        $product_id = \Yii::$app->request->post('customer_id'); // new pattern
        $price = \Yii::$app->request->post('find_price'); // new pattern
        $selectedorder = \Yii::$app->request->post('selecteditem');
        //  $customer_id =  \Yii::$app->request->post('customer_id');
        $html = 'no data';

        if ($product_id != 0 && $from_date != null && $to_date) {

            $search_from_date = null;
            $search_to_date = null;

            $x1 = explode('/', $from_date);
            $x2 = explode('/', $to_date);
            if ($x1 != null && count($x1) > 1) {
                $search_from_date = $x1[2] . '-' . $x1[1] . '-' . $x1[0];
            }
            if ($x2 != null && count($x2) > 1) {
                $search_to_date = $x2[2] . '-' . $x2[1] . '-' . $x2[0];
            }

            //  $html.= $search_from_date. ' and '.$search_to_date;


            $data = [];
            $sql = "SELECT t1.id,t1.order_id,t1.product_id,t1.price,t1.qty,t2.order_date,t1.line_total,t2.customer_id,t1.customer_id as t1_customer_id,t2.order_channel_id,t2.sale_from_mobile
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($search_from_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($search_to_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t1.qty > 0";

            if ($price > 0) {
                $sql .= " AND t1.price =" . (float)$price;
            }


            $sql .= " ORDER BY t1.id asc";
            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                // $html.='ok first level';
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $product_group_id = \backend\models\Product::findGroupId($product_id);
                    $model_check_already = \common\models\OrderTaxTemp::find()->where(['order_id' => $model[$i]['order_id'], 'product_group_id' => $product_group_id, 'product_id' => $model[$i]['product_id']])->one();
                    if ($model_check_already) continue;

                    $product_group_name = \backend\models\Product::findGroupName($model[$i]['product_id']);
                    $customer_name = '';

                    $check_customer_id = 0;

                    if ($model[$i]['order_channel_id'] != null || $model[$i]['order_channel_id'] > 0) {
                        if ($model[$i]['sale_from_mobile'] == 1) {
                            $customer_name = \backend\models\Customer::findName($model[$i]['t1_customer_id']);
                            $check_customer_id = $model[$i]['t1_customer_id'];
                        } else {
                            $customer_name = \backend\models\Deliveryroute::findName($model[$i]['order_channel_id']);
                            $check_customer_id = 'xxx';
                        }

                    } else {
                        if ($model[$i]['customer_id'] == null || $model[$i]['customer_id'] == 0 || $model[$i]['customer_id'] == '') {

                            $customer_name = \backend\models\Customer::findName($model[$i]['t1_customer_id']);
                            $check_customer_id = $model[$i]['t1_customer_id'];

                        } else if ($model[$i]['customer_id'] != null || $model[$i]['customer_id'] != 0 || $model[$i]['customer_id'] != '') {

                            $customer_name = \backend\models\Customer::findName($model[$i]['customer_id']);
                            $check_customer_id = $model[$i]['customer_id'];
                        }
                    }

                    if ($check_customer_id != 'xxx') {
                        $model_customer_reg_inv = \backend\models\Customer::find()->where(['id' => $check_customer_id])->one();
                        if ($model_customer_reg_inv) {
                            if ($model_customer_reg_inv->is_invoice_req == 1) continue;
                        }
                    }

                    $line_color = '';
                    if($selectedorder != null || $selectedorder !=''){
                     //   $selectedlist = explode(',',(String)$selectedorder);
//                        if(count($selectedlist) >0){
//                            // if($selectedorder[$i] == $model[$i]['order_id']){
                            for($xx=0;$xx<=count($selectedorder)-1;$xx++){
                                if($selectedorder[$xx] == $model[$i]['order_id']){
                                 $line_color = 'background-color: yellow;';
                                 }

                            }
//
//                            //  }
//                        }

                    }



                    $html .= '<tr style="'.$line_color.'">';
                    $html .= '<td style="text-align: center">
                            <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $model[$i]['id'] . '">เลือก</div>
                            <input type="hidden" class="line-find-order-id" value="' . $model[$i]['order_id'] . '">
                            <input type="hidden" class="line-find-product-id" value="' . $model[$i]['product_id'] . '">
                            <input type="hidden" class="line-find-qty" value="' . $model[$i]['qty'] . '">
                            <input type="hidden" class="line-find-price" value="' . $model[$i]['price'] . '">
                            <input type="hidden" class="line-find-product-group-id" value="' . \backend\models\Product::findGroupId($model[$i]['product_id']) . '">
                            <input type="hidden" class="line-find-product-group-name" value="' . $product_group_name . '">
                           </td>';
                    $html .= '<td style="text-align: left">' . \backend\models\Orders::getNumber($model[$i]['order_id']) . '</td>';
                    $html .= '<td style="text-align: left">' . date('d-m-Y', strtotime($model[$i]['order_date'])) . '</td>';
                    $html .= '<td style="text-align: left">' . \backend\models\Product::findCode($model[$i]['product_id']) . '</td>';
                    $html .= '<td style="text-align: left">' . $customer_name . '</td>';
                    $html .= '<td style="text-align: left">' . $product_group_name . '</td>';
                    $html .= '<td style="text-align: right">' . number_format($model[$i]['qty'], 1) . '</td>';
                    $html .= '<td style="text-align: right">' . number_format($model[$i]['price'], 1) . '</td>';
                    $html .= '<td style="text-align: right">' . number_format((float)$model[$i]['qty'] * (float)$model[$i]['price'], 2) . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="9" style="text-align: center;color: red;">';
                $html .= 'ไม่พบข้อมูล inner';
                $html .= '</td>';
                $html .= '</tr>';
            }


        } else {
            $html .= '<tr>';
            $html .= '<td colspan="9" style="text-align: center;color: red;">';
            $html .= 'ไม่พบข้อมูล outer';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    public function actionConvertnumtostring()
    {
        $txt = '';
        $amount = \Yii::$app->request->post('amount');
        if ($amount >= 0) {
            $txt = self::numtothai($amount);
        }
        echo $txt;
    }

    public function numtothai($num)
    {
        $return = "";
        $num = str_replace(",", "", $num);
        $number = explode(".", $num);
        if (sizeof($number) > 2) {
            return 'รูปแบบข้อมุลไม่ถูกต้อง';
            exit;
        } else if (sizeof($number) == 1) {
            $number[1] = 0;
        }
        // return $number[0];
        $return .= self::numtothaistring($number[0]) . "บาท";

        $stang = intval($number[1]);
        // return $stang;
        if ($stang > 0) {
            if (strlen($stang) == 1) {
                $stang = $stang . '0';
            }
            if ($stang == '10') {
                $return .= 'สิบสตางค์';
            } else if ($stang == '11') {
                $return .= 'สิบเอ็ดสตางค์';
            } else if ($stang == '12') {
                $return .= 'สิบสองสตางค์';
            } else if ($stang == '13') {
                $return .= 'สิบสามสตางค์';
            } else if ($stang == '14') {
                $return .= 'สิบสี่สตางค์';
            } else if ($stang == '15') {
                $return .= 'สิบห้าสตางค์';
            } else if ($stang == '16') {
                $return .= 'สิบหกสตางค์';
            } else if ($stang == '17') {
                $return .= 'สิบเจ็ดสตางค์';
            } else if ($stang == '18') {
                $return .= 'สิบแปดสตางค์';
            } else if ($stang == '19') {
                $return .= 'สิบเก้าสตางค์';
            } else {
                $return .= self::numtothaistring($stang) . "สตางค์";
            }

        } else {
            $return .= "ถ้วน";
        }
        return $return;
    }

    public function numtothaistring($num)
    {
        $return_str = "";
        $txtnum1 = array('', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        $num_arr = str_split($num);
        $count = count($num_arr);
        foreach ($num_arr as $key => $val) {
            // echo $count." ".$val." ".$key."</br>";
            if ($count > 1 && $val == 1 && $key == ($count - 1)) {
                $return_str .= "เอ็ด";
            } else if ($count > 1 && $val == 1 && $key == 2) {
                $return_str .= $txtnum2[$val];
            } else if ($count > 1 && $val == 2 && $key == ($count - 2)) {
                $return_str .= "ยี่" . $txtnum2[$count - $key - 1];
            } else if ($count > 1 && $val == 0) {
            } else {
                $return_str .= $txtnum1[$val] . $txtnum2[$count - $key - 1];
            }
        }
        return $return_str;
    }


    public function actionPrintshort()
    {
        $id = \Yii::$app->request->post('print_id');
        $this->renderPartial('_printshort', [
            'print_id' => $id,
            'branch_id' => 1
        ]);

        $session = \Yii::$app->session;
        $session->setFlash('msg-slip-tax', 'slip_tax.pdf');
        return $this->redirect(['customertaxinvoice/update', 'id' => $id]);
        // return $this->render('_printshortpreview');
    }

    public function actionPrintfull()
    {
        $id = \Yii::$app->request->post('print_id');
        $this->renderPartial('_printfull', [
            'print_id' => $id,
            'branch_id' => 1
        ]);

        $session = \Yii::$app->session;
        $session->setFlash('msg-slip-tax-full', 'slip_tax_full.pdf');
        return $this->redirect(['customertaxinvoice/update', 'id' => $id]);
    }

    public function actionPrintcheck()
    {
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');

        return $this->render('_printcheck', [
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);

    }

    public function actionRecalno()
    {
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');

        $f_date = date('Y-m-d');
        $t_date = date('Y-m-d');
        $cal_year = '';
        $cal_month = '';
        $cal_day = '';
        $xdate = explode('-', $from_date);
        if (count($xdate) > 1) {
            $f_date = $xdate[2] . '/' . $xdate[1] . '/' . $xdate[0];

        }
        $xdate2 = explode('-', $to_date);
        if (count($xdate2) > 1) {
            $t_date = $xdate2[2] . '/' . $xdate2[1] . '/' . $xdate2[0];
        }
       // echo $f_date.' and '. $t_date; return;

        $model = \common\models\CustomerTaxInvoice::find()->where(['>=', 'date(invoice_date)', date('Y-m-d',strtotime($f_date))])->andFilterWhere(['<=', 'date(invoice_date)', date('Y-m-d',strtotime($t_date))])->orderBy(['invoice_date' => SORT_ASC])->all();
        if ($model) {
            $first_no = '';
            $last_day = 0;
            foreach ($model as $value) {
                $cnum = 0;
                $cal_year = substr(date("Y",strtotime($value->invoice_date)),2,4);
                $cal_month = date('m', strtotime($value->invoice_date)) ;
                $cal_day = date('d', strtotime($value->invoice_date));

                if ($first_no != '') {

                    $prefix = $cal_year.$cal_month.$cal_day;

                    $cnum = substr((string)$first_no, 6, 3);

                    $len = strlen($cnum);

                    if($cnum == null || $cnum ==0){
                        $loop = 2;
                        $cnum = 0;
                    }else{
                        $clen = strlen($cnum + 1);
                        $loop = $len - $clen;
                    }

                    if($cal_day != $last_day){
                        $loop = 2;
                        $cnum = 0;
                    }else{
                        $clen = strlen($cnum + 1);
                        $loop = $len - $clen;
                    }

                    for ($i = 1; $i <= $loop; $i++) {
                        $prefix .= "0";
                    }

                    $prefix .= $cnum + 1;
                    $first_no = $prefix;

                } else {

                    $prefix = $cal_year.$cal_month.$cal_day.'001';
                    $first_no  = $prefix;
                }

                $last_day = $cal_day;

                \backend\models\Customertaxinvoice::updateAll(['invoice_no' => $first_no], ['id' => $value->id]);

            }
        }
        return $this->redirect(['customertaxinvoice/index']);
    }
}
