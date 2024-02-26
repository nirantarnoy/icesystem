<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Logincar;

/**
 * LogincarSearch represents the model behind the search form of `backend\models\Logincar`.
 */
class LogincarSearch extends Logincar
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'route_id', 'car_id', 'emp_1', 'emp_2', 'status'], 'integer'],
            [['login_date'], 'safe'],
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
        $query = Logincar::find();

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
//            'login_date' => $this->login_date,
            'route_id' => $this->route_id,
            'car_id' => $this->car_id,
            'emp_1' => $this->emp_1,
            'emp_2' => $this->emp_2,
            'status' => $this->status,
        ]);


        if ($this->login_date != null) {
            $tdate = date('Y-m-d');
            $xdate = explode('-', $this->login_date);
            if (count($xdate) > 1) {
                $tdate = $xdate[2] . "/" . $xdate[1] . "/" . $xdate[0];
            }

            $query->andFilterWhere(['date(login_date)' => date('Y-m-d', strtotime($tdate))]);
        }

        return $dataProvider;
    }
}
