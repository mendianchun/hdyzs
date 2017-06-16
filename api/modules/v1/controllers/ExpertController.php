<?php
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use common\models\Expert;
use common\models\ExpertSearch;

use yii\helpers\ArrayHelper;
use common\service\Service;


class ExpertController extends ActiveController
{
    public $modelClass = 'common\models\Expert';//对应的数据模型处理控制器

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

    public function actionSearch()
    {
        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

        $params['ExpertSearch']['name'] = isset($queryParam['name']) ? $queryParam['name'] : null;

        $userSearch = new ExpertSearch();
        $provider = $userSearch->search($params,$pageSize);
        $data = $provider->getModels();

        foreach($data as $k => &$v){
            if(!empty($v['free_time'])){
                $v['free_time'] = json_decode($v['free_time'],true);
            }
        }

        $totalPage = ceil($provider->totalCount / $pageSize);

        if(!isset($queryParam['page']) || $queryParam['page'] <= 0){
            $currentPage = 1;
        }else{
            $currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
        }

        $data['extraFields']['currentPage'] = $currentPage;
        $data['extraFields']['totalCount'] = $provider->totalCount;
        $data['extraFields']['totalPage'] = $totalPage;
        return $data;
    }

    public function actionTest(){
        $array = array(
            "1" => ['08:00-11:00','13:00-16:00'],
            "2" => ['09:00-11:30','13:00-16:00','20:00-22:00'],
            "3" => ['08:00-11:00','13:00-16:00'],
            "4" => ['08:00-11:00','13:00-16:00'],
            "5" => ['08:00-11:00','13:00-16:00'],
            "6" => ['08:00-11:00','13:00-16:00'],
        );

        echo json_encode($array);
    }
}  