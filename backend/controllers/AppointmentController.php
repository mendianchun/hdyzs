<?php

namespace backend\controllers;

use Yii;
use common\models\Appointment;
use common\models\AppointmentSearch;
use common\models\ExpertTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */
class AppointmentController extends Controller
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Appointment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->appointment_no]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Appointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->appointment_no]);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionCancel($id)
    {
	    $model = $this->findModel($id);

	    if ($model->load(Yii::$app->request->post()) ) {
	    	$model->status=3;
		    $model->save();
		    return $this->redirect(['view', 'id' => $model->appointment_no]);
	    } else {
		    return $this->renderAjax('Cancel', [
			    'model' => $model,
		    ]);
	    }

    }

    /**
     * Deletes an existing Appointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        $model = $this->findModel($id);
        if ($model->approve())  //审核
        {
            $model = $this->findModel($id);
            $this->ordertime($model->attributes);
            return $this->redirect(['index']);
        }
    }

    public function actionPay($id)
    {
        $model = $this->findModel($id);
        if ($model->pay())  //支付
        {
            return $this->redirect(['index']);
        }
    }


    private function ordertime($model)
    {
        $expert_uuid = $model['expert_uuid'];
        $start_time = $model['order_starttime'];
        $end_time = $model['order_endtime'];
        $appoint_no = $model['appointment_no'];
        $clinic_uuid = $model['clinic_uuid'];
        $date = date('Y-m-d', $start_time);
        $use_set = array();

        for ($start_time; $start_time < $end_time; $start_time = $start_time + 1800) {
            if ($start_time % 3600 == 0) {
                $hour = date('h', $start_time);
                $use_set[(int)$hour][1] = 1;
            } else {
                $hour = date('h', $start_time);
                $use_set[(int)$hour][2] = 1;
            }
        }
        $cnt = 0;

        $new_oredr['is_order'] = 1;
        $new_oredr['clinic_uuid'] = $clinic_uuid;
        $new_oredr['order_no'] = $appoint_no;
        foreach ($use_set as $k_sets => $value_sets) {
            foreach ($value_sets as $k => $v) {
                $op_status = ExpertTime::updateAll($new_oredr,
                    ['expert_uuid' => $expert_uuid,
                        'date' => $date,
                        'hour' => $k_sets,
                        'zone' => $k]);

                if ($op_status > 0) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    /*
     * 接口，供前端使用
     */
    public function actionGetpengdingcount()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $count = Appointment::getPengdingCount();

        return ['count'=>$count];
    }
}
