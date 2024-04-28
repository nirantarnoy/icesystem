<?php
$product_header = [];
$route_name = '';
$find_date = date('Y-m-d');
if ($route_id != null) {
    $route_name = \backend\models\Deliveryroute::findName($route_id);
}

//
//    $modelx = \common\models\QuerySaleByDistributor::find()->select(['product_id'])->join('inner join', 'product', 'query_sale_by_distributor.product_id=product.id')->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product.item_pos_seq' => SORT_ASC])->all();

$sql = "SELECT id FROM product where status =1 order by item_pos_seq ASC";

$modelx = \Yii::$app->db->createCommand($sql)->queryAll();

if ($modelx) {
    for ($xx = 0; $xx <= count($modelx) - 1; $xx++) {
        if (!in_array($modelx[$xx]['id'], $product_header)) {
            array_push($product_header, $modelx[$xx]['id']);
        }
    }
}

$model_stdgroup = \backend\models\Stdpricegroup::find()->groupBy('seq_no')->orderBy(['seq_no' => SORT_ASC])->all();


?>
    <form id="form-find" action="index.php?r=routesummarybystdgroup/index" method="post">
        <div class="row">
            <div class="col-lg-4">
                <label for="">สายส่ง</label>
                <?php
                echo \kartik\select2\Select2::widget([
                    'name' => 'route_id',
                    'data' => \yii\helpers\ArrayHelper::map(\backend\models\Deliveryroute::find()->where(['status' => 1])->all(), 'id', 'name'),
                    'value' => $route_id,
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                            'onchange'=> 'form.submit()',
                    ]
                ]);
                ?>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-lg-12" style="text-align: center"><h3>รวมยอดประจำวัน</h3></div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="text-align: center"><h3><?= $route_name ?></h3></div>
    </div>
<?php
 $total_all = 0;
?>
    <table id="table-data" style="width: 100%">
        <tr style="font-weight: bold;">
            <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= date('d/m/Y') ?></td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">รวมเงิน</td>
        </tr>
        <tr>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">เบิก</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php
              $line_car_issue_qty = 0;
            ?>
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
            <?php
               $product_issue_qty = getIssuecar($route_id,$product_header[$y],$find_date);
               $line_car_issue_qty+=$product_issue_qty;
            ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?=$product_issue_qty == 0?'-': number_format($product_issue_qty, 2) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;font-weight: bold;"><?= $line_car_issue_qty == 0? '-':number_format($line_car_issue_qty, 2) ?></td>
        </tr>
        <tr>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;">คืน</td>
            <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
            <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= 1>0? '-': number_format(0, 2) ?></td>
            <?php endfor; ?>
            <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= 1>0? '-':number_format(0, 2) ?></td>
        </tr>

        <?php foreach ($model_stdgroup as $value_group): ?>
            <tr>
                <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $value_group->name; ?></td>
                <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
                <?php $price_group_line_qty = 0;?>
                <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                    <?php
                     $line_product_price_qty = getQtyByPrice($route_id,$value_group->price,$value_group->type_id,$find_date,$product_header[$y]);
                    $price_group_line_qty += $line_product_price_qty;
                    ?>
                    <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= $line_product_price_qty == 0? '-':number_format($line_product_price_qty, 2) ?></td>
                <?php endfor; ?>
                <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= $price_group_line_qty ==0? '-':number_format($price_group_line_qty, 2) ?></td>
            </tr>
        <?php endforeach; ?>
         <tr>
             <td style="text-align: center;padding: 8px;border: 1px solid grey;"><b>รวมทั้งหมด</b></td>
             <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
             <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
                 <td style="text-align: center;padding: 8px;border: 1px solid grey;"></td>
             <?php endfor; ?>
             <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;"><?= 1>0? '-':number_format(0, 2) ?></td>
         </tr>
    </table>


<?php
function getIssuecar($route_id, $product_id, $order_date)
{
    $issue_qty = 0;

    if($route_id > 0){
        $sql = "SELECT SUM(t2.origin_qty) as qty";
        $sql .= " FROM journal_issue as t1 INNER JOIN journal_issue_line as t2 ON t2.issue_id = t1.id";
        $sql .= " WHERE t2.product_id =" . $product_id;
        $sql .= " AND not isnull(t1.delivery_route_id)";
        $sql .= " AND t1.status in (2)";
        $sql .= " AND date(t1.trans_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
        if ($route_id != null) {
            $sql .= " AND t1.delivery_route_id=" . $route_id;
        }
        $sql .= " GROUP BY t2.product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $issue_qty = $model[$i]['qty'];
            }
        }
    }

    return $issue_qty;
}

function getQtyByPrice($route_id, $price,$sale_type, $order_date,$product_id)
{
    $sale_qty = 0;

    if($route_id >0){
        $sql = "SELECT SUM(t2.qty) as qty";
        $sql .= " FROM orders as t1 INNER JOIN order_line as t2 ON t2.order_id = t1.id";
        $sql .= " WHERE t2.product_id =" . $product_id;
        // $sql .= " AND not isnull(t1.delivery_route_id)";
        $sql .= " AND t1.status <> 3";
        $sql .= " AND t2.status not in (3,500)";
        $sql .= " AND t2.price = ".$price;
        $sql .= " AND t2.sale_payment_method_id = ".$sale_type;
        $sql .= " AND date(t1.order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
        if ($route_id != null) {
            $sql .= " AND t1.order_channel_id=" . $route_id;
        }
        $sql .= " GROUP BY t2.price";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $sale_qty = $model[$i]['qty'];
            }
        }
    }


    return $sale_qty;
}


function getReceiveTransfer($route_id,$order_date,$product_id,$transfer_from_id)
{
    $sale_qty = 0;

    if($route_id >0){
        $sql = "SELECT SUM(qty) as qty";
        $sql .= " FROM query_issue";
        $sql .= " WHERE product_id =" . $product_id;
        // $sql .= " AND not isnull(t1.delivery_route_id)";
        $sql .= " AND date(order_date) =" . "'" . date('Y-m-d', strtotime($order_date)) . "'" . " ";
        if ($route_id != null) {
            $sql .= " AND order_channel_id=" . $route_id;
        }
        $sql .= " GROUP BY product_id";
        $query = \Yii::$app->db->createCommand($sql);
        $model = $query->queryAll();
        if ($model) {
            for ($i = 0; $i <= count($model) - 1; $i++) {
                $sale_qty = $model[$i]['qty'];
            }
        }
    }


    return $sale_qty;
}

?>