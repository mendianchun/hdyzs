<?php
namespace api\modules\v1\controllers;

use Yii;

use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

use common\models\UploadForm;
use yii\web\UploadedFile;

use yii\web\HttpException;

class UploadController extends ActiveController{


    public $modelClass = 'common\models\UploadForm';//对应的数据模型处理控制器


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'img'
                ],
            ]
        ]);
    }

    /**
     * ajax上传图片
     */
    public function actionImg()
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