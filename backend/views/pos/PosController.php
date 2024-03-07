<?php

namespace backend\controllers;

use backend\models\Journalissue;
use backend\models\JournalissueSearch;
use backend\models\Orderline;
use backend\models\Orders;
use backend\models\OrdersposSearch;
use backend\models\WarehouseSearch;
use common\models\LoginLog;
use Yii;
use backend\models\Car;
use backend\models\CarSearch;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\web\Session;
use linslin\yii2\curl;

/**
 * CarController implements the CRUD actions for Car model.
 */
class PosController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout', 'index', 'indextest', 'indextest2', 'print', 'printindex', 'dailysum', 'getcustomerprice', 'getoriginprice', 'closesale', 'cancelorder', 'manageclose',
                            'salehistory', 'getbasicprice', 'delete', 'orderedit', 'posupdate', 'posttrans', 'saledailyend', 'saledailyend2', 'printdo', 'createissue', 'updatestock', 'listissue', 'updateissue', 'printsummary','printpossummary', 'printcarsummary','startcaldailymanager'
                            , 'finduserdate', 'editsaleclose', 'createscreenshort', 'print2', 'calcloseshift', 'closesaletest','printtestnew','printtestnewdo'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'indextest'=> ['POST','GET'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
//        if (file_exists('../web/uploads/slip/slip_index.pdf')) {
//            unlink('../web/uploads/slip/slip_index.pdf');
//        }
        $this->layout = 'main_pos';
        return $this->render('index', [
            'model' => null
        ]);
    }

    public function actionIndextest($id)
    {
//        if (file_exists('../web/uploads/slip/slip_index.pdf')) {
//            unlink('../web/uploads/slip/slip_index.pdf');
//        }
        $this->layout = 'main_pos_new';
        return $this->render('indextest_new', [
            'model' => null,
            'model_line'=> null,
            'order_id'=>$id
        ]);
    }
    public function actionIndextest2($id)
    {
        echo $id;
    }

    public function actionGetcustomerprice()
    {
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }


        $data = [];
        $data_cus_price = [];
        $data_basic_price = [];
        $customer_id = \Yii::$app->request->post('customer_id');
        if ($customer_id) {
            $model = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id])->all();
            if ($model != null) {
                foreach ($model as $value) {
                    array_push($data_cus_price, ['product_id' => $value->product_id, 'sale_price' => $value->sale_price, 'price_name' => $value->name]);
                }
            }
        }
        $model_basic_price = \backend\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
        if ($model_basic_price) {
            foreach ($model_basic_price as $value2) {
                array_push($data_basic_price, ['product_id' => $value2->id, 'sale_price' => $value2->sale_price]);
            }
        }
        array_push($data, $data_cus_price, $data_basic_price);
        return json_encode($data);
    }

    public function actionGetoriginprice()
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $data = [];
        $model_basic_price = \backend\models\Product::find()->where(['is_pos_item' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->all();
        if ($model_basic_price) {
            foreach ($model_basic_price as $value) {
                array_push($data, ['product_id' => $value->id, 'sale_price' => $value->sale_price]);
            }
        }
        return json_encode($data);
    }

    public function actionGetbasicprice()
    {
        $customer_id = \Yii::$app->request->post('customer_id');
        $id = \Yii::$app->request->post('product_id');
        $data = [];
        $basic_price = 0;
        $sale_price = null;
        if ($id > 0 && $customer_id > 0) {
            $model_sale_price = \common\models\QueryCustomerPrice::find()->where(['cus_id' => $customer_id, 'product_id' => $id])->one();
            if ($model_sale_price) {
                $sale_price = $model_sale_price->sale_price;
            } else {
                $model = \backend\models\Product::find()->where(['id' => $id])->one();
                if ($model) {
                    $basic_price = $model->sale_price;
                }
            }
            array_push($data, ['sale_price' => $sale_price, 'basic_price' => $basic_price]);
        }
        //array_push($data, ['sale_price' => $sale_price, 'basic_price' => $basic_price]);
        return json_encode($data);
    }

    public function actionClosesale()
    {
        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        $user_id = 1;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $default_warehouse = $warehouse_primary;
        }
        if (!empty(\Yii::$app->user->id)) {
            $user_id = \Yii::$app->user->id;
        }

        $issue_no = '';

        $pay_total_amount = \Yii::$app->request->post('sale_total_amount');
        $pay_amount = \Yii::$app->request->post('sale_pay_amount');
        // $pay_change = \Yii::$app->request->post('sale_pay_change');
        $payment_type = \Yii::$app->request->post('sale_pay_type');

        $customer_id = \Yii::$app->request->post('customer_id');
        $product_list = \Yii::$app->request->post('cart_product_id');
        $line_qty = \Yii::$app->request->post('cart_qty');
        $line_price = \Yii::$app->request->post('cart_price');

        $print_type_doc = \Yii::$app->request->post('print_type_doc');
        //  $default_warehouse = \Yii::$app->request->post('default_warehouse_id');

        // echo $print_type_doc;return;
        $pos_date = \Yii::$app->request->post('sale_pay_date');

        // echo $customer_id;return;
        $sale_date = date('Y-m-d');
        $sale_time = date('H:i:s');
        $x_date = explode('/', $pos_date);
        if (count($x_date) > 1) {
            $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
        }

        if ($customer_id) {
            try {

                $current_date = date('Y-m-d H:i:s');
                $model_order = new \backend\models\Orders();
                $model_order->order_no = $model_order->getLastNoPos($sale_date, $company_id, $branch_id, $current_date);
                //   $model_order->order_date = date('Y-m-d H:i:s', strtotime($sale_date . ' ' . $sale_time));
                $model_order->order_date = $current_date;
                $model_order->customer_id = $customer_id;
                $model_order->sale_channel_id = 2; // pos
                $model_order->payment_status = 0;
                $model_order->order_total_amt = $pay_total_amount == null ? 0 : $pay_total_amount;
                $model_order->status = 1;
                $model_order->company_id = $company_id;
                $model_order->branch_id = $branch_id;
                $model_order->payment_method_id = $payment_type;
                // echo $payment_type;return;
                if ($model_order->save(false)) {
                    if (count($product_list) > 0) {
                        for ($i = 0; $i <= count($product_list) - 1; $i++) {
                            $model_order_line = new \backend\models\Orderline();
                            $model_order_line->order_id = $model_order->id;
                            $model_order_line->product_id = $product_list[$i];
                            $model_order_line->qty = $line_qty[$i];
                            $model_order_line->price = $line_price[$i];
                            $model_order_line->customer_id = $customer_id;
                            $model_order_line->price_group_id = 0;
                            $model_order_line->line_total = ($line_price[$i] * $line_qty[$i]);
                            $model_order_line->status = 1;
                            if ($model_order_line->save(false)) {
                                $model_stock = new \backend\models\Stocktrans();
                                $model_stock->journal_no = $model_order->order_no;
                                $model_stock->trans_date = date('Y-m-d H:i:s');
                                $model_stock->product_id = $product_list[$i];
                                $model_stock->qty = $line_qty[$i];
                                $model_stock->warehouse_id = $default_warehouse; // default
                                $model_stock->stock_type = 2;
                                $model_stock->activity_type_id = 5; // 1 prod rec 2 issue car
                                $model_stock->trans_ref_id = $model_order->id;
                                $model_stock->company_id = $company_id;
                                $model_stock->branch_id = $branch_id;
                                $model_stock->created_by = \Yii::$app->user->id;
                                if ($model_stock->save(false)) {
                                    $update_stock = $this->updateSummary($product_list[$i], $default_warehouse, $line_qty[$i]);
//                                    if (!$update_stock) {
////                                   $session = \Yii::$app->session;
////                                   $session->setFlash('msg_error', 'ไม่สามารถตัดสต๊อกได้เนื่องจากจำนวนไม่พอ');
////                                   return $this->redirect(['pos/index']);
//                                    }
                                }
                            }
                        }

                        $this->genissue($model_order->id, $company_id, $branch_id);
                    }

                    if ($pay_total_amount > 0 && $pay_amount > 0 && $payment_type == 1) { // cash only
                        $model_pay = new \backend\models\Paymentreceive();
                        $model_pay->trans_date = date('Y-m-d H:i:s');//date('Y-m-d H:i:s');
                        $model_pay->customer_id = $customer_id;
                        $model_pay->journal_no = $model_pay->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
                        $model_pay->status = 1;
                        $model_pay->company_id = $company_id;
                        $model_pay->branch_id = $branch_id;
                        $model_pay->crated_by = $user_id;
                        if ($model_pay->save(false)) {
                            $model_pay_line = new \common\models\PaymentReceiveLine();
                            $model_pay_line->payment_receive_id = $model_pay->id;
                            $model_pay_line->order_id = $model_order->id;
                            $model_pay_line->payment_amount = $pay_amount;
                            $model_pay_line->payment_channel_id = 0; // 1 เงินสด 2 โอน
                            $model_pay_line->payment_method_id = 2; // 2 เชื่อ
                            $model_pay_line->payment_type_id = 2;
                            $model_pay_line->payment_term_id = 0;
                            $model_pay_line->status = 1;
                            $model_pay_line->save(false);
                            // if ($model_pay_line->save(false)) {
                            //$status = true;
                            // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                            //$data = ['pay successfully'];
                            //}
                        }

                        $this->updateorderpayment($model_order->id, $pay_total_amount, $pay_amount);
                    } else {
                        $model_pay = new \backend\models\Paymentreceive();
                        $model_pay->trans_date = date('Y-m-d H:i:s');//date('Y-m-d H:i:s');
                        $model_pay->customer_id = $customer_id;
                        $model_pay->journal_no = $model_pay->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
                        $model_pay->status = 1;
                        $model_pay->company_id = $company_id;
                        $model_pay->branch_id = $branch_id;
                        $model_pay->crated_by = $user_id;
                        if ($model_pay->save(false)) {
                            $model_pay_line = new \common\models\PaymentReceiveLine();
                            $model_pay_line->payment_receive_id = $model_pay->id;
                            $model_pay_line->order_id = $model_order->id;
                            $model_pay_line->payment_amount = $pay_amount == null ? 0 : $pay_amount;
                            $model_pay_line->payment_channel_id = 0; // 1 เงินสด 2 โอน
                            $model_pay_line->payment_method_id = 2; // 2 เชื่อ
                            $model_pay_line->payment_type_id = 2;
                            $model_pay_line->payment_term_id = 0;
                            $model_pay_line->status = 1;
                            if ($model_pay_line->save(false)) {
                                $status = true;
                                // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
                                $data = ['pay successfully'];
                            }
                        }
                        // $this->updateorderpayment($model_order->id, $pay_total_amount, $pay_amount);
                    }

                    //  if($this->printindex(31)){
                    if ($model_order->id != null) { // if create order completed and will create bill

//                        $model = \backend\models\Orders::find()->where(['id' => $model_order->id])->one();
//                        $model_line = \backend\models\Orderline::find()->where(['order_id' => $model_order->id])->all();
//                        $change_amt = \backend\models\Paymenttransline::find()->select('change_amount')->where(['order_ref_id' => $model_order->id])->one();

                        $model = $model_order;//\backend\models\Orders::find()->select(['id','order_no','order_date','customer_id'])->where(['id' => $model_order->id])->one();
                        $model_line = \backend\models\Orderline::find()->select(['id', 'product_id', 'qty', 'price'])->where(['order_id' => $model_order->id])->all();
                        $change_amt = 0;//\backend\models\Paymenttransline::find()->select('change_amount')->where(['order_ref_id' => $model_order->id])->one();
                        $ch_amt = 0;

                        if ($change_amt != null) {
                            $ch_amt = $change_amt->change_amount;
                        }
//                    if ($print_type_doc == 2) {
//                        $session = \Yii::$app->session;
//                        $session->setFlash('msg-index', 'slip_index.pdf');
//                        $session->setFlash('msg-index-do', 'slip_index_do.pdf');
//                        $session->setFlash('after-save', true);
//                        $session->setFlash('msg-is-do', $print_type_doc);
//                        $session->setFlash('msg-do-order-id', $model_order->id);
//                        // echo "prin type is ".$print_type_doc;return;
//                    }
                        $session = \Yii::$app->session;
                        $session->setFlash('msg-index', 'slip_index.pdf');
                        $session->setFlash('after-save', true);
                        $session->setFlash('msg-is-do', $print_type_doc);

                        //$session->setFlash('msg-force-print', $print_type_doc);


//                        $this->layout = 'main_print';
                        //  return $this->render('_printoindex_screen', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id,'print_type'=>$print_type_doc]);
                        //    return $this->render('_printoindex_screen2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);

                        $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
                        if ($print_type_doc == 2) {
                            $session->setFlash('msg-index-do', 'slip_index_do.pdf');
                            $slip_path = '';
                            if ($branch_id == 1) {
                                $slip_path = '../web/uploads/company1/slip_do/slip_index_do.pdf';
                            } else if ($branch_id == 2) {
                                $slip_path = '../web/uploads/company2/slip_do/slip_index_do.pdf';
                            }
                            if (file_exists($slip_path)) {
                                unlink($slip_path);
                                //  sleep(4);
                                $this->createDo($model_order->id, $branch_id);
                            } else {
                                $this->createDo($model_order->id, $branch_id);
                            }
                            // $this->render('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, $print_type_doc]);
                        } else {

                        }
                    }
                }
            } catch (\Exception $exception) {

            }
        }
        $session = \Yii::$app->session;
        $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
        return $this->redirect(['pos/index']);


        ////////////

//        $model = \backend\models\Orders::find()->select(['id', 'order_no', 'order_date', 'customer_id'])->where(['id' => $response])->one();
//        $model_line = \backend\models\Orderline::find()->select(['id', 'product_id', 'qty', 'price'])->where(['order_id' => $response])->all();
//        $change_amt = 0;//\backend\models\Paymenttransline::find()->select('change_amount')->where(['order_ref_id' => $model_order->id])->one();
//        $ch_amt = 0;
//
//        if ($change_amt != null) {
//            $ch_amt = $change_amt->change_amount;
//        }
//
//        $session = \Yii::$app->session;
//        $session->setFlash('msg-index', 'slip_index.pdf');
//        $session->setFlash('after-save', true);
//        $session->setFlash('msg-is-do', $print_type_doc);
//
//        $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
//        if ($print_type_doc == 2) {
//            $session->setFlash('msg-index-do', 'slip_index_do.pdf');
//            $slip_path = '';
//            if ($branch_id == 1) {
//                $slip_path = '../web/uploads/company1/slip_do/slip_index_do.pdf';
//            } else if ($branch_id == 2) {
//                $slip_path = '../web/uploads/company2/slip_do/slip_index_do.pdf';
//            }
//            if (file_exists($slip_path)) {
//                unlink($slip_path);
//                //  sleep(4);
//                $this->createDo($model_order->id, $branch_id);
//            } else {
//                $this->createDo($model_order->id, $branch_id);
//            }
//        }
        // $this->render('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, $print_type_doc]);
    }

    public function actionClosesaletestold()
    {

        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        $user_id = 1;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
           // $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
           //$default_warehouse = 6;
        }
        if (!empty(\Yii::$app->user->id)) {
            $user_id = \Yii::$app->user->id;
        }

      //  $warehouse_primary = 6;

     //   $issue_no = '';

        $pay_total_amount = \Yii::$app->request->post('sale_total_amount');
        $pay_amount = \Yii::$app->request->post('sale_pay_amount');
        // $pay_change = \Yii::$app->request->post('sale_pay_change');
        $payment_type = \Yii::$app->request->post('sale_pay_type');

        $customer_id = \Yii::$app->request->post('customer_id');
        $product_list = \Yii::$app->request->post('cart_product_id');
        $line_qty = \Yii::$app->request->post('cart_qty');
        $line_price = \Yii::$app->request->post('cart_price');

        $print_type_doc = \Yii::$app->request->post('print_type_doc');
        $default_warehouse = \Yii::$app->request->post('default_warehouse_id');

        // echo $print_type_doc;return;
        $pos_date = \Yii::$app->request->post('sale_pay_date');

      //  echo $customer_id;return;
