<?php

namespace backend\controllers;

use Yii;
use common\models\DrugCode;
use common\models\DrugCodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\DrugCodeUploadForm;
use yii\web\UploadedFile;

/**
 * DrugcodeController implements the CRUD actions for DrugCode model.
 */
class DrugcodeController extends Controller
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
     * Lists all DrugCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DrugCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DrugCode model.
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
     * Creates a new DrugCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DrugCode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DrugCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DrugCode model.
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
     * Finds the DrugCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DrugCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DrugCode::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * import
     */
    public function actionImport()
    {
        $existCnt = 0;
        $failedCnt = 0;
        $dataErrorCnt = 0;
        $okCnt = 0;
        $totalCnt = 0;

        $model = new DrugCodeUploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                //要插入的表的名称
                $tableName = DrugCode::tableName();

                //要插入的字段
                $field = ['code','created_at'];

                //插入的时间
                $now = time();

                $fp = fopen($model->file->tempName, "r");
                while (!feof($fp)) {
                    $content = trim(fgets($fp));
                    if (substr($content, 0, 1) == '#' || empty($content))
                        continue;

                    //总数加1
                    $totalCnt++;

                    //格式检查，必须是20位的数字
                    if($this->checkCode($content)){
                        $dataErrorCnt++;
                        continue;
                    }

                    $insertData[] = [$content,$now];

                    if($totalCnt % 10000 == 0){
                        //执行插入--返回值为插入成功的数目
                        if(!empty($insertData)){
                            $okCnt += Yii::$app->db->createCommand()->batchInsert($tableName,$field,$insertData)->execute();
                            unset($insertData);
                        }
                    }
                }
                fclose($fp);

                if(!empty($insertData)){
                    $okCnt += Yii::$app->db->createCommand()->batchInsert($tableName,$field,$insertData)->execute();
                }
            }
        }

        $failedCnt = $totalCnt - $okCnt - $dataErrorCnt;
//        $result = "总数：" . $total_count . ",已经存在：" . $exist_count . ",成功数：" . $succ_count . ",失败数：" . $failed_count;
        return $this->render('import', [
            'model' => $model,
            'totalCnt' => $totalCnt,
            'dataErrorCnt' => $dataErrorCnt,
            'okCnt' => $okCnt,
            'failedCnt' => $failedCnt,
        ]);
    }

    protected function checkCode($code){
        //必须是20位的数字
        if(strlen($code) != 20 || !preg_match('/^\d+$/i', $code)){
            return false;
        }
        return true;
    }
}
