<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/12
 * Time: 下午2:16
 */
namespace api\modules\v1\controllers;

use Yii;

use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class PayController extends ActiveController{


    public $modelClass = 'common\models\User';//对应的数据模型处理控制器


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'pingxx'
                ],
            ]
        ]);
    }

    /**
     * pingxx支付
     */
    public function actionPingxx()
    {
        $model = new UploadForm();

        $data = array();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                $model->file->saveAs('uploads/' . $model->file->baseName . '.' . $model->file->extension);
                $code = 0;
                $message = 'succ';
                $data['url'] = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
            } else {
                $code = 600;
                $message = array_values($model->getFirstErrors())[0];
                $data = [];
            }
        }else {
            $code = 600;
            $message = 'no data';
            $data = [];
        }

        return [
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ];
    }
}