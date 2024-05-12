<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
//                    [
//                        'actions' => ['captcha'],
//                        'allow' => false,
//                    ],
                    [
                        'actions' => ['login', 'error', 'createadmin', 'changepassword', 'decodex', 'grab', 'addseconduser', 'getcominfo', 'transactionsalecar', 'transactionsalecar2', 'transactionsalecar3', 'createscreenshort', 'transactionsalepos', 'updateroute', 'calmachine', 'clearorder','testclosesum','updateorderpayment','caltransactionsaledistributor','caltransactionsaledistributorauto','startcaldailymanagerauto','summarybystdgroup'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'mas'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
//            'captcha' => [
//                'class' => 'yii\captcha\CaptchaAction',
//            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionCalmachine()
    {
//        $all = 50;
//        $mac_id = 2;
//        $channel_qty = 8;
//        for($i=0;$i<=$all-1;$i++){
//           // insert into machine_detail(machine_id,loc_name,loc_qty,status,company_id,branch_id)values(2,'A40',10,1,1,1);
//            $model = new \common\models\MachineDetail();
//            $model->machine_id = $mac_id;
//            $model->loc_name = "A".($i+1);
//            $model->loc_qty = $channel_qty;
//            $model->status = 1;
//            $model->company_id = 1;
//            $model->branch_id = 1;
//            $model->save(false);
//
//            $model2 = new \common\models\MachineDetail();
//            $model2->machine_id = $mac_id;
//            $model2->loc_name = "B".($i+1);
//            $model2->loc_qty = $channel_qty;
//            $model2->status = 1;
//            $model2->company_id = 1;
//            $model2->branch_id = 1;
//            $model2->save(false);
//
//            $model3 = new \common\models\MachineDetail();
//            $model3->machine_id = $mac_id;
//            $model3->loc_name = "C".($i+1);
//            $model3->loc_qty = $channel_qty;
//            $model3->status = 1;
//            $model3->company_id = 1;
//            $model3->branch_id = 1;
//            $model3->save(false);
//
//            $model3 = new \common\models\MachineDetail();
//            $model3->machine_id = $mac_id;
//            $model3->loc_name = "D".($i+1);
//            $model3->loc_qty = $channel_qty;
//            $model3->status = 1;
//            $model3->company_id = 1;
//            $model3->branch_id = 1;
//            $model3->save(false);
//        }

        //insert into production_status(loc_id,color_status,product_id,company_id,branch_id,user_id)values(330,'G',16,1,1,1);

        $model_init = \common\models\MachineDetail::find()->where(['company_id' => 1, 'branch_id' => 1])->all();
        if ($model_init) {
            foreach ($model_init as $value) {
                $model_x = new \common\models\ProductionStatus();
                $model_x->loc_id = $value->id;
                $model_x->color_status = 'G';
                $model_x->product_id = 2;
                $model_x->company_id = 1;
                $model_x->branch_id = 1;
                $model_x->user_id = 1;
                $model_x->save(false);
            }
        }
    }

    public function actionClearorder()
    {
        \backend\models\Orders::deleteAll(['branch_id' => 1])->limit(100);
        echo "ok";
    }

    public function actionGetcominfo()
    {
        echo php_uname('n');;
//        $full_address = exec("getmac");
//
//        $full_address = strtok($full_address, ' ');
//
//        echo "YOUR MAC address of client is: $full_address";

//        $mac = shell_exec("arp -a ".escapeshellarg($_SERVER['REMOTE_ADDR'])." | grep -o -E '(:xdigit:{1,2}:){5}:xdigit:{1,2}'");
//        echo $mac;
        //       $ipAddress = $_SERVER['REMOTE_ADDR'];
//        $arp=`arp -a $ipAddress`;
//        $output = shell_exec($arp);
        //       echo gethostname();
//        echo $_SERVER['REMOTE_ADDR'];
//        echo php_uname('n');
//        echo "comname=". getenv('COMPUTERNAME') . '<br />';
//        echo exec('getmac');
//        $mac_data = explode("/", exec('getmac'));
//        $comname = getenv('COMPUTERNAME');
//        $mac = $mac_data[0];
//
//        $result = null;
//        @exec ("ifconfig -a", $result);
//        return $result;
//
//        $company_id = \Yii::$app->user->identity->company_id;
//        $branch_id = \Yii::$app->user->identity->branch_id;
//
//        if ($comname != null && $mac != null) {
//            $model = new \common\models\ComputerList();
//            $model->computer_name = $comname;
//            $model->mac_address = $mac;
//            $model->company_id = $company_id;
//            $model->branch_id = $branch_id;
//            if ($model->save(false)) {
//                echo "ok";
//            }
//
//        }
    }

    public
    function actionGrab()
    {

        $aControllers = [];


        // $path = \Yii::$app->getBasePath() . 'icesystem/';
        $path = \Yii::$app->basePath;

        $ctrls = function ($path) use (&$ctrls, &$aControllers) {

            $oIterator = new \DirectoryIterator($path);

            foreach ($oIterator as $oFile) {

                if (!$oFile->isDot()

                    && (false !== strpos($oFile->getPathname(), 'controllers')

                        || false !== strpos($oFile->getPathname(), 'modules')

                    )

                ) {


                    if ($oFile->isDir()) {

                        $ctrls($oFile->getPathname());

                    } else {

                        if (strpos($oFile->getBasename(), 'Controller.php')) {


                            $content = file_get_contents($oFile->getPathname());

                            $controllerName = $oFile->getBasename('.php');


                            $route = explode(\Yii::$app->basePath, $oFile->getPathname());

                            $route = str_ireplace(array('modules', 'controllers', 'Controller.php'), '', $route[1]);

                            $route = preg_replace("/(\/){2,}/", "/", $route);


                            $aControllers[$controllerName] = [

                                'filepath' => $oFile->getPathname(),

                                'route' => mb_strtolower($route),

                                'actions' => [],

                            ];

                            preg_match_all('#function action(.*)\(#ui', $content, $aData);


                            $acts = function ($aData) use (&$aControllers, &$controllerName) {


                                if (!empty($aData) && isset($aData[1]) && !empty($aData[1])) {


                                    $aControllers[$controllerName]['actions'] = array_map(

                                        function ($actionName) {
                                            return mb_strtolower(trim($actionName, '{\\.*()'));
                                        },

                                        $aData[1]

                                    );


                                }

                            };


                            $acts($aData);

                        }

                    }


                }

            }

        };


        $ctrls($path);


        echo '<pre>';

        //   print_r($aControllers);

        foreach ($aControllers as $value) {

            //  $route_name = substr($value['route'],2);
            $route_name = substr($value['route'], 1);
            for ($x = 0; $x <= count($value['actions']) - 1; $x++) {
                $fullname = $route_name . '/' . $value['actions'][$x];
                if ($fullname != '') {
                    $chk = \common\models\AuthItem::find()->where(['name' => $fullname])->one();
                    if ($chk) continue;

                    $model = new \common\models\AuthItem();
                    $model->name = $fullname;
                    $model->type = 2;
                    $model->description = '';
                    $model->created_at = time();
                    $model->save(false);
                }
                echo $fullname . '<br/>';

            }
            //echo $route_name;
            // print_r($value['route']);
        }
        // print_r($aControllers['AdjustmentController']);

    }

    public function actionIndex()
    {
        //exec("D: && cd NodeJs_Project/ && node showcomname.js", $out, $err);
        //  $out = shell_exec("cd ".$path_nodefile." && node -v");
        //print_r($out);
        $ip = '';
//        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
//            //ip from share internet
//            $ip = $_SERVER['HTTP_CLIENT_IP'];
//        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
//            //ip pass from proxy
//            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//        }else{
//            $ip = $_SERVER['REMOTE_ADDR'];
//        }
//        echo 'ip is '.$ip;
        if (\Yii::$app->user->identity->id == null) {
            return $this->redirect(['site/login']);
        }
        //  echo \Yii::$app->user->identity->email;return;
//        $company_id = 1;
//        $branch_id = 1;

        $company_id = \Yii::$app->user->identity->company_id;
        $branch_id = \Yii::$app->user->identity->branch_id;

//        if(\Yii::$app->user->identity->company_id != null){
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if(\Yii::$app->user->identity->company_id != null){
//            $company_id = \Yii::$app->user->identity->branch_id;
//        }

//        if (isset($_SESSION['user_company_id'])) {
//            $company_id = $_SESSION['user_company_id'];
//        }
//        if (isset($_SESSION['user_branch_id'])) {
//            $branch_id = $_SESSION['user_branch_id'];
//        }

        $f_date = null;
        $t_date = null;

        $dash_board = \Yii::$app->request->post('dashboard_date');
        $x_date = explode('-', trim($dash_board));
        if ($x_date != null) {
            if (count($x_date) > 1) {
                $ff_date = $x_date[0];
                $tt_date = $x_date[1];

                $fff_date = explode('/', trim($ff_date));
                if (count($fff_date) > 0) {
                    $f_date = $fff_date[2] . '-' . $fff_date[1] . '-' . $fff_date[0];
                }
                $ttt_date = explode('/', trim($tt_date));
                if (count($ttt_date) > 0) {
                    $t_date = $ttt_date[2] . '-' . $ttt_date[1] . '-' . $ttt_date[0];
                }
            }
        }

//        echo $f_date.' and '.$t_date;return;

        $prod_cnt = \backend\models\Product::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $route_cnt = \backend\models\Deliveryroute::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $car_cnt = \backend\models\Car::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $order_cnt = \backend\models\Orders::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $order_pos_cnt = \backend\models\Orders::find()->where(['sale_channel_id' => 2, 'company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $order_normal_cnt = \backend\models\Orders::find()->where(['sale_channel_id' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->count();
        $order_lastest = \common\models\QuerySaleLastest::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all();


//        $sql = "select sale_channel_type,sum(m1) as m1 ,sum(m2) as m2,sum(m3) as m3,sum(m4) as m4,sum(m5) as m5,sum(m6) as m6,sum(m7) as m7,sum(m8) as m8,sum(m9) as m9,sum(m10) as m10,sum(m11) as m11,sum(m12) as m12 from query_sale_amount_by_sale_type";
//        if ($f_date != null && $t_date != null) {
//            $sql .= " where date(order_date) >='" . date('Y-m-d', strtotime($f_date)) . "' and date(order_date) <='" . date('Y-m-d', strtotime($t_date))."'";
//        }
//        $sql.=" group by sale_channel_type";
////echo $sql;return;
//        $query = \Yii::$app->db->createCommand($sql)->queryAll();
//        $category = ['มค.', 'กพ.', 'มีค.', 'เมษ.', 'พค.', 'มิย.', 'กค', 'สค', 'กย', 'ตค', 'พย', 'ธค'];
//        $data_by_type = [];
//        for ($i = 0; $i <= count($query) - 1; $i++) {
//            array_push($data_by_type, [
//                //  'type' => 'column',
//                'name' => $query[$i]['sale_channel_type'],
//                'data' => [
//                    ($query[$i]['m1'] * 1), ($query[$i]['m2'] * 1), ($query[$i]['m3'] * 1), ($query[$i]['m4'] * 1),
//                    ($query[$i]['m5'] * 1), ($query[$i]['m6'] * 1), ($query[$i]['m7'] * 1), ($query[$i]['m8'] * 1),
//                    ($query[$i]['m9'] * 1), ($query[$i]['m10'] * 1), ($query[$i]['m11'] * 1), ($query[$i]['m12'] * 1)
//                ]
//            ]);
//        }
//
//        $sql2 = "select code,name,sum(total_amount) as total_amount from query_sale_amount_by_product WHERE total_amount > 0";
//        if ($f_date != null && $t_date != null) {
//            $sql2 .= " AND date(order_date) >='" . date('Y-m-d', strtotime($f_date)) . "' and date(order_date) <='" . date('Y-m-d', strtotime($t_date))."'";
//        }
//        $sql2.=" group by code";
////        echo $sql2;
////        return;
//        $query2 = \Yii::$app->db->createCommand($sql2)->queryAll();
//        $data_by_prod_type = [];
//        $data_prod_data = [];
//        for ($i = 0; $i <= count($query2) - 1; $i++) {
//            array_push($data_prod_data, [
//                'name' => $query2[$i]['name'],
//                'y' => (float)$query2[$i]['total_amount'],
//                'selected' => false
//            ]);
//
//        }
//
//        array_push($data_by_prod_type, [
//                'name' => 'ยอดขาย',
//                'data' => $data_prod_data
//            ]
//        );
//        ['name' => 'Test',
//            'data' => [
//                ['name' => 'Chrome',
//                    'y' => 60.0,
//                    'selected' => true,],
//                ['name' => 'IE',
//                    'y' => 69.0,
//                    'selected' => false,]
//            ]
//        ]

        return $this->render('index', [
            'prod_cnt' => $prod_cnt,
            'route_cnt' => $route_cnt,
            'car_cnt' => $car_cnt,
            'order_cnt' => $order_cnt,
            'order_pos_cnt' => $order_pos_cnt,
            'order_normal_cnt' => $order_normal_cnt,
//            'data_by_type' => $data_by_type,
//            'data_by_prod_type' => $data_by_prod_type,
//            'category' => $category,
            'f_date' => $f_date,
            't_date' => $t_date,
            'order_lastest' => $order_lastest,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public
    function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $ip = '';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                //ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                //ip pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $model_info = \backend\models\User::find()->where(['id' => \Yii::$app->user->id])->one(); // check status
            $check_can_access = \backend\models\Computerlist::checkAccess(\Yii::$app->user->id, $ip); // check allow ip and mac

            // echo $ip;return;
            if ($model_info->status != 1) {
                return $this->redirect(['site/logout']);
//                return $this->render('login_new', [
//                    'model' => $model,
//                ]);
            }
//            if ($check_can_access <= 0 || $check_can_access == null) {
//                return $this->redirect(['site/logout']);
////                $model->password = '';
////                $this->layout = 'main_login';
////                $model->password = '';
////                return $this->render('login_new', [
////                    'model' => $model,
////                ]);
//            }

            $_SESSION['user_company_id'] = $model_info->company_id;
            $_SESSION['user_branch_id'] = $model_info->branch_id;
            $_SESSION['user_group_id'] = \backend\models\User::findGroup(\Yii::$app->user->id);


            $model_log = new \common\models\LoginLog();
            $model_log->user_id = \Yii::$app->user->id;
            $model_log->login_date = date('Y-m-d H:i:s');
            $model_log->status = 1;
            $model_log->ip = $ip;
            if ($model_log->save(false)) {
                //  $check_is_login = \common\models\LoginLogCal::find()->where(['user_id'=>$model_log->user_id,'date(login_date)'=>date('Y-m-d'),'status'=>1])->one();
                $check_is_login = \common\models\LoginLogCal::find()->where(['user_id' => $model_log->user_id, 'status' => 1])->andFilterWhere(['is', 'logout_date', new \yii\db\Expression('null')])->limit(1)->orderBy(['id' => SORT_DESC])->one();
                if (!$check_is_login) {
                    $model_login_cal = new \common\models\LoginLogCal();
                    $model_login_cal->login_date = date('Y-m-d H:i:s');
                    $model_login_cal->user_id = $model_log->user_id;
                    $model_login_cal->status = 1;
                    $model_login_cal->ip = $ip;
                    $model_login_cal->company_id = $model_info->company_id;
                    $model_login_cal->branch_id = $model_info->branch_id;
                    if ($model_login_cal->save(false)) {
                        // $update_remain_logout = \common\models\LoginLogCal::find()->where(['user_id'=>$model_log->user_id])->andFilterWhere(['!=','id',$model_login_cal->id]);
                    }
                } else {
                    $date1 = date('Y-m-d H:i:s', strtotime($check_is_login->login_date));//"2021-12-04 01:00:00";
                    $date2 = date('Y-m-d H:i:s');
                    $timestamp1 = strtotime($date1);
                    $timestamp2 = strtotime($date2);
                    $hour = abs($timestamp2 - $timestamp1) / (60 * 60);
                    //echo "Difference between two dates is " . $hour = abs($timestamp2 - $timestamp1)/(60*60) . " hour(s)";

                    // return;
                    if ($hour > 12) { // login over 12 hours.
                        //$check_is_login->login_date = date('Y-m-d H:i:s');
                        $check_is_login->logout_date = date('Y-m-d H:i:s');
                        $check_is_login->ip = $ip;
                        if ($check_is_login->save(false)) {
                            $model_login_cal = new \common\models\LoginLogCal();
                            $model_login_cal->login_date = date('Y-m-d H:i:s', strtotime($model_log->login_date));
                            $model_login_cal->user_id = $model_log->user_id;
                            $model_login_cal->status = 1;
                            $model_login_cal->ip = $ip;
                            $model_login_cal->company_id = $model_info->company_id;
                            $model_login_cal->branch_id = $model_info->branch_id;
                            if ($model_login_cal->save(false)) {
                            }
                        }
                    }
//
                }


//                $before_user = $this->getBeforeUser();
//                if($before_user){
//                    $model_logout = \common\models\LoginLog::find()->where(['user_id' => $before_user])->andFilterWhere(['status' => 1])->one();
//                    if ($model_logout) {
//                        $model_logout->logout_date = date('Y-m-d H:i:s');
//                        $model_logout->status = 2;
//                        $model_logout->save(false);
//                    }
//                }
                $_SESSION['user_second_id'] = '';
            }

            //  return $this->goBack();
            return $this->redirect(['site/index']);
        } else {
            $model->password = '';
            $this->layout = 'main_login';
            $model->password = '';
            return $this->render('login_new', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public
    function actionLogout()
    {
        $user_id = \Yii::$app->user->id;
        $model_info = \backend\models\User::find()->where(['id' => \Yii::$app->user->id])->one();
        if (Yii::$app->user->logout()) {
            $c_date = date('Y-m-d');
            $ip = '';
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                //ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                //ip pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
//            $model_logout = \common\models\LoginLog::find()->where(['user_id' => $user_id, 'date(login_date)' => $c_date])->andFilterWhere(['status' => 1])->one();
//            if ($model_logout) {
//                $model_logout->logout_date = date('Y-m-d H:i:s');
//                $model_logout->status = 2;
//                $model_logout->save(false);
//            }

            if (\common\models\LoginLog::updateAll(['logout_date' => date('Y-m-d H:i:s'), 'status' => 2], ['user_id' => $user_id, 'ip' => $ip, 'status' => 1])) {

                //         $check_is_has_login = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id,'status'=>1])->one();
                $check_is_has_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->limit(1)->orderBy(['id' => SORT_DESC])->one();
                if ($check_is_has_login) {

                    $check_is_has_login->logout_date = date('Y-m-d H:i:s');
                    //$check_is_has_login->status = 2;
                    $check_is_has_login->save(false);

//                    $check_is_login = \common\models\LoginLogCal::find()->where(['user_id'=>$user_id,'status'=>2])->limit(1)->orderBy(['id'=>SORT_DESC])->one();
//                    if(!$check_is_login){
//                        $model_login_cal = new \common\models\LoginLogCal();
//                        $model_login_cal->login_date = date('Y-m-d H:i:s');
//                        $model_login_cal->user_id = $user_id;
//                        $model_login_cal->status = 2;
//                        $model_login_cal->ip = $ip;
//                        $model_login_cal->company_id = $model_info->company_id;
//                        $model_login_cal->branch_id = $model_info->branch_id;
//                        $model_login_cal->save(false);
//                    }else{
//                        $check_is_login->login_date = date('Y-m-d H:i:s');
//                        $check_is_login->user_id = $user_id;
//                        $check_is_login->status = 2;
//                        $check_is_login->ip = $ip;
//                        $check_is_login->save(false);
//                    }
                }
            }

        }

        return $this->goHome();
    }

    function getBeforeUser()
    {
        $id = 0;
        $user_id = \backend\models\Orders::find()->where(['status' => 1, 'company_id' => 1, 'branch_id' => 1])->orderBy(['id' => SORT_DESC])->one();
        if ($user_id) {
            $id = $user_id->created_by;
        }
        return $id;
    }

    public
    function actionChangepassword()
    {
        $model = new \backend\models\Resetform();
        if ($model->load(Yii::$app->request->post())) {

            $model_user = \backend\models\User::find()->where(['id' => Yii::$app->user->id])->one();
            if ($model->oldpw != '' && $model->newpw != '' && $model->confirmpw != '') {
                if ($model->confirmpw != $model->newpw) {
                    $session = Yii::$app->session;
                    $session->setFlash('msg_err', 'รหัสยืนยันไม่ตรงกับรหัสใหม่');
                } else {
                    if ($model_user->validatePassword($model->oldpw)) {
                        $model_user->setPassword($model->confirmpw);
                        if ($model_user->save()) {
                            $session = Yii::$app->session;
                            $session->setFlash('msg_success', 'ทำการเปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
                            return $this->redirect(['site_/logout']);
                        }
                    } else {
                        $session = Yii::$app->session;
                        $session->setFlash('msg_err', 'รหัสผ่านเดิมไม่ถูกต้อง');
                    }
                }

            } else {
                $session = Yii::$app->session;
                $session->setFlash('msg_err', 'กรุณาป้อนข้อมูลให้ครบ');
            }

        }
        return $this->render('_setpassword', [
            'model' => $model
        ]);
    }

    public
    function actionForgetpassword()
    {

    }

    public
    function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $is_sendmail = $this->sendReset($model->email);
            //if ($model->sendEmail()) {
            if ($is_sendmail) {
                Yii::$app->session->setFlash('success', 'ตรวจสอบข้อความและดำเนินการต่อที่ Inbox Email ของคุณต่อได้เลย.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'พบข้อผิดพลาด, ทางเราไม่สามารถส่งข้อมูลไปยัง Email ที่ระบุ.');
            }
        }

        $this->layout = 'main_page';

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public
    function sendReset($email)
    {
        $is_send = 0;

        $user = \common\models\User::findOne([
            'status' => \common\models\User::STATUS_ACTIVE,
            'email' => $email,
        ]);

        if (!$user) {
            return false;
        }

        if (!\common\models\User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }
        $token = $user->password_reset_token;

        $mesg = 'สวัสดี คุณ' . 'test' . '<br />';
        $mesg = $mesg . 'คุณสามารถดำเนินการเปลี่ยนรหัสผ่านได้ที่ Link ด้านล่างนี้ ' . '<br />';
        $mesg = $mesg . '<p><a href="https://www.ngansorn.com/tutor/site_/reset-password/token/' . $token . '">เปลี่ยนรหัสผ่าน</a> </p>';


        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->isHTML(true);

        /* ------------------------------------------------------------------------------------------------------------- */
        /* ตั้งค่าการส่งอีเมล์ โดยใช้ SMTP ของ Gmail */
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "TLS";                 // sets the prefix to the servier
        $mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 587;                   // set the SMTP port for the GMAIL server
        $mail->Username = "ngansorntutor@gmail.com";  // Gmail Email username ngansorntutor@gmail.com ngansorn98168
        $mail->Password = "tpmcaakvxdibfxwq";            // App Password not Gmail password
        /* ------------------------------------------------------------------------------------------------------------- */

        $mail->setFrom('ngansorntutor@gmail.com', 'Ngansorn.com');
        $mail->AddAddress($email);
        $mail->AddReplyTo('system');
        $mail->Subject = 'ดำเนินการเปลี่ยนรหัสผ่าน';
        $mail->Body = $mesg;
        $mail->WordWrap = 50;
//
        if ($mail->Send()) {
            $is_send = 1;
        }
        return $is_send;

    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'รหัสผ่านของคุณได้เปลี่ยนเรียบร้อยแล้ว.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
    public
    function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public
    function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public
    function actionCreateadmin()
    {
        $model = new \common\models\User();
        $model->username = 'omnoiadmin';
        $model->setPassword('Ice123456');
        $model->generateAuthKey();
        $model->email = 'admin@icesystemomnoi.com';
        if ($model->save(false)) {
            \Yii::$app->session->set('login_worker', $model->username);
            echo "ok";
        }

    }

    public
    function actionApiLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return ['status' => false, 'data' => 'Not permission'];
        }

        \Yii::$app->response->format = \Yii\web\Response::FORMAT_JSON;
        $attributes = \Yii::$app->request->post();
        $model = new LoginForm();
        $model->username = $attributes['username'];
        $model->password = $attributes['password'];
        if ($model->login()) {
            return ['status' => true, 'data' => 'login successfully'];
        } else {
            return ['status' => false, 'data' => 'login fail'];
        }
//        $member = \common\models\Member::find()->where(['id'=>$attributes['id']])->one();
//
//        if($member){
//            $member->attributes = \Yii::$app->request->post();
//            $member->save();
//            return ['status'=> true,'data'=>'Record is updated'];
//        }else{
//            return ['status'=> false,'data'=>$member->getErrors()];
//        }
    }

    public
    function actionDecodex()
    {
        $x = "4d9cRXhpZgAATU0AKgAAAAgADgEAAAMAAAABDMAAAAEBAAMAAAABCZAAAAECAAMAAAADAAAA9gEPAAIAAAAHAAAAtgEQAAIAAAAIAAAAvgESAAMAAAABAAAAAAEaAAUAAAABAAAAxgEbAAUAAAABAAAAzgEoAAMAAAABAAIAAAExAAIAAAAfAAAA1gEyAAIAAAAUAAAA";
        echo base64_decode($x);
    }

    public
    function actionAddseconduser()
    {
        $id = \Yii::$app->request->post('second_user_id');
        if ($id) {

            $user_id = \Yii::$app->user->id;

            $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_DESC])->one();
            if ($model_login) {
//                $model_login->second_user_id = $id;
//                $model_login->save(false);
                for ($i = 0; $i <= count($id) - 1; $i++) {
                    $model_user_ref = new \common\models\LoginUserRef();
                    $model_user_ref->login_log_cal_id = $model_login->id;
                    $model_user_ref->user_id = $id[$i];
                    $model_user_ref->save(false);
                }
            }
        }
        return $this->redirect(['site/index']);
    }

    function actionTransactionsalecar()
    {
        $c_date = \Yii::$app->request->post('cal_date');
        $x_date = explode('-', $c_date);
        $f_date = date('Y-m-d');


        if (count($x_date) > 0) {
            $f_date = $x_date[0] . '/' . $x_date[1] . '/' . $x_date[2];
        }
        // echo $f_date;return;

        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        $cal_date = date('Y-m-d', strtotime($f_date));
        // $cal_date = date('Y-m-d');

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionCarSale::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);
        \common\models\TransactionCarSaleRoutePay::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);

        $model = \common\models\QuerySaleMobileDataNew::find()->select([
            'route_id',
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
            'SUM(line_total_cash) as line_total_cash',
            'SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_free) as line_qty_free'
        ])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id', 'product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionCarSale();
                $model_trans->trans_date = $cal_date;// date('Y-m-d');
                $model_trans->route_id = $value->route_id;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->cash_amount = $value->line_total_cash;
                $model_trans->credit_amount = $value->line_total_credit;
                $model_trans->free_qty = $value->line_qty_free;
                $model_trans->receive_cash = 0;
                $model_trans->receive_transter = 0;
                $model_trans->return_qty = 0;
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->save(false);
            }
        }

        $model2 = \common\models\QuerySaleMobileDataNew::find()->select(['route_id'])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id'])->all();
        if ($model2) {
            foreach ($model2 as $value) {
                $model_pay = new \common\models\TransactionCarSaleRoutePay();
                $model_pay->route_id = $value->route_id;
                $model_pay->trans_date = $cal_date;
                $model_pay->cash_amount = $this->getPaymentCash($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->transfer_amount = $this->getPaymentTransfer($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->return_car_qty = $this->getReturnCar($value->route_id, $cal_date);
                $model_pay->company_id = $company_id;
                $model_pay->branch_id = $branch_id;
                $model_pay->save(false);
            }
        }


        echo "success";
    }

    function actionTransactionsalecar2()
    {

        $company_id = 1;
        $branch_id = 1;

        $cal_date = date('Y-m-d');
        // $cal_date = date('Y-m-d');

        $started = microtime(true);

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionCarSale::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);
        \common\models\TransactionCarSaleRoutePay::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);

        $model = \common\models\QuerySaleMobileDataNew::find()->select([
            'route_id',
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
            'SUM(line_total_cash) as line_total_cash',
            'SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_free) as line_qty_free'
        ])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id', 'product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionCarSale();
                $model_trans->trans_date = date('Y-m-d H:i:s');
                $model_trans->route_id = $value->route_id;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->cash_amount = $value->line_total_cash;
                $model_trans->credit_amount = $value->line_total_credit;
                $model_trans->free_qty = $value->line_qty_free;
                $model_trans->receive_cash = 0;
                $model_trans->receive_transter = 0;
                $model_trans->return_qty = 0;
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->save(false);
            }
        }

        $model2 = \common\models\QuerySaleMobileDataNew::find()->select(['route_id'])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id'])->all();
        if ($model2) {
            foreach ($model2 as $value) {
                $model_pay = new \common\models\TransactionCarSaleRoutePay();
                $model_pay->route_id = $value->route_id;
                $model_pay->trans_date = $cal_date;
                $model_pay->cash_amount = $this->getPaymentCash($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->transfer_amount = $this->getPaymentTransfer($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->return_car_qty = $this->getReturnCar($value->route_id, $cal_date);
                $model_pay->company_id = $company_id;
                $model_pay->branch_id = $branch_id;
                $model_pay->save(false);
            }
        }
        $end = microtime(true);
        $total_time_use = $end - $started;
        $this->notifymessageorderclose(1, 1, number_format($total_time_use, 4));
        //  echo "success";

        $this->calTransactionsalecar3(); // cal branch 2
    }

    function calTransactionsalecar3()
    {

        $company_id = 1;
        $branch_id = 2;

        $cal_date = date('Y-m-d');
        // $cal_date = date('Y-m-d');

        $started = microtime(true);

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionCarSale::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);
        \common\models\TransactionCarSaleRoutePay::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);

        $model = \common\models\QuerySaleMobileDataNew::find()->select([
            'route_id',
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
            'SUM(line_total_cash) as line_total_cash',
            'SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_free) as line_qty_free'
        ])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id', 'product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionCarSale();
                $model_trans->trans_date = date('Y-m-d H:i:s');
                $model_trans->route_id = $value->route_id;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->cash_amount = $value->line_total_cash;
                $model_trans->credit_amount = $value->line_total_credit;
                $model_trans->free_qty = $value->line_qty_free;
                $model_trans->receive_cash = 0;
                $model_trans->receive_transter = 0;
                $model_trans->return_qty = 0;
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->save(false);
            }
        }

        $model2 = \common\models\QuerySaleMobileDataNew::find()->select(['route_id'])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['route_id'])->all();
        if ($model2) {
            foreach ($model2 as $value) {
                $model_pay = new \common\models\TransactionCarSaleRoutePay();
                $model_pay->route_id = $value->route_id;
                $model_pay->trans_date = $cal_date;
                $model_pay->cash_amount = $this->getPaymentCash($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->transfer_amount = $this->getPaymentTransfer($value->route_id, $company_id, $branch_id, $cal_date);
                $model_pay->return_car_qty = $this->getReturnCar($value->route_id, $cal_date);
                $model_pay->company_id = $company_id;
                $model_pay->branch_id = $branch_id;
                $model_pay->save(false);
            }
        }
        $end = microtime(true);
        $total_time_use = $end - $started;
        $this->notifymessageorderclose(1, 2, number_format($total_time_use, 4));
    }


    function actionCaltransactionsaledistributor($caldate)
    {

        $company_id = 1;
        $branch_id = 2;
        $cal_date = date('Y-m-d');
        $xdate = explode('-',$caldate);

        // $create_date = date_create('2024-02-20');

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        $findcaldate = date('Y-m-d');
        if(count($xdate) >1){
            $findcaldate = $xdate[0].'/'.$xdate[1].'/'.$xdate[2].' '.'00:01:01';
            $cal_date = date('Y-m-d',strtotime($findcaldate));
        }
       // print_r($xdate);return;
        $started = microtime(true);

//        echo $cal_date;return;

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionDistributorSale::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);
        \common\models\TransactionDistributorSalePay::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);

        $model = \common\models\QuerySaleDistributorData::find()->select([
            'customer_id',
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
            'SUM(line_total_cash) as line_total_cash',
            'SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_free) as line_qty_free'
        ])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['customer_id', 'product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionDistributorSale();
                $model_trans->trans_date = date('Y-m-d H:i:s',strtotime($findcaldate));
                $model_trans->customer_id = $value->customer_id;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->cash_amount = $value->line_total_cash;
                $model_trans->credit_amount = $value->line_total_credit;
                $model_trans->free_qty = $value->line_qty_free;
//                $model_trans->receive_cash = 0;
//                $model_trans->receive_transter = 0;
//                $model_trans->return_qty = 0;
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->save(false);
            }
        }

        $model2 = \common\models\QuerySaleByDistributor::find()->select(['customer_id'])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['customer_id'])->all();
        if ($model2) {
            foreach ($model2 as $value) {
                $model_pay = new \common\models\TransactionDistributorSalePay();
                $model_pay->customer_id = $value->customer_id;
                $model_pay->trans_date = $cal_date;
                $model_pay->cash_amount = $this->getPaymentDistributorCash($value->customer_id, $company_id, $branch_id, $cal_date);
                $model_pay->transfer_amount = $this->getPaymentDistributorTransfer($value->customer_id, $company_id, $branch_id, $cal_date);
                $model_pay->return_car_qty = 0; //$this->getReturnCar($value->customer_id, $cal_date);
                $model_pay->company_id = $company_id;
                $model_pay->branch_id = $branch_id;
                $model_pay->save(false);
            }
        }
        $end = microtime(true);
        $total_time_use = $end - $started;
        $this->notifymessageorderclose(1, 2, number_format($total_time_use, 4));
    }
    function actionCaltransactionsaledistributorauto()
    {

        $company_id = 1;
        $branch_id = 2;

        $t_date = date('Y-m-d',strtotime('-1 day'));

        $cal_date = date('Y-m-d');
        $xdate = explode('-',$t_date);

        // $create_date = date_create('2024-02-20');

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        $findcaldate = date('Y-m-d');
        if(count($xdate) >1){
            $findcaldate = $xdate[0].'/'.$xdate[1].'/'.$xdate[2].' '.'00:01:01';
            $cal_date = date('Y-m-d',strtotime($findcaldate));
        }
        // print_r($xdate);return;
        $started = microtime(true);

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        \common\models\TransactionDistributorSale::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);
        \common\models\TransactionDistributorSalePay::deleteAll(['date(trans_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id]);

        $model = \common\models\QuerySaleDistributorData::find()->select([
            'customer_id',
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
            'SUM(line_total_cash) as line_total_cash',
            'SUM(line_total_credit) as line_total_credit',
            'SUM(line_qty_free) as line_qty_free'
        ])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['customer_id', 'product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionDistributorSale();
                $model_trans->trans_date = date('Y-m-d H:i:s',strtotime($findcaldate));
                $model_trans->customer_id = $value->customer_id;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->cash_amount = $value->line_total_cash;
                $model_trans->credit_amount = $value->line_total_credit;
                $model_trans->free_qty = $value->line_qty_free;
//                $model_trans->receive_cash = 0;
//                $model_trans->receive_transter = 0;
//                $model_trans->return_qty = 0;
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->save(false);
            }
        }

        $model2 = \common\models\QuerySaleByDistributor::find()->select(['customer_id'])->where(['date(order_date)' => $cal_date, 'company_id' => $company_id, 'branch_id' => $branch_id])->groupBy(['customer_id'])->all();
        if ($model2) {
            foreach ($model2 as $value) {
                $model_pay = new \common\models\TransactionDistributorSalePay();
                $model_pay->customer_id = $value->customer_id;
                $model_pay->trans_date = $cal_date;
                $model_pay->cash_amount = $this->getPaymentDistributorCash($value->customer_id, $company_id, $branch_id, $cal_date);
                $model_pay->transfer_amount = $this->getPaymentDistributorTransfer($value->customer_id, $company_id, $branch_id, $cal_date);
                $model_pay->return_car_qty = 0; //$this->getReturnCar($value->customer_id, $cal_date);
                $model_pay->company_id = $company_id;
                $model_pay->branch_id = $branch_id;
                $model_pay->save(false);
            }
        }
        $end = microtime(true);
        $total_time_use = $end - $started;
        $this->notifymessageorderclose(1, 2, number_format($total_time_use, 4));
    }

    public function actionStartcaldailymanagerauto()
    {
        $company_id = 1;
        $branch_id = 2;
        $caldate = date('Y-m-d',strtotime('-1 day'));
        $xdate = explode('-',$caldate);

        // $create_date = date_create('2024-02-20');

        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');

        $findcaldate = date('Y-m-d');
        if(count($xdate) >1){
            $findcaldate = $xdate[0].'/'.$xdate[1].'/'.$xdate[2].' '.'00:01:01';
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

    public function notifymessageorderclose($company_id, $branch_id, $timeuse)
    {
        //$message = "This is test send request from camel paperless";
        $line_api = 'https://notify-api.line.me/api/notify';
        $line_token = '';

        //   6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE   token omnoi
        if ($company_id == 1 && $branch_id == 1) {
            // $line_token = 'ZMqo4ZqwBGafMOXKVht2Liq9dCGswp4IRofT2EbdRNN'; // vorapat
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            //   $line_token = '6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE'; // omnoi
            $line_token = trim($b_token);
        } else if ($company_id == 1 && $branch_id == 2) {
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            $line_token = trim($b_token);
            //   $line_token = 'TxAUAOScIROaBexBWXaYrVcbjBItIKUwGzFpoFy3Jrx'; // BKT
        }


        $message = '' . "\n";
        $message .= 'แจ้งประมวลผลรายงานทุก 23:50:' . "\n";
        $message .= "ประมวลผลรายงานขายสายส่ง วันที่: " . date('Y-m-d') . "(" . date('H:i') . ")" . "\n";
        $message .= "ใช้เวลาประมวลผล : " . $timeuse . " วินาที" . "\n";

        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData, '', '&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Authorization: Bearer " . $line_token . "\r\n"
                    . "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData
            )
        );
        $context = stream_context_create($headerOptions);
        $result = file_get_contents($line_api, FALSE, $context);
        $res = json_decode($result);
        return $res;
    }


    function getPaymentCash($route_id, $company_id, $branch_id, $cal_date)
    {
        $amount = 0;

//        $sql = "SELECT SUM(payment_amount) as amount  from query_payment_receive
//              WHERE date(trans_datex) = " . "'" . $cal_date . "'" . "
//              AND status = 1
//              AND payment_channel_id=1 AND payment_method_id = 2 AND  route_id =" . $route_id . "
//              AND company_id=" . $company_id . " AND branch_id=" . $branch_id;
//        $sql .= " GROUP BY route_id";
//
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();

        $model = \common\models\QueryPaymentReceive::find()->where(['date(trans_date)' => $cal_date, 'status' => 1, 'payment_channel_id' => 1, 'payment_method_id' => 2, 'route_id' => $route_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->sum('payment_amount');
        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//                $amount = $model[$i]['amount'];
//            }
            $amount = $model;

        }
        return $amount;
    }

    function getPaymentTransfer($route_id, $company_id, $branch_id, $cal_date)
    {
        $amount = 0;

        $sql = "SELECT SUM(t1.payment_amount) as amount  from query_payment_receive as t1 INNER JOIN customer as t2 on t2.id = t1.customer_id 
              WHERE date(t1.trans_date) = " . "'" . $cal_date . "'" . " 
              AND t1.status <> 100 
              AND t1.payment_channel_id=2 AND t1.payment_method_id = 2 AND  t2.delivery_route_id =" . $route_id . "
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;
        $sql .= " GROUP BY t1.route_id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $amount = $model[$i]['amount'];
            }
        }
        return $amount;
    }


    function getPaymentDistributorCash($customer_id, $company_id, $branch_id, $cal_date)
    {
        $amount = 0;

//        $sql = "SELECT SUM(payment_amount) as amount  from query_payment_receive
//              WHERE date(trans_datex) = " . "'" . $cal_date . "'" . "
//              AND status = 1
//              AND payment_channel_id=1 AND payment_method_id = 2 AND  route_id =" . $route_id . "
//              AND company_id=" . $company_id . " AND branch_id=" . $branch_id;
//        $sql .= " GROUP BY route_id";
//
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();

        $model = \common\models\QueryPaymentReceive::find()->where(['date(trans_date)' => $cal_date, 'status' => 1, 'payment_channel_id' => 1, 'payment_method_id' => 2, 'customer_id' => $customer_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->sum('payment_amount');
        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//                $amount = $model[$i]['amount'];
//            }
            $amount = $model;

        }
        return $amount;
    }

    function getPaymentDistributorTransfer($customer_id, $company_id, $branch_id, $cal_date)
    {
        $amount = 0;

        $sql = "SELECT SUM(t1.payment_amount) as amount  from query_payment_receive as t1 INNER JOIN customer as t2 on t2.id = t1.customer_id 
              WHERE date(t1.trans_date) = " . "'" . $cal_date . "'" . " 
              AND t1.status <> 100 
              AND t1.payment_channel_id=2 AND t1.payment_method_id = 2 AND  t1.customer_id =" . $customer_id . "
              AND t1.company_id=" . $company_id . " AND t1.branch_id=" . $branch_id;
        $sql .= " GROUP BY t2.id";

        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $amount = $model[$i]['amount'];
            }
        }
        return $amount;
    }

