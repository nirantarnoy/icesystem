<?php
date_default_timezone_set('Asia/Bangkok');

use chillerlan\QRCode\QRCode;
use common\models\LoginLog;
use kartik\daterange\DateRangePicker;
use yii\web\Response;

//require_once __DIR__ . '/vendor/autoload.php';
//require_once 'vendor/autoload.php';
// เพิ่ม Font ให้กับ mPDF


$company_id = 0;
$branch_id = 0;

if (!empty(\Yii::$app->user->identity->company_id)) {
    $company_id = \Yii::$app->user->identity->company_id;
}
if (!empty(\Yii::$app->user->identity->branch_id)) {
    $branch_id = \Yii::$app->user->identity->branch_id;
}

$user_id = \Yii::$app->user->id;

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];
$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp',
//$mpdf = new \Mpdf\Mpdf([
    //'tempDir' => '/tmp',
    'mode' => 'utf-8',
    // 'mode' => 'utf-8', 'format' => [80, 120],
    'fontdata' => $fontData + [
            'sarabun' => [ // ส่วนที่ต้องเป็น lower case ครับ
                'R' => 'THSarabunNew.ttf',
                'I' => 'THSarabunNewItalic.ttf',
                'B' => 'THSarabunNewBold.ttf',
                'BI' => "THSarabunNewBoldItalic.ttf",
            ]
        ],
]);

