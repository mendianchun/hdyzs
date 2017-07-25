<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/29
 * Time: 下午3:31
 */
namespace backend\controllers;

use Yii;
use common\models\Appointment;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */
class DiagnosisController extends Controller
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
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $params = Yii::$app->request->queryParams;
        $params['AppointmentSearch']['status'] = Appointment::STATUS_SUCC;
        $dataProvider = $searchModel->search($params);


//		echo '<pre>';
//		var_dump($dataProvider->models);
//		exit();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Appointment model.
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
     * Creates a new Appointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
//		$model = new Appointment();
//
//		if ($model->load(Yii::$app->request->post()) && $model->save()) {
//			return $this->redirect(['view', 'id' => $model->appointment_no]);
//		} else {
//			return $this->render('create', [
//				'model' => $model,
//			]);
//		}
    }

    public function actionMp3($appointment_no)
    {
        if (empty($appointment_no)) {
            echo "预约单号为空";
            exit;
        }

        $appointment = Appointment::findOne(['appointment_no' => $appointment_no]);
        if (!$appointment) {
            echo "没有此预约单";
            exit;
        }
        //	$appointment->audio_url = '/Users/alex/Documents/alex/e.mp3';
        if (!$appointment->audio_url || !is_file(Yii::$app->params['zhumu.basedir'] . "/" . $appointment->audio_url)) {
            echo "还未生成音频";
            exit;
        }

        $file = Yii::$app->params['zhumu.basedir'] . "/" . $appointment->audio_url;
        $file_size = filesize($file);
        $ranges = $this->getRange($file_size);

        $fp = fopen($file, "rb");

        if ($ranges != null) {
            if ($ranges['start'] > 0 || $ranges['end'] < $file_size) {
                header('HTTP/1.1 206 Partial Content');
            }

            header("Accept-Ranges: bytes");
            header("Content-Type: audio/mpeg");
            // 剩余长度
            header(sprintf('Content-Length: %u', $ranges['end'] - $ranges['start']));

            // range信息
            header(sprintf('Content-Range: bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));

            // fp指针跳到断点位置
            fseek($fp, sprintf('%u', $ranges['start']));
        } else {
            //下载文件需要用到的头
            header("Accept-Ranges: bytes");
            header("Content-Type: audio/mpeg");
            header("Content-Length: " . $file_size);
        }

        $buffer = 1024;
        $file_count = 0;
//        $fpw = fopen('echo.mp3.log','w');
        //向浏览器返回数据
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
//            fwrite($fpw,ftell($fp)."|".$buffer."|".$file_count."|".$file_size."\n");
            echo $file_con;
        }
        fclose($fp);
        exit;
    }


    public function actionRebuild($id)
    {

        $command = "php ".Yii::getAlias('@yii_base')."/yii zhumu -a=$id & ";
        exec($command);
        $this->redirect(['diagnosis/index']);
    }

    /**
     * Updates an existing Appointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
//		$model = $this->findModel($id);
//
//		if ($model->load(Yii::$app->request->post()) && $model->save()) {
//			return $this->redirect(['view', 'id' => $model->appointment_no]);
//		} else {
//			return $this->render('update', [
//				'model' => $model,
//			]);
//		}
    }

    /**
     * Deletes an existing Appointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//		$this->findModel($id)->delete();
//
//		return $this->redirect(['index']);
    }

    /**
     * Finds the Appointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appointment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionApprove($id)
    {
//		$model = $this->findModel($id);
//		if($model->approve())  //审核
//		{
//			return $this->redirect(['index']);
//		}
    }

    public function actionPay($id)
    {
//		$model = $this->findModel($id);
//		if($model->pay())  //支付
//		{
//			return $this->redirect(['index']);
//		}
    }

	public function actionToundx($id)
	{
		$appointment = Appointment::findOne(['appointment_no' => $id]);
		//$appointment->attributes['dx_status']=1;
		$appointment->dx_status=1;
		$appointment->real_endtime=0;
		Appointment::updateAll($appointment,['appointment_no' => $id]);
		$this->redirect(['diagnosis/index']);

	}


    /** 获取header range信息
     * @param  int $file_size 文件大小
     * @return Array
     */
    private function getRange($file_size)
    {
        if (isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if (count($range) < 2) {
                $range[1] = $file_size;
            }
            $range = array_combine(array('start', 'end'), $range);
            if (empty($range['start'])) {
                $range['start'] = 0;
            }
            if (empty($range['end'])) {
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }
}