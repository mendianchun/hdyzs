<?php
namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

class CountryController extends ActiveController
{
    public $modelClass = 'common\models\Country';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'index',
                ],
            ]
        ] );
    }

//    public function actions() {
//        $actions = parent::actions();
//        // 禁用""index,delete" 和 "create" 操作
//        unset($actions['index'],$actions['delete'], $actions['create']);
//
//        return $actions;
//
//    }
//    //重写index的业务实现
//    public function actionIndex()
//    {
//        $user = \yii::$app->user->identity;
//        return $user;
//        $modelClass = $this->modelClass;
//        return new ActiveDataProvider([
//            'query' => $modelClass::find()->asArray(),
//
//            'pagination' => false
//        ]);
//    }
    public function actionTest()
    {
        return ['test'];
    }
}  