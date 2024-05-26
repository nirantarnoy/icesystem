<?php

namespace backend\models;

use backend\models\Journalissue;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * JournalissueSearch represents the model behind the search form of `backend\models\Journalissue`.
 */
class TransformreservSearch extends Transformreserv
{
    public $globalSearch;

    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['product_id', 'status', 'company_id', 'branch_id', 'user_id'], 'integer'],
            [['qty'], 'number'],
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
        $query = Transformreserv::find();

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
            'trans_date' => $this->trans_date,
            'status' => $this->status,
        ]);

        if (!empty(\Yii::$app->user->identity->company_id)) {
            $query->andFilterWhere(['company_id' => \Yii::$app->user->identity->company_id]);
        }
        if (!empty(\Yii::$app->user->identity->branch_id)) {
            $query->andFilterWhere(['branch_id' => \Yii::$app->user->identity->branch_id]);
        }

       // $query->andFilterWhere(['like', 'journal_no', $this->globalSearch]);

        return $dataProvider;
    }
}