//$mpdf->SetMargins(-10, 1, 1);
//$mpdf->SetDisplayMode('fullpage');
$mpdf->AddPageByArray([
    'margin-left' => 5,
    'margin-right' => 0,
    'margin-top' => 0,
    'margin-bottom' => 1,
]);

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta content="utf-8" http-equiv="encoding">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print</title>
        <link href="https://fonts.googleapis.com/css?family=Sarabun&display=swap" rel="stylesheet">
        <style>
            /*body {*/
            /*    font-family: sarabun;*/
            /*    !*font-family: garuda;*!*/
            /*    font-size: 18px;*/
            /*}*/

            #div1 {
                font-family: sarabun;
                /*font-family: garuda;*/
                font-size: 18px;
            }

            table.table-header {
                border: 0px;
                border-spacing: 1px;
            }

            table.table-footer {
                border: 0px;
                border-spacing: 0px;
            }

            table.table-header td, th {
                border: 0px solid #dddddd;
                text-align: left;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            table.table-title {
                border: 0px;
                border-spacing: 0px;
            }

            table.table-title td, th {
                border: 0px solid #dddddd;
                text-align: left;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                /*background-color: #dddddd;*/
            }

            table.table-detail {
                border-collapse: collapse;
                width: 100%;
            }

            table.table-detail td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 2px;
            }

        </style>
        <!--    <script src="vendor/jquery/jquery.min.js"></script>-->
        <!--    <script type="text/javascript" src="js/ThaiBath-master/thaibath.js"></script>-->
    </head>
    <div id="div1">

        <form action="<?= \yii\helpers\Url::to(['orderissue/index'], true) ?>" method="post" id="form-search">
            <table class="table-header" style="width: 100%;font-size: 18px;" border="0">
                <tr>

                    <td style="width: 20%">
                        <label for="">ตั้งแต่วันที่</label>
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'from_date',
                            // 'value'=>'2015-10-19 12:00 AM',
                            // 'value' => $from_date != null ? date('Y-m-d H:i', strtotime($from_date)) : date('Y-m-d H:i'),
                            'value' => $from_date != null ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d'),
                            //    'useWithAddon'=>true,
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'ถึงวันที่',
                                //  'onchange' => 'this.form.submit();',
                                'autocomplete' => 'off',
                            ],
                            'pluginOptions' => [
                                'timePicker' => false,
                                'timePickerIncrement' => 1,
                                'locale' => ['format' => 'Y-m-d'],
                                'singleDatePicker' => true,
                                'showDropdowns' => true,
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>
                    <td style="width: 20%">
                        <label for="">ถึงวันที่</label>
                        <?php
                        echo DateRangePicker::widget([
                            'name' => 'to_date',
                            // 'value' => $to_date != null ? date('Y-m-d H:i', strtotime($to_date)) : date('Y-m-d H:i'),
                            'value' => $to_date != null ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d'),
                            //    'useWithAddon'=>true,
                            'convertFormat' => true,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => 'ถึงวันที่',
                                //  'onchange' => 'this.form.submit();',
                                'autocomplete' => 'off',
                            ],
                            'pluginOptions' => [
                                'timePicker' => false,
                                'timePickerIncrement' => 1,
                                'locale' => ['format' => 'Y-m-d'],
                                'singleDatePicker' => true,
                                'showDropdowns' => true,
                                'timePicker24Hour' => true
                            ]
                        ]);
                        ?>
                    </td>
                    <td>
                        <label for="">ประเภทจ่าย</label>
                        <select name="status" class="form-control" id="">
                            <?php
                            $selected0 = '';
                            $selected1 = '';
                            $selected2 = '';
                            if ($status == 0) {
                                $selected0 = "selected";
                            }
                            if ($status == 1) {
                                $selected1 = "selected";
                            }
                            if ($status == 2) {
                                $selected2 = "selected";
                            }
                            ?>
                            <option value="0" <?= $selected0 ?>>ทั้งหมด</option>
                            <option value="1" <?= $selected1 ?>>จ่ายแล้ว</option>
                            <option value="2" <?= $selected2 ?>>ยังไม่จ่าย</option>
                        </select>
                    </td>
                    <td>
                        <label for="">พนักงานขาย</label>
                        <?php
                        echo \kartik\select2\Select2::widget([
                            'name' => 'find_user_id',
                            'data' => \yii\helpers\ArrayHelper::map(\backend\models\User::find()->where(['company_id' => $company_id, 'branch_id' => $branch_id])->all(), 'id', 'username'),
                            'value' => $find_user_id,
                            'options' => [
                                'placeholder' => '--พนักงานขาย--'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multiple' => false,
                            ]
                        ]);
                        ?>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-primary" style="margin-top: 35px;" value="ค้นหา">
                    </td>
                    <td style="width: 25%"></td>
                </tr>
            </table>
        </form>
        <br/>
        <table class="table-header" width="100%">
            <tr>
                <td style="text-align: center; font-size: 20px; font-weight: bold">
                    รายงานขายเปรียบเทียบจ่าย
                </td>
            </tr>
        </table>
        <br>
        <table class="table-header" width="100%">
            <tr>
                <td style="text-align: center; font-size: 20px; font-weight: normal">
                    จากวันที่ <span style="color: red"><?= date('Y-m-d', strtotime($from_date)) ?></span>
                    ถึง <span style="color: red"><?= date('Y-m-d', strtotime($to_date)) ?></span></td>
            </tr>
        </table>
        <br>
        <table class="table-header" width="100%">
        </table>
        <table class="table table-bordered table-striped" id="table-data" style="width: 100%">
            <tr>
                <td style="text-align: left;border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center">
                    <b>ลูกค้า/สายส่ง</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>เลขที่ขาย</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>วันที่</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>สินค้า</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>จำนวนขาย</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>จำนวนจ่าย</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>สถานะจ่าย</b>
                </td>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>วันที่</b>
                <td style="border-top: 1px solid gray;border-bottom: 1px solid gray;text-align: center"><b>ผู้จ่าย</b>
                </td>
            </tr>
            <?php
            $sum_qty_all = 0;
            $sum_total_all = 0;
            $model_order = null;
            $model_order = getOrder($company_id, $branch_id, $from_date, $to_date, $find_user_id);

            ?>
            <?php if ($model_order != null): ?>
                <?php for ($x = 0; $x <= count($model_order) - 1; $x++): ?>
                    <?php
                    $cust_id = 0;
                    $is_customer = 0;
                    $cust_name = '';

                    $issue_qry = [];

                    if ($model_order[$x]['customer_id'] == null || $model_order[$x]['customer_id'] == 0) {
                        $cust_id = $model_order[$x]['order_channel_id'];
                        $cust_name = \backend\models\Deliveryroute::findName($cust_id);
                        $issue_qry = getIssueqty($model_order[$x]['id'], $model_order[$x]['product_id'], $company_id, $branch_id);
                    } else {
                        $cust_id = $model_order[$x]['customer_id'];
                        $cust_name = \backend\models\Customer::findName($cust_id);

                        $issue_qry = getIssueqty($model_order[$x]['id'], $model_order[$x]['product_id'], $company_id, $branch_id);
                    }
                    //print_r($issue_qry);return;
                    $status_show = "";
                    $issue_qty = 0;
                    $issue_user = '';
                    if ($issue_qry != null) {
                        $issue_qty = $issue_qry[0]['qty'];
                        if ($status == 1) {
                            if ($issue_qty == 0) continue;
                        } else if ($status == 2) {
                            if ($issue_qty > 0) continue;
                        }
                        $issue_user = \backend\models\User::findName($issue_qry[0]['updated_by']);
                        if ($model_order[$x]['qty'] <= (int)$issue_qry[0]['qty'] && $model_order[$x]['status'] != 3) {
                            $status_show = ' <i class="fa fa-check text-success"></i>';
                        } else if ($model_order[$x]['qty'] > (int)$issue_qry[0]['qty'] && $model_order[$x]['status'] != 3) {
                            $status_show = ' <i class="fa fa-ban text-danger"></i>';
                        } else {
                            $status_show = ' <i class="text-danger">ยกเลิก</i>';
                        }

                    }

                    ?>
                    <tr>
                        <td>
                            <?= $cust_name ?>
                        </td>
                        <td style="text-align: center"><?= $model_order[$x]['order_no'] ?></td>
                        <td style="text-align: center"><?= date('d-m-Y H:i:s',strtotime($model_order[$x]['order_date'])) ?></td>
                        <td style="text-align: center"><?= $model_order[$x]['product_name'] ?></td>
                        <td style="text-align: center"><?= number_format($model_order[$x]['qty'], 2) ?></td>
                        <td style="text-align: center"><?= number_format($issue_qty, 2) ?></td>
                        <td style="text-align: center">
                            <?= $status_show ?>
                        </td>
                        <td style="text-align: center"><?= $issue_user == ''?'':date('d-m-Y H:i:s' ,$issue_qry[0]['trans_date']) ?></td>
                        <td style="text-align: center"><?= $issue_user ?></td>
                    </tr>
                <?php endfor; ?>
            <?php endif; ?>

            <tfoot>
            <tr>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
                <td style="font-size: 16px;border-top: 1px solid black"></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <br/>
    </body>
    </html>

