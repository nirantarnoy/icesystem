<?php

namespace backend\controllers;

use backend\models\EmployeeSearch;
use Yii;
use backend\models\Assetsitem;
use backend\models\AssetsitemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AssetsitemController implements the CRUD actions for Assetsitem model.
 */
class AssetsitemController extends Controller
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
        ];
    }

    /**
     * Lists all Assetsitem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $viewstatus = 1;

        if (\Yii::$app->request->get('viewstatus') != null) {
            $viewstatus = \Yii::$app->request->get('viewstatus');
        }

        $pageSize = \Yii::$app->request->post("perpage");
        $searchModel = new AssetsitemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($viewstatus == 1) {
            $dataProvider->query->andFilterWhere(['assets.status' => $viewstatus]);
        }
        if ($viewstatus == 2) {
            $dataProvider->query->andFilterWhere(['assets.status' => 0]);
        }

        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        $dataProvider->pagination->pageSize = $pageSize;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'perpage' => $pageSize,
            'viewstatus' => $viewstatus,
        ]);
    }

    /**
     * Displays a single Assetsitem model.
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
     * Creates a new Assetsitem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Assetsitem();


        if ($model->load(Yii::$app->request->post())) {
            $company_id = 1;
            $branch_id = 1;
            if (!empty(\Yii::$app->user->identity->company_id)) {
                $company_id = \Yii::$app->user->identity->company_id;
            }
            if (!empty(\Yii::$app->user->identity->branch_id)) {
                $branch_id = \Yii::$app->user->identity->branch_id;
            }

            $model->company_id = $company_id;
            $model->branch_id = $branch_id;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Assetsitem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelphoto = \common\models\AssetsPhoto::find()->where(['asset_id' => $id])->all();
        if ($model->load(Yii::$app->request->post())) {
            $removephotolist = \Yii::$app->request->post('removephotolist');
            $asset_photo = UploadedFile::getInstancesByName("asset_photo");
            if ($model->save()) {
                if ($removephotolist != '') {
                    $z = explode(',', $removephotolist);
                    if (count($z) > 0) {
                        for ($m = 0; $m <= count($z) - 1; $m++) {
                            $delete_name = $this->getFilename($z[$m]);
                            if (\common\models\AssetsPhoto::deleteAll(['id' => $z[$m]])) {
//                                echo $delete_name;return ;
                                if ($delete_name != "") {
                                    unlink(Yii::getAlias('@backend') . '/web/uploads/images/assetsitem/' . $delete_name);
                                }
                            }
                        }
                    }
                }

                if (!empty($asset_photo)) {
//                echo count($customer_form); return ;
                    $i = 0;

                    foreach ($asset_photo as $value) {
                        $file_name = time() . $i . "." . $value->getExtension();
                        $value->saveAs(Yii::getAlias('@backend') . '/web/uploads/images/assetsitem/' . $file_name);
                        $model_photo = new \common\models\AssetsPhoto();
                        $model_photo->asset_id = $model->id;
                        $model_photo->photo = $file_name;
                        $model_photo->file_ext = $value->getExtension();
                        $model_photo->save(false);
                        $i += 1;
                    }
                }
                $session = Yii::$app->session;
                $session->setFlash('msg', 'บันทึกข้อมูลเรียบร้อย');
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('update', [
            'model' => $model,
            'modelphoto' => $modelphoto,
        ]);
    }

    /**
     * Deletes an existing Assetsitem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Assetsitem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Assetsitem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Assetsitem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionImportAsset()
    {
        $uploaded = UploadedFile::getInstanceByName('file_asset');
        if (!empty($uploaded)) {
            //echo "ok";return;
            $upfiles = time() . "." . $uploaded->getExtension();
            // if ($uploaded->saveAs(Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles)) {
            if ($uploaded->saveAs('../web/uploads/files/customers/' . $upfiles)) {
                //  echo "okk";return;
                // $myfile = Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles;
                $myfile = '../web/uploads/files/customers/' . $upfiles;
                $file = fopen($myfile, "r+");
                fwrite($file, "\xEF\xBB\xBF");

                setlocale(LC_ALL, 'th_TH.TIS-620');
                $i = -1;
                $res = 0;
                $data = [];
                while (($rowData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $i += 1;
                    $catid = 0;
                    $qty = 0;
                    $price = 0;
                    $cost = 0;
                    if ($rowData[1] == '' || $i == 0) {
                        continue;
                    }

                    $model_dup = \backend\models\Assetsitem::find()->where(['asset_no' => trim($rowData[1]), 'company_id' => 1, 'branch_id' => 1])->one();
                    if ($model_dup != null) {
                        continue;
                    }


                    $modelx = new \backend\models\Assetsitem();
                    // $modelx->code = $rowData[0];
                    $modelx->asset_no = $rowData[1];
                    $modelx->asset_name = $rowData[2];
                    $modelx->description = $rowData[3];
                    $modelx->status = 1;
                    $modelx->company_id = 1;
                    $modelx->branch_id = 1;
                    if ($modelx->save(false)) {
                        $res += 1;
                    }
                }
                //    print_r($qty_text);return;

                if ($res > 0) {
                    $session = Yii::$app->session;
                    $session->setFlash('msg', 'นำเข้าข้อมูลเรียบร้อย');
                    return $this->redirect(['index']);
                } else {
                    $session = Yii::$app->session;
                    $session->setFlash('msg-error', 'พบข้อมผิดพลาดนะ');
                    return $this->redirect(['index']);
                }
                // }
                fclose($file);
//            }
//        }
            }
        }
    }

    public function actionImportAssetByCustomer()
    {
        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $uploaded = UploadedFile::getInstanceByName('file_asset_customer');
        if (!empty($uploaded)) {
            //echo "ok";return;
            $upfiles = time() . "." . $uploaded->getExtension();
            // if ($uploaded->saveAs(Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles)) {
            if ($uploaded->saveAs('../web/uploads/files/customers/' . $upfiles)) {
                //  echo "okk";return;
                // $myfile = Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles;
                $myfile = '../web/uploads/files/customers/' . $upfiles;
                $file = fopen($myfile, "r+");
                fwrite($file, "\xEF\xBB\xBF");

                setlocale(LC_ALL, 'th_TH.TIS-620');
                $i = -1;
                $res = 0;
                $data = [];
                while (($rowData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $i += 1;
                    $catid = 0;
                    $qty = 0;
                    $price = 0;
                    $cost = 0;
                    if ($rowData[1] == '' || $i == 0) {
                        continue;
                    }

//                    $model_dup = \backend\models\Assetsitem::find()->where(['asset_no' => trim($rowData[1]), 'company_id' => 1, 'branch_id' => 1])->one();
//                    if ($model_dup != null) {
//                        continue;
//                    }

                    $customer_id = $this->findCustomerId(trim($rowData[0]));
                    $asset_id = $this->findAssetId(trim($rowData[3]));


//                    $model_chk = \backend\models\Customerasset::find()->where(['customer_id' => $customer_id, 'product_id' =>$asset_id])->one();
//                    if ($model_chk) {
//                        // echo 'ok';return;
//                        $model_chk->qty = $asset_qty[$i];
//                        $model_chk->save(false);
//                    } else {
                    if ($customer_id > 0 && $asset_id > 0) {
                        $model_asset = new \backend\models\Customerasset();
                        $model_asset->customer_id = $customer_id;
                        $model_asset->product_id = $asset_id;
                        $model_asset->qty = 1;
                        $model_asset->start_date = date('Y-m-d');
                        $model_asset->status = 1;
                        $model_asset->company_id = $company_id;
                        $model_asset->branch_id = $branch_id;
                        if ($model_asset->save(false)) {
                            $res += 1;
                        }
                    }

//                    }

                }
                //    print_r($qty_text);return;

                if ($res > 0) {
                    $session = Yii::$app->session;
                    $session->setFlash('msg', 'นำเข้าข้อมูลเรียบร้อย');
                    return $this->redirect(['index']);
                } else {
                    $session = Yii::$app->session;
                    $session->setFlash('msg-error', 'พบข้อมผิดพลาดนะ');
                    return $this->redirect(['index']);
                }
                // }
                fclose($file);
//            }
//        }
            }
        }
    }

    public function findCustomerId($cuscode)
    {
        $id = 0;
        if ($cuscode != '') {
            $model = \backend\models\Customer::find()->select(['id'])->where(['code' => $cuscode])->one();
            if ($model) {
                $id = $model->id;
            }
        }
        return $id;
    }

    public function findAssetId($asset_no)
    {
        $id = 0;
        if ($asset_no != '') {
            $model = \backend\models\Assetsitem::find()->select(['id'])->where(['asset_no' => $asset_no])->one();
            if ($model) {
                $id = $model->id;
            }
        }
        return $id;
    }

    public function actionPrint()
    {
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        $find_customer_id = \Yii::$app->request->post('find_customer_id');
        return $this->render('_print', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'find_customer_id' => $find_customer_id,
        ]);
    }

    public function actionGetItem()
    {
        $company_id = 0;
        $branch_id = 0;

        $txt = \Yii::$app->request->post('txt');

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }
        $html = '';
        $model = null;
        if ($txt == '' || $txt == null) {
            $model = \backend\models\Assetsitem::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
        } else {
            $model = \backend\models\Assetsitem::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['OR', ['LIKE', 'asset_no', $txt], ['LIKE', 'asset_name', $txt]])->all();
        }

        if ($model) {
            foreach ($model as $value) {
                $html .= '<tr>';
                $html .= '<td style="text-align: center">
                        <div class="btn btn-outline-success btn-sm" onclick="addselecteditem($(this))" data-var="' . $value->id . '">เลือก</div>
                        <input type="hidden" class="line-find-code" value="' . $value->asset_no . '">
                        <input type="hidden" class="line-find-name" value="' . $value->asset_name . '">
                        <input type="hidden" class="line-find-price" value="0">
                        <input type="hidden" class="line-onhand" value="0">
                       </td>';
                $html .= '<td>' . $value->asset_no . '</td>';
                $html .= '<td>' . $value->asset_name . '</td>';
                $html .= '<td>' . number_format(0) . '</td>';
                $html .= '<td>' . number_format(0) . '</td>';
                $html .= '<td>' . number_format(0) . '</td>';
                $html .= '</tr>';
            }
        }
        echo $html;
    }

    public function actionAssetRequest()
    {
        $model = \common\models\CustomerAssetRequest::find()->where(['status' => 0])->all();

        return $this->render('_request', [
            'model' => $model,
        ]);
    }

    public function actionAssetRequestUpdate($id)
    {
        $model = \common\models\CustomerAssetRequest::find()->where(['id' => $id])->one();
        $model_photo = \common\models\AssetsPhoto::find()->where(['request_id' => $id])->all();
        return $this->render('_request_update', [
            'model' => $model,
            'model_photo' => $model_photo,
        ]);
    }

    public function actionAssetrequestsave()
    {
//        echo "ok";return;

        $company_id = 0;
        $branch_id = 0;

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $id = \Yii::$app->request->post('request_id');
        $asset_no = \Yii::$app->request->post('asset_no');
        $new_code = \Yii::$app->request->post('new_asset_no');
        $new_qty = \Yii::$app->request->post('new_asset_qty');
//        echo $id;return;
        if ($id && $new_code != '') {

            $customer_id = 0;
            $route_id = 0;
            $user_id = 0;
            $location = '';
            $model_has = \backend\models\Assetsitem::find()->where(['asset_no' => trim($asset_no)])->one();
            if ($model_has) { // has already asset code
                if (\common\models\AssetsPhoto::updateAll(['asset_id' => $model_has->id, 'in_use' => 1], ['request_id' => $id])) {
                    $model = \common\models\CustomerAssetRequest::find()->where(['id' => $id])->one();
                    if ($model) {
                        $route_id = \backend\models\Customer::findRouteId($model->customer_id);
                        $user_id = $model->created_by;
                        $location = $model->location;
                        $customer_id = $model->customer_id;
                        $model->status = 1;
                        $model->asset_id = $model_has->id;
                        if ($model->save(false)) {
                            $model_check_has_asset = \backend\models\Customerasset::find()->where(['customer_id' => $customer_id, 'product_id' => $model_has->id])->one();
                            if (!$model_check_has_asset) { // if not have
                                $model_assign_customer = new \backend\models\Customerasset();
                                $model_assign_customer->customer_id = $customer_id;
                                $model_assign_customer->product_id = $model_has->id;
                                $model_assign_customer->qty = $new_qty;
                                $model_assign_customer->start_date = date('Y-m-d H:i:s');
                                $model_assign_customer->status = 1;
                                $model_assign_customer->company_id = $company_id;
                                $model_assign_customer->branch_id = $branch_id;
                                $model_assign_customer->save(false);
                            }

                        }
                    }
                    $this->createAssetCheckTrans($customer_id, $model_has->id, $company_id, $branch_id, $route_id, $user_id, $location);
                }
            } else {// not has already asset code
                $model_new_asset = new \backend\models\Assetsitem();
                $model_new_asset->asset_no = $new_code;
                $model_new_asset->asset_name = $new_code;
                $model_new_asset->description = '';
                $model_new_asset->company_id = $company_id;
                $model_new_asset->branch_id = $branch_id;
                $model_new_asset->status = 1;
                if ($model_new_asset->save(false)) {
                    if (\common\models\AssetsPhoto::updateAll(['asset_id' => $model_new_asset->id, 'in_use' => 1], ['request_id' => $id])) {
                        $model = \common\models\CustomerAssetRequest::find()->where(['id' => $id])->one();
                        if ($model) {
                            $route_id = \backend\models\Customer::findRouteId($model->customer_id);
                            $user_id = $model->created_by;
                            $customer_id = $model->customer_id;
                            $location = $model->location;
                            $model->status = 1;
                            $model->asset_id = $model_new_asset->id;
                            if ($model->save(false)) {
                                $model_assign_customer = new \backend\models\Customerasset();
                                $model_assign_customer->customer_id = $customer_id;
                                $model_assign_customer->product_id = $model_new_asset->id;
                                $model_assign_customer->qty = $new_qty;
                                $model_assign_customer->start_date = date('Y-m-d H:i:s');
                                $model_assign_customer->status = 1;
                                $model_assign_customer->company_id = $company_id;
                                $model_assign_customer->branch_id = $branch_id;
                                $model_assign_customer->save(false);
                            }
                        }
                    }

                }
                $this->createAssetCheckTrans($customer_id, $model_new_asset->id, $company_id, $branch_id, $route_id, $user_id, $location);
            }


        }
        return $this->redirect(['assetsitem/asset-request']);
    }

    public function actionGetassetphoto()
    {
        $html = '';
        $asset_id = \Yii::$app->request->post('id');
        if ($asset_id > 0) {
            $model = \common\models\AssetsPhoto::find()->where(['asset_id' => $asset_id])->all();
            if ($model) {
                $html .= '<div class="row">';
                foreach ($model as $value) {
                    $html .= '<div class"col-lg-3">';
                    $html .= '<img src="' . \Yii::$app->urlManagerFrontend->getBaseUrl() . '/uploads/assetphoto/' . $value->photo . '" width="100%">';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function createAssetCheckTrans($customer_id, $product_id, $company_id, $branch_id, $route_id, $user_id, $location)
    {
        $newfilesave = '';
        $model = \common\models\AssetsPhoto::find()->where(['asset_id' => $product_id])->all();
        if ($model) {
            foreach ($model as $value) {
                $newfilesave .= $value->photo . ",";
            }

        }
        $model = new \common\models\CustomerAssetStatus();
        $model->customer_id = $customer_id;
        $model->cus_asset_id = $product_id;
        $model->trans_date = date('Y-m-d H:i:s');
        $model->company_id = $company_id;
        $model->branch_id = $branch_id;
        $model->route_id = $route_id;
        $model->created_by = $user_id;
        $model->status = 1;
        $model->latlong = $location;
        $model->photo = $newfilesave;
        $model->created_at = time();
        if ($model->save(false)) {

            $this->updateCustomerlocation($location, $customer_id);
            $status = 1;

            $model_line = new \common\models\CustomerAssetStatusLine();
            $model_line->asset_status_id = $model->id;
            $model_line->checklist_id = 1;
            $model_line->check_status = 1;
            $model_line->save(false);

        }
    }

    public function updateCustomerlocation($location, $customer_id)
    {
        if ($customer_id) {
            $model = \backend\models\Customer::find()->where(['id' => $customer_id])->one();
            if ($model) {
                $model->location_info = $location;
                $model->save(false);
            }
        }
    }

    public function actionDeleteRequest()
    {
        $id = \Yii::$app->request->post('delete_id');
        if ($id) {
            if (\common\models\AssetsPhoto::deleteAll(['request_id' => $id])) {
                \common\models\CustomerAssetRequest::deleteAll(['id' => $id]);
            } else {
                \common\models\CustomerAssetRequest::deleteAll(['id' => $id]);
            }
        }

        return $this->redirect(['assetsitem/asset-request']);
    }

    public function actionCheckinprint()
    {
        $from_date = \Yii::$app->request->post('from_date');
        $to_date = \Yii::$app->request->post('to_date');
        $find_customer_id = \Yii::$app->request->post('find_customer_id');
        return $this->render('_printcheckin', [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'find_customer_id' => $find_customer_id,
        ]);
    }

    public function actionImportsummarypricegroup()
    {
        $uploaded = UploadedFile::getInstanceByName('file_price_group');
        if (!empty($uploaded)) {
            //echo "ok";return;
            $upfiles = time() . "." . $uploaded->getExtension();
            // if ($uploaded->saveAs(Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles)) {
            if ($uploaded->saveAs('../web/uploads/files/customers/' . $upfiles)) {
                //  echo "okk";return;
                // $myfile = Yii::$app->request->baseUrl . '/uploads/files/' . $upfiles;
                $myfile = '../web/uploads/files/customers/' . $upfiles;
                $file = fopen($myfile, "r+");
                fwrite($file, "\xEF\xBB\xBF");

                setlocale(LC_ALL, 'th_TH.TIS-620');
                $i = -1;
                $res = 0;
                $data = [];
                while (($rowData = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $i += 1;
                    $catid = 0;
                    $qty = 0;
                    $price = 0;
                    $cost = 0;
                    if ($rowData[1] == '' || $i == 0) {
                        continue;
                    }

//                    $model_dup = \backend\models\Stdpricegroup::find()->where(['asset_no' => trim($rowData[1]), 'company_id' => 1, 'branch_id' => 1])->one();
//                    if ($model_dup != null) {
//                        continue;
//                    }

                    $product_id = \backend\models\Product::findProductIdByName(trim($rowData[3]));
                    $type_id = 0;
                    if(trim($rowData[6]) == 'ขายสด'){
                        $type_id = 1;
                    }
                    if(trim($rowData[6]) == 'ขายเชื่อ'){
                        $type_id = 2;
                    }
                    if(trim($rowData[6]) == 'ฟรี'){
                        $type_id = 3;
                    }

                    $modelx = new \backend\models\Stdpricegroup();
                    // $modelx->code = $rowData[0];
                    $modelx->name = $rowData[1];
                    $modelx->product_id = $product_id;
                    $modelx->price = $rowData[4];
                    $modelx->type_id = $type_id;
                    $modelx->seq_no = $rowData[5];

                    if ($modelx->save(false)) {
                        $res += 1;
                    }
                }
                //    print_r($qty_text);return;

                if ($res > 0) {
                    $session = Yii::$app->session;
                    $session->setFlash('msg', 'นำเข้าข้อมูลเรียบร้อย');
                    return $this->redirect(['index']);
                } else {
                    $session = Yii::$app->session;
                    $session->setFlash('msg-error', 'พบข้อมผิดพลาดนะ');
                    return $this->redirect(['index']);
                }
                // }
                fclose($file);
//            }
//        }
            }
        }
    }
}
