<?php

namespace frontend\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;


class ProductionController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'addprodrec' => ['POST'],
                    'cancelprodrec' => ['POST'],
                    'addprodrecreprocess' => ['POST'],
                    'warehouselist' => ['POST'],
                    'addreprocesswip' => ['POST'],
                    'productionreprint' => ['POST'],
                    'findprodrec' => ['POST'],
                    'findproductionrec' => ['POST'],
                    'findproductionrec2' => ['POST'],
                    'findproductionrec3' => ['POST'],
                    'findproductionrec4' => ['POST'],
                    'addscrap' => ['POST'],
                    'prodreclist' => ['POST'], // add new
                    'prodrecfind' => ['POST'], // add new
                    'onhandlist' => ['POST'], // add new
                    'onhandselect' => ['POST'], // add new
                    'reprocessmainlist' => ['POST'], // add new
                    'addrealcount' => ['POST'], // add new
                    'reservlist' => ['POST'], // add new
                    'reservselected' => ['POST'], // add new
                    'reservconfirm' => ['POST'], // add new
                    'machinelist' => ['POST'],
                    'manageprod' => ['POST'],
                    'productionlist' => ['POST'],
                    'updateprodstatus' => ['POST'],
                    'addprodrecprod' => ['POST'],
                    'addprodrecmobile' => ['POST'],
                    'listmobileall' => ['POST'],
                    'deleprodrecline'=> ['POST'],
                    'addproducttransform'=>['POST'],
                    'scraplist'=>['POST'],
                    'addscrapmobile' => ['POST'],
                    'scrapcancel' => ['POST'],
                    'producttransferlist'=> ['POST'],
                    'addproducttransfermobile' => ['POST'],
                    'deleproducttransferline' => ['POST'],
                    'deleteproducttransform' => ['POST'],
                    'transferlist' => ['POST'],
                    'addprodrectransfer' => ['POST'],

                ],
            ],
        ];
    }

    public function actionAddprodrec()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $qty = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];

        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 1) {
            $act_id = 15; //รับผลิต
        } else if ($production_type == 2) {
            $act_id = 26; // reprocess รถ
        } else if ($production_type == 3) {
            $act_id = 27; // reprocess
        } else if ($production_type == 5) {
            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
        }

        if ($product_id && $warehouse_id && $qty) {

            //$main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
    //        $warehouse_id = 6; // หนองขาหยั่ง
            $warehouse_id = 5; // บางกระทึก
    //        $warehouse_id = 16; // อ้อมหน้อย
            $model_journal = new \backend\models\Stockjournal();
            if ($production_type == 1) {
                sleep(3);
                $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            } else if ($production_type == 5) {
                sleep(2);
                $model_journal->journal_no = $model_journal->getLastNoReceiveTransfer($company_id, $branch_id);
            } else {
                sleep(2);
                //       $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->journal_no = $model_journal->getLastNoCarreprocess($company_id, $branch_id);
            }


            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = $act_id; // $production_type;
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $product_id;
                $model->qty = $qty;
                $model->warehouse_id = $warehouse_id;//$warehouse_id;
                $model->stock_type = 1;
                $model->activity_type_id = $act_id; // 15 prod rec
                $model->production_type = $production_type;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateSummary($product_id, $warehouse_id, $qty);
                }
            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddprodrectransfer()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $qty = 0;
        $transfer_from_branch = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];
        $transfer_from_branch = $req_data['transfer_branch_id'];

        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 1) {
            $act_id = 15; //รับผลิต
        } else if ($production_type == 2) {
            $act_id = 26; // reprocess รถ
        } else if ($production_type == 3) {
            $act_id = 27; // reprocess
        } else if ($production_type == 5) {
            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
        }

        if ($product_id && $warehouse_id && $qty) {

            //$main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
       //     $warehouse_id = 6; // หนองขาหยั่ง
            $warehouse_id = 5; // bkt
            $model_journal = new \backend\models\Stockjournal();
            if ($production_type == 1) {
                sleep(3);
                $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            } else if ($production_type == 5) {
                sleep(2);
                $model_journal->journal_no = $model_journal->getLastNoReceiveTransfer($company_id, $branch_id);
            } else {
                sleep(2);
                //       $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->journal_no = $model_journal->getLastNoCarreprocess($company_id, $branch_id);
            }


            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = $act_id; // $production_type;
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $product_id;
                $model->qty = $qty;
                $model->warehouse_id = $warehouse_id;//$warehouse_id;
                $model->stock_type = 1;
                $model->activity_type_id = $act_id; // 15 prod rec
                $model->production_type = $production_type;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                $model->transfer_branch_id = $transfer_from_branch;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateSummary($product_id, $warehouse_id, $qty);
                }
            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddproducttransfermobile()
    {
        $company_id = 0;
        $branch_id = 0;
        $datalist = null;
        $warehouse_id = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $datalist = $req_data['data'];
        $warehouse_id = $req_data['warehouse_id'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];

        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 1) {
            $act_id = 15; //รับผลิต
        } else if ($production_type == 2) {
            $act_id = 26; // reprocess รถ
        } else if ($production_type == 3) {
            $act_id = 27; // reprocess
        } else if ($production_type == 5) {
            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
        }

        if ($warehouse_id != null && $datalist != null) {

            //$main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $warehouse_id = 1; // หนองดินแดง
            $model_journal = new \backend\models\Stockjournal();
            if ($production_type == 1) {
                sleep(1);
                $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            } else if ($production_type == 5) {
                sleep(1);
                $model_journal->journal_no = $model_journal->getLastNoReceiveTransfer($company_id, $branch_id);
            } else {
                sleep(1);
                //       $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->journal_no = $model_journal->getLastNoCarreprocess($company_id, $branch_id);
            }


            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = $act_id; // $production_type;
            if ($model_journal->save(false)) {
                for($i=0;$i<=count($datalist)-1;$i++){
                    $model = new \backend\models\Stocktrans();
                    $model->journal_no = $model_journal->journal_no;
                    $model->journal_id = $model_journal->id;
                    $model->trans_date = date('Y-m-d H:i:s');
                    $model->product_id = $datalist[$i]['product_id'];
                    $model->qty = $datalist[$i]['qty'];
                    $model->warehouse_id = $warehouse_id;//$warehouse_id;
                    $model->stock_type = 1;
                    $model->activity_type_id = $act_id; // 15 prod rec
                    $model->production_type = $production_type;
                    $model->company_id = $company_id;
                    $model->branch_id = $branch_id;
                    $model->created_by = $user_id;
                    $model->transfer_branch_id = $datalist[$i]['transfer_branch_id'];
                    if ($model->save(false)) {
                        $status = 1;
                        $this->updateSummary($datalist[$i]['product_id'], $warehouse_id, $datalist[$i]['qty']);
                    }
                }

            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddprodrecFromMachine()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $qty = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];

        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 1) {
            $act_id = 15; //รับผลิต
        } else if ($production_type == 2) {
            $act_id = 26; // reprocess รถ
        } else if ($production_type == 3) {
            $act_id = 27; // reprocess
        } else if ($production_type == 5) {
            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
        }

        if ($product_id && $warehouse_id && $qty) {

            $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            $model_journal = new \backend\models\Stockjournal();
            if ($production_type == 1) {
                $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            } else if ($production_type == 5) {
                $model_journal->journal_no = $model_journal->getLastNoReceiveTransfer($company_id, $branch_id);
            } else {
                //       $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->journal_no = $model_journal->getLastNoCarreprocess($company_id, $branch_id);
            }


            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = $act_id; // $production_type;
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $product_id;
                $model->qty = $qty;
                $model->warehouse_id = $main_warehouse;//$warehouse_id;
                $model->stock_type = 1;
                $model->activity_type_id = $act_id; // 15 prod rec
                $model->production_type = $production_type;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateSummary($product_id, $main_warehouse, $qty);
                }
            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddprodrecprod()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $loc_id = "";
        $qty = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];
        $loc_id = $req_data['loc_id'];


        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 6) {
            $act_id = 15; //รับผลิต
        }

