<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace api\modules;

use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\rest\ActiveController;



class ApiBaseController extends ActiveController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::className()],
                    ['class' => QueryParamAuth::className(), 'tokenParam' => 'token'],
                ]
            ],
//            'exceptionFilter' => [
//                'class' => ErrorToExceptionFilter::className()
//            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
//            'index' => [
//                'class' => 'yii\rest\IndexAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//            ],
//            'view' => [
//                'class' => 'yii\rest\ViewAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//            ],
//            'create' => [
//                'class' => 'yii\rest\CreateAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//                'scenario' => $this->createScenario,
//            ],
//            'update' => [
//                'class' => 'yii\rest\UpdateAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//                'scenario' => $this->updateScenario,
//            ],
//            'delete' => [
//                'class' => 'yii\rest\DeleteAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }
}
