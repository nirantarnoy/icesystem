<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Customertaxinvoice;

/**
 * CustomertaxinvoiceSearch represents the model behind the search form of `backend\models\Customertaxinvoice`.
 */
class CustomertaxinvoiceSearch extends Customertaxinvoice
{
    public $from_date,$to_date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'payment_term_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['invoice_no', 'invoice_date', 'payment_date', 'remark', 'total_text','from_date','to_date'], 'safe'],
            [['total_amount', 'vat_amount', 'net_amount'], 'number'],
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
        $query = Customertaxinvoice::find();

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
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'invoice_date' => $this->invoice_date,
            'payment_term_id' => $this->payment_term_id,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'total_amount' => $this->total_amount,
            'vat_amount' => $this->vat_amount,
            'net_amount' => $this->net_amount,
        ]);

        if($this->from_date != null && $this->to_date){
            $xfdate = explode('-',$this->from_date);
            $xtdate = explode('-',$this->to_date);
            $fdate = date('Y-m-d');
            $tdate = date('Y-m-d');
            if(count($xfdate)>1){
                $fdate = $xfdate[2].'/'.$xfdate[1].'/'.$xfdate[0];
            }
            if(count($xtdate)>1){
                $tdate = $xtdate[2].'/'.$xtdate[1].'/'.$xtdate[0];
            }

            $query->andFilterWhere(['>=','date(invoice_date)',date('Y-m-d',strtotime($fdate))])->andFilterWhere(['<=','date(invoice_date)',date('Y-m-d',strtotime($tdate))]);
        }

        $query->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'total_text', $this->total_text]);

        return $dataProvider;
    }
}