//    function actionTransactionsalepos(){
//        $company_id = 0;
//        $branch_id = 0;
//        if (!empty(\Yii::$app->user->identity->company_id)) {
//            $company_id = \Yii::$app->user->identity->company_id;
//        }
//        if (!empty(\Yii::$app->user->identity->branch_id)) {
//            $branch_id = \Yii::$app->user->identity->branch_id;
//        }
//
//
//
//        $model = \common\models\QuerySalePosData::find()->select([
//            'customer_id',
//            'product_id',
//            'SUM(line_qty_cash) as line_qty_cash',
//            'SUM(line_qty_credit) as line_qty_credit',
//            'SUM(line_total_cash) as line_total_cash',
//            'SUM(line_total_credit) as line_total_credit'
//        ])->where(['date(order_date)'=>date('Y-m-d')])->groupBy(['customer_id','product_id'])->all();
//        if($model){
//            foreach ($model as $value){
//                $model_trans = new \common\models\TransactionPosSale();
//                $model_trans->trans_date = date('Y-m-d');
//                $model_trans->customer_id = $value->customer_id;
//                $model_trans->product_id = $value->product_id;
//                $model_trans->cash_qty = $value->line_qty_cash;
//                $model_trans->credit_qty = $value->line_qty_credit;
//                $model_trans->cash_amount = $value->line_total_cash;
//                $model_trans->credit_amount = $value->line_total_credit;
//                $model_trans->company_id = $company_id;
//                $model_trans->branch_id = $branch_id;
//                $model_trans->save(false);
//            }
//        }
//    }
    function getReturnCar($route_id, $cal_date)
    {
        $issue_qty = 0;

        $sql = "SELECT SUM(t1.qty) as qty";
        $sql .= " FROM stock_trans as t1 ";
        $sql .= " WHERE t1.activity_type_id=7";
        $sql .= " AND date(t1.trans_date) =" . "'" . $cal_date . "'" . " ";
        $sql .= " AND t1.trans_ref_id=" . $route_id;


        $sql .= " GROUP BY t1.trans_ref_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $issue_qty = $model[$i]['qty'];
            }
        }
        return $issue_qty;
    }


    function actionTransactionsalepos()
    {
        $company_id = 0;
        $branch_id = 0;
        if (!empty(\Yii::$app->user->identity->company_id)) {
            $company_id = \Yii::$app->user->identity->company_id;
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $branch_id = \Yii::$app->user->identity->branch_id;
        }

        // $cal_date = date('Y-m-d',strtotime("2022/06/22"));
        $cal_date = date('Y-m-d');

        //\common\models\TransactionCarSale::deleteAll(['date(trans_date)'=>date('Y-m-d')]);
        // \common\models\TransactionPosSaleSum::deleteAll(['date(trans_date)' => $cal_date]);

        $user_login_datetime = date('Y-m-d H:i:s', strtotime('2022-06-25 07:30:39'));


        $model = \common\models\QuerySalePosData::find()->select([
            'product_id',
            'SUM(line_qty_cash) as line_qty_cash',
            'SUM(line_qty_credit) as line_qty_credit',
        ])->where(['date(order_date)' => $cal_date])->groupBy(['product_id'])->all();
        if ($model) {
            foreach ($model as $value) {
                $model_trans = new \common\models\TransactionPosSaleSum();
                $model_trans->trans_date = $cal_date;
                $model_trans->product_id = $value->product_id;
                $model_trans->cash_qty = $value->line_qty_cash;
                $model_trans->credit_qty = $value->line_qty_credit;
                $model_trans->free_qty = 0;
                $model_trans->balance_in_qty = $this->getBalancein($value->product_id, $user_login_datetime, $cal_date, $company_id, $branch_id);
                $model_trans->balance_out_qty = 0;
                $model_trans->prodrec_qty = $this->getProdDaily($value->product_id, $user_login_datetime, date('Y-m-d H:i:s'), $company_id, $branch_id, 123);
                $model_trans->reprocess_qty = $this->getProdRepackDaily($value->product_id, $user_login_datetime, $cal_date);
                $model_trans->return_qty = $this->getReturnCarPos($value->product_id, $cal_date);
                $model_trans->issue_car_qty = $this->getIssuecar($value->product_id, $cal_date);
                $model_trans->issue_transfer_qty = $this->getTransferout($value->product_id, $cal_date);
                $model_trans->issue_refill_qty = $this->getIssueRefillDaily($value->product_id, $user_login_datetime, $cal_date);
                $model_trans->scrap_qty = $this->getScrapDaily($value->product_id, $user_login_datetime, $cal_date);
                $model_trans->counting_qty = $this->getDailycount($value->product_id, $company_id, $branch_id, $cal_date);
                $model_trans->shift = $this->checkDailyShift($cal_date);
                $model_trans->company_id = $company_id;
                $model_trans->branch_id = $branch_id;
                $model_trans->user_id = 0;
                $model_trans->login_datetime = date('Y-m-d H:i:s');
                $model_trans->save(false);
            }
        }
    }

    function getIssuecar($product_id, $order_date)
    {
        $issue_qty = 0;

        $sql = "SELECT SUM(t2.qty) as qty";
        $sql .= " FROM journal_issue as t1 INNER JOIN journal_issue_line as t2 ON t2.issue_id = t1.id";
        $sql .= " WHERE t2.product_id =" . $product_id;
        $sql .= " AND t1.status=2";
        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";

        $sql .= " GROUP BY t2.product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $issue_qty = $model[$i]['qty'];
            }
        }
        return $issue_qty;
    }

    function getTransferout($product_id, $order_date)
    {
        $issue_qty = 0;

        $sql = "SELECT SUM(t2.qty) as qty";
        $sql .= " FROM journal_transfer as t1 INNER JOIN transfer_line as t2 ON t2.transfer_id=t1.id";
        $sql .= " WHERE  t2.product_id =" . $product_id;

        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";

        $sql .= " GROUP BY t2.product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $issue_qty = $model[$i]['qty'];
            }
        }
        return $issue_qty;
    }

    public function checkDailyShift($cal_date)
    {
        $nums = 1;
        $model_num = \common\models\TransactionPosSaleSum::find()->where(['date(trans_date)' => $cal_date])->max('shift');
        if ($model_num) {
            $nums = $model_num;
        }
        return $nums;
    }

    function getProdDaily($product_id, $user_login_datetime, $t_date, $company_id, $branch_id, $user_id)
    {
        $qty = 0;
        $cancel_qty = 0;
        $second_user_id = [];
        //  if ($product_id != null) {

        $model_login = \common\models\LoginLogCal::find()->where(['user_id' => $user_id])->orderBy(['id' => SORT_DESC])->one();
        if ($model_login) {

            //  $second_user_id = $model_login->second_user_id;
            $model_user_ref = \common\models\LoginUserRef::find()->select('user_id')->where(['login_log_cal_id' => $model_login->id])->all();
            if ($model_user_ref) {
                foreach ($model_user_ref as $value) {
                    array_push($second_user_id, $value->user_id);
                }
            }
        }

        if (count($second_user_id) > 0) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $second_user_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s', strtotime($t_date))]])->sum('qty');
        } else {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['and', ['>=', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime))], ['<=', 'trans_date', date('Y-m-d H:i:s', strtotime($t_date))]])->sum('qty');
        }

        //  }

