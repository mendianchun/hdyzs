<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/28
 * Time: 上午9:18
 */

namespace backend\models;

use yii\base\Model;
use yii\helpers\VarDumper;
use common\models\User;
use common\service\Service;

class Expert extends \common\models\Expert
{

	public $username;
	public $password;
//	public $head_img;
	public $password_repeat;
	public $mobile;
	public $email;

//	public $fee_per_times;
//	public $fee_per_hour;
//	public $skill;
//	public $introduction;
//	public function fields()
//	{
//		$fields = parent::fields();
//
//		// 去掉一些包含敏感信息的字段
//		//unset($fields['patient_mobile'], $fields['patient_idcard'], $fields['created_at']);
//
//		return $fields;
//	}
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'required'],
			['username', 'required'],
			['mobile', 'required'],
			['password', 'required'],
			['email', 'required'],
			['head_img', 'required'],

			[['mobile'], 'number'],
			[['mobile'], 'string', 'min' => 11],
			[['email'], 'email'],

			['name', 'unique',  'message' => '专家姓名已经存在.'],
			['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '登录名已经存在.'],
			['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '手机号已经存在.'],
			['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'email已经存在.'],

			[['introduction'], 'string', 'max' => 200],
			[['url'], 'string', 'max' => 200],
			['password', 'string', 'min' => 6],

			['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致！'],

			[['name', 'username', 'mobile','email', 'password', 'head_img', 'fee_per_times', 'fee_per_hour', 'skill', 'introduction', 'url','expert_status'], 'string'],

		];
	}

	public function attributeLabels()
	{
		return [
			'name' => '专家姓名',
			'username' => '登录名',
			'password' => '密码',
			'password_repeat'=>'重输密码',
			'mobile'=>'手机号',
			'email'=>'E-mail',
			'head_img' => '头像',
			'free_time' => '空闲时间',
			'fee_per_times' => '每次费用',
			'fee_per_hour' => '每小时费用',
			'skill' => '特长',
			'introduction' => '介绍',
			'url' => '链接',
		];
	}

	public function newExpert()
	{

		if (!$this->validate()) {
			return null;
		}
		$user = new User();
		$user->username = $this->username;
		$user->mobile = $this->mobile;
		$user->email = $this->email;
		$user->type=User::USER_EXPERT;

		$user->setPassword($this->password);
		$user->generateAuthKey();



		$uuid = Service::create_uuid();
		$user->uuid = $uuid;
		if($user->save()){
			$expert=new \common\models\Expert();

			$expert->name =$this->name;
			$expert->head_img =$this->head_img;

			$expert->free_time =$this->free_time;

			$expert->fee_per_times = $this->fee_per_times;;
			$expert->fee_per_hour =$this->fee_per_hour;
			$expert->skill =$this->skill;
			$expert->introduction =$this->introduction;
			$expert->url =$this->url;
			$expert->user_uuid =$uuid;
			if($expert->save()>0){
				return array('uuid'=>$uuid,'id'=>$expert->id);
			}else{
				return null;
			}
		}else{
			return null;
		}

	}


}