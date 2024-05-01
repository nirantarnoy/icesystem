<?php
namespace backend\models;
use Yii;
use yii\db\ActiveRecord;
date_default_timezone_set('Asia/Bangkok');

class Product extends \common\models\Product
{
    public function behaviors()
    {
        return [
            'timestampcdate'=>[
                'class'=> \yii\behaviors\AttributeBehavior::className(),
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT=>'created_at',
                ],
                'value'=> time(),
            ],
            'timestampudate'=>[
                'class'=> \yii\behaviors\AttributeBehavior::className(),
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT=>'updated_at',
                ],
                'value'=> time(),
            ],
            'timestampcby'=>[
                'class'=> \yii\behaviors\AttributeBehavior::className(),
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT=>'created_by',
                ],
                'value'=> Yii::$app->user->id,
            ],
            'timestamuby'=>[
                'class'=> \yii\behaviors\AttributeBehavior::className(),
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_UPDATE=>'updated_by',
                ],
                'value'=> Yii::$app->user->id,
            ],
//            'timestampcompany'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'company_id',
//                ],
//                'value'=> isset($_SESSION['user_company_id'])? $_SESSION['user_company_id']:1,
//            ],
//            'timestampbranch'=>[
//                'class'=> \yii\behaviors\AttributeBehavior::className(),
//                'attributes'=>[
//                    ActiveRecord::EVENT_BEFORE_INSERT=>'branch_id',
//                ],
//                'value'=> isset($_SESSION['user_branch_id'])? $_SESSION['user_branch_id']:1,
//            ],
            'timestampupdate' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => time(),
            ],
        ];
    }

    public static function findProductId($code, $company_id, $branch_id){
        $model = Product::find()->select('id')->where(['code'=>trim($code),'company_id'=>$company_id,'branch_id'=>$branch_id])->one();
        return $model != null?$model->id:0;
    }
    public static function findProductIdByName($name){
        $model = Product::find()->select('id')->where(['name'=>trim($name)])->one();
        return $model != null?$model->id:0;
    }

    public static function findCode($id){
        $model = Product::find()->select('code')->where(['id'=>$id])->one();
        return $model != null?$model->code:'';
    }
    public static function findGroupId($id){
        $model = Product::find()->select('product_group_id')->where(['id'=>$id])->one();
        return $model != null?$model->product_group_id:0;
    }
    public static function findGroupName($id){
        $modelname = null;
        $model = Product::find()->select('product_group_id')->where(['id'=>$id])->one();
        if($model){
            $modelname = \backend\models\Productgroup::find()->select('name')->where(['id'=>$model->product_group_id])->one();
        }
        return $modelname != null?$modelname->name:'';
    }
    public static function findName($id){
        $model = Product::find()->select('name')->where(['id'=>$id])->one();
        return $model !=null?$model->name:'';
    }
    public static function findDescription($id){
        $model = Product::find()->select('description')->where(['id'=>$id])->one();
        return $model !=null?$model->description:'';
    }
    public static function findPhoto($id){
        $model = Product::find()->where(['id'=>$id])->one();
        return $model !=null?$model->photo:'';
    }
    public static function findInfo($id){
        $model = Product::find()->where(['id'=>$id])->one();
        return $model !=null?$model : null;
    }
    public static function findUnitname($product_id){
        $model = Product::find()->where(['id'=>$product_id])->one();
        if($model){
            $model_unit = \backend\models\Unit::find()->where(['id'=>$model->unit_id])->one();
            return $model_unit !=null?$model_unit->name:'';
        }else{
            return '';
        }
    }

}
