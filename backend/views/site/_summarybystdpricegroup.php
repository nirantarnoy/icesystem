<?php
$product_header = [];


//
//    $modelx = \common\models\QuerySaleByDistributor::find()->select(['product_id'])->join('inner join', 'product', 'query_sale_by_distributor.product_id=product.id')->where(['BETWEEN', 'date(order_date)', date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))])
//        ->andFilterWhere(['product.company_id' => $company_id, 'product.branch_id' => $branch_id])->groupBy('product_id')->orderBy(['product.item_pos_seq' => SORT_ASC])->all();

$sql = "SELECT id FROM product where status =1 order by item_pos_seq ASC";

$modelx = \Yii::$app->db->createCommand($sql)->queryAll();

if ($modelx) {
    for ($xx=0;$xx<=count($modelx)-1;$xx++) {
        if (!in_array($modelx[$xx]['id'], $product_header)) {
            array_push($product_header, $modelx[$xx]['id']);
        }
    }
}


?>
<table id="table-data">
    <tr style="font-weight: bold;">
        <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?=date('d/m/Y')?></td>
        <!--            <td style="text-align: center;padding: 0px;border: 1px solid grey">จำนวน</td>-->
        <?php for ($y = 0; $y <= count($product_header) - 1; $y++): ?>
            <td style="text-align: center;padding: 8px;border: 1px solid grey;"><?= \backend\models\Product::findCode($product_header[$y]) ?></td>
        <?php endfor; ?>
        <td style="text-align: right;padding: 8px;border: 1px solid grey;background-color: skyblue;">รวมเงิน</td>
        </td>
    </tr>
</table>