<?php
function getOrder($company_id, $branch_id, $from_date, $to_date, $find_user_id)
{
    $data = [];
    if ($company_id != null && $branch_id != null && $from_date != null && $to_date != null) {
        if ($find_user_id <= 0 || $find_user_id == null) {
            try {
                $model = \backend\models\Orders::find()->select(['id', 'order_no', 'customer_id', 'order_channel_id', 'status','order_date'])->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'sale_channel_id' => 2])->andFilterWhere(['AND', ['>=', 'date(order_date)', date('Y-m-d', strtotime($from_date))], ['<=', 'date(order_date)', date('Y-m-d', strtotime($to_date))]])->orderBy(['customer_id' => SORT_ASC])->all();
                foreach ($model as $value) {
                    $modelline = \backend\models\Orderline::find()->select(['order_id', 'product_id', 'qty'])->where(['order_id' => $value->id])->all();
                    foreach ($modelline as $value2) {
                        array_push($data, ['id' => $value->id, 'customer_id' => $value->customer_id, 'order_channel_id' => $value->order_channel_id, 'order_no' => $value->order_no, 'product_id' => $value2->product_id, 'product_name' => \backend\models\Product::findName($value2->product_id), 'qty' => $value2->qty, 'status' => $value->status,'order_date'=>$value->order_date]);
                    }
                }
            } catch (\Exception $exception) {
                print_r($exception);
            }
        } else {
            try {

                $model = \backend\models\Orders::find()->select(['id', 'order_no', 'customer_id', 'order_channel_id', 'status','order_date'])->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'created_by' => $find_user_id, 'sale_channel_id' => 2])->andFilterWhere(['AND', ['>=', 'date(order_date)', date('Y-m-d', strtotime($from_date))], ['<=', 'date(order_date)', date('Y-m-d', strtotime($to_date))]])->orderBy(['customer_id' => SORT_ASC])->all();
                foreach ($model as $value) {
                    $modelline = \backend\models\Orderline::find()->select(['order_id', 'product_id', 'qty'])->where(['order_id' => $value->id])->all();
                    foreach ($modelline as $value2) {
                        array_push($data, ['id' => $value->id, 'customer_id' => $value->customer_id, 'order_channel_id' => $value->order_channel_id, 'order_no' => $value->order_no, 'product_id' => $value2->product_id, 'product_name' => \backend\models\Product::findName($value2->product_id), 'qty' => $value2->qty, 'status' => $value->status,'order_date'=>$value->order_date]);
                    }
                }


            } catch (\Exception $exception) {
                print_r($exception);
            }
