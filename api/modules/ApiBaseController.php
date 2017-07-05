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
        return [];
    }

    protected function verbs()
    {
        return [];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }
}
