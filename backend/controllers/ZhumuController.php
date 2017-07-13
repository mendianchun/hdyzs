<?php

namespace backend\controllers;

use Yii;
use common\models\Zhumu;
use common\models\ZhumuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\service\Service;
use common\models\SystemConfig;

/**
 * ZhumuController implements the CRUD actions for Zhumu model.
 */
class ZhumuController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Zhumu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ZhumuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Zhumu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Zhumu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Zhumu();
        $model->uuid = Service::create_uuid();

        if ($model->load(Yii::$app->request->post())){
            //获取瞩目zcode
            $url = Yii::$app->params['zhumu.getuser'];

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
            if (isset($systemConfig)) {
                $api_key = $systemConfig['value'];
            }

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
            if (isset($systemConfig)) {
                $api_secret = $systemConfig['value'];
            }

            $postData = ['api_key' => $api_key,'api_secret'=>$api_secret,'logintype'=>3,'loginname'=>$model->username];

            $zhumu_ret = Service::curl_post($postData,$url);
            if(is_string($zhumu_ret)){
                $zhumu_ret_array = json_decode($zhumu_ret,true);
                if(isset($zhumu_ret_array['zcode'])){
                    $model->zcode = ''.$zhumu_ret_array['zcode'].'';
                }else{
                    $model->zcode = '111111';
                }
            }

            if($model->save()) {
                return $this->redirect(['index']);
            }else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Zhumu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())){
            //获取瞩目zcode
            $url = Yii::$app->params['zhumu.getuser'];

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
            if (isset($systemConfig)) {
                $api_key = $systemConfig['value'];
            }

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
            if (isset($systemConfig)) {
                $api_secret = $systemConfig['value'];
            }

            $postData = ['api_key' => $api_key,'api_secret'=>$api_secret,'logintype'=>3,'loginname'=>$model->username];

            $zhumu_ret = Service::curl_post($postData,$url);
            if(is_string($zhumu_ret)){
                $zhumu_ret_array = json_decode($zhumu_ret,true);
                if(isset($zhumu_ret_array['zcode'])){
                    $model->zcode = ''.$zhumu_ret_array['zcode'].'';
                }else{
                    $model->zcode = '111111';
                }
            }

            if($model->save()) {
                return $this->redirect(['index']);
            }else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Zhumu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $zhumu = $this->findModel($id);
        $zhumu->status = Zhumu::STATUS_DELETED;
        $zhumu->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Zhumu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Zhumu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Zhumu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
