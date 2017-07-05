<?php
namespace api\models;

use yii\base\Model;
use api\models\User;
use common\service\Service;

/**
 * Signup form
 */
class Signup extends Model
{
    public $mobile;
    public $username;
    public $verifyCode;
    public $email;
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['mobile','required'],
            ['mobile', 'unique', 'targetClass' => '\api\models\User', 'message' => '手机号已存在.'],
            ['mobile','string','max'=>11],

            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\api\models\User', 'message' => '用户名已经存在.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致！'],
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

//        $post = Yii::$app->request->post();

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->mobile = $this->mobile;
        $user->setPassword($this->password);
        $user->generateAuthKey();
//        $user->password = '*';
        $user->uuid = Service::create_uuid();

        //  $user->save(); VarDumper::dump($user->errors);exit(0);
        return $user->save() ? $user : null;
    }
}