//        $sale_date = date('Y-m-d');
//        $sale_time = date('H:i:s');
//        $x_date = explode('/', $pos_date);
//        if (count($x_date) > 1) {
//            $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
//        }
        // ================================== call go api ======

        $data = [];

        if($product_list !=null){
            for($x=0;$x<=count($product_list)-1;$x++){
                array_push($data, ["product_id" => (int)$product_list[$x], "qty" => (float)$line_qty[$x], "price" => (float)$line_price[$x]]);
            }
        }
        $xdata = [
            'customer_id' => (int)$customer_id,
            "data_list" => $data,
            "sale_pay_type" => 1,
            "sale_total_amount" => (float)$pay_total_amount,
            "sale_pay_amount" => (float)$pay_amount,
            "user_id" => (int)$user_id,
            "warehouse_id" => (int)$default_warehouse,
            "company_id" => (int)$company_id,
            "branch_id" => (int)$branch_id,
            "payment_method_id" => (int)$payment_type,
        ];

//       // $url = 'http://192.168.60.180:1223/api/pos/posclose';
//        //$url = 'http://103.253.73.108:1223/api/pos/posclose';
//        $url = 'http://203.156.30.38:12234/api/pos/posclose';
        $url = 'http://141.98.19.240:1223/api/pos/posclose'; // current api use
        // Initializes a new cURL session
        $curl = curl_init($url);
// Set the CURLOPT_RETURNTRANSFER option to true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// Set the CURLOPT_POST option to true for POST request
        curl_setopt($curl, CURLOPT_POST, true);
// Set the request data as JSON using json_encode function
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($xdata));
// Set custom headers for RapidAPI Auth and Content-Type header
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
// Execute cURL request with all previous settings
        $start_time = microtime(true);
        $response = curl_exec($curl);
        $end_time = microtime(true);
        echo "time used is " . ($end_time - $start_time) . "<br />";
// Close cURL session
        curl_close($curl);

        //echo $response . PHP_EOL;
        $after_save_order_id = 0;
        $res_data = json_decode($response, true);
        // print_r($res_data);
        if($res_data != null){
//            $model_header = ["id"=>$res_data["id"],"order_no"=>$res_data["order_no"],"order_date"=>date('Y-m-d H:i:s'),"customer_id"=>$res_data["customer_id"],"time_used"=>$res_data["time_use"]];
//            $model_detail = [];
//            $lines = $res_data["order_line"];
            $after_save_order_id = $res_data["id"];
//            if($lines != null){
//                for($i=0;$i<=count($lines)-1;$i++){
//                    array_push($model_detail,["id"=>$lines[$i]["id"],"product_id"=>$lines[$i]["product_id"],"qty"=>$lines[$i]["qty"],"price"=>$lines[$i]["price"]]);
//                }
//            }

//        print_r($model_header);
//        echo "<br />";
//        print_r($model_detail);
            $change_amt = 0;//\backend\models\Paymenttransline::find()->select('change_amount')->where(['order_ref_id' => $model_order->id])->one();
            $ch_amt = 0;

//            if ($change_amt != null) {
//                $ch_amt = $change_amt->change_amount;
//            }

            // original before change

//            if($model_header["order_no"] != null || $model_header["order_no"]!=""){
//                $session = \Yii::$app->session;
//                $session->setFlash('msg-index', 'slip_index.pdf');
//                $session->setFlash('after-save', true);
//                $session->setFlash('msg-is-do', $print_type_doc);
//
//                $this->renderPartial('_printtoindexgoapi', ['model' => $model_header, 'model_line' => $model_detail, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
//                if ($print_type_doc == 2) {
//                    $session->setFlash('msg-index-do', 'slip_index_do.pdf');
//                    $slip_path = '';
//                    if ($branch_id == 1) {
//                        $slip_path = '../web/uploads/company1/slip_do/slip_index_do.pdf';
//                    } else if ($branch_id == 2) {
//                        $slip_path = '../web/uploads/company2/slip_do/slip_index_do.pdf';
//                    }
//                    if (file_exists($slip_path)) {
//                        unlink($slip_path);
//                        //  sleep(4);
//                        $this->createDoGoApi($model_header,$model_detail, $branch_id);
//                    } else {
//                        $this->createDoGoApi($model_header,$model_detail, $branch_id);
//                    }
//                }
//                //  return;
//                // ======= end call go api ======
//
//                $session = \Yii::$app->session;
//                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
//            }

            // end original

            //if($model_header["order_no"] != null || $model_header["order_no"]!=""){
                if(0 > 0){
                    $session = \Yii::$app->session;
                $session->setFlash('msg-index', 'slip_index.pdf');
                $session->setFlash('after-save', true);
                $session->setFlash('msg-is-do', $print_type_doc);
                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');

          //      return $this->render('indextest_new', ['model' => $model_header, 'model_line' => $model_detail, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
                return $this->render('indextest_new', ['order_id' => $res_data['id']]);
                //          return $this->redirect(['pos/indextest','id'=>$model_header['id']]);
//                if ($print_type_doc == 2) {
//                    $session->setFlash('msg-index-do', 'slip_index_do.pdf');
//                    $slip_path = '';
//                    if ($branch_id == 1) {
//                        $slip_path = '../web/uploads/company1/slip_do/slip_index_do.pdf';
//                    } else if ($branch_id == 2) {
//                        $slip_path = '../web/uploads/company2/slip_do/slip_index_do.pdf';
//                    }
//                    if (file_exists($slip_path)) {
//                        unlink($slip_path);
//                        //  sleep(4);
//                        $this->createDoGoApi($model_header,$model_detail, $branch_id);
//                    } else {
//                        $this->createDoGoApi($model_header,$model_detail, $branch_id);
//                    }
//                }
                //  return;
                // ======= end call go api ======

            }

        }
        return $this->redirect(['pos/printtestnew', 'order'=>$after_save_order_id,'print_type_doc'=>$print_type_doc]);

        //return $this->redirect(['pos/indextest',['model'=>null,'model_line'=>null,'change_amount'=>0,'branch_id'=>$branch_id]]);
    }

    public function actionClosesaletest()
    {

        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        $user_id = 1;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            // $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            //$default_warehouse = 6;
        }
        if (!empty(\Yii::$app->user->id)) {
            $user_id = \Yii::$app->user->id;
        }

        //  $warehouse_primary = 6;

        //   $issue_no = '';

        $pay_total_amount = \Yii::$app->request->post('sale_total_amount');
        $pay_amount = \Yii::$app->request->post('sale_pay_amount');
        // $pay_change = \Yii::$app->request->post('sale_pay_change');
        $payment_type = \Yii::$app->request->post('sale_pay_type');

        $customer_id = \Yii::$app->request->post('customer_id');
        $product_list = \Yii::$app->request->post('cart_product_id');
        $line_qty = \Yii::$app->request->post('cart_qty');
        $line_price = \Yii::$app->request->post('cart_price');

        $print_type_doc = \Yii::$app->request->post('print_type_doc');
        $default_warehouse = \Yii::$app->request->post('default_warehouse_id');

        // echo $print_type_doc;return;
        $pos_date = \Yii::$app->request->post('sale_pay_date');

        //  echo $customer_id;return;
//        $sale_date = date('Y-m-d');
//        $sale_time = date('H:i:s');
//        $x_date = explode('/', $pos_date);
//        if (count($x_date) > 1) {
//            $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
//        }
        // ================================== call go api ======

        $data = [];

        if($product_list !=null){
            for($x=0;$x<=count($product_list)-1;$x++){
                array_push($data, ["product_id" => (int)$product_list[$x], "qty" => (float)$line_qty[$x], "price" => (float)$line_price[$x]]);
            }
        }
        $xdata = [
            'customer_id' => (int)$customer_id,
            "data_list" => $data,
            "sale_pay_type" => 1,
            "sale_total_amount" => (float)$pay_total_amount,
            "sale_pay_amount" => (float)$pay_amount,
            "user_id" => (int)$user_id,
            "warehouse_id" => (int)$default_warehouse,
            "company_id" => (int)$company_id,
            "branch_id" => (int)$branch_id,
            "payment_method_id" => (int)$payment_type,
        ];

//       // $url = 'http://192.168.60.180:1223/api/pos/posclose';
//        //$url = 'http://103.253.73.108:1223/api/pos/posclose';
//        $url = 'http://203.156.30.38:12234/api/pos/posclose';
        $url = 'http://141.98.19.240:1223/api/pos/posclose'; // current api use
        // Initializes a new cURL session
        $curl = curl_init($url);
// Set the CURLOPT_RETURNTRANSFER option to true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// Set the CURLOPT_POST option to true for POST request
        curl_setopt($curl, CURLOPT_POST, true);
// Set the request data as JSON using json_encode function
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($xdata));
// Set custom headers for RapidAPI Auth and Content-Type header
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
// Execute cURL request with all previous settings
        $start_time = microtime(true);
        $response = curl_exec($curl);
        $end_time = microtime(true);
        echo "time used is " . ($end_time - $start_time) . "<br />";
