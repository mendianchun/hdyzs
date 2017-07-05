<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/7/5
 * Time: 下午3:48
 */

namespace api\modules\v1\controllers;

use Yii;

use api\modules\ApiBaseController;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use common\service\Service;

use common\models\ScoreLog;
use common\models\User;
use common\models\Clinic;

class ScoreController extends ApiBaseController
{
	public $modelClass = 'api\models\Score';


	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'authenticator' => [
				'optional' => [
					'getscore',
					'updatescore',
				],
			]
		]);
	}




	public function actionGetscore()
	{
		$get_params = Yii::$app->request->get();
		$mobile = $get_params['mobile'];
		$sign = $get_params['sign'];
		//echo  md5($mobile.Yii::$app->params['score.key']);
		if($this->checkSign($mobile,$sign)){
			$user = User::findByMobile($mobile);
			if($user){
				$clinic = $user->clinicUu;
				$result['score']=$clinic->attributes['score'];
				return $result;
			}else{
				return Service::sendError(21102,'用户不存在');
			}

		}else{
			return Service::sendError(21101,'参数错误');
		}
	}

	public function actionUpdatescore(){
		$get_params = Yii::$app->request->get();
		$mobile = $get_params['mobile'];
		$sign = $get_params['sign'];
		if(!$this->checkSign($mobile,$sign)){
			return Service::sendError(21101,'参数错误');
		}

		$user = User::findByMobile($mobile);
		if($user){
			$clinic = $user->clinicUu;
			$score = $get_params['score'];
			$reason = $get_params['reason'];

			$status = $this->updateScore($clinic->attributes,$score,$reason);
			if($status){
				return Service::sendSucc();
			}else{
				return Service::sendError(21103,'积分修改失败');
			}
		}else{
			return Service::sendError(21102,'用户不存在');
		}


	}


	private function checkSign($mobile,$sign)
	{
		$key = Yii::$app->params['score.key'];
		$check_sign = md5($mobile.$key);
		if($sign == $check_sign){
			return true;
		}else{
			return false;
		}
	}

	private function updateScore($clinic,$score, $reason = '')
	{
		if (empty($score))
			return false;

		$score = intval($score);

		$oldScore = $clinic['score'];
		//更新积分
		Yii::$app->db->createCommand()->update('clinic', ['score' => new \yii\db\Expression("`score` + " . $score)], 'user_uuid = "' .$clinic['user_uuid'] . '"')->execute();

		//记录积分日志
		$scoreLog = new ScoreLog();
		$scoreLog->clinic_uuid = $clinic['user_uuid'];
		$scoreLog->old_score = $oldScore;
		$scoreLog->add_score = $score;
		$scoreLog->new_score = $oldScore + $score;
		$scoreLog->reason = $reason;
		$scoreLog->save();

		return true;
	}

}