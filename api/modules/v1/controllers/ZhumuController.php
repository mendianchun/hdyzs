<?php
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use common\models\Zhumu;
use common\models\SystemConfig;

use yii\helpers\ArrayHelper;
use common\service\Service;


class ZhumuController extends ActiveController
{
    public $modelClass = 'common\models\Zhumu';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'getinfo',
                ],
            ]
        ]);
    }

    public function actionGetinfo()
    {
        $systemConfig = SystemConfig::findOne(['name'=>'zhumu_app_key']);
        if(isset($systemConfig)){
            $app_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name'=>'zhumu_app_secret']);
        if(isset($systemConfig)){
            $app_secret = $systemConfig['value'];
        }

        //随机选择一个瞩目账号
        $maxId = Zhumu::find()->where(["status"=>Zhumu::STATUS_ACTIVE])->max('id');
        $randId = rand(0,$maxId);

        $zhumu = Zhumu::find()->where(['>=','id',$randId])->andWhere(['status'=>Zhumu::STATUS_ACTIVE])->one();
        $zhumuArray = $zhumu->attributes;

        unset($zhumuArray['id'],$zhumuArray['status'],$zhumuArray['create_at'],$zhumuArray['update_at']);
        $zhumuArray['app_key'] = $app_key;
        $zhumuArray['app_secret'] = $app_secret;
        return Service::sendSucc($zhumuArray);
    }
}  