// Close cURL session
        curl_close($curl);

        //echo $response . PHP_EOL;
        $after_save_order_id = 0;
        $res_data = json_decode($response, true);
        // print_r($res_data);
        if($res_data != null){
            $after_save_order_id = $res_data["id"];
//            if(0 > 0){
//                $session = \Yii::$app->session;
//                $session->setFlash('msg-index', 'slip_index.pdf');
//                $session->setFlash('after-save', true);
//                $session->setFlash('msg-is-do', $print_type_doc);
//                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
//                return $this->render('indextest_new', ['order_id' => $res_data['id']]);
//
//
//            }

        }
        if($after_save_order_id == null || $after_save_order_id == 0){
            return $this->redirect(['pos/indextest',['id'=>0]]);
        }
        return $this->redirect(['pos/printtestnew', 'order'=>$after_save_order_id,'print_type_doc'=>$print_type_doc]);

        //return $this->redirect(['pos/indextest',['model'=>null,'model_line'=>null,'change_amount'=>0,'branch_id'=>$branch_id]]);
    }

    public function actionPrint2()
    {
        $id = \Yii::$app->request->post('order_id');
        $ch_amt = \Yii::$app->request->post('ch_amt');
        $branch_id = 1;
        $model = \backend\models\Orders::find()->where(['id' => $id])->one();
        $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
        //sleep(3);
        $this->layout = 'main_print';
        return $this->render('_printoindex_screen2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
    }
    public function actionPrinttestnew($order,$print_type_doc)
    {
        $id = $order;
        $ch_amt = 0;
        $branch_id = 1;
        $model = \backend\models\Orders::find()->where(['id' => $id])->one();
        $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
        //sleep(3);
        $this->layout = 'main_print';
        return $this->render('_printoindex_screen', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id,'print_type'=>$print_type_doc]);
    }
    public function actionPrinttestnewdo($order)
    {
        $id = $order;
        $ch_amt = 0;
        $branch_id = 1;
        $model = \backend\models\Orders::find()->where(['id' => $id])->one();
        $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
        //sleep(3);
        $this->layout = 'main_print';
        return $this->render('_printoindex_screen2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
    }

    public function genissue($order_id, $company_id, $branch_id)
    {
        $issue_no = '';

        if ($order_id != null) {
            // $model_order = \backend\models\Orders::find()->where(['id' => $order_id])->one();
            $model_line = \backend\models\Orderline::find()->select(['id', 'product_id', 'qty', 'price'])->where(['order_id' => $order_id])->all();

            if ($model_line != null) {
                $model = new Journalissue();

                $model->journal_no = $model->getLastNo(date('Y-m-d'), $company_id, $branch_id);
                $model->trans_date = date('Y-m-d H:i:s');
                $model->status = 2; // close
                $model->reason_id = 1;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->order_ref_id = $order_id;
                if ($model->save(false)) {
                    foreach ($model_line as $value) {
                        if ($value->product_id == '') continue;
                        $model_issue_line = new \backend\models\Journalissueline();
                        $model_issue_line->issue_id = $model->id;
                        $model_issue_line->product_id = $value->product_id;
                        $model_issue_line->qty = $value->qty;
                        $model_issue_line->avl_qty = $value->qty;
                        $model_issue_line->sale_price = $value->price;
                        $model_issue_line->origin_qty = $value->qty;
                        $model_issue_line->status = 1;
                        $model_issue_line->save(false);

                    }
                    $issue_no = $model->journal_no;
                }
            }
        }
        return $issue_no;
    }

    public function createDo($order_id, $branch_id)
    {
        if ($order_id != null) {
            $model = \backend\models\Orders::find()->where(['id' => $order_id])->one();
            $model_line = \backend\models\Orderline::find()->where(['order_id' => $order_id])->all();
            $change_amt = \backend\models\Paymenttransline::find()->where(['order_ref_id' => $order_id])->one();
            $ch_amt = 0;
            if ($change_amt != null) {
                $ch_amt = $change_amt->change_amount;
            }

            $this->renderPartial('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
        }
    }
    public function createDoGoApi($model_header, $model_line,$branch_id)
    {
        if ($model_header != null) {
            $change_amt = 0;//\backend\models\Paymenttransline::find()->where(['order_ref_id' => $order_id])->one();
            $ch_amt = 0;
//            if ($change_amt != null) {
//                $ch_amt = $change_amt->change_amount;
//            }

            $this->renderPartial('_printindex2goapi', ['model' => $model_header, 'model_line' => $model_line, 'change_amount' => $ch_amt, 'branch_id' => $branch_id]);
        }
    }

    public function actionPrintdo()
    {
        $id = \Yii::$app->request->post('order_id');
//        if ($id) {
//             //echo $id; return;
//            $model = \backend\models\Orders::find()->where(['id' => $id])->one();
//            $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
//            $change_amt = \backend\models\Paymenttransline::find()->where(['order_ref_id' => $id])->one();
//            $ch_amt = 0;
//            if ($change_amt != null) {
//                $ch_amt = $change_amt->change_amount;
//            }
//            if (file_exists('../web/uploads/slip_do/slip_index_do.pdf')) {
//                if(unlink('../web/uploads/slip_do/slip_index_do.pdf')){
//                    $this->render('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt]);
//                }
//            }else{
//                $this->render('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt]);
//            }
//            echo '../web/uploads/slip_do/slip_index_do.pdf';
//        }

    }

    public function updateSummary($product_id, $wh_id, $qty)
    {
        if ($wh_id != null && $product_id != null && $qty > 0) {
            $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
            if ($model) {
                if ((int)$model->qty < (int)$qty) {
                    return false;
                }
                $model->qty = (int)$model->qty - (int)$qty;
                if ($model->save(false)) {
                    return true;
                }
            } else {
//                $model_new = new \backend\models\Stocksum();
//                $model_new->warehouse_id = $wh_id;
//                $model_new->product_id = $product_id;
//                $model_new->qty = $qty;
//                $model_new->save(false);
            }
        }
        return false;
    }

    public function updateorderpayment($order_id, $order_amt, $pay_amt)
    {
        if ($order_id) {
            if ($pay_amt >= $order_amt) {
                $model = \backend\models\Orders::find()->where(['id' => $order_id])->one();
                if ($model) {
                    $model->payment_status = 1;
                    $model->save(false);
                }
            }
        }
    }

    public function actionSalehistory()
    {
        if (file_exists('../web/uploads/slip/slip.pdf')) {
            //  unlink('../web/uploads/slip/slip.pdf');
        }


        $searchModel = new OrdersposSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['sale_channel_id' => 2]);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        //  $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('_history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCancelorder($id)
    {
        if ($id) {
//            $company_id = 1;
//            $branch_id = 1;
//            $default_warehouse = 6; //6
//            if (!empty(\Yii::$app->user->identity->company_id)) {
//                $company_id = \Yii::$app->user->identity->company_id;
//            }
//            if (!empty(\Yii::$app->user->identity->branch_id)) {
//                $branch_id = \Yii::$app->user->identity->branch_id;
//                if ($branch_id == 2) {
//                    $default_warehouse = 5;
//                }
//            }
            $company_id = 0;
            $branch_id = 0;
            $default_warehouse = 0; // 6
            if (!empty(\Yii::$app->user->identity->company_id)) {
                $company_id = \Yii::$app->user->identity->company_id;
            }
            if (!empty(\Yii::$app->user->identity->branch_id)) {
                $branch_id = \Yii::$app->user->identity->branch_id;
                $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
                $default_warehouse = $warehouse_primary;
            }
            $model = \backend\models\Orders::find()->where(['id' => $id])->one();
            if ($model) {
                $model->status = 3; // cancel
                if ($model->save(false)) {
                    $model_issue = \backend\models\Journalissue::find()->where(['order_ref_id' => $id])->one();
                    if ($model_issue) {
                        $model_issue->status = 200; // cancel issue
                        if ($model_issue->save(false)) {
                            // return qty to palate
                            $model_issue_stock = \common\models\IssueStockTemp::find()->where(['issue_id' => $model_issue->id])->all();
                            if ($model_issue_stock) {
                                foreach ($model_issue_stock as $val) {
                                    $model_cancel_issue = \common\models\IssueStockTemp::find()->where(['issue_id' => $val->issue_id])->andFilterWhere(['>', 'qty', 0])->one();
                                    if ($model_cancel_issue) {
                                        $model_cancel_issue->qty = 0;
                                        $model_cancel_issue->save(false);
                                    }
                                    // return to palate
                                    //   $model_palate = \backend\models\Stocktrans::find()->where(['id' => $val->prodrec_id])->one();
//                                    if ($model_palate) {
//                                        // $model_palate->qty = ($model_palate->qty + $val->qty);
//                                        $model_palate->qty = ($model_palate->qty + $val->qty);
//                                        $model_palate->save(false);
//                                    }
                                    // create trans and update stock
                                    $this->updatestockcancel($val->product_id, $val->qty, $default_warehouse, '', $company_id, $branch_id);
                                }
                            } else {
                                $model_issue_line = \backend\models\Journalissueline::find()->where(['issue_id' => $model_issue->id])->all();
                                foreach ($model_issue_line as $line) {
                                    $this->updatestockcancel($line->product_id, $line->qty, $default_warehouse, '', $company_id, $branch_id);
                                }

                            }
                        }
                    }
                }
            }

            $session = \Yii::$app->session;
            $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
            return $this->redirect(['pos/salehistory']);
        }
    }

    public function actionDelete($id)
    {
        Orderline::deleteAll(['order_id' => $id]);
        $this->findModel($id)->delete();

        return $this->redirect(['pos/salehistory']);
    }

    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionOrderedit()
    {
        $id = \Yii::$app->request->post('order_id');
        $data = [];
        $html = '';
        if ($id) {
            $model = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
            if ($model) {
                foreach ($model as $value) {
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center"><input type="hidden" class="order-line-id" name="order_line_id[]" value="' . $value->id . '">' . \backend\models\Product::findCode($value->product_id) . '</td>';
                    $html .= '<td>' . \backend\models\Product::findName($value->product_id) . '</td>';
                    $html .= '<td><input type="number" min="0" style="text-align: right" class="form-control line-qty" name="line_qty[]" onchange="calline($(this))" value="' . $value->qty . '"></td>';
                    $html .= '<td style="text-align: right"><input type="number" min="0" style="text-align: right" class="form-control line-price" name="line_price[]" onchange="calline($(this))" value="' . $value->price . '"></td>';
                    $html .= '<td style="text-align: right"><input type="hidden" class="line-total" value="' . $value->qty * $value->price . '">' . number_format($value->qty * $value->price) . '</td>';
                    $html .= '</tr>';
                }
            }
        }
        $model_order = \backend\models\Orders::find()->where(['id' => $id])->one();
        if ($model_order) {
            $customer_name = \backend\models\Customer::findName($model_order->customer_id);
            $payment_data = \backend\models\Paymentmethod::findName($model_order->payment_method_id);
            array_push($data, ['order_id' => $id, 'order_no' => $model_order->order_no, 'order_date' => $model_order->order_date, 'customer_name' => $customer_name, 'payment_method' => $payment_data, 'html' => $html]);
        }
        return json_encode($data);
    }

    public function actionPosupdate()
    {
        $order_id = \Yii::$app->request->post('order_id');
        $line_id = \Yii::$app->request->post('order_line_id');
        $line_qty = \Yii::$app->request->post('line_qty');
        $line_price = \Yii::$app->request->post('line_price');

        if ($order_id && $line_id != null) {
            $new_total = 0;
            for ($i = 0; $i <= count($line_id) - 1; $i++) {
                $model = \backend\models\Orderline::find()->where(['id' => $line_id[$i]])->one();
                if ($model) {
                    // echo "hol";return;
                    $new_total = $new_total + ($line_qty[$i] * $line_price[$i]);
                    $model->qty = $line_qty[$i] == null ? 0 : $line_qty[$i];
                    $model->price = $line_price[$i] == null ? 0 : $line_price[$i];
                    $model->save(false);
                }
            }
            $this->updateOrder($order_id, $new_total);
        }
        return $this->redirect(['pos/salehistory']);
    }

    public function updateOrder($id, $total)
    {
        if ($id) {
            $model = \backend\models\Orders::find()->where(['id' => $id])->one();
            if ($model) {
                $model->order_total_amt = $total;
                $model->save(false);
            }
        }
    }

    public function actionPrint($id)
    {
        if ($id) {
            $model = \backend\models\Orders::find()->where(['id' => $id])->one();
            $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
            $user_oper = \backend\models\User::findName($model->created_by);
            $this->renderPartial('_print', ['model' => $model, 'model_line' => $model_line, 'user_oper' => $user_oper]);
            //   $content =  $this->renderPartial('_print', ['model' => $model, 'model_line' => $model_line]);
            $session = \Yii::$app->session;
            $session->setFlash('msg-index', 'slip.pdf');
            $session->setFlash('after-print', true);
            $this->redirect(['pos/salehistory']);
        }

    }

    public function printindex($id)
    {
        if ($id) {
            $model = \backend\models\Orders::find()->where(['id' => $id])->one();
            $model_line = \backend\models\Orderline::find()->where(['order_id' => $id])->all();
            $change_amt = \backend\models\Paymenttransline::find()->where(['order_ref_id' => $id])->one();
            $this->render('_printtoindex', [
                'model' => $model,
                'model_line' => $model_line,
                'change_amount' => $change_amt->change_amount
            ]);
//            $session = \Yii::$app->session;
//            $session->setFlash('msg', 'slip_index.pdf');
            //$this->redirect(['pos/index']);
            return true;
        }
        return false;

    }

    public function actionDailysum()
    {
        // $x = '2021-03-03';
        // $t_date = date('Y-m-d',strtotime($x));

        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $from_date = date('Y-m-d H:i', strtotime(date('Y-m-d') . ' ' . '00:00'));
        $to_date = date('Y-m-d H:i');
        $product_id = null;
        $emp_id = null;

//        $from_date = \Yii::$app->request->post('from_date');
//        $to_date = \Yii::$app->request->post('to_date');
//        $product_id = \Yii::$app->request->post('product_id');
//        $emp_id = \Yii::$app->request->post('emp_id');
//
////        $f_date = date('Y-m-d');
////        $t_date = date('Y-m-d');
////
////        $x_date = explode('/', $from_date);
////        if (count($x_date) > 1) {
////            $f_date = $x_date[2] . '-' . $x_date[1] . '-' . $x_date[0];
////        }
////
////        $xx_date = explode('/', $to_date);
////        if (count($xx_date) > 1) {
////            $t_date = $xx_date[2] . '-' . $xx_date[1] . '-' . $xx_date[0];
////        }
//        $from_date_time = null;
//        $to_date_time = null;
//
//        if ($from_date != null && $to_date != null) {
//            $fx_datetime = explode(' ', $from_date);
//            $tx_datetime = explode(' ', $to_date);
//
//            $f_date = null;
//            $f_time = null;
//            $t_date = null;
//            $t_time = null;
//
//
//            if (count($fx_datetime) > 0) {
//                $f_date = $fx_datetime[0];
//                $f_time = $fx_datetime[1];
//
//                $x_date = explode('-', $f_date);
//                $xx_date = date('Y-m-d');
//                if (count($x_date) > 1) {
//                    $xx_date = trim($x_date[1]) . '/' . trim($x_date[2]) . '/' . trim($x_date[0]);
//                }
//                $from_date_time = date('Y-m-d H:i:s', strtotime($xx_date . ' ' . $f_time));
//            }
//
//            if (count($tx_datetime) > 0) {
//                $t_date = $tx_datetime[0];
//                $t_time = $tx_datetime[1];
//
//                $n_date = explode('-', $t_date);
//                $nn_date = date('Y-m-d');
//                if (count($n_date) > 1) {
//                    $nn_date = trim($n_date[1]) . '/' . trim($n_date[2]) . '/' . trim($n_date[0]);
//                }
//                $to_date_time = date('Y-m-d H:i:s', strtotime($nn_date . ' ' . $t_time));
//            }
//
//        }

        $searchModel = new \backend\models\SaleposdataSearch();

        // print_r(\Yii::$app->request->queryParams);return;

        //if(!empty((\Yii::$app->request->queryParams['SaleposdataSearch']))){
        if (isset(\Yii::$app->request->queryParams['SaleposdataSearch'])) {
            $from_date = \Yii::$app->request->queryParams['SaleposdataSearch']['from_date'];
            $to_date = \Yii::$app->request->queryParams['SaleposdataSearch']['to_date'];
            $product_id = \Yii::$app->request->queryParams['SaleposdataSearch']['product_id'];
            $emp_id = \Yii::$app->request->queryParams['SaleposdataSearch']['emp_id'];
        }


        $from_date_time = null;
        $to_date_time = null;

        if ($from_date != null && $to_date != null) {
            $fx_datetime = explode(' ', $from_date);
            $tx_datetime = explode(' ', $to_date);

            $f_date = null;
            $f_time = null;
            $t_date = null;
            $t_time = null;


            if (count($fx_datetime) > 0) {
                $f_date = $fx_datetime[0];
                $f_time = $fx_datetime[1];

                $x_date = explode('-', $f_date);
                $xx_date = date('Y-m-d');
                if (count($x_date) > 1) {
                    $xx_date = trim($x_date[1]) . '/' . trim($x_date[2]) . '/' . trim($x_date[0]);
                }
                $from_date_time = date('Y-m-d H:i:s', strtotime($xx_date . ' ' . $f_time));
            }

            if (count($tx_datetime) > 0) {
                $t_date = $tx_datetime[0];
                $t_time = $tx_datetime[1];

                $n_date = explode('-', $t_date);
                $nn_date = date('Y-m-d');
                if (count($n_date) > 1) {
                    $nn_date = trim($n_date[1]) . '/' . trim($n_date[2]) . '/' . trim($n_date[0]);
                }
                $to_date_time = date('Y-m-d H:i:s', strtotime($nn_date . ' ' . $t_time));
            }
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->select(['code', 'name', 'price', 'SUM(qty) as qty',
            'SUM(line_total) as line_total', 'SUM(line_total_cash) as line_total_cash,SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_cash) as line_qty_cash', 'SUM(line_qty_credit) as line_qty_credit','item_pos_seq']);

        // $dataProvider->pagination->pageSize = 100;

        $dataProvider->query->andFilterWhere(['>', 'qty', 0]);
        $dataProvider->query->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id]);
        if ($product_id != '') {
            $dataProvider->query->andFilterWhere(['product_id' => $product_id]);
        }
        if ($emp_id != null) {
            $dataProvider->query->andFilterWhere(['in', 'created_by', $emp_id]);
        }

        $dataProvider->query->andFilterWhere(['AND', ['>=', 'order_date', $from_date_time], ['<=', 'order_date', $to_date_time]]);
        $dataProvider->query->groupBy(['code', 'name', 'price','item_pos_seq']);
        $dataProvider->setSort([
            'defaultOrder' => ['item_pos_seq' => SORT_ASC]
        ]);

        $searchModel2 = new \backend\models\SalepospaySearch();
        $dataProvider2 = $searchModel2->search(Yii::$app->request->queryParams);
        $dataProvider2->query->select(['code', 'name', 'SUM(payment_amount) as payment_amount']);
        $dataProvider2->query->andFilterWhere(['>', 'payment_amount', 0]);
        //  $dataProvider2->query->andFilterWhere(['date(order_date)' => $t_date]);
        $dataProvider2->query->andFilterWhere(['AND', ['>=', 'order_date', $from_date_time], ['<=', 'order_date', $to_date_time]]);
        $dataProvider2->query->groupBy(['code', 'name', 'sale_channel_id']);
        $dataProvider2->setSort([
            'defaultOrder' => ['code' => SORT_ASC]
        ]);
//        $searchModel2 = new \backend\models\SalepospaySearch();
//        $dataProvider2 = $searchModel2->search(Yii::$app->request->queryParams);
//        $dataProvider2->query->select(['code', 'name', 'SUM(payment_amount) as payment_amount']);
//        $dataProvider2->query->andFilterWhere(['>', 'payment_amount', 0]);
//        $dataProvider2->query->andFilterWhere(['date(payment_date)' => $t_date]);
//        $dataProvider2->query->groupBy(['code', 'name']);
//        $dataProvider2->setSort([
//            'defaultOrder' => ['code' => SORT_ASC]
//        ]);

        return $this->render('_dailysum', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModel2' => $searchModel2,
            'dataProvider2' => $dataProvider2,
            'from_date_time' => $from_date_time,
            'to_date_time' => $to_date_time,
            'product_id' => $product_id,
            'emp_id' => $emp_id,
        ]);
    }

    public function actionPosttrans()
    {
        $user_id = \Yii::$app->user->id;
        $user_login_time = \backend\models\User::findLogintime($user_id);
        $user_login_datetime = '';
        $t_date = date('Y-m-d H:i:s');
        $model_c_login = LoginLog::find()->where(['user_id' => $user_id, 'status' => 1])->andFilterWhere(['date(login_date)' => date('Y-m-d')])->one();
        if ($model_c_login != null) {
            $user_login_datetime = date('Y-m-d H:i:s', strtotime($model_c_login->login_date));
        } else {
            $user_login_datetime = date('Y-m-d H:i:s');
        }

        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

//        echo $user_login_datetime;
//        return;

//        $x_date = explode('/', $pos_date);
//        if (count($x_date) > 1) {
//            $t_date = $x_date[2] . '-' . $x_date[1] . '-' . $x_date[0];
//        }
        $order_qty = 0;
        $order_amount = 0;
        $order_cash_qty = 0;
        $order_credit_qty = 0;
        $production_qty = 0;
        $order_product_item = null;

//        $model_order = \backend\models\Orders::find()->where(['date(order_date)'=>$t_date])->all();
//        if($model_order){
//            foreach ($model_order as $value){
//                $model_sale_qty = \backend\models\Orderline::find()->where(['order_id'=>$value->id])->sum('qty');
//                $order_qty = $order_qty + $model_sale_qty;
//            }
//            foreach ($model_order as $value){
//                $model_sale_amount = \backend\models\Orderline::find()->where(['order_id'=>$value->id])->sum('line_total');
//                $order_amount = $order_amount + $model_sale_amount;
//            }
//        }

//        $order_qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('qty');
//        $order_amount = \common\models\QuerySalePosPay::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('payment_amount');
//        $order_amount = \common\models\QuerySalePosPay::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('line_total');
//        $order_cash_qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', $user_login_datetime, $t_date])->sum('qty');
//        $order_credit_qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', $user_login_datetime, $t_date])->sum('qty');
//        $order_cash_qty = \common\models\QuerySaleDataSummary::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['LIKE', 'name', 'สด'])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('qty');
//        $order_credit_qty = \common\models\QuerySaleDataSummary::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['NOT LIKE', 'name', 'สด'])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('qty');

        //  $order_product_item = \common\models\QuerySaleDataSummary::find()->select('product_id')->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->groupBy('product_id')->all();

        // $order_cash_amount_sum = \common\models\QuerySalePosPay::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'payment_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['LIKE', 'name', 'สด'])->sum('payment_amount');
        //  $order_credit_amount_sum = \common\models\QuerySalePosPay::find()->where(['created_by' => $user_id])->andFilterWhere(['between', 'payment_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['LIKE', 'name', 'เครดิต'])->sum('payment_amount');
        // $production_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->andFilterWhere(['company_id'=>$company_id,'branch_id'=>$branch_id])->sum('qty');
        $issue_refill_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 18])->andFilterWhere(['between', 'trans_date', $user_login_datetime, $t_date])->sum('qty');

