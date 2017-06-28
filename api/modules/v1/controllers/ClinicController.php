<?php
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use common\models\Clinic;

use yii\helpers\ArrayHelper;
use common\service\Service;
use common\models\DrugCodeClinic;
use common\models\DrugCode;
use common\models\SystemConfig;

class ClinicController extends ActiveController
{
    public $modelClass = 'common\models\Clinic';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                ],
            ]
        ]);
    }

    /**
     * Creates a new Clinic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['name'])
            || !isset($post['address'])
            || !isset($post['tel'])
            || !isset($post['chief'])
            || !isset($post['idcard'])
            || !isset($post['Business_license_img'])
            || !isset($post['local_img'])
            || !isset($post['doctor_certificate_img'])
        ) {
            return Service::sendError(10400, '缺少参数');
        }

        //当前用户
        $user = \yii::$app->user->identity;

        //判断当前用户类型，只有诊所(type=2)才可以注册，并且之前没有注册过
        if ($user->type != 2) {
            return Service::sendError(20701, '用户类型错误，不能注册诊所');
        }

        if (Clinic::findOne(['user_uuid' => $user->uuid])) {
            return Service::sendError(20702, '用户已经注册过诊所了');
        }

        //检查名字是否已经被注册
        if (Clinic::findOne(['name' => $post['name']])) {
            return Service::sendError(20702, '诊所名称已经被注册了');
        }

        $clinic = new Clinic();
        $clinic->name = $post['name'];
        $clinic->address = $post['address'];
        $clinic->tel = $post['tel'];
        $clinic->chief = $post['chief'];
        $clinic->idcard = $post['idcard'];
        $clinic->Business_license_img = $post['Business_license_img'];
        $clinic->local_img = $post['local_img'];
        $clinic->doctor_certificate_img = $post['doctor_certificate_img'];
        $clinic->user_uuid = $user->uuid;
        if ($clinic->save()) {
            return Service::sendSucc();
        } else {
            return Service::sendError(20703, '注册出错');
        }
    }

    public function actionUpdate()
    {
        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['name'])
            || !isset($post['address'])
            || !isset($post['tel'])
            || !isset($post['chief'])
            || !isset($post['idcard'])
            || !isset($post['Business_license_img'])
            || !isset($post['local_img'])
            || !isset($post['doctor_certificate_img'])
        ) {
            return Service::sendError(10400, '缺少参数');
        }

        //当前用户
        $user = \yii::$app->user->identity;

        //判断当前用户类型，只有诊所(type=2)才可以注册
        if ($user->type != 2) {
            return Service::sendError(20701, '用户类型错误，不能注册诊所');
        }

        $clinic = $user->clinicUu;
        if (!$clinic) {
            return Service::sendError(20704, '用户还没有注册诊所');
        }

        //审核过程中的不能进行编辑
        if ($clinic->verify_status == 1) {
            return Service::sendError(20705, '正在审核中，不能编辑');
        }

        //检查名字是否已经被注册
        if (Clinic::find()->where(['<>', 'user_uuid', $user->uuid])
            ->andwhere(['name' => $post['name']])
            ->one()
        ) {
            return Service::sendError(20702, '诊所名称已经被注册了');
        }

        $clinic->name = $post['name'];
        $clinic->address = $post['address'];
        $clinic->tel = $post['tel'];
        $clinic->chief = $post['chief'];
        $clinic->idcard = $post['idcard'];
        $clinic->Business_license_img = $post['Business_license_img'];
        $clinic->local_img = $post['local_img'];
        $clinic->doctor_certificate_img = $post['doctor_certificate_img'];
        $clinic->verify_status = 1;
        $clinic->verify_reason = '';
        $clinic->user_uuid = $user->uuid;
        if ($clinic->save()) {
            return Service::sendSucc();
        } else {
            return Service::sendError(20706, '编辑出错');
        }
    }

    public function actionDrugcode()
    {
        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['code'])) {
            return Service::sendError(10400, '缺少参数');
        }

        $code = trim($post['code']);

        //当前用户
        $user = \yii::$app->user->identity;

        //判断当前用户类型，只有诊所(type=2)才可以提交药品监管码
        if ($user->type != 2) {
            return Service::sendError(20707, '用户类型错误，不能提交药品监管码');
        }

        $clinic = $user->clinicUu;
        if (!$clinic) {
            return Service::sendError(20704, '用户还没有注册诊所');
        }

        //只有审核通过的才能提交药品监管码
        if ($clinic->verify_status != 2) {
            return Service::sendError(20708, '用户当前状态不能提交药品监管码');
        }

        //判断该药品监管码是否提交过
        $drug_code_clinic = DrugCodeClinic::findOne(['code' => $code]);
        if(!empty($drug_code_clinic)){
            return Service::sendError(20709, '该药品监管码已经提交过');
        }

        //判断药品监管码是否存在
        $drug_code = DrugCode::findOne(['code' => $code]);
        if(empty($drug_code)){
            return Service::sendError(20710, '该药品监管码不存在');
        }

        $drug_code_clinic = new DrugCodeClinic();
        $drug_code_clinic->code = $code;
        $drug_code_clinic->clinic_uuid = $clinic->user_uuid;
        if($drug_code_clinic->save()){
            //获取系统药品监管码对应的积分配置
            $systemConfig = SystemConfig::findOne(['name' => 'drug_code_score']);
            if (isset($systemConfig)) {
                $drug_code_score = $systemConfig['value'];
            }
            if(intval($drug_code_score) > 0){
                //更新诊所积分
                $clinic->updateScore($drug_code_score,'drug_code:'.$code);
            }
            return Service::sendSucc();
        }else{
            return Service::sendError(20711, '处理失败');
        }
    }
}  