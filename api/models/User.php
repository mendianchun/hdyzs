<?php
namespace api\models;

use Yii;

class User extends \common\models\User implements \OAuth2\Storage\UserCredentialsInterface
{

    /**
     * Implemented for Oauth2 Interface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var \filsh\yii2\oauth2server\Module $module */
        $module = Yii::$app->getModule('oauth2');
        $token = $module->getServer()->getResourceController()->getToken();
        return !empty($token['user_id'])
            ? static::findIdentity($token['user_id'])
            : null;
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function checkUserCredentials($username, $password)
    {
        $user = static::findByUsername($username);
        if(empty($user)){
            $user = static::findByMobile($username);
            if(empty($user)){
                $user = static::findByEmail($username);
            }
        }

        $post = Yii::$app->request->post();
        if (empty($user) || $user['type'] != $post['type']) {
            return false;
        }
        return $user->validatePassword($password);
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function getUserDetails($username)
    {
        $user = static::findByUsername($username);
        if(empty($user)){
            $user = static::findByMobile($username);
            if(empty($user)){
                $user = static::findByEmail($username);
            }
        }
        return ['user_id' => $user->getId()];
    }
}