<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/28
 * Time: 上午9:18
 */

namespace backend\models;

use yii\base\Model;
use common\models\Adminuser;
use yii\helpers\VarDumper;

class Expert extends \common\models\Expert
{

	public $username;
	public $password;
	public $password_repeat;
	public $mobile;

//	public function fields()
//	{
//		$fields = parent::fields();
//
//		// 去掉一些包含敏感信息的字段
//		unset($fields['patient_mobile'], $fields['patient_idcard'], $fields['created_at']);
//
//		return $fields;
//	}
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [

			['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致！'],
		];
	}

	public function attributeLabels()
	{
		return [
			'username' => '登录名',
			'password' => '密码',
			'password_repeat'=>'重输密码',
			'mobile'=>'手机号',
		];
	}


}