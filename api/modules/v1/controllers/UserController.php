<?php
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use common\models\User;
use common\models\UserSearch;
use api\modules\v1\models\LoginForm;
use yii\web\IdentityInterface;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Storage\Pdo;
use OAuth2\GrantType\RefreshToken;


class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'login',
                    'signup-test',
                    'login-oauth',
                    'index',
                ],
            ]
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();
        // 禁用""index,delete" 和 "create" 操作
        unset($actions['index'], $actions['delete'], $actions['create']);

        return $actions;

    }

    /**
     * 添加测试用户
     */
    public function actionSignupTest()
    {
        $user = new User();
        $user->generateAuthKey();
        $user->setPassword('123456');
        $user->username = '111';
        $user->email = '111@111.com';
        $user->save(false);

        return [
            'code' => 0
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm;
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                return ['access-token' => $user->api_token];
            } else {
                return $user->errors;
            }
        } else {
            return $model->errors;
        }
    }

//    public function actionLoginOauth()
//    {
//        $storage = new Pdo(array('dsn' => Yii::$app->db->dsn, 'username' => Yii::$app->db->username, 'password' => Yii::$app->db->password));
//        $server = new \OAuth2\Server($storage, array('enforce_state' => false, 'access_lifetime' => Yii::$app->params['user.apiTokenExpire']));
//        $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
//        $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
//        $server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
//        $server->addGrantType(new RefreshToken($storage));
//
////        $request = Request::createFromGlobals();
//        $response = $server->handleTokenRequest(\OAuth2\Request::createFromGlobals());
////        $response = new \OAuth2\Response();
////        $response = $server->handleAuthorizeRequest(\OAuth2\Request::createFromGlobals(),$response,false,123);
//        $response_array = $response->getParameters();
//        return $response_array;
//    }

    public function actionProfile()
    {
        $user = \yii::$app->user->identity;
        $data = [
            'uid' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ];

        return [
            'code' => 0,
            'data' => $data,
            'message' => 'succ',
        ];
    }

    public function actionIndex()
    {
        $users = new yii\data\ActiveDataProvider(['query' => \api\models\User::find()]);
        $data = $users->getModels();
        return [
            'code' => 0,
            'data' => $data,
            'message' => 'succ',
        ];
    }

    public function actionSearch($username)
    {
        $params['UserSearch']['username'] = $username;
        $userSearch = new UserSearch();
        $provider = $userSearch->search($params);
        $data = $provider->getModels();
        $returnData = array();
        foreach ($data as $user) {
            $returnData[] = [
                'uid' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
            ];
        }
        return [
            'code' => 0,
            'data' => $returnData,
            'message' => 'succ',
        ];
    }
}  