//        $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 15, 'production_type' => 1, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['date(trans_date)'=>date('Y-m-d')])->sum('qty');
//        // $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id'=>$company_id,'branch_id'=>$branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
//        $cancel_qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 28, 'production_type' => 28, 'company_id' => $company_id, 'branch_id' => $branch_id])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['date(trans_date)'=>date('Y-m-d')])->sum('qty');


        return $qty - $cancel_qty; // ลบยอดยกเลิกผลิต
        //return $cancel_qty; // ลบยอดยกเลิกผลิต
    }

    function getBalancein($product_id, $user_login_datetime, $t_date, $company_id, $branch_id)
    {
        $qty = 0;
        if ($product_id != null) {
            $model = \common\models\BalanceDaily::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
            if ($model) {
                $qty = $model->balance_qty;
            }
        }

        return $qty;
    }

    function getProdTransferDaily($product_id, $user_login_datetime, $t_date)
    {
        $qty = 0;
        if ($product_id != null) {
            //  $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
            $qty = \backend\models\Stocktrans::find()->where(['production_type' => 5])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
        }

        return $qty;
    }

    function getIssueRefillDaily($product_id, $user_login_datetime, $t_date)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 18])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
        }

        return $qty;
    }

    function getIssueReprocessDaily($product_id, $user_login_datetime, $t_date, $default_wh)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['activity_type_id' => 20])->andFilterWhere(['product_id' => $product_id, 'warehouse_id' => $default_wh])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
        }

        return $qty;
    }

    function getScrapDaily($product_id, $user_login_datetime, $t_date)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Scrap::find()->join('inner join', 'scrap_line', 'scrap_line.scrap_id = scrap.id')->where(['scrap_line.product_id' => $product_id])->andFilterWhere(['between', 'scrap.trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('scrap_line.qty');
        }

        return $qty;
    }

    function getDailycount($product_id, $company_id, $branch_id, $t_date)
    {
        $qty = 0;
        if ($product_id != null && $company_id != null && $branch_id != null) {
            $model = \common\models\DailyCountStock::find()->where(['product_id' => $product_id, 'company_id' => $company_id, 'branch_id' => $branch_id, 'status' => 0])->andFilterWhere(['date(trans_date)' => date('Y-m-d', strtotime($t_date))])->all();
            if ($model) {
                foreach ($model as $value) {
                    $qty += $value->qty;
                }

            }
        }
        return $qty;
    }

    function getProdRepackDaily($product_id, $user_login_datetime, $t_date)
    {
        $qty = 0;
        if ($product_id != null) {
            $qty = \backend\models\Stocktrans::find()->where(['in', 'activity_type_id', [26, 27]])->andFilterWhere(['product_id' => $product_id])->andFilterWhere(['between', 'trans_date', date('Y-m-d H:i:s', strtotime($user_login_datetime)), date('Y-m-d H:i:s', strtotime($t_date))])->sum('qty');
        }

        return $qty;
    }

    function getReturnCarPos($product_id, $order_date)
    {
        $issue_qty = 0;
        $sql = "SELECT SUM(t1.qty) as qty";
        $sql .= " FROM stock_trans as t1 ";
        $sql .= " WHERE  t1.product_id =" . $product_id;
        $sql .= " AND t1.activity_type_id=7";
        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";

        $sql .= " GROUP BY t1.product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $issue_qty = $model[$i]['qty'];
            }
        }
        return $issue_qty;
    }

