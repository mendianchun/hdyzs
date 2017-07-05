<?php

namespace backend\components;

use Yii;
use yii\helpers\Url;

class AdminLog
{
    public static function write($event)
    {
        // 排除日志表自身,没有主键的表不记录（没想到怎么记录。。每个表尽量都有主键吧，不一定非是自增id）
        if($event->sender instanceof \common\models\AdminLog || !$event->sender->primaryKey()) {
            return;
        }
        // 显示详情有待优化,不过基本功能完整齐全
        if ($event->name == \yii\db\ActiveRecord::EVENT_AFTER_INSERT) {
            $description = "新增了表%s %s:%s的%s";
        } elseif($event->name == \yii\db\ActiveRecord::EVENT_AFTER_UPDATE) {
            $description = "修改了表%s %s:%s的%s";
        } else {
            $description = "删除了表%s %s:%s的%s";
        }
        if (!empty($event->changedAttributes)) {
            $desc = '';
            foreach($event->changedAttributes as $name => $value) {
                $desc .= $name . ' : ' . $value . '=>' . $event->sender->getAttribute($name) . ',';
            }
            $desc = substr($desc, 0, -1);
        } else {
            $desc = '';
        }
//        $userName = Yii::$app->user->identity->username;
        $tableName = $event->sender->tableSchema->name;
        //drug_code表的批量导入过滤掉，因为太多了。
        if($tableName == 'drug_code' && $event->name == \yii\db\ActiveRecord::EVENT_AFTER_INSERT){
            return;
        }
        $description = sprintf($description, $tableName, $event->sender->primaryKey()[0], $event->sender->getPrimaryKey(), $desc);

        $route = Url::to();
        $userId = Yii::$app->user->id;
        $ip = ip2long(Yii::$app->request->userIP);
        $data = [
            'route' => $route,
            'description' => $description,
            'user_id' => $userId,
            'created_at' => time(),
            'ip' => $ip
        ];
        $model = new \common\models\AdminLog();
        $model->setAttributes($data);
        $model->save();
    }
}