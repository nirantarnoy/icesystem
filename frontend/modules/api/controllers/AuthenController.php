<?php

namespace frontend\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\web\Controller;


class AuthenController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['POST'],
                    'loginpos' => ['POST'],
                    'loginqrcode' => ['POST'],
                    'orderlist' => ['POST'],
                    'logoutpos' => ['POST']
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        $username = '';
        $password = '';
        $status = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //$post = file_get_contents("php://input");
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $username = $req_data['username'];
            $password = $req_data['password'];
        }
        $data = [];
        if ($username != '' && $password != '') {
            $model = \common\models\User::find()->where(['username' => $username])->one();
            if ($model) {
                if ($model->validatePassword($password)) {
                    $model_info = \backend\models\Employee::find()->where(['id' => $model->employee_ref_id])->one();
                    if ($model_info) {
                        $car_info = $this->getCar($model_info->id, $model->company_id, $model->branch_id);
                        $member_id = 0;

                        array_push($data, [
                                'username' => $username,
                                'user_id' => '' . $model->id,
                                'emp_id' => '' . $model->employee_ref_id, // person 1
                                'emp2_id' => '' . $member_id, // person 2
                                'emp_code' => $model_info->code,
                                'emp_name' => $model_info->fname . ' ' . $model_info->lname,
                                'emp_photo' => 'http://103.253.73.108/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,
                                //      'emp_photo' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,
                                'emp_route_id' => $car_info == null ? 0 : $car_info[0]['route_id'],
                                'emp_route_name' => $car_info == null ? 0 : $car_info[0]['route_name'],
                                'emp_car_id' => $car_info == null ? 0 : $car_info[0]['car_id'],
                                'emp_car_name' => $car_info == null ? 0 : $car_info[0]['car_name'],
                                'company_id' => $model->company_id,
                                'branch_id' => $model->branch_id,
                                'branch_name' => \backend\models\Branch::findName($model->branch_id),
                                'route_type' => $car_info == null ? 1 : \backend\models\Deliveryroute::findRouteType($car_info[0]['route_id']),
                            ]
                        );
                        $status = true;
                    }

                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionLoginpos()
    {
        $username = '';
        $password = '';
        $status = false;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //$post = file_get_contents("php://input");
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $username = $req_data['username'];
            $password = $req_data['password'];
        }
        $data = [];
        if ($username != '' && $password != '') {
            $model = \common\models\User::find()->where(['username' => $username])->one();
            if ($model) {
                if ($model->validatePassword($password)) {
                    $model_info = \backend\models\Employee::find()->where(['id' => $model->employee_ref_id])->one();
                    if ($model_info) {
                        // $car_info = $this->getCar($model_info->id, $model->company_id, $model->branch_id);
                        $member_id = 0;

                        array_push($data, [
                                'username' => $username,
                                'user_id' => '' . $model->id,
                                'emp_id' => '' . $model->employee_ref_id, // person 1
                                'emp_code' => $model_info->code,
                                'emp_name' => $model_info->fname . ' ' . $model_info->lname,
                                'emp_photo' => 'http://103.253.73.108/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,
                                //      'emp_photo' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,

                                'company_id' => $model->company_id,
                                'branch_id' => $model->branch_id,
                                'branch_name' => \backend\models\Branch::findName($model->branch_id),
                            ]
                        );
                        if ($this->createPosLoginLog($model_info->company_id, $model_info->branch_id, $model->id) == 1) {
                            $status = true;
                        }
                    }
                }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function actionLoginqrcode()
    {
        $car = '';
        $driver = '';
        $password = '';
        $member = '';

        $status = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //$post = file_get_contents("php://input");
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $car = $req_data['car'];
            $driver = $req_data['driver'];
            $password = $req_data['password'];
            $member = $req_data['member'];
        }
        $data = [];
        if ($car != '' && $driver != '') {
            $find_user_id = \backend\models\Employee::findUserid($driver);
            $model = \common\models\User::find()->where(['id' => $find_user_id])->one();
            if ($model) {
//                //  if ($model->validatePassword($password)) {
                $model_info = \backend\models\Employee::find()->where(['id' => $model->employee_ref_id])->one();
                $model_info_2 = null;
                if ($model_info) {
                    //    $car_info = $this->getCar($model_info->id, $model->company_id, $model->branch_id);
                    $car_info = $this->getCarId($car, $model->company_id, $model->branch_id);
                    if ($car_info) {
                        $member_id = 0;
                        if ($member != null || $member != '') {
                            $find_user_member_id = \backend\models\Employee::findUserid($member);
                            $model_member_user = \common\models\User::find()->where(['id' => $find_user_member_id])->one();
                            if ($model_member_user) {
                                $member_id = $model_member_user->employee_ref_id;
                                $model_info_2 = \backend\models\Employee::find()->where(['id' => $member_id])->one();
                            }
                        }

                        $emp_id = [];
                        $emp_id[0] = $model->employee_ref_id;
                        $emp_id[1] = $member_id; //$member!=null?\backend\models\Employee::findUserid($member):0;//\backend\models\User::getIdFromUsername($member);

                        $isdriver = [];
                        $isdriver[0] = 1;
                        $isdriver[1] = 0;

                        $has_driver_login = $this->checkHaslogin($model->employee_ref_id, 1);
                        $has_memeber_login = $this->checkHasloginMember($member_id, 0);

                        if ($has_driver_login > 0 || $has_memeber_login > 0) { // has today login
                            $status = 900;
                        } else {
                            if ($this->addEmpDaily($car_info[0]['car_id'], $car_info[0]['route_id'], null, $emp_id, $isdriver, $model->company_id, $model->branch_id)) {
                                $status = 1;

                                //add login route log

                                $model_login_log = new \common\models\LoginRouteLog();
                                $model_login_log->login_date = date('Y-m-d H:i:s');
                                $model_login_log->car_id = $car_info[0]['car_id'];
                                $model_login_log->route_id = $car_info[0]['route_id'];
                                $model_login_log->emp_1 = $model->employee_ref_id;
                                $model_login_log->emp_2 = $member_id;
                                $model_login_log->status = 1;
                                $model_login_log->save(false);

                                $daily_login_count = \common\models\LoginRouteLog::find()->where(['emp_1' => $model->employee_ref_id, 'date(login_date)' => date('Y-m-d')])->count();

                                // end add login route log


                                array_push($data, [
                                        'username' => $driver,
                                        'user_id' => '' . $model->id,
                                        'emp_id' => '' . $model->employee_ref_id, // person 1
                                        'emp2_id' => '' . $member_id, // person 2
                                        'emp_code' => $model_info->code,
                                        'emp_name' => $model_info->fname . ' ' . $model_info->lname,
                                        'emp_photo' => 'http://103.253.73.108/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,
                                        //      'emp_photo' => 'http://192.168.1.120/icesystem/backend/web/uploads/images/employee/' . $model_info->photo,
                                        'emp_route_id' => $car_info == null ? 0 : $car_info[0]['route_id'],
                                        'emp_route_name' => $car_info == null ? 0 : $car_info[0]['route_name'],
                                        'emp_car_id' => $car_info == null ? 0 : $car_info[0]['car_id'],
                                        'emp_car_name' => $car_info == null ? 0 : $car_info[0]['car_name'],
                                        'company_id' => $model->company_id,
                                        'branch_id' => $model->branch_id,
                                        'branch_name' => \backend\models\Branch::findName($model->branch_id),
                                        'route_type' => $car_info == null ? 1 : \backend\models\Deliveryroute::findRouteType($car_info[0]['route_id']),
                                        'route_code' => $car_info == null ? "" : \backend\models\Deliveryroute::findRoutecode($car_info[0]['route_id']),
                                        'emp_name2' => $model_info_2 != null ? $model_info_2->fname . ' ' . $model_info_2->lname : '',
                                        'login_shift' => $daily_login_count,
                                    ]
                                );
                            } else {
                                $status = 0;
                            }
                        }
                    }
                }
//                // }
            }
        }

        return ['status' => $status, 'data' => $data];
    }

    public function checkHaslogin($user_id, $is_driver)
    {
        $res = 0;
        if ($user_id != null || $user_id != '') {
            $model = \backend\models\Cardaily::find()->where(['date(trans_date)' => date('Y-m-d'), 'employee_id' => $user_id])->one();
            if ($model) {
                if ($model->is_driver == $is_driver) {
                    $res = 1;
                } else {
                    $res = 0;
                }
                //$res = 1;

            } else {
                $res = 0;
            }

            return $res;
        }
    }

    public function checkHasloginMember($user_id, $is_driver)
    {
        $res = 0;
        if ($user_id != null || $user_id != '' || $user_id != 0) {
            $model = \backend\models\Cardaily::find()->where(['date(trans_date)' => date('Y-m-d'), 'employee_id' => $user_id])->one();
            if ($model) {
                if ($model->is_driver == $is_driver) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } else {
                $res = 0;
            }

            return $res;
        }
    }

    public function getCar($emp_id, $company_id, $branch_id)
    {
        $data = [];
        if ($emp_id) {
            $model = \common\models\CarDaily::find()->where(['employee_id' => $emp_id, 'date(trans_date)' => date('Y-m-d'), 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
                array_push($data, [
                    'car_id' => $model->car_id,
                    'car_name' => \backend\models\Car::findName($model->car_id),
                    'route_id' => \backend\models\Car::findRouteId($model->car_id),
                    'route_name' => \backend\models\Car::findRouteName($model->car_id),
                ]);
            }
        }
        return $data;
    }

    public
    function getCarId($car, $company_id, $branch_id)
    {
        $data = [];
        if ($car != null) {
            $model = \backend\models\Car::find()->where(['code' => trim($car), 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
                array_push($data, [
                    'car_id' => $model->id,
                    'car_name' => $model->name,
                    'route_id' => $model::findRouteId($model->id),
                    'route_name' => $model::findRouteName($model->id),
                ]);
            }
        }
        return $data;
    }

    public
    function addEmpDaily($car_id, $route_id, $t_date, $emp_id, $isdriver, $company_id, $branch_id)
    {

        if ($route_id == null || $route_id == '') {
            $route_id = 0;
        }

        if ($t_date == null) {
            $t_date = date('Y-m-d');
        } else {
            $x_date = explode('/', $t_date);
            $x_date2 = null;
            if (count($x_date) > 1) {
                $x_date2 = $x_date[2] . '/' . $x_date[1] . '/' . $x_date[0];
            }
            $t_date = date('Y-m-d', strtotime($x_date2));
        }

        if ($car_id) {
            if ($emp_id != null) {
                //if(\backend\models\Cardaily::deleteAll(['date(trans_date)' => $t_date, 'car_id' => $car_id])){
                for ($i = 0; $i <= count($emp_id) - 1; $i++) {
                    if ($emp_id[$i] == '' || $emp_id[$i] == 0 && $emp_id[$i] == 27) continue; // $emp_id[$i] = 0;
                    if ($this->checkOld($emp_id[$i], $car_id, $t_date, $company_id, $branch_id)) {
                        //echo "has no ";return;
                        $model = \backend\models\Cardaily::find()->where(['date(trans_date)' => $t_date, 'employee_id' => $emp_id[$i], 'car_id' => $car_id])->one();
                        if ($model) {
                            $model->is_driver = $isdriver[$i] == 1 ? 1 : 0;
                            $model->save(false);
                        } else {
                            $model = new \backend\models\Cardaily();
                            $model->car_id = $car_id;
                            $model->employee_id = $emp_id[$i];
                            $model->trans_date = $t_date;
                            $model->is_driver = $isdriver[$i] == 1 ? 1 : 0;
                            $model->status = 1;
                            $model->company_id = $company_id;
                            $model->branch_id = $branch_id;
                            $model->save(false);
                        }

                    } else {
                        // echo "has ";return;
                        $model = new \backend\models\Cardaily();
                        $model->car_id = $car_id;
                        $model->employee_id = $emp_id[$i];
                        $model->trans_date = $t_date;
                        $model->is_driver = $isdriver[$i] == 1 ? 1 : 0;
                        $model->status = 1;
                        $model->company_id = $company_id;
                        $model->branch_id = $branch_id;
                        $model->save(false);
                    }

                    // save login history


                }
                // }

            } else {
                return false;
            }
        }
        return true;
    }

    public
    function checkOld($emp_id, $car_id, $t_date, $company_id, $branch_id)
    {
        $model = \backend\models\Cardaily::find()->where(['employee_id' => $emp_id, 'date(trans_date)' => $t_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->count();
//        if ($model>0) {
//            \backend\models\Cardaily::deleteAll(['car_id' => $car_id, 'employee_id' => $emp_id, 'date(trans_date)' => $t_date]);
//        }
        return $model;
    }

//    public function actionLogin()
//    {
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        //$post = file_get_contents("php://input");
//        $req_data = \Yii::$app->request->getBodyParams();
//        $req_data2 = \Yii::$app->getRequest()->getBodyParams();
//
//        $req_data3 = \yii::$app->request->post();
//        // return $params;
//        // print_r($req_data3);
//        $username = $req_data;
//        $password = $req_data3;
//        $data = [];
//        array_push($data, [
//            'username' => 'It is ' . $username,
//            'password' => 'It is ' . $password
//
//        ]);
//        if ($username != '' && $password != '') {
//            $model = \common\models\User::find()->where(['username' => $username])->one();
//            if ($model) {
//                if ($model->validatePassword($password)) {
//                    $model_info = \backend\models\Employee::find()->where(['id' => $model->employee_ref_id])->one();
//                    if ($model_info) {
//                        array_push($data, [
//                                'username' => $username,
//                                'user_id' => '' . $model->id,]
//                        );
//                    }
//
//                }
//
//            }
//        }
//
//        return ['status' => true, 'data' => $data];
//    }
    public function createPosLoginLog($company_id, $branch_id, $user_id)
    {
        $res = 0;
        $model_log = new \common\models\LoginLog();
        $model_log->user_id = $user_id;
        $model_log->login_date = date('Y-m-d H:i:s');
        $model_log->status = 1;
        $model_log->ip = '';
        if ($model_log->save(false)) {
            //  $check_is_login = \common\models\LoginLogCal::find()->where(['user_id'=>$model_log->user_id,'date(login_date)'=>date('Y-m-d'),'status'=>1])->one();
            $check_is_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id, 'status' => 1])->andFilterWhere(['is', 'logout_date', new \yii\db\Expression('null')])->limit(1)->orderBy(['id' => SORT_DESC])->one();
            if (!$check_is_login) {
                $model_login_cal = new \common\models\LoginLogCal();
                $model_login_cal->login_date = date('Y-m-d H:i:s');
                $model_login_cal->user_id = $user_id;
                $model_login_cal->status = 1;
                $model_login_cal->ip = '';
                $model_login_cal->company_id = $company_id;
                $model_login_cal->branch_id = $branch_id;
                if ($model_login_cal->save(false)) {
                    $res = 1;
                    // $update_remain_logout = \common\models\LoginLogCal::find()->where(['user_id'=>$model_log->user_id])->andFilterWhere(['!=','id',$model_login_cal->id]);
                }
            } else {
                $date1 = date('Y-m-d H:i:s', strtotime($check_is_login->login_date));//"2021-12-04 01:00:00";
                $date2 = date('Y-m-d H:i:s');
                $timestamp1 = strtotime($date1);
                $timestamp2 = strtotime($date2);
                $hour = abs($timestamp2 - $timestamp1) / (60 * 60);

                if ($hour > 12) { // login over 12 hours.
                    $check_is_login->logout_date = date('Y-m-d H:i:s');
                    $check_is_login->ip = '';
                    if ($check_is_login->save(false)) {
                        $model_login_cal = new \common\models\LoginLogCal();
                        $model_login_cal->login_date = date('Y-m-d H:i:s', strtotime($model_log->login_date));
                        $model_login_cal->user_id = $model_log->user_id;
                        $model_login_cal->status = 1;
                        $model_login_cal->ip = '';
                        $model_login_cal->company_id = $company_id;
                        $model_login_cal->branch_id = $branch_id;
                        if ($model_login_cal->save(false)) {
                        }
                    }
                }
                $res = 1;
            }
        }
        return $res;
    }

    public function actionLogoutpos()
    {
        $user_id = '';
        $status = 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $req_data = \Yii::$app->request->getBodyParams();
        if ($req_data != null) {
            $user_id = $req_data['user_id'];
        }
        $data = [];
        if ($user_id) {

//            $c_date = date('Y-m-d');
//            $ip = '';
//            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//                //ip from share internet
//                $ip = $_SERVER['HTTP_CLIENT_IP'];
//            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//                //ip pass from proxy
//                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//            } else {
//                $ip = $_SERVER['REMOTE_ADDR'];
//            }

            if (\common\models\LoginLog::updateAll(['logout_date' => date('Y-m-d H:i:s'), 'status' => 2], ['user_id' => $user_id, 'status' => 1])) {
                // $status = $ip;
                $check_is_has_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->limit(1)->orderBy(['id' => SORT_DESC])->one();
                if ($check_is_has_login) {
                    $check_is_has_login->logout_date = date('Y-m-d H:i:s');
                    $check_is_has_login->status = 2;
                    if ($check_is_has_login->save(false)) {
                        $status = 1;
                        array_push($data, ['logout_success' => 1]);
                    }
                }
            } else {
                array_push($data, ['logout_success' => 1]);
            }
        }

        return ['status' => $status, 'data' => $data];
    }
}