//        if ($production_type == 1) {
//            $act_id = 15; //รับผลิต
//        } else if ($production_type == 2) {
//            $act_id = 26; // reprocess รถ
//        } else if ($production_type == 3) {
//            $act_id = 27; // reprocess
//        } else if ($production_type == 5) {
//            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
//        }

        if ($product_id && $warehouse_id && $qty) {

            $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            $model_journal = new \backend\models\Stockjournal();
            if ($production_type == 6) {
                $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            }

            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = $act_id; // $production_type;
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $product_id;
                $model->qty = $qty;//0;
                $model->warehouse_id = $main_warehouse;//$warehouse_id;
                $model->stock_type = 1;
                $model->activity_type_id = $act_id; // 15 prod rec
                $model->production_type = 1; // normal receive
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                $model->production_loc_id = $loc_id;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateSummary($product_id, $main_warehouse, $qty);


                    \common\models\ProductionStatus::updateAll(['total_hour' => 0, 'end_date' => NULL, 'color_status' => 'R', 'start_date' => date('Y-m-d H:i:s')], ['loc_id' => $loc_id]);
                }
            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddprodrecreprocess()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $qty = 0;
        $issue_repack_no = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = $req_data['production_type'];
        $issue_repack_no = $req_data['issue_repack_no'];

        $data = [];
        $status = false;
        $journal_no = '';

        if ($product_id && $company_id && $branch_id && $qty && $issue_repack_no != null) {
            $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            $model_journal = new \backend\models\Stockjournal();
            $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = $product_id;
                $model->qty = $qty;
                $model->warehouse_id = $default_warehouse;
                $model->stock_type = 1;
                $model->activity_type_id = 27; // 15 prod rec 26 repack car 27 repack
                $model->production_type = $production_type;  // 1 รับผลิต 2 รับ rp รถ 3 รับผลิต rp
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateSummary($product_id, $default_warehouse, $qty);


                    // update product status


                }
            }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function updateSummary($product_id, $wh_id, $qty)
    {
        if ($wh_id != null && $product_id != null && $qty > 0) {
            $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
            if ($model) {
                $new_qty =0;
                if($model->qty != null){
                    $new_qty = (float)$model->qty + (float)$qty;
                }else{
                    $new_qty = (float)$qty;
                }
                $model->qty = $new_qty;
                $model->save(false);
            } else {
                $model_new = new \backend\models\Stocksum();
                $model_new->company_id = 1;
                $model_new->branch_id = 1;
                $model_new->warehouse_id = $wh_id;
                $model_new->product_id = $product_id;
                $model_new->qty = (float)$qty;
                $model_new->save(false);
            }
        }
    }

    public function actionWarehouselist()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $warehouse_code = $req_data['wh_code'];

        $data = [];
        $status = false;

        if ($company_id) {
            $model = \common\models\Warehouse::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['LIKE', 'code', $warehouse_code])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        //'image' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        //'image' => 'http://119.59.100.74/icesystem/backend/web/uploads/images/products/' . $product_info->photo,
                        'code' => $value->code,
                        'name' => $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddreprocesswip()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $product_id = $req_data['product_id'];
        // $warehouse_id = $req_data['warehouse_id'];
        $qty = $req_data['qty'];
        $reason = $req_data['reason'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id != null && $product_id != null && $branch_id != null) {
            $model = new \common\models\IssueReprocess();
            $model->journal_no = 'IW-';
            $model->trans_date = date('Y-m-d H:i:s');
            $model->status = 1;
            $model->created_at = time();
            $model->created_by = 1;
            $model->reason = $reason;
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            if ($model->save(false)) {
                $model_line = new \common\models\IssueReprocessLine();
                $model_line->issue_id = $model->id;
                $model_line->product_id = $product_id;
                $model_line->to_warehouse_id = 0;
                $model_line->qty = $qty;
                $model_line->status = 1;
                if ($model_line->save(false)) {
                    $this->updatestock($product_id, $qty, $company_id, $branch_id);
                    $status = 1;
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionProductionreprint()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $journal_no = $req_data['journal_no'];
        $trans_date = $req_data['trans_date'];

        $data = [];
        $status = false;

        $journal_date = date('Y-m-d');
        $t_date = null;
        $exp_order_date = explode(' ', $trans_date);
        if ($exp_order_date != null) {
            if (count($exp_order_date) > 1) {
                $x_date = explode('-', $exp_order_date[0]);
                if (count($x_date) > 1) {
                    $t_date = $x_date[0] . "/" . $x_date[1] . "/" . $x_date[2];
                }
            }
        }
        if ($t_date != null) {
            $journal_date = $t_date;
        }
        //if ($journal_no ) {
        $model = null;
        if ($journal_no == '') {
            $model = \common\models\StockTrans::find()->where(['date(trans_date)' => $journal_date])->andFilterWhere(['!=', 'status', 3])->all();
        } else {
            $model = \common\models\StockTrans::find()->where(['LIKE', 'journal_no', $journal_no, 'date(trans_date)' => $journal_date])->andFilterWhere(['!=', 'status', 3])->all();
        }

        // $model = \common\models\QueryCustomerPrice::find()->all();
        if ($model) {
            $status = true;
            foreach ($model as $value) {
                array_push($data, [
                    'id' => $value->id,
                    'journal_no' => $value->journal_no,
                    'journal_date' => date('d-m-Y', strtotime($value->trans_date)),
                    'journal_time' => date('H:i:s', strtotime($value->trans_date)),
                    'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                    'product_name' => \backend\models\Product::findName($value->product_id),
                    'qty' => $value->qty,
                    'user' => \backend\models\User::findName($value->created_by),
                ]);
            }
        }
        //}

        return ['status' => $status, 'data' => $data];
    }

    public function updatestock($product_id, $qty, $company_id, $branch_id)
    {
        if ($product_id != null && $qty != null) {
            $warehouse_id = $this->findReprocesswarehouse($company_id, $branch_id);
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $model_trans::getIssueReprocessCar($company_id, $branch_id);
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $warehouse_id;
            $model_trans->stock_type = 2; // 1 in 2 out
            $model_trans->activity_type_id = 21; // 6 issue car
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            if ($model_trans->save(false)) {

//                $model = \common\models\StockSum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id])->one();
//                if ($model) {
//                    $model->qty = 0;
//                    $model->save(false);
//                }
                $model = \backend\models\Stocksum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id])->one();
                if ($model) {
                    $model->qty = (float)$model->qty - (float)$qty;
                    $model->save(false);
                }
            }

        }
    }

    public function findReprocesswarehouse($company_id, $branch_id)
    {
        $id = 0;
        if ($company_id != null && $branch_id != null) {
            $model = \backend\models\Warehouse::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_reprocess' => 1])->one();
            if ($model) {
                $id = $model->id;
            }
        }
        return $id;
    }

    public function actionFindprodrec()
    {
        $prodrec_no = null;
        $company_id = null;
        $branch_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $prodrec_no = $req_data['prodrec_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];


        $data = [];
        $status = false;


        if ($prodrec_no != null) {
            $model = \common\models\StockTrans::find()->where(['journal_no' => $prodrec_no, 'company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 15])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'rec_no' => $value->journal_no,
                        'rec_date' => date('d/m/Y', strtotime($value->trans_date)),
                        'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                        'product_id' => $value->product_id,
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'qty' => $value->qty,
                        'status' => $value->status = 3 ? 'Cancel' : '',
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionFindproductionrec()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $journal_no = $req_data['journal_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;

            $model = \common\models\StockTrans::find()->where(['journal_no' => $journal_no])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 15])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // if ($value->qty == null || $value->qty <= 0) continue;
                    array_push($data, [
                        'id' => $value->id,
                        'warehouse_id' => $value->warehouse_id,
                        'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'qty' => $this->findIssueconfirm($value->id, $value->product_id)
                        //'qty' => $value->qty
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }


    public function actionFindproductionrec2()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $journal_no = $req_data['journal_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;

            $model = \common\models\StockTrans::find()->where(['journal_no' => $journal_no])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 15])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // if ($value->qty == null || $value->qty <= 0) continue;
                    $issue_reprocess_qty = $this->findIssueReservReprocessqty($value->id, $company_id, $branch_id);
                    array_push($data, [
                        'id' => $value->id,
                        'warehouse_id' => $value->warehouse_id,
                        'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'rec_qty' => $value->qty,
                        'confirm_qty' => $this->findIssueconfirm($value->id, $value->product_id),
                        'reserv_qty' => $this->findIssueReserv($value->id, $value->product_id),
                        'scrap_qty' => $this->findScrap($value->id, $value->product_id),
                        'status' => $value->status == 3 ? 'Cancel' : '',
                        'counted' => $this->checkCounted($journal_no, $company_id, $branch_id),
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionFindproductionrec3()
    {
        $customer_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $journal_no = $req_data['journal_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        // $user_id = $req_data['user_ids'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;

            $model = \common\models\StockTrans::find()->where(['journal_no' => $journal_no])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // if ($value->qty == null || $value->qty <= 0) continue;
                    $issue_reprocess_qty = $this->findIssueReservReprocessqty($value->id, $company_id, $branch_id);
                    $transform_reserv_qty = $this->findTransformreserv($company_id, $branch_id, $value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        'warehouse_id' => $value->warehouse_id,
                        'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'rec_qty' => $value->qty,
                        'confirm_qty' => $this->findIssueconfirm($value->id, $value->product_id),
                        'reserv_qty' => $this->findIssueReserv($value->id, $value->product_id), // + $transform_reserv_qty,
                        'scrap_qty' => $this->findScrap($value->id, $value->product_id),
                        'status' => $value->status == 3 ? 'Cancel' : '',
                        'counted' => $this->checkCounted($journal_no, $company_id, $branch_id),
                        'issue_reprocess_qty' => $issue_reprocess_qty,
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionFindproductionrec4()
    {
        $customer_id = 0;
        $user_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $journal_no = $req_data['journal_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null) {
            $trans_date = date('Y/m/d');
            $t_date = null;

            $model = \common\models\StockTrans::find()->where(['journal_no' => $journal_no])->andFilterWhere(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // if ($value->qty == null || $value->qty <= 0) continue;
                    $issue_reprocess_qty = $this->findIssueReservReprocessqty($value->id, $company_id, $branch_id);
                    array_push($data, [
                        'id' => $value->id,
                        'warehouse_id' => $value->warehouse_id,
                        'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'rec_qty' => $value->qty,
                        'confirm_qty' => $this->findIssueconfirm($value->id, $value->product_id),
                        'reserv_qty' => $this->findIssueReserv($value->id, $value->product_id),
                        'scrap_qty' => $this->findScrap($value->id, $value->product_id),
                        'status' => $value->status == 3 ? 'Cancel' : '',
                        'counted' => $this->checkCounted2($journal_no, $company_id, $branch_id, $user_id),
                        'issue_reprocess_qty' => $issue_reprocess_qty,
                    ]);
                }
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function checkCounted($journal_no, $company_id, $branch_id)
    {
        $res = 0;
        $model = \common\models\DailyCountStock::find()->where(['journal_no' => $journal_no, 'company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => date('Y-m-d')])->one();
        if ($model) {
            $production_bill_id = \backend\models\Stockjournal::findProdrecId($journal_no, $company_id, $branch_id);
            $check_issue_temp = \common\models\IssueStockTemp::find()->where(['prodrec_id' => $production_bill_id])->one();
            if ($check_issue_temp) {
                $res = 1;
            } else {
                $res = 0;
            }

        }
        return $res;
    }

    public function checkCounted2($journal_no, $company_id, $branch_id, $user_id)
    {
        $res = 0;
        $model = \common\models\DailyCountStock::find()->where(['journal_no' => $journal_no, 'company_id' => $company_id, 'branch_id' => $branch_id, 'user_id' => $user_id, 'date(trans_date)' => date('Y-m-d')])->one();
        if ($model) {
//            $production_bill_id = \backend\models\Stockjournal::findProdrecId($journal_no, $company_id, $branch_id);
//            $check_issue_temp = \common\models\IssueStockTemp::find()->where(['prodrec_id' => $production_bill_id])->one();
//            if ($check_issue_temp) {
//                $res = 1;
//            } else {
//                $res = 0;
//            }
            $res = 1;

        }
        return $res;
    }

    public function findIssueconfirm($prod_rec_id, $product_id)
    {
        $qty = 0;
        if ($product_id != null && $prod_rec_id != null) {
            $model = \common\models\IssueStockTemp::find()->where(['product_id' => $product_id, 'prodrec_id' => $prod_rec_id])->andFilterWhere(['status' => 100])->sum('qty');
            if ($model) {
                $qty = $model;
            }
        }
        return $qty;
    }

    public function findIssueReserv($prod_rec_id, $product_id)
    {
        $qty = 0;
        if ($product_id != null && $prod_rec_id != null) {
            $model = \common\models\IssueStockTemp::find()->where(['product_id' => $product_id, 'prodrec_id' => $prod_rec_id])->andFilterWhere(['status' => 1])->sum('qty');
            if ($model) {
                $qty = $model;
            }
        }
        return $qty;
    }

    public function findScrap($prod_rec_id, $product_id)
    {
        $qty = 0;
        if ($product_id != null && $prod_rec_id != null) {
            $model = \common\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap.id = scrap_line.scrap_id')->where(['scrap_line.product_id' => $product_id, 'scrap.prodrec_id' => $prod_rec_id])->sum('scrap_line.qty');
            if ($model) {
                $qty = $model;
            }
        }
        return $qty;
    }

    public function findTransformreserv($company_id, $branch_id, $product_id)
    {
        $qty = 0;
        if ($product_id != null && $company_id != null && $branch_id != null) {
            $model = \common\models\TransformReserv::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1, 'date(trans_date)' => date('Y-m-d')])->sum('qty');
            if ($model) {
                $qty = $model;
            }
        }
        return $qty;
    }

    public function actionAddscrap()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $production_rec_id = 0;
        $user_id = 0;
        $order_id = 0;
        $order_no = null;
        $qty = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $production_rec_id = $req_data['prod_rec_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $order_no = $req_data['order_no'];
        // $production_type = $req_data['production_type'];

        $data = [];
        $status = false;
        $journal_no = '';

        if ($order_no != null) {
            $order_id = \backend\models\Orders::findId($order_no);
        }

        if ($product_id && $production_rec_id && $qty && $user_id && $company_id && $branch_id) {
            $model_journal = new \backend\models\Scrap();
            $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
            $model_journal->trans_date = date('Y-m-d H:i:s');
            $model_journal->prodrec_id = $production_rec_id;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->created_by = $user_id;
            $model_journal->scrap_type_id = 1;
            $model_journal->order_id = $order_id;
            $model_journal->status = 1;
            if ($model_journal->save(false)) {
                $model = new \common\models\ScrapLine();
                $model->scrap_id = $model_journal->id;
                $model->product_id = $product_id;
                $model->qty = $qty;
                $model->note = '';
                $model->status = 1;
                if ($model->save(false)) {
                    $status = 1;
                }
            }

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }


    public function actionProdreclist()
    {
        $company_id = 0;
        $branch_id = 0;
        $trans_date = null;
        $journal_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $trans_date = $req_data['trans_date'];
        $journal_id = $req_data['journal_id'];
//        $product_code = $req_data['item_code'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $find_date = date('Y-m-d');
            $t_date = null;
            $exp_order_date = explode(' ', $trans_date);
            if ($exp_order_date != null) {
                if (count($exp_order_date) > 1) {
                    $x_date = explode('-', $exp_order_date[0]);
                    if (count($x_date) > 1) {
                        $t_date = $x_date[0] . "/" . $x_date[1] . "/" . $x_date[2];
                    }
                }
            }
            if ($t_date != null) {
                $find_date = $t_date;
            }
            $model = null;
            if ($journal_id != null) {
                $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => $find_date])->andFilterWhere(['=', 'product_code', $journal_id])->orderBy(['trans_date' => SORT_DESC])->all();

            }
//            if ($journal_id != null) {
//                if($journal_id != null){
//                    $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => $find_date])->andFilterWhere(['LIKE', 'journal_no', $journal_id,'product_code'=>$product_code])->orderBy(['trans_date' => SORT_DESC])->all();
//                }else{
//                    $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => $find_date])->andFilterWhere(['LIKE', 'journal_no', $journal_id])->orderBy(['trans_date' => SORT_DESC])->all();
//                }
//
//            } else {
//                if($journal_id != null){
//                    $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => $find_date,'product_code'=>$product_code])->orderBy(['trans_date' => SORT_DESC])->all();
//                }else{
//                    $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => $find_date])->orderBy(['trans_date' => SORT_DESC])->all();
//                }
//
//            }

            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    $issue_reprocess_qty = $this->findIssueReservReprocessqty($value->id, $company_id, $branch_id);
                    array_push($data, [
                        'id' => $value->id,
                        'company_id' => $value->company_id,
                        'branch_id' => $value->branch_id,
                        'product_code' => $value->product_code,
                        'user_name' => $value->username,
                        'warehouse_name' => $value->warehouse_code,
                        'qty' => $value->qty,
                        'production_type' => $value->production_type == null ? 0 : $value->production_type,
                        'journal_no' => $value->journal_no,
                        'trans_date' => $value->trans_date,
                        'status' => $value->status == 3 ? 'Cancel' : '',
                        'issue_qty' => $this->findIssuestocktemp($value->journal_no, $company_id, $branch_id),
                        'issue_reprocess_qty' => $issue_reprocess_qty,
                        'loc_name' => $this->findLocName($value->id),
                        'scrap_qty' => $this->findScrap($value->id, $value->product_id),
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function findLocName($stock_trans_id)
    {
        $loc_name = '';
        if ($stock_trans_id) {
            $model = \backend\models\Stocktrans::find()->where(['id' => $stock_trans_id])->one();
            if ($model) {
                $model_loc = \common\models\MachineDetail::find()->where(['id' => $model->production_loc_id])->one();
                if ($model_loc) {
                    $loc_name = $model_loc->loc_name;
                }
            }
        }
        return $loc_name;
    }

    public function findIssuestocktemp($journal_no, $company_id, $branch_id)
    {
        $qty = 0;
        $prod_rec_id = \backend\models\Stocktrans::findProdrecId($journal_no, $company_id, $branch_id);
        if ($prod_rec_id) {
            $model_qty = \common\models\IssueStockTemp::find()->where(['prodrec_id' => $prod_rec_id])->sum('qty');
            if ($model_qty) {
                $qty = $model_qty;
            }
        }
        return $qty;
    }

    public function actionProdrecfind()
    {
        $company_id = 0;
        $branch_id = 0;
        $journal_no = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $journal_no = $req_data['journal_no'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {

            $model = \common\models\QueryProdrecTrans::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'journal_no' => $journal_no])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'company_id' => $value->company_id,
                        'branch_id' => $value->branch_id,
                        'product_code' => $value->product_code,
                        'user_name' => $value->username,
                        'warehouse_name' => $value->warehouse_code,
                        'qty' => $value->qty,
                        'production_type' => $value->production_type,
                        'journal_no' => $value->journal_no,
                        'trans_date' => $value->trans_date,
                        'status' => $value->status == 3 ? 'Cancel' : '',
                        'loc_name' => $this->findLocName($value->id),
                        'scrap_qty' => $this->findScrap($value->id, $value->product_id),
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionOnhandlist()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            if ($default_warehouse) {
                $model = \common\models\StockSum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'warehouse_id' => $default_warehouse])->andFilterWhere(['>', 'qty', 0])->all();
                // $model = \common\models\QueryCustomerPrice::find()->all();
                if ($model) {
                    $status = true;
                    foreach ($model as $value) {
                        array_push($data, [
                            'id' => $value->id,
                            'company_id' => $value->company_id,
                            'branch_id' => $value->branch_id,
                            'warehouse_id' => $value->warehouse_id,
                            'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                            'product_id' => $value->product_id,
                            'product_name' => \backend\models\Product::findName($value->product_id),
                            'onhand_qty' => $value->qty,
                        ]);
                    }
                }
            }

        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionOnhandselect()
    {
        $selected_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $selected_id = $req_data['id'];
        $data = [];
        $status = false;

        if ($selected_id) {
            $model = \common\models\StockSum::find()->where(['id' => $selected_id])->one();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                array_push($data, [
                    'id' => $model->id,
                    'company_id' => $model->company_id,
                    'branch_id' => $model->branch_id,
                    'warehouse_id' => $model->warehouse_id,
                    'warehouse_name' => \backend\models\Warehouse::findName($model->warehouse_id),
                    'product_id' => $model->product_id,
                    'product_code' => \backend\models\Product::findCode($model->product_id),
                    'product_name' => \backend\models\Product::findName($model->product_id),
                    'onhand_qty' => $model->qty,
                ]);
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionCancelprodrec()
    {
        $prodrec_no = null;
        $company_id = null;
        $branch_id = null;
        $user_id = null;
        $product_id = null;
        $qty = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $prodrec_no = $req_data['prodrec_no'];
        $product_id = $req_data['product_id'];
        $qty = $req_data['qty'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $user_id = $req_data['user_id'];


        $data = [];
        $status = false;


        if ($prodrec_no != null && $company_id != null && $branch_id != null && $product_id != null) {

//            if($this->checkProdrecIssue($prodrec_no,$company_id,$branch_id)){
//
//            }else{
            $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            // cancel production receive
            $model_journal = new \backend\models\Stockjournal();
            $model_journal->journal_no = $model_journal->getReturnProdLastNo($company_id, $branch_id);
            //  $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
            $model_journal->trans_date = date('Y-m-d H:i:s');

            $journal_no = $model_journal->journal_no;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = 28; // cancel production
            if ($model_journal->save(false)) {
                $model = new \backend\models\Stocktrans();
                $model->journal_no = $model_journal->journal_no;
                $model->journal_id = $model_journal->id;
                $model->trans_date = date('Y-m-d H:i:s');
                $model->product_id = \backend\models\Product::findProductId($product_id, $company_id, $branch_id);
                $model->qty = $qty;
                $model->warehouse_id = $main_warehouse;//$warehouse_id;
                $model->stock_type = 2; // 2 out
                $model->activity_type_id = 28; // 28 prod rec cancel
                $model->production_type = 28;
                $model->trans_ref_id = $prodrec_no;
                $model->company_id = $company_id;
                $model->branch_id = $branch_id;
                $model->created_by = $user_id;
                if ($model->save(false)) {
                    $status = 1;
                    $this->updateCancelProdrecSummary($product_id, $main_warehouse, $qty);
                    if (\backend\models\Stocktrans::updateAll(['status' => 3], ['id' => $prodrec_no])) {
                        $model_product_loc_id = \backend\models\Stocktrans::find()->where(['id' => $prodrec_no])->one();
                        if ($model_product_loc_id) {
                            $replace_date = date('Y-m-d H:i:s', strtotime('-36 hours', strtotime(date('Y-m-d H:i:s'))));
                            \common\models\ProductionStatus::updateAll(['color_status' => 'G', 'start_date' => $replace_date, 'end_date' => NULL], ['loc_id' => $model_product_loc_id->production_loc_id]); // return production status to green color
                        }
                    }
                }
            }
            array_push($data, ['journal_no' => $journal_no]);
            // }

        }

        return ['status' => $status, 'data' => $data];
    }

    public function checkProdrecIssue($prodrec_no, $company_id, $branch_id)
    {
        $res = 0;
        if ($prodrec_no != null && $company_id != null && $branch_id != null) {
            $prodrec_id = \backend\models\Stocktrans::findProdrecId($prodrec_no, $company_id, $branch_id);
            $res = \common\models\IssueStockTemp::find()->where(['prodrec_id' => $prodrec_id, 'statue' => 100, 'company_id' => $company_id, 'branch_id' => $branch_id])->count();
        }
        return $res;
    }

    public function updateCancelProdrecSummary($product_id, $wh_id, $qty)
    {
        if ($wh_id != null && $product_id != null && $qty > 0) {
            $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
            if ($model) {
                $model->qty = (float)$model->qty - (float)$qty;
                $model->save(false);
            } else {
                $model_new = new \backend\models\Stocksum();
                $model_new->warehouse_id = $wh_id;
                $model_new->product_id = $product_id;
                $model_new->qty = (float)$qty;
                $model_new->save(false);
            }
        }
    }

    public function actionReprocessmainlist()
    {
        $company_id = null;
        $branch_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;
        $default_warehouse = 0;
        if ($company_id != null) {
            $default_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
            $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -7 day"));
            $model = \common\models\StockTrans::find()->select(['id', 'journal_no', 'trans_date', 'product_id', 'qty'])->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'activity_type_id' => 27, 'status' => 0])->andFilterWhere(['>=', 'date(trans_date)', $pre_date])->orderBy(['id' => SORT_DESC])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        'journal_no' => $value->journal_no,
                        'trans_date' => $value->trans_date,
                        'product_id' => $value->product_id,
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'warehouse_name' => $default_warehouse,
                        'qty' => $value->qty,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddrealcount()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $warehouse_id = 0;
        $user_id = 0;
        $qty = 0;
        $rep_no = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $rep_no = $req_data['journal_no'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = 0;
        $journal_no = '';


        if ($product_id && $qty) {

            \common\models\DailyCountStock::deleteAll(['journal_no' => $rep_no, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 0]); // clear before save

            $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);

            $model_journal = new \common\models\DailyCountStock();

            $model_journal->trans_date = date('Y-m-d H:i:s');
            $model_journal->journal_no = $rep_no;
            $model_journal->product_id = $product_id;
            $model_journal->qty = $qty;
            $model_journal->warehouse_id = $main_warehouse;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->status = 0;
            $model_journal->user_id = $user_id;
            if ($model_journal->save(false)) {
                $status = 1;
                array_push($data, ['id' => $model_journal->id]);
            }

        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionReservlist()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id) {
            $pre_date = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
            $model = \common\models\TransformReserv::find()->select(['id', 'product_id', 'trans_date', 'qty'])->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 0])->andFilterWhere(['>=', 'date(trans_date)', $pre_date])->orderBy(['id' => SORT_DESC])->all();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    $remain_qty = 0;
                    $remain_qty = ($value->qty - $this->findIssueReservqty($value->id));
                    if ($remain_qty <= 0) continue;
                    array_push($data, [
                        'id' => $value->id,
                        'trans_date' => date('Y-m-d H:i:s', strtotime($value->trans_date)),
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'reserv_qty' => $value->qty,
                        'remain_qty' => $remain_qty,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionReservselected()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $id = $req_data['id'];

        $data = [];
        $status = false;

        if ($company_id) {
            $model = \common\models\TransformReserv::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 0, 'id' => $id])->one();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                array_push($data, [
                    'id' => $model->id,
                    'trans_date' => date('Y-m-d H:i:s', strtotime($model->trans_date)),
                    'product_id' => $model->product_id,
                    'product_code' => \backend\models\Product::findCode($model->product_id),
                    'product_name' => \backend\models\Product::findName($model->product_id),
                    'reserv_qty' => $model->qty,
                    'remain_qty' => ($model->qty - $this->findIssueReservqty($model->id)),
                ]);

            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function findIssueReservqty($reserv_id)
    {
        $res = 0;
        $model = \backend\models\Stocktrans::find()->where(['trans_ref_id' => $reserv_id, 'activity_type_id' => 20])->sum('qty');
        if ($model) {
            $res = $model;
        }
        return $res;
    }

    public function findIssueReservReprocessqty($prodrec_id, $company_id, $branch_id)
    {
        $res = 0;
        $model = \backend\models\Journalissue::find()->where(['trans_ref_id' => $prodrec_id, 'reason_id' => 4, 'company_id' => $company_id, 'branch_id' => $branch_id])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_line_qty = \backend\models\Journalissueline::find()->where(['issue_id' => $value->id])->sum('qty');
                if ($model_line_qty) {
                    $res += $model_line_qty;
                }
            }

        }
        return $res;
    }

    public function actionReservconfirm()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $id = $req_data['id'];
        $user_id = $req_data['user_id'];
        $qty = $req_data['qty'];
        $journal_no = $req_data['journal_no'];

        $data = [];
        $status = false;

        if ($company_id) {
            $model = \common\models\TransformReserv::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'id' => $id])->one();
            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                $main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
                $prodrec_id = \backend\models\Stocktrans::findProdrecId($journal_no, $company_id, $branch_id);

                $model_issue = new \backend\models\Journalissue();
                $model_issue->journal_no = $model_issue::getLastNo(date('Y-m-d'), $company_id, $branch_id);
                $model_issue->trans_date = date('Y-m-d H:i:s');
                $model_issue->status = 1;
                $model_issue->company_id = $company_id;
                $model_issue->branch_id = $branch_id;
                $model_issue->reason_id = 4; // เบิกแปรสภาพ
                $model_issue->created_by = $user_id;
                $model_issue->trans_ref_id = $prodrec_id; // prodrec_id
                if ($model_issue->save(false)) {
                    $model_line = new \backend\models\Journalissueline();
                    $model_line->issue_id = $model_issue->id;
                    $model_line->product_id = $model->product_id;
                    $model_line->qty = $qty;
                    $model_line->avl_qty = 0;
                    $model_line->sale_price = 0;
                    $model_line->status = 1;
                    if ($model_line->save(false)) {

                        if ($model->product_id != null && $model->qty > 0) {
                            $model_trans = new \backend\models\Stocktrans();
                            $model_trans->journal_no = $model_issue->journal_no;
                            $model_trans->trans_date = date('Y-m-d H:i:s');
                            $model_trans->product_id = $model->product_id;
                            $model_trans->qty = $qty;
                            $model_trans->warehouse_id = $main_warehouse;
                            $model_trans->stock_type = 2; // 1 in 2 out
                            $model_trans->activity_type_id = 20; // 20 issue reprocess
                            $model_trans->company_id = $company_id;
                            $model_trans->branch_id = $branch_id;
                            $model_trans->trans_ref_id = $model->id; // id จอง
                            $model_trans->prodrec_id = $prodrec_id; // id prodrec
                            $model_trans->created_by = $user_id;
                            // $model_trans->status = 1; // issue all is empty stock
                            if ($model_trans->save(false)) {
                                $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $main_warehouse, 'product_id' => $model->product_id])->one();
                                if ($model_sum) {
                                    $model_sum->qty = (float)$model_sum->qty - (float)$qty;
                                    if ($model_sum->save(false)) {
                                        // check issue tranform balance
                                        $chk_issue_reserv_balance = \backend\models\Stocktrans::find()->where(['product_id' => $model->product_id, 'trans_ref_id' => $model->id])->sum('qty');
                                        if ($chk_issue_reserv_balance >= $model->qty) {
                                            $model->status = 1;
                                            $model->save(false);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionMachinelist()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_code = 0;
        $find_loc_name = '';
        $user_id = null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_code = $req_data['product_code'];
        $find_loc_name = $req_data['find_loc_name'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $loop_qty = 0;
            $mac_id = 0;
            if ($product_code == 'T1') {
                $mac_id = 1;
            } else if ($product_code == 'T2') {
                $mac_id = 2;
            } else if ($product_code == 'T3') {
                $mac_id = 3;
            }
            $model_find_max = null;
            if ($mac_id > 0) {
                $model_find_max = \common\models\MachineDetail::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'machine_id' => $mac_id])->max('loc_name');
            } else {
                $model_find_max = \common\models\MachineDetail::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->max('loc_name');
            }

            if ($model_find_max != null) {
                $loop_qty = substr($model_find_max, 1);
            }
            $model = null;
            if ($mac_id > 0) {
                if ($find_loc_name != '') {
                    $model = \common\models\MachineDetail::find()->where(['machine_id' => $mac_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['LIKE', 'loc_name', $find_loc_name])->all();
                } else {
                    $model = \common\models\MachineDetail::find()->where(['machine_id' => $mac_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->all();
                }

            } else {
                if ($find_loc_name != '') {
                    $model = \common\models\MachineDetail::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['LIKE', 'loc_name', $find_loc_name])->all();
                } else {
                    $model = \common\models\MachineDetail::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
                }

            }

//            $t = 0;
//            if($company_id ==1 && $branch_id ==1){
//                $t = $mac_id == 1 ? 15 : 16;
//            }else  if($company_id ==1 && $branch_id ==2){
//                $t = 75;
//            }

            // omnoi
            $t = 0; // nky
          //  $t = 75; // bkt
            if ($company_id == 1 && $branch_id == 1) {
              //  $t = $mac_id == 1 ? 15 : 16; // nky
                $t = $mac_id == 1 ? 1 : 2; // omnoi
                if ($mac_id == 3) {
                    $t = 3;
                }
            } else if ($company_id == 1 && $branch_id == 2) {
                $t = 75;
            }

            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'warehouse_id' => \backend\models\Machine::findWarehouseId($value->machine_id),
                        'warehouse_code' => \backend\models\Machine::findWarehouseCode($value->machine_id),
                        'warehouse_name' => \backend\models\Machine::findWarehouseName($value->machine_id),
                        'machine_id' => $value->machine_id,
                        'machine_name' => \backend\models\Machine::findName($value->machine_id),
                        'product_id' => $t,
                        'product_name' => \backend\models\Product::findName($t),
                        'loc_id' => $value->id,
                        'loc_name' => $value->loc_name,
                        'loc_per_qty' => $value->loc_qty,
                        'color_status' => $this->getProductionStatus($value->id, $t, $company_id, $branch_id, $user_id),
                        'loop_qty' => $loop_qty,
                    ]);
                }

            }

        }

        return ['status' => $status, 'data' => $data];
    }

    public function getProductionStatus($loc_id, $product_id, $company_id, $branch_id, $user_id)
    {
        $update_color = "N";

        if ($loc_id != null) {

            // return $product_id;
            $model = \common\models\ProductionStatus::find()->where(['loc_id' => $loc_id, 'product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
               // $update_color = "NX";
                $cal_hour = 0;
                $color_status = "N";
                if ($model->end_date != null) {
                    //$date2 = "2022-07-09 02:00:00";
                    $timestamp1 = strtotime($model->start_date);
                    $timestamp2 = strtotime(date('Y-m-d H:i:s'));
                    // $timestamp2 = strtotime($date2);
                    $cal_hour = abs($timestamp2 - $timestamp1) / (60 * 60);
                }


                if ($cal_hour <= 24) {
                    $color_status = "R";
                } else if ($cal_hour >= 24 && $cal_hour <= 36) {
                    $color_status = "Y";
                } else if ($cal_hour >= 36) {
                    $color_status = "G";
                }

                $update_color = $color_status;

                $model->color_status = $color_status;
                $model->end_date = date('Y-m-d H:i:s');
                $model->total_hour = $cal_hour;
                $model->updated_by = $user_id;
                $model->save(false);

            }
        }
        return $update_color;
    }

    public function actionCreateproductiontrans()
    {
        $company_id = 0;
        $branch_id = 0;
        $machine_id = 0;
        $loc_id = 0;
        $product_id = 0;
        $qty = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $machine_id = $req_data['machine_id'];
        $loc_id = $req_data['loc_id'];
        $product_id = $req_data['product_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $model = new \common\models\ProductionTrans();
            $model->product_id = $product_id;
            $model->start_date = date("Y-m-d H:i:s");
            $model->machine_detail_id = $loc_id;
            $model->status = 1;
            $model->emp_id = $user_id;
            if ($model->save(false)) {
                $status = true;
                array_push($data, ['result' => ['successfully']]);
            }
        }
        return ['status' => $status, 'data' => $data];
    }

    public function actionCloseproductiontrans()
    {
        $company_id = 0;
        $branch_id = 0;
        $machine_id = 0;
        $loc_id = 0;
        $product_id = 0;
        $qty = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $machine_id = $req_data['machine_id'];
        $loc_id = $req_data['loc_id'];
        $product_id = $req_data['product_id'];
        $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $model = new \common\models\ProductionTrans();
            $model->product_id = $product_id;
            $model->start_date = date("Y-m-d H:i:s");
            $model->machine_detail_id = $loc_id;
            $model->status = 1;
            $model->emp_id = $user_id;
            if ($model->save(false)) {
                $status = true;
                array_push($data, ['result' => ['successfully']]);
            }
        }
        return ['status' => $status, 'data' => $data];
    }


    //// production section

    public function actionProductionlist()
    {
        $company_id = 0;
        $branch_id = 0;
        $prod_code = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $prod_code = $req_data['product_code'];

        $data = [];
        $status = false;
        $model = null;

        if ($company_id) {
            if ($prod_code != null) {
                $prod_id = \backend\models\Product::findProductId($prod_code, $company_id, $branch_id);
                $model = \common\models\Production::find()->select(['id', 'prod_no', 'prod_date', 'product_id', 'qty', 'status'])->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $prod_id, 'status' => 1])->all();
            } else {
                $model = \common\models\Production::find()->select(['id', 'prod_no', 'prod_date', 'product_id', 'qty', 'status'])->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => [1, 2], 'status' => 1])->all();
            }

            // $model = \common\models\QueryCustomerPrice::find()->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    // $product_info = \backend\models\Product::findInfo($value->product_id);
                    array_push($data, [
                        'id' => $value->id,
                        'prod_no' => $value->prod_no,
                        'prod_date' => $value->prod_date,
                        'product_id' => $value->product_id,
                        'product_code' => \backend\models\Product::findCode($value->product_id),
                        'product_name' => \backend\models\Product::findName($value->product_id),
                        'qty' => $value->qty,
                        'good_qty' => 0,
                        'remain_qty' => ($value->qty) - 0,
                        'status' => $value->status,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionUpdateprodstatus()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = null;
        $loc_id = null;
        $loc_name = null;
        $user_id = null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $product_id = $req_data['product_id'];
        $loc_id = $req_data['loc_id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if ($loc_id != null) {
            $model = \common\models\ProductionStatus::find()->where(['loc_id' => $loc_id, 'product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
                $cal_hour = 0;
                $color_status = "N";
                if ($model->end_date != null) {
                    $timestamp1 = strtotime($model->start_date);
                    $timestamp2 = strtotime(date('Y-m-d H:i:s'));
                    $cal_hour = abs($timestamp2 - $timestamp1);
                }

                if ($cal_hour <= 24) {
                    $color_status = "R";
                } else if ($cal_hour >= 24 && $cal_hour <= 36) {
                    $color_status = "Y";
                } else if ($cal_hour >= 36) {
                    $color_status = "G";
                }

                $model->color_status = $color_status;
                $model->end_date = date('Y-m-d H:i:s');
                $model->total_hour = $cal_hour;
                $model->updated_by = $user_id;
                $model->save(false);

            }
        }


    }

    public function actionAddprodrecmobile()
    {
        $company_id = 1;
        $branch_id = 1;
        $product_id = 0;
        $warehouse_id = 1;
        $user_id = 0;
        $qty = 0;
        $datalist = null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
//        $company_id = $req_data['company_id'];
//        $branch_id = $req_data['branch_id'];
        //   $product_id = $req_data['product_id'];
        //    $warehouse_id = $req_data['warehouse_id'];
        //   $qty = $req_data['qty'];
        $user_id = $req_data['user_id'];
        $production_type = 1; // $req_data['production_type'];
        $datalist = $req_data['data'];

        $data = [];
        $status = false;
        $journal_no = '';

        $act_id = 15;
        if ($production_type == 1) {
            $act_id = 15; //รับผลิต
        } else if ($production_type == 2) {
            $act_id = 26; // reprocess รถ
        } else if ($production_type == 3) {
            $act_id = 27; // reprocess
        } else if ($production_type == 5) {
            $act_id = 15; // รับผลิต + รับโอนจากต่างสาขา
        }

        if (count($datalist) > 0) {
            for ($i = 0; $i <= count($datalist) - 1; $i++) {
                if ($datalist[$i]['qty'] <= 0) continue;
                $product_id = $datalist[$i]['product_id'];
                $qty = $datalist[$i]['qty'];


                if ($product_id && $warehouse_id && $qty) {

                    //$main_warehouse = \backend\models\Warehouse::findPrimary($company_id, $branch_id);
                    $warehouse_id = 1; // หนองขาหยั่ง
                    $model_journal = new \backend\models\Stockjournal();
                    if ($production_type == 1) {
                        sleep(1);
                        $model_journal->journal_no = $model_journal->getLastNo($company_id, $branch_id);
                    } else if ($production_type == 5) {
                        sleep(1);
                        $model_journal->journal_no = $model_journal->getLastNoReceiveTransfer($company_id, $branch_id);
                    } else {
                        sleep(1);
                        //       $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                        $model_journal->journal_no = $model_journal->getLastNoCarreprocess($company_id, $branch_id);
                    }


                    $model_journal->trans_date = date('Y-m-d H:i:s');


                    $model_journal->company_id = $company_id;
                    $model_journal->branch_id = $branch_id;
                    $model_journal->production_type = $act_id; // $production_type;
                    if ($model_journal->save(false)) {
                        $journal_no = $model_journal->journal_no;
                        $model = new \backend\models\Stocktrans();
                        $model->journal_no = $model_journal->journal_no;
                        $model->journal_id = $model_journal->id;
                        $model->trans_date = date('Y-m-d H:i:s');
                        $model->product_id = $product_id;
                        $model->qty = $qty;
                        $model->warehouse_id = $warehouse_id;//$warehouse_id;
                        $model->stock_type = 1;
                        $model->activity_type_id = $act_id; // 15 prod rec
                        $model->production_type = $production_type;
                        $model->company_id = $company_id;
                        $model->branch_id = $branch_id;
                        $model->created_by = $user_id;
                        $model->lot_no =  $datalist[$i]['taypay_no']; // for mobile prodrec specific taypay_no
                        $model->status = 0;
                        if ($model->save(false)) {
                            $status = 1;
                            $this->updateSummary($product_id, $warehouse_id, $qty);
                        }
                    }
//            $model = \backend\models\Stockjournal::find()->where(['id' => $model_journal->id])->one();
//            $model_line = \backend\models\Stocktrans::find()->where(['journal_id' => $model_journal->id])->all();

                    array_push($data, ['journal_no' => $journal_no]);
                    //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
                }
            }
        }


        return ['status' => $status, 'data' => $data];
    }

    public function actionListmobileall()
    {
        $company_id = 1;
        $branch_id = 1;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        $model = \common\models\StockTrans::find()->where(['activity_type_id'=>15,'status'=>0,'company_id' => $company_id, 'branch_id' => $branch_id,'date(trans_date)'=>date('Y-m-d')])->orderBy(['id'=>SORT_DESC])->all();
        // $model = \common\models\QueryCustomerPrice::find()->all();
        if ($model) {
            $status = true;
            foreach ($model as $value) {
                // $product_info = \backend\models\Product::findInfo($value->product_id);
                array_push($data, [
                    'id' => $value->id,
                    'journal_no' => $value->journal_no,
                    'trans_date' => $value->trans_date,
                    'product_name'=> \backend\models\Product::findName($value->product_id),
                    'qty' =>$value->qty,
                    'created_name' => \backend\models\User::findName($value->created_by),
                ]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionProducttransferlist()
    {
        $company_id = 1;
        $branch_id = 1;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $user_id = $req_data['user_id'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        $model = \common\models\StockTrans::find()->where(['activity_type_id'=>15,'production_type'=>5,'company_id' => $company_id, 'branch_id' => $branch_id,'date(trans_date)'=>date('Y-m-d')])->andFilterWhere(['OR',['!=','status',500],['is','status', new \yii\db\Expression('null')]])->orderBy(['id'=>SORT_DESC])->all();
        // $model = \common\models\QueryCustomerPrice::find()->all();
        if ($model) {
            $status = true;
            foreach ($model as $value) {
                // $product_info = \backend\models\Product::findInfo($value->product_id);
                array_push($data, [
                    'id' => $value->id,
                    'journal_no' => $value->journal_no,
                    'trans_date' => $value->trans_date,
                    'product_id'=> $value->product_id,
                    'product_name'=> \backend\models\Product::findName($value->product_id),
                    'qty' =>$value->qty,
                    'created_name' => \backend\models\User::findName($value->created_by),
                    'warehouse_name' => \backend\models\Warehouse::findName($value->warehouse_id),
                ]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionDeleprodrecline()
    {
        $id = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $id = $req_data['id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if($id && $user_id){
            $model = \common\models\StockTrans::find()->where(['id'=>$id])->one();
            if ($model) {
                $status = true;
                $main_warehouse = 1; //\backend\models\Warehouse::findPrimary($company_id, $branch_id);

                // cancel production receive
                $model_journal = new \backend\models\Stockjournal();
                $model_journal->journal_no = $model_journal->getReturnProdLastNo($model->company_id, $model->branch_id);
                //  $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->trans_date = date('Y-m-d H:i:s');

                $journal_no = $model_journal->journal_no;
                $model_journal->company_id = $model->company_id;
                $model_journal->branch_id = $model->branch_id;
                $model_journal->production_type = 28; // cancel production
                if ($model_journal->save(false)) {
                    $modelx = new \backend\models\Stocktrans();
                    $modelx->journal_no = $model_journal->journal_no;
                    $modelx->journal_id = $model_journal->id;
                    $modelx->trans_date = date('Y-m-d H:i:s');
                    $modelx->product_id = $model->product_id;
                    $modelx->qty = $model->qty;
                    $modelx->warehouse_id = $main_warehouse;//$warehouse_id;
                    $modelx->stock_type = 2; // 2 out
                    $modelx->activity_type_id = 28; // 28 prod rec cancel
                    $modelx->production_type = 28;
                    $modelx->company_id = $model->company_id;
                    $modelx->branch_id = $model->branch_id;
                    $modelx->created_by = $user_id;
                    if ($modelx->save(false)) {
                        $status = 1;
                        if($this->reducestock($model->product_id,$main_warehouse,$model->qty) == 1){
                            $model->status = 500;
                            $model->save(false);
                        }
                    }
                }
                array_push($data, ['journal_no' => $journal_no]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionDeleproducttransferline()
    {
        $id = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $id = $req_data['id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if($id && $user_id){
            $model = \common\models\StockTrans::find()->where(['id'=>$id])->one();
            if ($model) {
                $status = true;
                $main_warehouse = 1; //\backend\models\Warehouse::findPrimary($company_id, $branch_id);

                // cancel production receive
                $model_journal = new \backend\models\Stockjournal();
                $model_journal->journal_no = $model_journal->getReturnProdLastNo($model->company_id, $model->branch_id);
                //  $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                $model_journal->trans_date = date('Y-m-d H:i:s');

                $journal_no = $model_journal->journal_no;
                $model_journal->company_id = $model->company_id;
                $model_journal->branch_id = $model->branch_id;
                $model_journal->production_type = 28; // cancel production
                if ($model_journal->save(false)) {
                    $modelx = new \backend\models\Stocktrans();
                    $modelx->journal_no = $model_journal->journal_no;
                    $modelx->journal_id = $model_journal->id;
                    $modelx->trans_date = date('Y-m-d H:i:s');
                    $modelx->product_id = $model->product_id;
                    $modelx->qty = $model->qty;
                    $modelx->warehouse_id = $main_warehouse;//$warehouse_id;
                    $modelx->stock_type = 2; // 2 out
                    $modelx->activity_type_id = 28; // 28 prod rec cancel
                    $modelx->production_type = 28;
                    $modelx->company_id = $model->company_id;
                    $modelx->branch_id = $model->branch_id;
                    $modelx->created_by = $user_id;
                    if ($modelx->save(false)) {
                        $status = 1;
                        if($this->reducestock($model->product_id,$main_warehouse,$model->qty) == 1){
                            $model->status = 500;
                            $model->save(false);
                        }
                    }
                }
                array_push($data, ['journal_no' => $journal_no]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function reducestock($product_id, $wh_id, $qty)
    {
        $res = 0;
        if ($wh_id != null && $product_id != null && $qty > 0) {
            $model = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
            if ($model) {
                $model->qty = (float)$model->qty - (float)$qty;
                if($model->save(false)){
                    $res = 1;
                }
            }
        }
        return $res;
    }

    public function actionAddproducttransform(){
        $from_product_id = 0;
        $issue_qty = 0;
        $data_list = null;
        $user_id = 0;
        $company_id = 0;
        $branch_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $from_product_id = $req_data['product_id'];
        $issue_qty = $req_data['issue_qty'];
        $user_id = $req_data['user_id'];
        $data_list = $req_data['data'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($from_product_id != null && $data_list != null) {

                $model_reserv = new \backend\models\Transformreserv();
                $model_reserv->journal_no = $model_reserv::getLastNo(date('Y-m-d'),$company_id,$branch_id);
                $model_reserv->trans_date = date('Y-m-d H:i:s');
                $model_reserv->product_id = $from_product_id;
                $model_reserv->qty = $issue_qty;
                $model_reserv->status = 0;
                $model_reserv->user_id = $user_id;
                $model_reserv->company_id = $company_id;
                $model_reserv->branch_id = $branch_id;
                if ($model_reserv->save(false)) {

                    for ($i = 0; $i <= count($data_list) - 1; $i++) {
                        if ($data_list[$i]['qty'] == '' || $data_list[$i]['qty'] == 0) continue;
                        $this->updateStockIn($data_list[$i]['product_id'], $data_list[$i]['qty'], 1, $model_reserv->id, $company_id, $branch_id,$user_id);
                    }
                    if($this->updatestockout($from_product_id,$issue_qty,$company_id,$branch_id,$user_id) == 1){
                        $status = 1;
                    }
                }
            }


        return ['status' => $status, 'data' => $data];
    }
    public function updateStockIn($product_id, $qty, $wh_id, $journal_id, $company_id, $branch_id,$user_id)
    {
        if ($product_id != null && $qty > 0) {

            $model_journal = new \backend\models\Stockjournal();
            $model_journal->journal_no = $model_journal->getLastNoReprocess($company_id, $branch_id);
            $model_journal->trans_date = date('Y-m-d H:i:s');
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->production_type = 27;
            if ($model_journal->save(false)) {
                $model_trans = new \backend\models\Stocktrans();
                $model_trans->journal_no = $model_journal->journal_no;
                $model_trans->trans_date = date('Y-m-d H:i:s');
                $model_trans->product_id = $product_id;
                $model_trans->qty = $qty;
                $model_trans->warehouse_id = $wh_id;
                $model_trans->stock_type = 1; // 1 in 2 out
                $model_trans->activity_type_id = 27; // 27 receive reprocess
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->created_by = $user_id;
                $model_trans->trans_ref_id = $journal_id;
                $model_trans->status = 0;
                if ($model_trans->save(false)) {
                    $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                    if ($model_sum) {
                        $model_sum->qty = (float)$model_sum->qty + (float)$qty;
                        $model_sum->save(false);
                    }

                }
            }
        }
    }
    public function updatestockout($product_id, $qty, $company_id, $branch_id, $user_id)
    {
        $res = 0;
        if ($product_id != null && $qty != null) {
            $warehouse_id = 1; //$this->findReprocesswarehouse($company_id, $branch_id);
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $model_trans::getIssueReprocessCar($company_id, $branch_id);
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $warehouse_id;
            $model_trans->stock_type = 2; // 1 in 2 out
            $model_trans->activity_type_id = 18; // 6 issue car
            $model_trans->company_id = $company_id;
            $model_trans->created_by = $user_id;
            $model_trans->branch_id = $branch_id;
            if ($model_trans->save(false)) {

//                $model = \common\models\StockSum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id])->one();
//                if ($model) {
//                    $model->qty = 0;
//                    $model->save(false);
//                }
                $model = \backend\models\Stocksum::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id])->one();
                if ($model) {
                    $model->qty = (float)$model->qty - (float)$qty;
                    if($model->save(false)){
                        $res= 1;
                    }
                }
            }

        }
        return $res;
    }

    public function actionGettransformall(){
        $company_id = 1;
        $branch_id = 1;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        $model = \common\models\TransformReserv::find()->where(['date(trans_date)'=>date('Y-m-d'),'company_id' => $company_id, 'branch_id' => $branch_id,'status'=>0])->all();
        // $model = \common\models\QueryCustomerPrice::find()->all();
        if ($model) {
            $status = true;
            foreach ($model as $value) {
                // $product_info = \backend\models\Product::findInfo($value->product_id);
                array_push($data, [
                    'id' => $value->id,
                    'journal_no' => $value->journal_no,
                    'trans_date' => $value->trans_date,
                    'product_name'=> \backend\models\Product::findName($value->product_id),
                    'qty' =>$value->qty,
                    'created_name' => \backend\models\User::findName($value->user_id),
                ]);
            }
        }


        return ['status' => $status, 'data' => $data];
    }

    public function actionDeleteproducttransform()
    {
        $id = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $id = $req_data['id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;

        if($id && $user_id){
            $model_transform = \common\models\TransformReserv::find()->where(['id'=>$id])->one();
            if($model_transform){
                $model = \common\models\StockTrans::find()->where(['trans_ref_id'=>$id,'activity_type_id'=>27,'created_by'=>$user_id])->one();
                if ($model) {
                    $status = true;
                    $main_warehouse = 1; //\backend\models\Warehouse::findPrimary($company_id, $branch_id);

                    // cancel production receive
                    $model_journal = new \backend\models\Stockjournal();
                    $model_journal->journal_no = $model_journal->getReturnProdLastNo($model->company_id, $model->branch_id);
                    //  $model_journal->journal_no = $model_journal->getLastNoNew($company_id, $branch_id, $act_id, $production_type);
                    $model_journal->trans_date = date('Y-m-d H:i:s');

                    $journal_no = $model_journal->journal_no;
                    $model_journal->company_id = $model->company_id;
                    $model_journal->branch_id = $model->branch_id;
                    $model_journal->production_type = 28; // cancel production
                    if ($model_journal->save(false)) {
                        $modelx = new \backend\models\Stocktrans();
                        $modelx->journal_no = $model_journal->journal_no;
                        $modelx->journal_id = $model_journal->id;
                        $modelx->trans_date = date('Y-m-d H:i:s');
                        $modelx->product_id = $model->product_id;
                        $modelx->qty = $model->qty;
                        $modelx->warehouse_id = $main_warehouse;//$warehouse_id;
                        $modelx->stock_type = 2; // 2 out
                        $modelx->activity_type_id = 28; // 28 prod rec cancel
                        $modelx->production_type = 28;
                        $modelx->company_id = $model->company_id;
                        $modelx->branch_id = $model->branch_id;
                        $modelx->created_by = $user_id;
                        if ($modelx->save(false)) {

                            $status = 1;
                            if($this->reducestock($model->product_id,$main_warehouse,$model->qty) == 1){
                                if($this->addqtyfromreturntransform($model->company_id,$model->branch_id,$model_transform->product_id,$model_transform->qty,$main_warehouse,0,$user_id)){
                                    $model->status = 500;
                                    $model->save(false);
                                    $model_transform->status = 1;
                                    $model_transform->save(false);
                                }
                            }
                        }
                    }
                    array_push($data, ['journal_no' => $journal_no]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function addqtyfromreturntransform($company_id,$branch_id,$product_id,$qty,$wh_id,$journal_id,$user_id){
        $res = 0;
        $model_journal = new \backend\models\Stockjournal();
        $model_journal->journal_no = $model_journal->getLastNoReprocess($company_id, $branch_id);
        $model_journal->trans_date = date('Y-m-d H:i:s');
        $model_journal->company_id = $company_id;
        $model_journal->branch_id = $branch_id;
        $model_journal->production_type = 27;
        if ($model_journal->save(false)) {
            $model_trans = new \backend\models\Stocktrans();
            $model_trans->journal_no = $model_journal->journal_no;
            $model_trans->trans_date = date('Y-m-d H:i:s');
            $model_trans->product_id = $product_id;
            $model_trans->qty = $qty;
            $model_trans->warehouse_id = $wh_id;
            $model_trans->stock_type = 1; // 1 in 2 out
            $model_trans->activity_type_id = 27; // 27 receive reprocess
            $model_trans->company_id = $company_id;
            $model_trans->branch_id = $branch_id;
            $model_trans->created_by = $user_id;
            $model_trans->trans_ref_id = $journal_id;
            $model_trans->status = 0;
            if ($model_trans->save(false)) {
                $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => $wh_id, 'product_id' => $product_id])->one();
                if ($model_sum) {
                    $model_sum->qty = (float)$model_sum->qty + (float)$qty;
                    $model_sum->save(false);
                    $res+=1;
                }

            }
        }
        return $res;
    }

    public function actionScraplist()
    {
        $company_id = 0;
        $branch_id = 0;
        $trans_date = null;
        $journal_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $model = \common\models\QueryScrap::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'date(trans_date)' => date('Y-m-d')])->andFilterWhere(['>','qty',0])->orderBy(['id' => SORT_DESC])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'line_id'=> $value->line_id,
                        'journal_no'=>$value->journal_no,
                        'product_id' => $value->product_id,
                        'product_code' => $value->code,
                        'product_name' => $value->name,
                        'qty' => $value->qty,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAddscrapmobile()
    {
        $company_id = 0;
        $branch_id = 0;
        $product_id = 0;
        $production_rec_id = 0;
        $user_id = 0;
        $order_id = 0;
        $order_no = null;
        $qty = 0;
        $datalist = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $datalist = $req_data['data'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;
        $journal_no = '';

        if ($order_no != null) {
            $order_id = \backend\models\Orders::findId($order_no);
        }

        if ($datalist != null && $user_id && $company_id && $branch_id) {
            $model_journal = new \backend\models\Scrap();
            $journal_no =  $model_journal->getLastNo($company_id, $branch_id);
            $model_journal->journal_no = $journal_no;
            $model_journal->trans_date = date('Y-m-d H:i:s');
            $model_journal->prodrec_id = $production_rec_id;
            $model_journal->company_id = $company_id;
            $model_journal->branch_id = $branch_id;
            $model_journal->created_by = $user_id;
            $model_journal->scrap_type_id = 1;
            $model_journal->order_id = $order_id;
            $model_journal->status = 1;
            if ($model_journal->save(false)) {
                for($i=0;$i<=count($datalist)-1;$i++){
                    $model = new \common\models\ScrapLine();
                    $model->scrap_id = $model_journal->id;
                    $model->product_id = $datalist[$i]['product_id'];
                    $model->qty = $datalist[$i]['qty'];
                    $model->note = '';
                    $model->status = 1;
                    if ($model->save(false)) {
                        $status = 1;
                        $model_sum = \backend\models\Stocksum::find()->where(['warehouse_id' => 1, 'product_id' => $datalist[$i]['product_id']])->one();
                        if ($model_sum) {
                            $model_sum->qty = (float)$model_sum->qty - (float)$datalist[$i]['qty'];
                            $model_sum->save(false);
                        }
                    }
                }
            }

            array_push($data, ['journal_no' => $journal_no]);
            //  $this->renderPartial('_printtoindex', ['model' => $model, 'model_line' => $model_line, 'change_amount' => 0]);
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionScrapcancel(){
        $company_id = 0;
        $branch_id = 0;
        $id = 0;
        $user_id = 0;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $id = $req_data['id'];
        $user_id = $req_data['user_id'];

        $data = [];
        $status = false;
        if($user_id && $id){
            $model = \common\models\ScrapLine::find()->where(['id'=>$id])->one();
            $model->qty = 0;
            if($model->save(false)){
                  $status = true;
                  array_push($data,['message'=>'success']);
            }
        }


        return ['status' => $status, 'data' => $data];
    }

    public function actionTransferlist()
    {
        $company_id = 0;
        $branch_id = 0;
        $trans_date = null;
        $journal_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;

        if ($company_id && $branch_id) {
            $model = \common\models\TransferBranch::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'name'=> $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
    public function actionTransferlistrec()
    {
        $company_id = 0;
        $branch_id = 0;
        $trans_date = null;
        $journal_id = null;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $transfer_branch = $req_data['name'];

        $data = [];
        $status = false;
        $model = null;

        if ($company_id && $branch_id) {
            if($transfer_branch != ''){
                $model = \common\models\TransferBranch::find()->where(['name' => $transfer_branch])->all();
            }else{
                $model = \common\models\TransferBranch::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            }

            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'desc'=> $value->description,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
}