//        echo $user_login_datetime.'<br />';
//        echo $t_date.'<br />';
//       echo $order_cash_qty.'<br />';
//       echo $order_credit_qty.'<br />';
//
//       return;

        return $this->render('_closesale', [
//            'order_qty' => $order_qty,
//            'order_amount' => $order_amount,
//            'order_cash_qty' => $order_cash_qty,
//            'order_credit_qty' => $order_credit_qty,
//            'order_cash_amount_sum' => $order_cash_amount_sum,
//            'order_credit_amount_sum' => $order_credit_amount_sum,
//            'production_qty' => $production_qty,
            'issue_refill_qty' => $issue_refill_qty,
            // 'order_product_item' => $order_product_item
        ]);
    }

    public function actionSaledailyend()
    {
        $user_id = \Yii::$app->user->id;
//        $user_login_time = \backend\models\User::findLogintime($user_id);
//        $user_login_datetime = \backend\models\User::findLogindatetime($user_id);
//        $t_date = date('Y-m-d H:i:s');
//        $company_id = 1;
//        $branch_id = 1;
//        $default_warehouse = 6;
//        if (!empty(\Yii::$app->user->identity->company_id)) {
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if (!empty(\Yii::$app->user->identity->branch_id)) {
//            $branch_id = \Yii::$app->user->identity->branch_id;
//            if ($branch_id == 2) {
//                $default_warehouse = 5;
//            }
//        }

        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $default_warehouse = $warehouse_primary;
        }

        // $car_warehouse = \backend\models\Warehouse::findWarehousecar($company_id, $branch_id);

        $line_prod_id = \Yii::$app->request->post('line_prod_id');
        //$line_balance_in_id = \Yii::$app->request->post('line_balance_in_id');
        $line_balance_in = \Yii::$app->request->post('line_balance_in');
        $line_balance_out = \Yii::$app->request->post('line_balance_out');
        $line_production_qty = \Yii::$app->request->post('line_production_qty');
        $line_cash_qty = \Yii::$app->request->post('line_cash_qty');
        $line_credit_qty = \Yii::$app->request->post('line_credit_qty');
        $line_car_issue_qty = \Yii::$app->request->post('line_car_issue_qty');
        $line_cash_amount = \Yii::$app->request->post('line_cash_amount');
        $line_credit_amount = \Yii::$app->request->post('line_credit_amount');
        $line_repack_qty = \Yii::$app->request->post('line_repack_qty');
        $line_refill_qty = \Yii::$app->request->post('line_refill_qty');
        $line_scrap_qty = \Yii::$app->request->post('line_scrap_qty');
        $line_stock_count = \Yii::$app->request->post('line_stock_count');
        $line_diff = \Yii::$app->request->post('line_diff');
        $login_date = \Yii::$app->request->post('login_date');
        $line_transfer_qty = \Yii::$app->request->post('line_transfer_qty');

        //print_r($line_prod_id);return;
        //     $order_cash_qty = \common\models\QuerySaleDataSummary::find()->select(['product_id', 'SUM(qty) as qty'])->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', $user_login_datetime, $t_date])->andFilterWhere(['LIKE','name','สด'])->groupBy('product_id')->all();
        //      $order_cash_amount = \common\models\QuerySaleDataSummary::find()->select(['product_id', 'SUM(line_total) as line_total'])->where(['created_by' => $user_id])->andFilterWhere(['between', 'order_date', $user_login_datetime, $t_date])->groupBy('product_id')->all();
//        $order_credit_qty = \common\models\QuerySalePosData::find()->where(['created_by'=>$user_id])->andFilterWhere(['between','order_date',$user_login_datetime,$t_date])->sum('qty');
//
//
        $res = 0;
        if ($user_id != null && $line_prod_id != null) {
//            $fx_datetime = explode(' ', $login_date);
//            $login_at = null;
//            if (count($fx_datetime) > 0) {
//                $f_date = $fx_datetime[0];
//                $f_time = $fx_datetime[1];
//
//                $x_date = explode('-', $f_date);
//                $xx_date = date('Y-m-d');
//                if (count($x_date) > 1) {
//                    $xx_date = trim($x_date[2]) . '/' . trim($x_date[1]) . '/' . trim($x_date[0]);
//                }
//               // echo trim($xx_date.' '.$f_time);return;
//                $login_at = date('Y-m-d H:i:s', strtotime($xx_date . ' ' . $f_time));
//
//            }


            $cur_shift = $this->getTransShift($company_id, $branch_id);
            for ($i = 0; $i <= count($line_prod_id) - 1; $i++) {
                $model = new \common\models\SaleDailySum();
                $model->emp_id = $user_id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $line_prod_id[$i];
                $model->total_cash_qty = $line_cash_qty[$i];
                $model->total_credit_qty = $line_credit_qty[$i];
                $model->total_cash_price = $line_cash_amount[$i];
                $model->total_credit_price = $line_credit_amount[$i];
                $model->total_prod_qty = $line_production_qty[$i];
                $model->trans_shift = $cur_shift;
                $model->balance_in = $line_balance_in[$i];
                $model->balance_out = $line_balance_out[$i];
                $model->prod_repack_qty = $line_repack_qty[$i];
                $model->scrap_qty = $line_scrap_qty[$i];
                $model->issue_refill_qty = $line_refill_qty[$i];
                $model->real_stock_count = $line_stock_count[$i];
                // $model->line_diff = $line_diff[$i] == null ? 0 : $line_diff[$i];
                $model->line_diff = $line_diff[$i];
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->login_date = date('Y-m-d H:i:s', strtotime($this->getLoginlog($user_id)));
                $model->status = 1; // close and cannot edit everything
                if ($model->save(false)) {
                    $res += 1;
                    $model_balance = \common\models\BalanceDaily::find()->where(['product_id' => $line_prod_id[$i], 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
                    if ($model_balance) {
                        $model_balance->balance_qty = $line_stock_count[$i];
                        $model_balance->save(false);
                    } else {
                        $model_balance_new = new \common\models\BalanceDaily(); // create current balance in
                        $model_balance_new->product_id = $line_prod_id[$i];
                        $model_balance_new->balance_qty = $line_stock_count[$i];
                        $model_balance_new->from_user_id = $user_id;
                        $model_balance_new->trans_date = date('Y-m-d H:i:s');
                        $model_balance_new->status = 1;
                        $model_balance_new->company_id = $company_id;
                        $model_balance_new->branch_id = $branch_id;
                        $model_balance_new->save(false);
                    }

                    $this->adjustStock($default_warehouse, $line_prod_id[$i], $line_stock_count[$i], $company_id, $branch_id); // update stock main
                    \common\models\DailyCountStock::updateAll(['status' => 1], ['status' => 0, 'company_id' => $company_id, 'branch_id' => $branch_id]); // update count stock

                }
            }
        }

        if ($res > 0) {

            if ($this->saveTransactionsalepos($line_prod_id, $line_balance_in, $line_balance_out, $line_production_qty, $line_cash_qty, $line_credit_qty, $line_repack_qty, $line_refill_qty, $line_scrap_qty, $line_stock_count, $login_date, $line_transfer_qty,$line_car_issue_qty)) {
                $session = \Yii::$app->session;
                $session->setFlash('after-save', true);
                $session->setFlash('msg', 'บันทึกจบรายการเรียบร้อย');
                // return $this->redirect(['pos/posttrans']);
                return $this->redirect(['site/logout']);
            }
//            $session = \Yii::$app->session;
//            $session->setFlash('after-save', true);
//            $session->setFlash('msg', 'บันทึกจบรายการเรียบร้อย');
//            // return $this->redirect(['pos/posttrans']);
//            return $this->redirect(['site/logout']);
        }

    }

    public function actionSaledailyend2()
    {

        $line_prod_id = \Yii::$app->request->post('line_prod_id');
        //$line_balance_in_id = \Yii::$app->request->post('line_balance_in_id');
        $line_balance_in = \Yii::$app->request->post('line_balance_in');
        $line_balance_out = \Yii::$app->request->post('line_balance_out');
        $line_production_qty = \Yii::$app->request->post('line_production_qty');
        $line_cash_qty = \Yii::$app->request->post('line_cash_qty');
        $line_credit_qty = \Yii::$app->request->post('line_credit_qty');
        $line_car_issue_qty = \Yii::$app->request->post('line_car_issue_qty');
        $line_transfer_qty = \Yii::$app->request->post('line_transfer_qty');
        $line_credit_amount = \Yii::$app->request->post('line_credit_amount');
        $line_repack_qty = \Yii::$app->request->post('line_repack_qty');
        $line_refill_qty = \Yii::$app->request->post('line_refill_qty');
        $line_scrap_qty = \Yii::$app->request->post('line_scrap_qty');
        $line_stock_count = \Yii::$app->request->post('line_stock_count');
        $line_diff = \Yii::$app->request->post('line_diff');
        $login_date = \Yii::$app->request->post('login_date');

        if ($this->saveTransactionsalepos($line_prod_id, $line_balance_in, $line_balance_out, $line_production_qty, $line_cash_qty, $line_credit_qty, $line_repack_qty, $line_refill_qty, $line_scrap_qty, $line_stock_count, $login_date, $line_transfer_qty,$line_car_issue_qty)) {
            $session = \Yii::$app->session;
            $session->setFlash('after-save', true);
            $session->setFlash('msg', 'บันทึกจบรายการเรียบร้อย');
            // return $this->redirect(['pos/posttrans']);
            return $this->redirect(['site/logout']);

            // echo "OK";
        }
    }

    function saveTransactionsalepos($line_prod_id, $line_balance_in, $line_balance_out, $line_production_qty, $line_cash_qty, $line_credit_qty, $line_repack_qty, $line_refill_qty, $line_scrap_qty, $line_stock_count, $login_date, $line_transfer_qty,$line_car_issue_qty)
    {
        $company_id = 0;
        $branch_id = 0;
        $res = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $user_id = \Yii::$app->user->id;
        // $cal_date = date('Y-m-d',strtotime("2022/06/22"));
        $cal_date = date('Y-m-d');

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        //\common\models\TransactionPosSaleSum::deleteAll(['date(trans_date)' => $cal_date]);

        $user_login_datetime = date('Y-m-d H:i:s', strtotime('2022-06-25 07:30:39'));

        $cur_shift = $this->getTransShift($company_id, $branch_id);
        if ($line_prod_id != null) {
            if (count($line_prod_id)) {
                for ($i = 0; $i <= count($line_prod_id) - 1; $i++) {
//                    $new_line_credit_qty = 0;
//                    $isssue_car_qty = 0;
//                    if ($line_credit_qty[$i] > 0) {
//                        $isssue_car_qty = $this->getIssueCarQty($line_prod_id[$i], $user_id, $login_date, date('Y-m-d H:i:s'), $company_id, $branch_id);
//                        if ($line_credit_qty[$i] > $isssue_car_qty) {
//                            $new_line_credit_qty = ($line_credit_qty[$i] - $isssue_car_qty);
//                        } else {
//                            $new_line_credit_qty = ($isssue_car_qty - $line_credit_qty[$i]);
//                        }
//                    }


                    $model_trans = new \common\models\TransactionPosSaleSum();
                    $model_trans->trans_date = $cal_date;
                    $model_trans->product_id = $line_prod_id[$i];
                    $model_trans->cash_qty = $line_cash_qty[$i];
                    $model_trans->credit_qty = $line_credit_qty[$i];//$new_line_credit_qty;
                    $model_trans->free_qty = $this->getFreeQty($line_prod_id[$i], $user_id, $login_date, date('Y-m-d H:i:s'), $company_id, $branch_id); //0;
                    $model_trans->balance_in_qty = $line_balance_in[$i];
                    $model_trans->balance_out_qty = 0;
                    $model_trans->prodrec_qty = $line_production_qty[$i];
                    $model_trans->reprocess_qty = $line_repack_qty[$i];
                    $model_trans->return_qty = $this->getProdReprocessCarDaily($line_prod_id[$i], $login_date, date('Y-m-d H:i:s'), $company_id, $branch_id);
                    $model_trans->issue_car_qty = $this->getIssueCarQty($line_prod_id[$i], $user_id, $login_date, date('Y-m-d H:i:s'), $company_id, $branch_id); //$line_car_issue_qty[$i];//$isssue_car_qty;
                    $model_trans->issue_transfer_qty = $line_transfer_qty[$i];// $this->getTransferout($value->product_id, $cal_date);
                    $model_trans->issue_refill_qty = $line_refill_qty[$i];
                    $model_trans->scrap_qty = $line_scrap_qty[$i];//$this->getScrapDaily($value->product_id, $user_login_datetime, $cal_date);
                    $model_trans->counting_qty = $line_stock_count[$i];
                    $model_trans->shift = $cur_shift;//$this->checkDailyShift($cal_date);
                    $model_trans->company_id = $company_id;
                    $model_trans->branch_id = $branch_id;
                    $model_trans->user_id = $user_id;
                    $model_trans->login_datetime = date('Y-m-d H:i:s', strtotime($login_date));
                    $model_trans->logout_datetime = date('Y-m-d H:i:s');
                    if ($model_trans->save(false)) {
                        $res += 1;
                    }
                }
            }
        }

        return $res;
    }

    function getIssueCarQty($product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($user_id != null) {
            $qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id, 'product_id' => $product_id])
                ->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id, 'payment_method_id' => 2])->andFilterWhere(['>', 'order_channel_id', 0])->sum('line_qty_credit');
        }
        return $qty;
    }
    function getFreeQty($product_id, $user_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($user_id != null) {
            $qty = \common\models\QuerySalePosData::find()->where(['created_by' => $user_id, 'product_id' => $product_id])
                ->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id,'price'=>0])->sum('qty');
        }
        return $qty;
    }

    function getProdReprocessCarDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26]])->andFilterWhere(['product_id' => $product_id])
                ->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])
                ->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->sum('qty');
        }

        return $qty;
    }

    function getReturnCarReprocess($product_id, $login_date)
    {
        $good_qty = 0;
        $model = \backend\models\Stocktrans::find()->where(['product_id' => $product_id, 'activity_type_id' => 26])->andFilterWhere(['>=', 'date(trans_date)', date('Y-m-d', strtotime($login_date))])->sum('qty');
        if ($model) {
            $good_qty = $model;
        }
        return $good_qty;
    }

    public function getLoginlog($user_id)
    {
        $login_date = date('Y-m-d H:i:s');
        if ($user_id) {
            $model = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->limit(1)->orderBy(['id' => SORT_DESC])->one();
            if ($model) {
                $login_date = $model->login_date;
            }
        }
        return $login_date;
    }

    public function getTransShift($company_id, $branch_id)
    {
        $nums = 1;
        $model = \common\models\SaleDailySum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->max('trans_shift');
        if ($model) {
            $nums = $model + 1;
        }
        return $nums;
    }

    public function actionCreateissue()
    {
//        $company_id = 1;
//        $branch_id = 1;
//        $default_warehouse = 6;
//        if (!empty(\Yii::$app->user->identity->company_id)) {
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if (!empty(\Yii::$app->user->identity->branch_id)) {
//            $branch_id = \Yii::$app->user->identity->branch_id;
//            if ($branch_id == 2) {
//                $default_warehouse = 5;
//            }
//        }
        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $default_warehouse = $warehouse_primary;
        }

        $model = new Journalissue();

        if ($model->load(Yii::$app->request->post())) {
            $prod_id = \Yii::$app->request->post('line_prod_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_issue_price = \Yii::$app->request->post('line_issue_line_price');

            //  $sale_for_next_date = \Yii::$app->request->post('is_sale_for_next_date');

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }

            if ($model->is_for_next_date == 'on') {
                $next_date = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($sale_date)));
            } else {
                $next_date = $sale_date . ' ' . date('H:i:s');
            }


            $model->journal_no = $model->getLastNo($next_date, $company_id, $branch_id);
            $model->trans_date = date('Y-m-d H:i:s', strtotime($next_date));
            $model->status = 1;
            $model->reason_id = 1;
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if ($model->save(false)) {
                if ($prod_id != null) {
                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
                        if ($prod_id[$i] == '' && $line_qty[$i] <= 0) continue;
                        $model_line = new \backend\models\Journalissueline();
                        $model_line->issue_id = $model->id;
                        $model_line->product_id = $prod_id[$i];
                        $model_line->qty = $line_qty[$i];
                        $model_line->avl_qty = $line_qty[$i];
                        $model_line->sale_price = $line_issue_price[$i];
                        $model_line->origin_qty = $line_qty[$i];
                        $model_line->status = 1;
                        if ($model_line->save()) {
                            //$this->updatestock($prod_id[$i], $line_qty[$i], $default_warehouse, $model->journal_no, $company_id, $branch_id);
                        }
                    }

                    $this->createOrderforissue($model->delivery_route_id, $sale_date, $company_id, $branch_id, $default_warehouse, $prod_id, $line_qty, $line_issue_price, $model->id);
                }


                if ($model->id != null) {
                    $model_print = \backend\models\Journalissue::find()->where(['id' => $model->id])->one();
                    $model_line = \backend\models\Journalissueline::find()->where(['issue_id' => $model->id])->all();

                    $this->renderPartial('_printissue', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0, 'branch_id' => $branch_id]);
                }

                $session = \Yii::$app->session;
                $session->setFlash('msg-index-car-pos', 'slip.pdf');
                $session->setFlash('after-save', true);
                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');


                return $this->redirect(['pos/index','id'=>0]);
            }

        }

        return $this->renderAjax('_createissue', [
            'model' => $model,
        ]);
    }

    public function createOrderforissue($route_id, $sale_date, $company_id, $branch_id, $default_warehouse, $product_list, $line_qty, $line_price, $issue_id)
    {
        if ($route_id && $product_list != null) {
            $res = 0;
            $model_order = new \backend\models\Orders();
            $model_order->order_no = $model_order->getLastNoCarPos($sale_date, $company_id, $branch_id);
            $model_order->order_date = date('Y-m-d H:i:s', strtotime($sale_date . ' ' . date('H:i:s')));
            $model_order->order_channel_id = $route_id;
            $model_order->sale_channel_id = 2; // pos
            $model_order->payment_status = 0;
            $model_order->order_total_amt = 0;
            $model_order->status = 1;
            $model_order->company_id = $company_id;
            $model_order->branch_id = $branch_id;
            $model_order->payment_method_id = 2; // 1 สด 2 เชื่อ
            if ($model_order->save(false)) {
                if (count($product_list) > 0) {
                    for ($i = 0; $i <= count($product_list) - 1; $i++) {
                        if ($line_qty[$i] <= 0) continue;
                        $model_order_line = new \backend\models\Orderline();
                        $model_order_line->order_id = $model_order->id;
                        $model_order_line->product_id = $product_list[$i];
                        $model_order_line->qty = $line_qty[$i];
                        $model_order_line->price = $line_price[$i];
                        $model_order_line->customer_id = $route_id;
                        $model_order_line->price_group_id = 0;
                        $model_order_line->line_total = ($line_price[$i] * $line_qty[$i]);
                        $model_order_line->status = 1;
                        if ($model_order_line->save(false)) {
                            $res += 1;
                            $model_stock = new \backend\models\Stocktrans();
                            $model_stock->journal_no = $model_order->order_no;
                            $model_stock->trans_date = date('Y-m-d H:i:s');
                            $model_stock->product_id = $product_list[$i];
                            $model_stock->qty = $line_qty[$i];
                            $model_stock->warehouse_id = $default_warehouse; // default
                            $model_stock->stock_type = 2;
                            $model_stock->activity_type_id = 5; // pos sale
                            $model_stock->trans_ref_id = $model_order->id;
                            $model_stock->company_id = $company_id;
                            $model_stock->branch_id = $branch_id;
                            $model_stock->created_by = \Yii::$app->user->id;
                            if ($model_stock->save()) {
                                // $this->updateSummary($product_list[$i], $default_warehouse, $line_qty[$i]);
                            }
                        }
                    }
                }

                if ($res > 0) {
                    $model_update = \backend\models\Journalissue::find()->where(['id' => $issue_id])->one();
                    if ($model_update) {
                        $model_update->order_ref_id = $model_order->id;
                        $model_update->save(false);
                    }
                }

//                if ($pay_total_amount > 0 && $pay_amount > 0) {
//                    $model_pay = new \backend\models\Paymentreceive();
//                    $model_pay->trans_date = date('Y-m-d H:i:s');//date('Y-m-d H:i:s');
//                    $model_pay->customer_id = $customer_id;
//                    $model_pay->journal_no = $model_pay->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
//                    $model_pay->status = 1;
//                    $model_pay->company_id = $company_id;
//                    $model_pay->branch_id = $branch_id;
//                    if ($model_pay->save()) {
//                        $model_pay_line = new \common\models\PaymentReceiveLine();
//                        $model_pay_line->payment_receive_id = $model_pay->id;
//                        $model_pay_line->order_id = $model_order->id;
//                        $model_pay_line->payment_amount = $pay_amount;
//                        $model_pay_line->payment_channel_id = 0; // 1 เงินสด 2 โอน
//                        $model_pay_line->payment_method_id = 2; // 2 เชื่อ
//                        $model_pay_line->payment_type_id = 2;
//                        $model_pay_line->payment_term_id = 0;
//                        $model_pay_line->status = 1;
//                        if ($model_pay_line->save(false)) {
//                            $status = true;
//                            // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
//                            $data = ['pay successfully'];
//                        }
//                    }
//
//                    $this->updateorderpayment($model_order->id, $pay_total_amount, $pay_amount);
//                } else {
//                    $model_pay = new \backend\models\Paymentreceive();
//                    $model_pay->trans_date = date('Y-m-d H:i:s');//date('Y-m-d H:i:s');
//                    $model_pay->customer_id = $customer_id;
//                    $model_pay->journal_no = $model_pay->getLastNo2(date('Y-m-d'), $company_id, $branch_id);
//                    $model_pay->status = 1;
//                    $model_pay->company_id = $company_id;
//                    $model_pay->branch_id = $branch_id;
//                    if ($model_pay->save()) {
//                        $model_pay_line = new \common\models\PaymentReceiveLine();
//                        $model_pay_line->payment_receive_id = $model_pay->id;
//                        $model_pay_line->order_id = $model_order->id;
//                        $model_pay_line->payment_amount = $pay_amount == null ? 0 : $pay_amount;
//                        $model_pay_line->payment_channel_id = 0; // 1 เงินสด 2 โอน
//                        $model_pay_line->payment_method_id = 2; // 2 เชื่อ
//                        $model_pay_line->payment_type_id = 2;
//                        $model_pay_line->payment_term_id = 0;
//                        $model_pay_line->status = 1;
//                        if ($model_pay_line->save(false)) {
//                            $status = true;
//                            // $this->updatePaymenttransline($customer_id, $order_id, $pay_amount, $payment_channel_id);
//                            $data = ['pay successfully'];
//                        }
//                    }
//                    // $this->updateorderpayment($model_order->id, $pay_total_amount, $pay_amount);
//                }


//                if ($model_order->id != null) {
//                    $model = \backend\models\Orders::find()->where(['id' => $model_order->id])->one();
//                    $model_line = \backend\models\Orderline::find()->where(['order_id' => $model_order->id])->all();
//                    $change_amt = \backend\models\Paymenttransline::find()->where(['order_ref_id' => $model_order->id])->one();
//                    $ch_amt = 0;
//                    if ($change_amt != null) {
//                        $ch_amt = $change_amt->change_amount;
//                    }
//
//                    $session = \Yii::$app->session;
//                    $session->setFlash('msg-index', 'slip_index.pdf');
//                    $session->setFlash('after-save', true);
//                    $session->setFlash('msg-is-do', $print_type_doc);
//
//                    $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt]);
//                    if ($print_type_doc == 2) {
//                        $session->setFlash('msg-index-do', 'slip_index_do.pdf');
//                        if (file_exists('../web/uploads/slip_do/slip_index_do.pdf')) {
//                            unlink('../web/uploads/slip_do/slip_index_do.pdf');
//                            //  sleep(4);
//                            $this->createDo($model_order->id);
//                        } else {
//                            $this->createDo($model_order->id);
//                        }
//                        // $this->render('_printtoindex2', ['model' => $model, 'model_line' => $model_line, 'change_amount' => $ch_amt, $print_type_doc]);
//                    } else {
//
//                    }
//                }
            }
        }
    }

    public function updatestock($product_id, $qty, $wh_id, $journal_no, $company_id, $branch_id)
    {
        if ($product_id != null && $qty > 0) {
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $journal_no;
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $wh_id;
            $model_trans->stock_type = 2; // 1 in 2 out
            $model_trans->activity_type_id = 6; // 6 issue cars
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            $model_trans->created_by = \Yii::$app->user->id;
            if ($model_trans->save(false)) {
                $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                if ($model) {
                    $model->qty = $model->qty - (int)$qty;
                    $model->save(false);
                }
            }
        }
    }

    public function actionListissue()
    {
        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new JournalissueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->query->andFilterWhere(['status' => 1]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->renderAjax('_issuelist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
        ]);
    }

    public function actionUpdateissue($id)
    {
        $model = $this->findIssueModel($id);
        $model_line = \backend\models\Journalissueline::find()->where(['issue_id' => $id])->all();

        if ($model->load(Yii::$app->request->post())) {
            $prod_id = \Yii::$app->request->post('line_prod_id');
            $line_qty = \Yii::$app->request->post('line_qty');
            $line_issue_price = \Yii::$app->request->post('line_issue_line_price');

            $removelist = \Yii::$app->request->post('removelist');

            $x_date = explode('/', $model->trans_date);
            $sale_date = date('Y-m-d');
            if (count($x_date) > 1) {
                $sale_date = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }

            $model->trans_date = date('Y-m-d', strtotime($sale_date));
            $model->status = 1;
            if ($model->save()) {
                if ($prod_id != null) {
                    for ($i = 0; $i <= count($prod_id) - 1; $i++) {
                        if ($prod_id[$i] == '') continue;

                        $model_chk = \backend\models\Journalissueline::find()->where(['issue_id' => $model->id, 'product_id' => $prod_id[$i]])->one();
                        if ($model_chk) {
                            $model_chk->qty = $line_qty[$i];
                            $model_chk->save();
                        } else {
                            $model_line = new \backend\models\Journalissueline();
                            $model_line->issue_id = $model->id;
                            $model_line->product_id = $prod_id[$i];
                            $model_line->qty = $line_qty[$i];
                            $model_line->sale_price = $line_issue_price[$i];
                            $model_line->status = 1;
                            $model_line->save();
                        }
                    }
                }

                if ($removelist != '') {
                    $x = explode(',', $removelist);
                    if (count($x) > 0) {
                        for ($m = 0; $m <= count($x) - 1; $m++) {
                            \common\models\Journalissueline::deleteAll(['id' => $x[$m]]);
                        }
                    }
                }
                $session = \Yii::$app->session;
                $session->setFlash('msg', 'บันทึกรายการเรียบร้อย');
                return $this->redirect(['pos/index']);
            }
        }

        return $this->render('_createissue', [
            'model' => $model,
            'model_line' => $model_line
        ]);
    }

    protected function findIssueModel($id)
    {
        if (($model = Journalissue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function updatestockcancel($product_id, $qty, $wh_id, $journal_no, $company_id, $branch_id)
    {
        if ($product_id != null && $qty > 0) {
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $journal_no;
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $wh_id;
            $model_trans->stock_type = 1; // 1 in 2 out
            $model_trans->activity_type_id = 8; // คืนขาย
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            $model_trans->created_by = \Yii::$app->user->id;
            if ($model_trans->save(false)) {
                $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                if ($model) {
                    $model->qty = (int)$model->qty + (int)$qty;
                    $model->save(false);
                }
            }
        }
    }

    public function adjustStock($warehouse_id, $product_id, $qty, $company_id, $branch_id)
    {
        $res = 0;
        if ($company_id != null && $branch_id != null && $warehouse_id && $product_id) {
            $model = new \backend\models\Adjustment();
            $model->journal_no = $model->getLastNo($company_id, $branch_id);
            $model->trans_date = date('Y-m-d H:i:s');
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if ($model->save(false)) {
                $model_line = new \backend\models\Stocktrans();
                $model_line->journal_no = $model->journal_no;
                $model_line->journal_stock_id = $model->id;
                $model_line->trans_date = date('Y-m-d H:i:s');
                $model_line->product_id = $product_id;
                $model_line->warehouse_id = $warehouse_id;
                $model_line->qty = $qty;
                $model_line->stock_type = 1; // out
                $model_line->activity_type_id = 11;
                $model_line->company_id = $company_id;
                $model_line->branch_id = $branch_id;
                if ($model_line->save(false)) {
                    $model_stock = \backend\models\Stocksum::find()->where(['warehouse_id' => $warehouse_id, 'product_id' => $product_id])->one();
                    if ($model_stock) {
                        $model_stock->qty = (int)$qty; // replace stock qty
                        $model_stock->save(false);
                    } else {
                        $model_new = new \backend\models\Stocksum();
                        $model_new->company_id = $company_id;
                        $model_new->branch_id = $branch_id;
                        $model_new->product_id = $product_id;
                        $model_new->qty = $qty;
                        $model_new->warehouse_id = $warehouse_id;
                        $model_new->save(false);
                    }
                    $res += 1;
                }
            }
        }

        return $res;
    }

    public function actionPrintsummary()
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
        $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_user_id = \Yii::$app->request->post('find_user_id');
        $is_invoice_req = \Yii::$app->request->post('is_invoice_req');
        $btn_order_type = \Yii::$app->request->post('btn_order_type');
        return $this->render('_print_sale_summary', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'find_sale_type' => $find_sale_type,
            'find_user_id' => $find_user_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'is_invoice_req' => $is_invoice_req,
            'btn_order_type'=>$btn_order_type,
        ]);
    }

    public function actionPrintcarsummary()
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
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_user_id = \Yii::$app->request->post('find_user_id');
        $is_invoice_req = \Yii::$app->request->post('is_invoice_req');
        $btn_order_type = \Yii::$app->request->post('btn_order_type');
        return $this->render('_print_sale_car_summary', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            //    'find_sale_type'=>$find_sale_type,
            'find_user_id' => $find_user_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'is_invoice_req' => $is_invoice_req,
            'btn_order_type'=>$btn_order_type,
        ]);
    }

    public function actionManageclose()
    {
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $find_user_id = \Yii::$app->request->post('find_user_id');
        //$to_date = \Yii::$app->request->post('to_date');
        //  $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_user_shift = \Yii::$app->request->post('user_shift');
        return $this->render('_closesale_history', [
            'find_user_shift' => $find_user_shift,
            // 'to_date' => $to_date,
            //    'find_sale_type'=>$find_sale_type,
            'find_user_id' => $find_user_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
        ]);
    }

    public function actionFinduserdate()
    {
        $user_id = \Yii::$app->request->post('user_id');
        $html = '';
        if ($user_id) {
            $model = \common\models\SaleDailySum::find()->select(['trans_shift', 'trans_date'])->where(['emp_id' => $user_id])->groupBy(['trans_shift'])->orderBy(['trans_shift' => SORT_DESC])->all();
            if ($model) {
                foreach ($model as $value) {
                    $html .= '<option value="' . $value->trans_shift . '">';
                    $html .= date('Y-m-d', strtotime($value->trans_date));
                    $html .= '</option>';
                }
            }
        }
        echo $html;
    }

    public function actionEditsaleclose()
    {
        $company_id = 0;
        $branch_id = 0;
        $default_warehouse = 0; // 6
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
            $warehouse_primary = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $default_warehouse = $warehouse_primary;
        }

        // $car_warehouse = \backend\models\Warehouse::findWarehousecar($company_id, $branch_id);

        $line_prod_id = \Yii::$app->request->post('line_prod_id');
        $line_count_new = \Yii::$app->request->post('line_count_new');
        //$line_balance_in_id = \Yii::$app->request->post('line_balance_in_id');
        $line_balance_in = \Yii::$app->request->post('line_balance_in');
