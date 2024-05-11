<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "route_trans_expend".
 *
 * @property int $id
 * @property string|null $trans_date
 * @property int|null $created_at
 * @property int|null $created_by
 */
class RouteTransExpend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_trans_expend';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_date'], 'safe'],
            [['created_at', 'created_by'], 'integer'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trans_date' => 'Trans Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