//    function getTransferout($route_id, $product_id, $order_date, $user_id)
//    {
//        $issue_qty = 0;
//
//        $sql = "SELECT SUM(t2.qty) as qty";
//        $sql .= " FROM journal_transfer as t1 INNER JOIN transfer_line as t2 ON t2.transfer_id=t1.id";
//        $sql .= " WHERE  t2.product_id =" . $product_id;
//        if ($route_id != null) {
//            $sql .= " AND t1.from_route_id=" . $route_id;
//        }
//        if ($user_id != null) {
//            $sql .= " AND t1.created_by=" . $user_id;
//        }
//
//        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
//
//        $sql .= " GROUP BY t2.product_id";
//        $query = \Yii::$app->db->createCommand($sql);
//        $model = $query->queryAll();
//        if ($model) {
//            for ($i = 0; $i <= count($model) - 1; $i++) {
//                $issue_qty = $model[$i]['qty'];
//            }
//        }
//        return $issue_qty;
//    }


    public function actionCreatescreenshort()
    {
        $img = \Yii::$app->request->post('img');//getting post img data
        //$img = explode(",",trim($img));//converting the data
        // $target=time().'img.png';//making file name

        $imgx = str_replace(['data:image/png;base64,', ' '], ['', '+'], $img);
        //  file_put_contents('../../web/uploads/'.$target, base64_decode($img));

        if ($imgx != null) {
            $newfile = time() . ".png";
            $outputfile = '../web/uploads/assetcheck/' . $newfile;          //save as image.jpg in uploads/ folder

            $filehandler = fopen($outputfile, 'wb');
            //file open with "w" mode treat as text file
            //file open with "wb" mode treat as binary file

            fwrite($filehandler, base64_decode(trim($imgx)));
            // we could add validation here with ensuring count($data)>1

            // clean up the file resource
            fclose($filehandler);
            // file_put_contents($newfile,base64_decode($base64_string));
            // $newfile = base64_decode($base64_string);
        }
        echo "ok";
    }

    public function actionUpdateroute()
    {
        $cal_date = date('Y-m-d', strtotime("2022/06/26"));
        \backend\models\Orders::updateAll(['emp_1' => 17], ['order_channel_id' => 946, 'sale_from_mobile' => 1, 'date(order_date)' => $cal_date]);
        echo "ok";
    }

    public function actionTestclosesum(){
        $product_id = 1;
        $qty = 1;
        $company_id = 1;
        $branch_id = 2;
        $route_id = 900;

        if ($product_id != null && $qty > 0 && $company_id != null && $branch_id != null) {
            $order_shift = \common\models\OrderStock::find()->where(['route_id' => $route_id, 'date(trans_date)' => date('Y-m-d')])->andFilterWhere(['>','issue_id',0])->groupBy(['issue_id'])->count('issue_id');
            $model = \common\models\SaleRouteDailyClose::find()->where(['product_id' => $product_id, 'route_id' => $route_id, 'date(trans_date)' => date('Y-m-d'), 'order_shift' => $order_shift])->one();
            if ($model) {
                echo "ok";
//                $model->qty = $qty;
//                $model->trans_date = date('Y-m-d H:i:s');
//                $model->save(false);
            } else {
                echo "no";
//                $model_new = new \common\models\SaleRouteDailyClose();
//                $model_new->trans_date = date('Y-m-d H:i:s');
//                $model_new->route_id = $route_id;
//                $model_new->product_id = $product_id;
//                $model_new->qty = $qty;
//                $model_new->company_id = $company_id;
//                $model_new->branch_id = $branch_id;
//                $model_new->order_shift = $order_shift;
//                $model_new->crated_by = $user_id;
//                $model_new->save(false);
            }
        }
    }
    public function actionUpdateorderpayment(){
        $model = \common\models\QueryOrderWaitForUpdatePayment::find()->where(['date(trans_date)'=>date('Y-m-d')])->limit(30)->all();
        $res = 0;
        $company_id = 0;
        $branch_id = 0;
        if($model){
            foreach($model as $value){
                $company_id = $value->company_id;
                $branch_id = $value->branch_id;
                $modelorder = \common\models\Orders::find()->where(['id'=>$value->order_id,'payment_status'=>0])->one();
                if($modelorder){
                    $modelorder->payment_status = 1;
                    $modelorder->save(false);
                    $res+=1;
                }
            }
        }
        if($res > 0){
         //   $this->notifymessageorderupdatepayment($company_id,$branch_id,1);
        }
    }
    public function notifymessageorderupdatepayment($company_id, $branch_id, $timeuse)
    {
        //$message = "This is test send request from camel paperless";
        $line_api = 'https://notify-api.line.me/api/notify';
        $line_token = '';

        //   6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE   token omnoi
        if ($company_id == 1 && $branch_id == 1) {
            // $line_token = 'ZMqo4ZqwBGafMOXKVht2Liq9dCGswp4IRofT2EbdRNN'; // vorapat
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            //   $line_token = '6kL3UlbKb1djsoGE7KFXSo9SQ1ikYb2MxmTHDeEy3GE'; // omnoi
            $line_token = trim($b_token);
        } else if ($company_id == 1 && $branch_id == 2) {
            $b_token = \backend\models\Branch::findLintoken($company_id, $branch_id);
            $line_token = trim($b_token);
            //   $line_token = 'TxAUAOScIROaBexBWXaYrVcbjBItIKUwGzFpoFy3Jrx'; // BKT
        }


        $message = '' . "\n";
        $message .= 'แจ้งประมวลอัพเดทวางบิลทุก 23:50:' . "\n";
        $message .= "ประมวลผลรายงานวันที่: " . date('Y-m-d') . "(" . date('H:i') . ")" . "\n";
        $message .= "ใช้เวลาประมวลผล : " . $timeuse . " วินาที" . "\n";

        $queryData = array('message' => $message);
        $queryData = http_build_query($queryData, '', '&');
        $headerOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Authorization: Bearer " . $line_token . "\r\n"
                    . "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData
            )
        );
        $context = stream_context_create($headerOptions);
        $result = file_get_contents($line_api, FALSE, $context);
        $res = json_decode($result);
        return $res;
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

    public function actionSummarybystdgroup()
    {
        return $this->render('_summarybystdpricegroup');
    }
}

 