//                    try {
//                        $model_issue = \backend\models\Journalissue::find()->select('order_ref_id')->where(['updated_by'=>$find_user_id])->andFilterWhere(['>','order_ref_id',0])->andFilterWhere(['AND',['>=','date(trans_date)',date('Y-m-d', strtotime($from_date))],['<=','date(trans_date)',date('Y-m-d',strtotime($to_date))]])->all();
//                        foreach($model_issue as $item){
//                            $model = \backend\models\Orders::find()->select(['id', 'order_no', 'customer_id', 'order_channel_id'])->where(['company_id' => $company_id, 'branch_id' => $branch_id, 'id' => $item->order_ref_id,'sale_channel_id'=>2])->andFilterWhere(['AND',['>=','date(order_date)',date('Y-m-d', strtotime($from_date))],['<=','date(order_date)',date('Y-m-d',strtotime($to_date))]])->orderBy(['customer_id'=>SORT_ASC])->all();
//                            foreach ($model as $value) {
//                                $modelline = \backend\models\Orderline::find()->select(['order_id', 'product_id', 'qty'])->where(['order_id' => $value->id])->all();
//                                foreach ($modelline as $value2) {
//                                    array_push($data, ['id' => $value->id, 'customer_id' => $value->customer_id, 'order_channel_id' => $value->order_channel_id, 'order_no' => $value->order_no, 'product_id' => $value2->product_id, 'product_name' => \backend\models\Product::findName($value2->product_id), 'qty' => $value2->qty]);
//                                }
//                            }
//                        }
//
//                    } catch (\Exception $exception) {
//                        print_r($exception);
//                    }
        }


    }
    return $data;
}

function getIssueqty($order_id, $product_id, $company_id, $branch_id)
{
    $data = [];
    if ($order_id != null && $product_id != null) {
        $model_issue = \backend\models\Journalissue::find()->select(['id', 'updated_by','trans_date'])->where(['order_ref_id' => $order_id, 'company_id' => $company_id, 'branch_id' => $branch_id])->one();
        if ($model_issue) {
            $qty = \common\models\IssueStockTemp::find()->where(['issue_id' => $model_issue->id, 'product_id' => $product_id])->sum('qty');
            $x_qty = $qty != null ? $qty : 0;
            $line_date = getIssueTransDate($model_issue->id);
            array_push($data, ['updated_by' => $model_issue->updated_by, 'qty' => $x_qty,'trans_date'=>$line_date]);
        } else {

        }

    }
    return $data;
}

function getIssueTransDate($issue_id){
  $t_date = null;
  if($issue_id){
      $model = \common\models\IssueStockTemp::find()->select(['crated_at'])->where(['issue_id' => $issue_id])->one();
      if($model){
          $t_date = $model->crated_at;
      }
  }
  return $t_date;
}

?>
<?php
$this->registerJsFile(\Yii::$app->request->baseUrl . '/js/jquery.table2excel.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<JS
         $("#btn-export-excel").click(function(){
          $("#table-data").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Excel Document Name"
          });
        });
function printContent(el)
      {
         var restorepage = document.body.innerHTML;
         var printcontent = document.getElementById(el).innerHTML;
         document.body.innerHTML = printcontent;
         window.print();
         document.body.innerHTML = restorepage;
     }
JS;
$this->registerJs($js, static::POS_END);
?>