<?php
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use api\models\User;
use common\models\UserSearch;

//use api\modules\v1\models\LoginForm;
//use yii\web\IdentityInterface;
//use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use common\service\Service;
use api\models\Signup;

//use OAuth2\Request;
//use OAuth2\Response;
//use OAuth2\Storage\Pdo;
//use OAuth2\GrantType\RefreshToken;


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
                    'signup-test',
                    'index',
                    'view',
                    'create',
                    'search',
                    'update',
                    'delete',
//                    'test',
                    'login',
                ],
            ]
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();
        // 禁用""index,delete" 和 "create" 操作
//        unset($actions['index'], $actions['delete'], $actions['create'], $actions['update']);

        return $actions;

    }

    public function actionTest()
    {
//        $data = array('123');
        return Service::sendSucc('234');
        return Service::sendError('20302','data error');
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

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
//        $post = Yii::$app->request->post();
//        $user = new User();
//        $user->generateAuthKey();
//        $user->setPassword($post['password']);
//        $user->username = $post['username'];
//        $user->email = $post['email'];
//        $user->mobile = $post['mobile'];
//        $user->uuid = Service::create_uuid();
//        if($user->save()){
//            return [];
//        }
//        else{
//            $code = 21001;
//            $message = array_values($user->getFirstErrors())[0];;
//            return [
//                'code' => $code,
//                'message' => $message,
//            ];
//        }

        $Signup = new Signup();

//        $post = Yii::$app->request->post();
//        $Signup->setUserName($post['username']);
//        $Signup->setMobile($post['mobile']);
        $Signup->setAttributes(Yii::$app->request->post());

        if ($user = $Signup->signup()) {
            return [];
        }else{
            $code = 21001;
            $message = array_values($Signup->getFirstErrors())[0];;
            return [
                'code' => $code,
                'message' => $message,
            ];
        }
    }

    public function actionUpdate()
    {
        $post = Yii::$app->request->post();
        $user = User::findOne($post['id']);
        if (!$user) {
            $code = 210001;
            $message = '用户不存在';
            return [
                'code' => $code,
                'message' => $message,
            ];
        }
        $user->email = $post['email'];
        if ($user->save()) {
            return [];
        } else {
            $code = 21002;
            $message = array_values($user->getFirstErrors())[0];;
            return [
                'code' => $code,
                'message' => $message,
            ];
        }
    }

    public function test(){
        return [];
    }

    public function actionDelete()
    {
        $post = Yii::$app->request->post();
        $user = User::findOne($post['id']);
        if (!$user) {
            $code = 21003;
            $message = '用户不存在';
            return [
                'code' => $code,
                'data' => [],
                'message' => $message,
            ];
        }
        $user->status = 0;
        if ($user->save()) {
            return [];
        } else {
            $code = 21003;
            $message = array_values($user->getFirstErrors())[0];;
            return [
                'code' => $code,
                'data' => [],
                'message' => $message,
            ];
        }
    }

    public function actionProfile()
    {
        $user = \yii::$app->user->identity;
        return $user;
    }

//    public function actionIndex()
//    {
//        $query = User::find();
//        $users = new yii\data\ActiveDataProvider(['query' => $query]);
////        $query->andFilterWhere(['like', 'username', 'weixi']);
//        $data = $users->getModels();
//        return $data;
//    }

    public function actionSearch($username)
    {
        $params['UserSearch']['username'] = $username;
        $userSearch = new UserSearch();
        $provider = $userSearch->search($params);
        $data = $provider->getModels();
        return $data;
    }
}  