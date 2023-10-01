<?php

namespace frontend\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;


class CustomerController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['POST'],
                    'bootlist' => ['POST'],
                    'poslist' => ['POST'],
                    'assetlist' => ['POST'],
                    'assetchecklist' => ['POST'],
                    'checklist' => ['POST'],
                    'addnewasset' => ['POST'],
                    'checkin' => ['POST'],
                    'routeall' => ['POST'],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $company_id = 1;
        $branch_id = 1;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;
        if ($route_id) {
            $model = \common\models\Customer::find()->where(['delivery_route_id' => $route_id, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'code' => $value->code,
                        'name' => $value->name,
                        'route_id' => $value->delivery_route_id
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionCheckin()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $route_id = $req_data['route_id'];
        $customer_id = $req_data['customer_id'];
        $user_id = $req_data['user_id'];
        $base64_string = $req_data['image'];
        $location = $req_data['location'];

        $data = [];
        $status = false;

        if ($company_id != null && $branch_id != null && $customer_id != null && $user_id != null) {
            $newfilesave = '';
            if ($base64_string != null) {
                $has_photo = 1;

                for ($xp = 0; $xp <= count($base64_string) - 1; $xp++) {
                    $newfile = time() + $xp . ".jpg";
                    $outputfile = '../web/uploads/assetcheck/' . $newfile;          //save as image.jpg in uploads/ folder

                    $filehandler = fopen($outputfile, 'wb');
                    //file open with "w" mode treat as text file
                    //file open with "wb" mode treat as binary file

                    fwrite($filehandler, base64_decode(trim($base64_string[$xp])));
                    // we could add validation here with ensuring count($data)>1

                    // clean up the file resource
                    fclose($filehandler);
                    // file_put_contents($newfile,base64_decode($base64_string));
                    // $newfile = base64_decode($base64_string);
                    if ($xp == 0) {
                        $newfilesave = $newfile;
                    } else {
                        $newfilesave = $newfilesave . ',' . $newfile;
                    }

                }

            }
            $model = new \backend\models\Customercheckin();
            $model->customer_id = $customer_id;
            $model->checkin_date = date('Y-m-d H:i:s');
            $model->company_id = $company_id;
            $model->branch_id = $branch_id;
            $model->latlong = $location;
            $model->route_id = $route_id;
            $model->photo = $newfilesave;
            if($model->save(false)){
                $status = 1;
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionBootlist()
    {
        $company_id = 1;
        $branch_id = 1;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;
        if ($company_id && $branch_id) {
            $model = \common\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'name' => 'สด-หน้าบ้าน'])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'code' => $value->code,
                        'name' => $value->name
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionPoslist()
    {
        $company_id = 1;
        $branch_id = 1;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;
        if ($company_id && $branch_id) {
            $model = \common\models\Customer::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'is_show_pos' => 1])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'code' => $value->code,
                        'name' => $value->name
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAssetlist()
    {
        $company_id = 1;
        $branch_id = 1;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];

        $data = [];
        $status = false;
        if ($customer_id) {
            $model = \common\models\CustomerAsset::find()->where(['customer_id' => $customer_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'product_id' => $value->product_id,
                        'code' => \backend\models\Assetsitem::findCode($value->product_id),
                        'name' => \backend\models\Assetsitem::findName($value->product_id),
                        'qty' => $value->qty,
                        'status' => $value->status,
                        'photo' => '',
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionAssetchecklist()
    {
        $status = 0;
        $datalist = null;
        $user_id = null;
        $company_id = null;
        $branch_id = null;
        $route_id = null;
        $location = null;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        // $image = base64_decode($req_data['image']);
        //$image = mb_convert_encoding(base64_decode($req_data['image']), 'UTF-8', 'UTF-8');
        // $image = $_FILES['image']['tmp_name'];

        //$image = UploadedFile::getInstanceByName('image');
        //$name = time(). uniqid().'.jpg';//$req_data['name'];
//        if(is_object($image)){
//            $status = 1000;
//            $filename = time()."_".uniqid().'.'.$image->extension;
//            $imagePath = \Yii::$app->getUrlManager()->baseUrl."/uploads/".$filename;
//            move_uploaded_file($_FILES['image']['tmp_name'],$imagePath);
//        }

        //  move_uploaded_file($_FILES['image']['tmp_name'],$imagePath);
        //   $realimage = \Yii::$app->getUrlManager()->baseUrl . '/uploads/assetcheck/' . $image;
        // move_uploaded_file($_FILES['image']['tmp_name'],$imagePath);
//        $realimage = \Yii::getAlias('@frontend/web/').'uploads/assetcheck/' . $image;
//        file_put_contents($name, $realimage);


        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $customer_id = $req_data['customer_id'];
        $product_id = $req_data['product_id'];
        $user_id = $req_data['user_id'];
        $route_id = $req_data['route_id'];
        $datalist = $req_data['datalist'];
        $base64_string = $req_data['image'];
        $location = $req_data['location'];

        if ($company_id != null && $branch_id != null && $customer_id != null && $user_id != null) {
            $newfilesave = '';
            $has_photo = 0;
            if ($base64_string != null) {
                $has_photo = 1;

                for ($xp = 0; $xp <= count($base64_string) - 1; $xp++) {
                    $newfile = time() + $xp . ".jpg";
                    $outputfile = '../web/uploads/assetcheck/' . $newfile;          //save as image.jpg in uploads/ folder

                    $filehandler = fopen($outputfile, 'wb');
                    //file open with "w" mode treat as text file
                    //file open with "wb" mode treat as binary file

                    fwrite($filehandler, base64_decode(trim($base64_string[$xp])));
                    // we could add validation here with ensuring count($data)>1

                    // clean up the file resource
                    fclose($filehandler);
                    // file_put_contents($newfile,base64_decode($base64_string));
                    // $newfile = base64_decode($base64_string);
                    if ($xp == 0) {
                        $newfilesave = $newfile;
                    } else {
                        $newfilesave = $newfilesave . ',' . $newfile;
                    }

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
                if ($has_photo) {
                    $this->checkAssetPhoto($base64_string, $product_id); // check and update asset photo
                }

                $this->updateCustomerlocation($location, $customer_id);
                $status = 1;
                if ($datalist != null) {
                    for ($i = 0; $i <= count($datalist) - 1; $i++) {
                        $model_line = new \common\models\CustomerAssetStatusLine();
                        $model_line->asset_status_id = $model->id;
                        $model_line->checklist_id = $datalist[$i]['id'];
                        $model_line->check_status = $datalist[$i]['is_check'];
                        $model_line->save(false);
                    }
                }
            }
        }
        return ['status' => $status, 'data' => $base64_string];
    }

    public function checkAssetPhoto($base64_string, $asset_id)
    {
        $model = \common\models\AssetsPhoto::find()->where(['asset_id' => $asset_id])->one();
        if (!$model) {

            if ($base64_string != null) {
                for ($xp = 0; $xp <= count($base64_string) - 1; $xp++) {
                    if ($xp == 0) { // only first
                        $newfile = time() . ".jpg";
                        $outputfile = '../web/uploads/assetphoto/' . $newfile;          //save as image.jpg in uploads/ folder

                        $filehandler = fopen($outputfile, 'wb');
                        //file open with "w" mode treat as text file
                        //file open with "wb" mode treat as binary file

                        fwrite($filehandler, base64_decode(trim($base64_string[$xp])));
                        // we could add validation here with ensuring count($data)>1

                        // clean up the file resource
                        fclose($filehandler);

                        $model_new = new \common\models\AssetsPhoto();
                        $model_new->asset_id = $asset_id;
                        $model_new->photo = $newfile;
                        $model_new->file_ext = ".jpg";
                        $model_new->save(false);
                    }

                }
            }

        }

    }

    public function actionChecklist()
    {
        $company_id = 0;
        $branch_id = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        //   $warehouse_code = $req_data['wh_code'];

        $data = [];
        $status = 0;

        if ($company_id) {
            $model = \backend\models\Assetchecklist::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();
            if ($model) {
                $status = 1;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'code' => $value->code,
                        'name' => $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
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

    public function actionAddnewasset()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $customer_id = $req_data['customer_id'];
        $company_id = $req_data['company_id'];
        $branch_id = $req_data['branch_id'];
        $asset_no = $req_data['asset_no'];
        $base64_string = $req_data['image'];
        $user_id = $req_data['user_id'];
        $location = $req_data['location'];

        $data = [];
        $status = false;
        if ($company_id && $branch_id && $customer_id && $asset_no != '') {
            // $asset_id = 0;
            $asset_id = \backend\models\Assetsitem::findIdFromCode($asset_no);
            $model = new \common\models\CustomerAssetRequest();
            $model->customer_id = $customer_id;
            $model->asset_id = $asset_id;
            $model->status = 0;
            $model->company_id = $company_id;
            $model->created_at = time();
            $model->created_by = $user_id;
            $model->branch_id = $branch_id;
            $model->location = $location;
            if ($model->save(false)) {
                if ($base64_string != null) {
                    for ($xp = 0; $xp <= count($base64_string) - 1; $xp++) {
                        if ($xp == 0) { // only first
                            $newfile = time() . ".jpg";
                            $outputfile = '../web/uploads/assetphoto/' . $newfile;          //save as image.jpg in uploads/ folder

                            $filehandler = fopen($outputfile, 'wb');
                            //file open with "w" mode treat as text file
                            //file open with "wb" mode treat as binary fi

                            fwrite($filehandler, base64_decode(trim($base64_string[$xp])));
                            // we could add validation here with ensuring count($data)>1

                            // clean up the file resource
                            fclose($filehandler);

                            $model_new = new \common\models\AssetsPhoto();
                            $model_new->request_id = $model->id;
                            $model_new->asset_id = $asset_id;
                            $model_new->photo = $newfile;
                            $model_new->file_ext = ".jpg";
                            $model_new->in_use = 0;
                            $model_new->save(false);
                        }

                    }
                } else {
                    array_push($data, ['message' => 'no image']);
                }
                $status = true;
            }


        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionRouteall()
    {
        $company_id = 1;
        $branch_id = 1;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        $route_id = $req_data['route_id'];

        $data = [];
        $status = false;
        if ($route_id) {
            $model = \common\models\DeliveryRoute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 1,])->andFilterWhere(['>','id',1])->all();
            if ($model) {
                $status = true;
                foreach ($model as $value) {
                    array_push($data, [
                        'id' => $value->id,
                        'code' => $value->code,
                        'name' => $value->name,
                    ]);
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }
}