//        $line_balance_out = \Yii::$app->request->post('line_balance_out');
//        $line_production_qty = \Yii::$app->request->post('line_production_qty');
//        $line_cash_qty = \Yii::$app->request->post('line_cash_qty');
//        $line_credit_qty = \Yii::$app->request->post('line_credit_qty');
//        $line_cash_amount = \Yii::$app->request->post('line_cash_amount');
//        $line_credit_amount = \Yii::$app->request->post('line_credit_amount');
//        $line_repack_qty = \Yii::$app->request->post('line_repack_qty');
//        $line_refill_qty = \Yii::$app->request->post('line_refill_qty');
//        $line_scrap_qty = \Yii::$app->request->post('line_scrap_qty');
        $line_stock_count = \Yii::$app->request->post('line_stock_count');
//        $line_diff = \Yii::$app->request->post('line_diff');
//        $login_date = \Yii::$app->request->post('login_date');

        $current_trans_shift = \Yii::$app->request->post('find_user_shift');

        if ($current_trans_shift != null && $line_prod_id != null) {

            $model = new \backend\models\Adjustment();
            $model->journal_no = $model->getLastNo($company_id, $branch_id);
            $model->trans_date = date('Y-m-d H:i:s');
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;

            if ($model->save(false)) {
                for ($i = 0; $i <= count($line_prod_id) - 1; $i++) {
                    if ($line_prod_id[$i] == null || $line_prod_id[$i] == '' || $line_count_new[$i] == 0 || $line_count_new[$i] == '' || $line_count_new[$i] == null || $line_count_new[$i] == 0.00 || $line_count_new[$i] == '0.00') continue;

//                    $model_update = \common\models\SaleDailySum::find()->where(['trans_shift' => $current_trans_shift, 'product_id' => $line_prod_id[$i], 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
//                    if ($model_update) {
//                        $model_update->balance_in = $line_count_new[$i];
//                        if ($model_update->save(false)) {

                    if ($line_count_new[$i] > 0 || $line_count_new[$i] != null || $line_count_new[$i] != '') {

                        $new_qty = 0;
                        $stock_type = 0;
                        if ($line_count_new[$i] >= $line_stock_count[$i]) {
                            $new_qty = (int)$line_count_new[$i] - (int)$line_stock_count[$i];
                            $stock_type = 1;
                        } else {
                            $new_qty = (int)$line_stock_count[$i] - (int)$line_count_new[$i];
                            $stock_type = 2;
                        }

                        $model_line = new \backend\models\Stocktrans();
                        $model_line->journal_no = $model->journal_no;
                        $model_line->journal_stock_id = $model->id;
                        $model_line->trans_date = date('Y-m-d H:i:s');
                        $model_line->product_id = $line_prod_id[$i];
                        $model_line->warehouse_id = $default_warehouse;
                        $model_line->qty = $new_qty;
                        $model_line->stock_type = $stock_type;
                        $model_line->activity_type_id = 11;
                        $model_line->company_id = $company_id;
                        $model_line->branch_id = $branch_id;
                        if ($model_line->save(false)) {
                            $model_stock = \backend\models\Stocksum::find()->where(['warehouse_id' => $default_warehouse, 'product_id' => $line_prod_id[$i], 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
                            if ($model_stock) {
                                if ($stock_type == 1) {
                                    $model_stock->qty = (int)$model_stock->qty + (int)$new_qty;
                                } else {
                                    $model_stock->qty = (int)$model_stock->qty - (int)$new_qty;
                                }

                                $model_stock->save(false);

                            }
                            \common\models\BalanceDaily::deleteAll(['product_id' => $line_prod_id[$i], 'company_id' => $company_id, 'branch_id' => $branch_id]);
                            $model_balance_daily = new \common\models\BalanceDaily();
                            $model_balance_daily->trans_date = date('Y-m-d H:i:s');
                            $model_balance_daily->product_id = $line_prod_id[$i];
                            $model_balance_daily->balance_qty = $line_count_new[$i];
                            $model_balance_daily->company_id = $company_id;
                            $model_balance_daily->branch_id = $branch_id;
                            $model_balance_daily->status = 1;
                            $model_balance_daily->from_user_id = \Yii::$app->user->id;
                            $model_balance_daily->save(false);
                        }
                    }
//                        }
//                    }


                }
            }

        }
        return $this->redirect(['pos/manageclose']);
    }

    public function actionCreatescreenshort()
    {
        $img = \Yii::$app->request->post('img');//getting post img data
        //$img = substr(explode(";",$img),1,7);//converting the data
        $target = time() . 'img.png';//making file name

        //  file_put_contents('../../web/uploads/'.$target, base64_decode($img));

        if ($img != null) {
            $newfile = time() . ".jpg";
            $outputfile = '../web/uploads/assetcheck/' . $newfile;          //save as image.jpg in uploads/ folder

            $filehandler = fopen($outputfile, 'wb');
            //file open with "w" mode treat as text file
            //file open with "wb" mode treat as binary file

            fwrite($filehandler, base64_decode(trim($img)));
            // we could add validation here with ensuring count($data)>1

            // clean up the file resource
            fclose($filehandler);
            // file_put_contents($newfile,base64_decode($base64_string));
            // $newfile = base64_decode($base64_string);
        }
        echo "ok";
    }

    public function actionCalcloseshift()
    {
        $user_login_datetime = \Yii::$app->request->post('user_login_datetime');
        $t_date = \Yii::$app->request->post('t_date');
        $user_id = \Yii::$app->request->post('user_id');

        // $user_login_datetime = '2023-01-07 15:51:11';

        if ($user_id != null) {

            \common\models\SalePosCloseCashQty::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCreditQty::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCashAmount::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseCreditAmount::deleteAll(['user_id' => $user_id]);
            \common\models\SalePosCloseIssueCarQty::deleteAll(['user_id' => $user_id]);

            $sql = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_cash";
            $sql .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql .= " AND orders.payment_method_id = 1";
            $sql .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            // $sql .= " AND orders.created_by=181";
            $sql .= " AND orders.created_by=" . $user_id;
            $sql .= " GROUP BY order_line.product_id";

            $query = \Yii::$app->db->createCommand($sql);
            $model = $query->queryAll();
            if ($model) {
                for ($i = 0; $i <= count($model) - 1; $i++) {
                    $product_id = $model[$i]['product_id'];
                    $qty = $model[$i]['line_total_cash'];

                    if ($product_id != null) {
                        $model_x = new \common\models\SalePosCloseCashQty();
                        $model_x->product_id = $product_id;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }


            $sql2 = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_credit";
            $sql2 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql2 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql2 .= " AND orders.payment_method_id = 2";
          //  $sql2 .= " AND orders.order_channel_id = 0";
            $sql2 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql2 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            //$sql .= " AND orders.created_by=181";
            $sql2 .= " AND orders.created_by=" . $user_id;
            $sql2 .= " GROUP BY order_line.product_id";

            $query2 = \Yii::$app->db->createCommand($sql2);
            $model2 = $query2->queryAll();
            if ($model2) {
                for ($i = 0; $i <= count($model2) - 1; $i++) {
                    $product_id2 = $model2[$i]['product_id'];
                    $qty2 = $model2[$i]['line_total_credit'];

                    if ($product_id2 != null) {
                        $model_x = new \common\models\SalePosCloseCreditQty();
                        $model_x->product_id = $product_id2;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty2;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }

            $sql20 = "SELECT order_line.product_id, SUM(order_line.qty) as line_total_credit";
            $sql20 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql20 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql20 .= " AND orders.order_channel_id > 0";
            $sql20 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql20 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            $sql20 .= " AND orders.created_by=" . $user_id;
            $sql20 .= " GROUP BY order_line.product_id";

            $query20 = \Yii::$app->db->createCommand($sql20);
            $model20 = $query20->queryAll();
            if ($model20) {
                for ($i = 0; $i <= count($model20) - 1; $i++) {
                    $product_id20 = $model20[$i]['product_id'];
                    $qty20 = $model20[$i]['line_total_credit'];

                    if ($product_id20 != null) {
                        $model_x = new \common\models\SalePosCloseIssueCarQty();
                        $model_x->product_id = $product_id20;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->ent_date = date('Y-m-d H:i:s');
                        $model_x->qty = $qty20;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }


            $sql3 = "SELECT order_line.product_id, SUM(order_line.line_total) as line_total_cash";
            $sql3 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql3 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql3 .= " AND orders.payment_method_id = 1";
            $sql3 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql3 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            //$sql .= " AND orders.created_by=181";
            $sql3 .= " AND orders.created_by=" . $user_id;
            $sql3 .= " GROUP BY order_line.product_id";

            $query3 = \Yii::$app->db->createCommand($sql3);
            $model3 = $query3->queryAll();
            if ($model3) {
                for ($i = 0; $i <= count($model3) - 1; $i++) {
                    $product_id3 = $model3[$i]['product_id'];
                    $amount3 = $model3[$i]['line_total_cash'];

                    if ($product_id3 != null) {
                        $model_x = new \common\models\SalePosCloseCashAmount();
                        $model_x->product_id = $product_id3;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $amount3;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }

            $sql4 = "SELECT order_line.product_id, SUM(order_line.line_total) as line_total_credit";
            $sql4 .= " FROM orders inner join order_line on orders.id = order_line.order_id";
            $sql4 .= " WHERE orders.sale_channel_id = 2 and orders.status <> 3 ";
            $sql4 .= " AND orders.payment_method_id = 2";
            $sql4 .= " AND orders.order_date>=" . "'" . date('Y-m-d H:i:s', strtotime($user_login_datetime)) . "'";
            $sql4 .= " AND orders.order_date<=" . "'" . date('Y-m-d H:i:s') . "'";
            // $sql .= " AND orders.created_by=181";
            $sql4 .= " AND orders.created_by=" . $user_id;
            $sql4 .= " GROUP BY order_line.product_id";

            $query4 = \Yii::$app->db->createCommand($sql4);
            $model4 = $query4->queryAll();
            if ($model4) {
                for ($i = 0; $i <= count($model4) - 1; $i++) {
                    $product_id4 = $model4[$i]['product_id'];
                    $amount4 = $model4[$i]['line_total_credit'];

                    if ($product_id4 != null) {
                        $model_x = new \common\models\SalePosCloseCreditAmount();
                        $model_x->product_id = $product_id4;
                        $model_x->start_date = date('Y-m-d H:i:s', strtotime($user_login_datetime));
                        $model_x->end_date = date('Y-m-d H:i:s');
                        $model_x->qty = $amount4;
                        $model_x->trans_date = date('Y-m-d H:i:s');
                        $model_x->user_id = $user_id;
                        $model_x->save(false);
                    }

                }
            }

        }
        return $this->redirect(['pos/posttrans']);
    }
    public function actionPrintpossummary()
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
        $find_sale_type = \Yii::$app->request->post('find_sale_type');
        $find_user_id = \Yii::$app->request->post('find_user_id');
        $is_invoice_req = \Yii::$app->request->post('is_invoice_req');
        $btn_order_type = \Yii::$app->request->post('btn_order_type');
        return $this->render('_print_sale_pos_summary', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'find_sale_type' => $find_sale_type,
            'find_user_id' => $find_user_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'is_invoice_req' => $is_invoice_req,
            'btn_order_type'=>$btn_order_type,
        ]);
    }

    ///// NEW FOR CAL MANAGER SUMMARY

    public function actionStartcaldailymanager($caldate)
    {
        $company_id = 1;
        $branch_id = 2;
        $xdate = explode('-',$caldate);

        // $create_date = date_create('2024-02-20');

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        $findcaldate = date('Y-m-d');
        if(count($xdate) >1){
            $findcaldate = $xdate[2].'/'.$xdate[1].'/'.$xdate[0].' '.'00:01:01';
            $from_date = $findcaldate;
            $to_date = $findcaldate;
        }

        $find_sale_type = 0;
        $sum_qty_all = 0;
        $sum_total_all = 0;

        $total_qty = 0;
        $total_qty2 = 0;
        $total_qty3 = 0;
        $total_qty4 = 0;
        $total_qty5 = 0;
        $total_qty_all = 0;

        $total_amount = 0;
        $total_amount2 = 0;
        $total_amount3 = 0;
        $total_amount4 = 0;
        $total_amount5 = 0;
        $total_amount_all = 0;
        $find_user_id = null;
        $is_invoice_req = null;
        $btn_order_type = null;

        $model_product_daily = \backend\models\Product::find()->where(['status' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->orderBy(['item_pos_seq' => SORT_ASC])->all();
        \common\models\TransactionManagerDaily::deleteAll(['date(trans_date)'=>date('Y-m-d',strtotime($findcaldate))]);
        foreach ($model_product_daily as $value) {
            $line_product_price_list = $this->getProductpricelist($value->id, $from_date, $to_date, $company_id, $branch_id);
            if ($line_product_price_list != null) {



                for ($x = 0; $x <= count($line_product_price_list) - 1; $x++) {

                    $find_order = $this->getOrdercash($value->id, $from_date, $to_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_product_price_list[$x]['line_price']);
                    $find_order2 = $this->getOrderCredit($value->id, $from_date, $to_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_product_price_list[$x]['line_price']);
                    $find_order4 = $this->getOrderCarOtherCredit($value->id, $from_date, $to_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_product_price_list[$x]['line_price']);
                    $find_order5 = $this->getOrderRoute($value->id, $from_date, $to_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_product_price_list[$x]['line_price']);

                    $line_qty = $find_order != null ? $find_order[0]['qty'] : 0;
                    $line_qty2 = $find_order2 != null ? $find_order2[0]['qty'] : 0;
//                        $line_qty3 = $find_order3 != null ? $find_order3[0]['qty'] : 0;
                    $line_qty4 = $find_order4 != null ? $find_order4[0]['qty'] : 0;
                    $line_qty5 = $find_order5 != null ? $find_order5[0]['qty'] : 0;

                    $line_total_qty = ($line_qty + $line_qty2 + $line_qty4 + $line_qty5);
                    $total_qty_all = ($total_qty_all + $line_total_qty);

                    $line_amount = $find_order != null ? $find_order[0]['line_total'] : 0;
                    $line_amount2 = $find_order2 != null ? $find_order2[0]['line_total'] : 0;
                    // $line_amount3 = $find_order3 != null ? $find_order3[0]['line_total']:0;
                    $line_amount4 = $find_order4 != null ? $find_order4[0]['line_total'] : 0;
                    $line_amount5 = $find_order5 != null ? $find_order5[0]['line_total'] : 0;

                    $line_total_amt = ($line_amount + $line_amount2 + $line_amount4 + $line_amount5);
                    $total_amount_all = ($total_amount_all + $line_total_amt);

                    $total_qty = ($total_qty + $line_qty);
                    $total_qty2 = ($total_qty2 + $line_qty2);
                    //  $total_qty3 = ($total_qty3 + $line_qty3);
                    $total_qty4 = ($total_qty4 + $line_qty4);
                    $total_qty5 = ($total_qty5 + $line_qty5);

                    $total_amount = ($total_amount + $line_amount);
                    $total_amount2 = ($total_amount2 + $line_amount2);
                    //  $total_amount3 = ($total_amount3 + $line_amount3);
                    $total_amount4 = ($total_amount4 + $line_amount4);
                    $total_amount5 = ($total_amount5 + $line_amount5);


                    $model_add = new \common\models\TransactionManagerDaily();
                    $model_add->trans_date = date('Y-m-d H:i:s',strtotime($findcaldate));
                    $model_add->product_id = $value->id;
                    $model_add->price = $line_product_price_list[$x]['line_price'];
                    $model_add->cash_qty = $line_qty;
                    $model_add->credit_pos_qty = $line_qty2;
                    $model_add->car_qty = $line_qty5;
                    $model_add->other_branch_qty = $line_qty4;
                    $model_add->qty_total = $line_total_qty;
                    $model_add->cash_amount = $line_amount;
                    $model_add->credit_pos_amount = $line_amount2;
                    $model_add->car_amount = $line_amount5;
                    $model_add->other_branch_amount = $line_amount4;
                    $model_add->amount_total = $line_total_amt;
                    $model_add->save(false);


                }
            }else{
                echo "no data";
            }
        }
    }


    function getProductpricelist($product_id, $f_date, $t_date, $company_id, $branch_id)
    {
        $data = [];
        $sql = "SELECT t1.price
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t1.qty > 0
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;
        $sql .= " GROUP BY t1.price";
        $sql .= " ORDER BY t1.price asc";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                array_push($data, [
                    'line_price' => $model[$i]['price'],
                ]);
            }
        }
        return $data;
    }

    function getOrderRoute($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_price)
    {
        $data = [];
        $sql = "SELECT sum(qty) as qty, sum(line_total) as line_total
              FROM query_sale_mobile_data_new
             WHERE  date(order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . "
             AND date(order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . "
             AND product_id=" . $product_id . "
             AND price=" . $line_price . "
             AND company_id=" . $company_id . " AND branch_id=" . $branch_id;


        if ($find_user_id != null) {
            $sql .= " AND created_by=" . $find_user_id;
        }
//    if ($is_invoice_req != null) {
//        $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
//    }
        $sql .= " GROUP BY product_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {


                array_push($data, [
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
//                array_push($data, [
//                'qty' => 0,
//                'line_total' =>0,
//            ]);
        return $data;
    }

    function getOrdercash($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_price)
    {
        $data = [];
        $sql = "SELECT sum(t1.qty) as qty, sum(t1.line_total) as line_total
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id 
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t2.sale_channel_id = 2
             AND t1.price=" . $line_price . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

        $sql .= " AND t2.payment_method_id=1";


        if ($find_user_id != null) {
            $sql .= " AND t2.created_by=" . $find_user_id;
        }
        if ($is_invoice_req != null) {
            $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
        }
        $sql .= " GROUP BY t1.product_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {


                array_push($data, [
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
        return $data;
    }

    function getOrderCredit($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_price)
    {
        $data = [];
        $sql = "SELECT sum(t1.qty) as qty, sum(t1.line_total) as line_total
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id 
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t2.sale_channel_id = 2
              AND t1.price=" . $line_price . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

        $sql .= " AND (t2.order_channel_id = 0 OR t2.order_channel_id is null) AND t2.payment_method_id= 2";


        if ($find_user_id != null) {
            $sql .= " AND t2.created_by=" . $find_user_id;
        }
        if ($is_invoice_req != null) {
            $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
        }
        $sql .= " GROUP BY t1.product_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {


                array_push($data, [
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
        return $data;
    }

    function getOrderCarCredit($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_price)
    {
        $data = [];
        $sql = "SELECT sum(t1.qty) as qty, sum(t1.line_total) as line_total
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id INNER JOIN delivery_route as t4 on t2.order_channel_id = t4.id
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t2.sale_channel_id = 2
              AND t1.price=" . $line_price . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

        $sql .= " AND t2.order_channel_id > 0";
        $sql .= " AND t4.is_other_branch = 0";

        if ($find_user_id != null) {
            $sql .= " AND t2.created_by=" . $find_user_id;
        }
        if ($is_invoice_req != null) {
            $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
        }
        $sql .= " GROUP BY t1.product_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {


                array_push($data, [
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
        return $data;
    }

    function getOrderCarOtherCredit($product_id, $f_date, $t_date, $find_sale_type, $find_user_id, $company_id, $branch_id, $is_invoice_req, $btn_order_type, $line_price)
    {
        $data = [];
        $sql = "SELECT sum(t1.qty) as qty, sum(t1.line_total) as line_total
              FROM order_line as t1 INNER JOIN orders as t2 ON t1.order_id = t2.id LEFT  JOIN customer as t3 ON t2.customer_id=t3.id INNER JOIN delivery_route as t4 on t2.order_channel_id = t4.id
             WHERE  date(t2.order_date) >=" . "'" . date('Y-m-d', strtotime($f_date)) . "'" . " 
             AND date(t2.order_date) <=" . "'" . date('Y-m-d', strtotime($t_date)) . "'" . " 
             AND t1.product_id=" . $product_id . " 
             AND t2.status <> 3
             AND t2.sale_channel_id = 2
              AND t1.price=" . $line_price . "
             AND t2.company_id=" . $company_id . " AND t2.branch_id=" . $branch_id;

        $sql .= " AND t2.order_channel_id > 0";
        $sql .= " AND t4.is_other_branch = 1";

        if ($find_user_id != null) {
            $sql .= " AND t2.created_by=" . $find_user_id;
        }
        if ($is_invoice_req != null) {
            $sql .= " AND t3.is_invoice_req =" . $is_invoice_req;
        }
        $sql .= " GROUP BY t1.product_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {


                array_push($data, [
                    'qty' => $model[$i]['qty'],
                    'line_total' => $model[$i]['line_total'],
                ]);
            }
        }
        return $data;
    }

}
