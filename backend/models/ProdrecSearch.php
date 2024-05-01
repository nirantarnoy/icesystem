<?php

namespace backend\models;

use backend\models\Stocktrans;
use yii\base\BaseObject;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class ProdrecSearch extends Stocktrans
{
    public $from_date,$to_date,$prodrec_type;
    public function rules()
    {
        return [
            [['id', 'company_id', 'branch_id', 'product_id', 'warehouse_id', 'location_id', 'qty', 'created_at','transfer_branch_id'], 'integer'],
            [['journal_no', 'trans_date', 'lot_no','activity_type_id', 'created_by','from_date','to_date','prodrec_type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Stocktrans::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
           // 'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            // 'trans_date' => $this->trans_date,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'location_id' => $this->location_id,
            'transfer_branch_id' => $this->transfer_branch_id,
            'qty' => $this->qty,
           // 'created_at' => $this->created_at,
          //  'created_by' => $this->created_by,
        ]);

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $query->andFilterWhere(['company_id' => \Yii::$app->user->identity->company_id]);
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $query->andFilterWhere(['branch_id' => \Yii::$app->user->identity->branch_id]);
        }

        if($this->activity_type_id != null){
            $query->andFilterWhere(['activity_type_id' => $this->activity_type_id]);
        }
        if($this->created_by != null){
            $query->andFilterWhere(['created_by' => $this->created_by]);
        }

        if($this->from_date != null && $this->to_date != null){
            $fx_datetime = explode(' ',$this->from_date);
            $tx_datetime = explode(' ',$this->to_date);

            $f_date = null;
            $f_time = null;
            $t_date = null;
            $t_time = null;

            $from_date_time = null;
            $to_date_time = null;

            if(count($fx_datetime) > 0){
                $f_date = $fx_datetime[0];
                $f_time = $fx_datetime[1];

                $x_date = explode('-', $f_date);
                $xx_date = date('Y-m-d');
                if (count($x_date) > 1) {
                    $xx_date = trim($x_date[1]) . '/' . trim($x_date[2]) . '/' . trim($x_date[0]);
                }
                $from_date_time = date('Y-m-d H:i:s',strtotime($xx_date.' '.$f_time));
                //$from_date_time = date('Y-m-d',strtotime($xx_date));
                $query->andFilterWhere(['>=','trans_date', $from_date_time]);
            }

            if(count($tx_datetime) > 0){
                $t_date = $tx_datetime[0];
                $t_time = $tx_datetime[1];

                $n_date = explode('-', $t_date);
                $nn_date = date('Y-m-d');
                if (count($n_date) > 1) {
                    $nn_date = trim($n_date[1]) . '/' . trim($n_date[2]) . '/' . trim($n_date[0]);
                }
                $to_date_time = date('Y-m-d H:i:s',strtotime($nn_date.' '.$t_time));
                $query->andFilterWhere(['<=','trans_date',$to_date_time]);
            }

        }else{
            $query->andFilterWhere(['>=','date(trans_date)', date('Y-m-d')]);
            $query->andFilterWhere(['<=','date(trans_date)',date('Y-m-d')]);
        }

        if($this->prodrec_type!=''){
            $query->andFilterWhere(['LIKE','journal_no',$this->prodrec_type]);
        }


        $query->andFilterWhere(['like', 'journal_no', $this->journal_no])
            ->andFilterWhere(['like', 'lot_no', $this->lot_no]);

        return $dataProvider;
    }
}
