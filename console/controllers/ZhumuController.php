<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use common\models\Appointment;
use common\models\AppointmentVideo;
use common\models\SystemConfig;
use common\service\Service;

class ZhumuController extends Controller
{
    public $appointment_no;

    public function options($actionID)
    {
        return ['appointment_no'];
    }

    public function optionAliases()
    {
        return ['a' => 'appointment_no'];
    }

    public function actionTest()
    {

        $file1 = "/Users/damen/work/code/hdykt/zhumu/170614173547021730/1234/20170628161755.mp4";
        $file2 = "/Users/damen/work/code/hdykt/zhumu/170614173547021730/1234/20170623144751.mp4";

        $file = "/Users/damen/work/code/hdykt/zhumu/170614173547021730/1234/final.mp4";

        $ffmpeg = FFMpeg::create();

        $video = $ffmpeg->open($file1);

        $video->concat(array($file1, $file2))
            ->saveFromSameCodecs($file, TRUE);
    }

    public function actionIndex()
    {
        echo "--------------------" . date("Y-m-d H:i:s") . "-run begin---------------------\n";
        if (!empty($this->appointment_no)) {
            //生成失败的，手动生成
            $appoinments = Appointment::find()
                ->where(['appointment_no' => $this->appointment_no])
                ->all();
        } else {
            //拿出所有未生成音频的预约单 有实际结束时间但没有生成音频地址的
            $appoinments = Appointment::find()
                ->where(['dx_status' => Appointment::DX_STATUS_DO, 'audio_status' => Appointment::AUDIO_STATUS_UNDO])
                ->all();
        }
        if (!empty($appoinments) && is_array($appoinments)) {
            foreach ($appoinments as $appoinment) {
                //开始生成
                $appoinment->audio_status = Appointment::AUDIO_STATUS_DONING;
                $appoinment->save();
                $audio_url = $this->getAudio($appoinment->appointment_no);
                if (!empty($audio_url) && is_file(($audio_url))) {
                    //生成成功，保存相对地址
                    $appoinment->audio_url = str_replace(Yii::$app->params['zhumu.basedir'] . "/", "", $audio_url);
                    $appoinment->audio_created_at = time();
                    $appoinment->audio_status = Appointment::AUDIO_STATUS_SUCC;
                    $appoinment->save();
//                    var_dump($appoinment,$appoinment->save());
                    echo $appoinment->appointment_no . "处理完成，音频地址：" . $audio_url . "\n";
                } else {
                    //生成失败
                    $appoinment->audio_status = Appointment::AUDIO_STATUS_FAILED;
                    $appoinment->save();
//                    var_dump($appoinment,$appoinment->save());
                    echo $appoinment->appointment_no . "处理失败\n";
                }
            }
        } else {
            echo "没有需要处理的预约单\n";
        }

        echo "--------------------" . date("Y-m-d H:i:s") . "-run end---------------------\n";
    }

    private function getAudio($appointment_no)
    {
        if (empty($appointment_no))
            return false;
//        $basedir = getcwd();
//        $basedir = dirname(__FILE__);
//        $basedir = Yii::getAlias('@yii_base');
        //音频地址
        $targetFolder = Yii::$app->params['zhumu.basedir'] . "/" . $appointment_no;
        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($targetFolder, 0777);

        //先要获取会议的视频
//        $videos = $this->getVideo($appointment_no, $targetFolder);
////        var_dump($videos);
//
//        $audio = $this->video2Audio($videos, $targetFolder);

        //先要获取会议的m4a资源
        $resources = $this->getResource($appointment_no);
        if ($resources === false || empty($resources))
            return false;
//        var_dump($videos);

        $audio = $this->resource2Audio($resources, $appointment_no);

        return $audio;
    }

    private function getResource($appointment_no)
    {
        $resourceArray = array();
        //获取所有视频会议信息
        $appointmentVideos = AppointmentVideo::findAll(['appointment_no' => $appointment_no]);
        if (is_array($appointmentVideos)) {
            foreach ($appointmentVideos as $appointmentVideo) {
//                echo $appointment_no . "|" . $appointmentVideo->meeting_number . "|" . $appointmentVideo->zhumu_uuid . "\n";

                if (empty($appointmentVideo->video_url) || !is_file($appointmentVideo->video_url)) {
                    $ret = $this->downloadResource($appointmentVideo->zhumuUu->zcode, $appointment_no, $appointmentVideo->meeting_number);
                    //下载资源失败，直接返回，标名此次获取音频失败。
                    if ($ret['code'] == 0) {
//                        $appointmentVideo->video_url = $ret['data'];
                        $video_url =$this->resource2Audio($ret['data'], $appointment_no, $appointmentVideo->meeting_number);
                        //保存相对地址
                        $appointmentVideo->video_url = str_replace(Yii::$app->params['zhumu.basedir'] . "/", "", $video_url);
                        $appointmentVideo->save();
                    } elseif ($ret['code'] == -100) {
                        echo "-100:会议号：" . $appointmentVideo->meeting_number . "下载资源失败\n";
                        return false;
                    } else {
                        echo "-200:会议号：" . $appointmentVideo->meeting_number . "资源为空\n";
                        continue;
                    }
                }
                $resourceArray[] = $video_url;
            }
        }
        return $resourceArray;
    }

    private function downloadResource($zcode, $appointment_no, $meeting_number)
    {
        static $api_key = null;
        static $api_secret = null;

        //从瞩目下载视频
        $meetingFolder = Yii::$app->params['zhumu.basedir'] . "/" . $appointment_no . "/" . $meeting_number;

        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($meetingFolder, 0777);

        $url = Yii::$app->params['zhumu.mcrecording'];

        if ($api_key === null) {
            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
            if (isset($systemConfig)) {
                $api_key = $systemConfig['value'];
            }
        }

        if ($api_secret === null) {
            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
            if (isset($systemConfig)) {
                $api_secret = $systemConfig['value'];
            }
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'meeting_id' => $meeting_number, 'zcode' => $zcode];

        $ret = Service::curl_post($postData, $url);
        if (is_string($ret)) {
            $retArr = json_decode($ret, true);
//            print_r($retArr);exit;
            if (isset($retArr['Data']['meetings'][0]['recording_files']) && is_array($retArr['Data']['meetings'][0]['recording_files'])) {
                $resoureArray = array();
                foreach ($retArr['Data']['meetings'][0]['recording_files'] as $file) {
                    if (strtoupper($file['file_type']) == 'M4A') {
                        $m4a = $meetingFolder . "/" . date("YmdHis") . "_" . rand(1000, 9999) . ".m4a";
                        if (Service::download($file['file_path'], $m4a, $file['file_size'])) {
                            $resoureArray[] = $m4a;
                        } else {
                            return ['code' => -100, 'data' => null];
                        }
                    }
                }
                if (!empty($resoureArray)) {
                    return ['code' => 0, 'data' => $resoureArray];
                }
            }
        }

        return ['code' => -200, 'data' => null];
    }

    private function resource2Audio($resources, $appointment_no, $meeting_number = 0)
    {
        if (!is_array($resources) || empty($resources))
            return false;

        if ($meeting_number != 0) {
            $targetFolder = Yii::$app->params['zhumu.basedir'] . "/" . $appointment_no . "/" . $meeting_number;
        } else {
            $targetFolder = Yii::$app->params['zhumu.basedir'] . "/" . $appointment_no;
        }

        $m4a = $targetFolder . "/" . date("YmdHis") . "_" . rand(1000, 9999) . ".m4a";
        $list = $targetFolder . "/list.txt";

        //生成list文件
        $fp = fopen($list, "w");
        if ($fp) {
            foreach ($resources as $v) {
                $input = "file '" . $v . "'\n";
                fwrite($fp, $input);
            }
            fclose($fp);
        }

        $cmd = 'ffmpeg -f concat -safe 0 -i ' . $list . ' -c copy ' . $m4a;
        exec($cmd);
        return $m4a;

    }

    //----------------------------------------disable-------------------------------------------------//
    private function getVideo($appointment_no, $targetFolder)
    {
        $videoArray = array();
        //获取所有视频会议信息
        $appointmentVideos = AppointmentVideo::findAll(['appointment_no' => $appointment_no]);
        if (is_array($appointmentVideos)) {
            foreach ($appointmentVideos as $appointmentVideo) {
//                echo $appointment_no . "|" . $appointmentVideo->meeting_number . "|" . $appointmentVideo->zhumu_uuid . "\n";

                if (empty($appointmentVideo->video_url) || !is_file($appointmentVideo->video_url)) {
                    $video_url = $this->downloadVideo(1, 2, $appointmentVideo->meeting_number, $targetFolder);
                    $appointmentVideo->video_url = $video_url;
                    $appointmentVideo->save();
                }
                $videoArray[] = $appointmentVideo->video_url;
            }
        }
        return $videoArray;
    }

    private function downloadVideo($username, $password, $meeting_number, $targetFolder)
    {
        //从瞩目下载视频
        $meetingFolder = $targetFolder . "/" . $meeting_number;
        $time = date("YmdHis");
        $video = $meetingFolder . "/" . $time . ".mp4";

        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($meetingFolder, 0777);

        $filePrefix = rand(1, 2);
        copy($targetFolder . "/../" . $filePrefix . ".mp4", $video);

        return $video;
    }

    private function video2Audio($videos, $targetFolder)
    {
        if (!is_array($videos))
            return false;

        $time = date("YmdHis");
        $mp3 = $targetFolder . "/" . $time . ".mp3";
        $mp4 = $targetFolder . "/" . $time . ".mp4";

        $ffmpeg = FFMpeg::create();

        //合成视频
        $video = $ffmpeg->open($videos[0]);

        $video->concat($videos)
            ->saveFromSameCodecs($mp4, TRUE);

        //转成音频
        // Open your video file
        $video_mp4 = $ffmpeg->open($mp4);

        // Set an audio format
        $audio_format = new Mp3();

        // Extract the audio into a new file
        $video_mp4->save($audio_format, $mp3);

        return $mp3;

    }


    public function actionDownload()
    {
        $url = 'http://localhost/1.mp4';
        if ($url == '') return false;
        $fp = fopen($url, 'r') or exit('Open url faild!');
        $file_path = time() . ".mp4";
        $myfile = fopen($file_path, "w") or die("Unable to open file!");

        if ($fp) {
            while (!feof($fp)) {
                fwrite($myfile, fgets($fp) . "");
            }
            fclose($fp);
            fclose($myfile);

            // 下载完删除
            //unlink($file_path);
        }
    }


//    public function actionIndex()
//    {
//
//        $appointmentVideoStatus = array();
//
//        echo "---------------------run begin---------------------\n";
//        //拿出所有未生成音频的预约单及房间号
//        $appointmentVideo = AppointmentVideo::find()->where(['status' => AppointmentVideo::STATUS_UNDO])->all();
//        foreach ($appointmentVideo as $a_appointmentVideo) {
//            $appointment_no = $a_appointmentVideo->appointment_no . "\n";
//
//            //查看预约单是否已经完成
//            if (!isset($appointmentVideoStatus[$appointment_no])) {
//                $appoinment = Appointment::findOne(['appointment_no' => $appointment_no]);
//                if (!empty($appoinment) && $appoinment->real_endtime > 0) {
//                    $appointmentVideoStatus[$appointment_no] = 1;
//                } else {
//                    $appointmentVideoStatus[$appointment_no] = 0;
//                }
//            }
//
//            if ($appointmentVideoStatus[$appointment_no] == 1) {
//                $mp3 = $this->genAudio($a_appointmentVideo);
//                echo $a_appointmentVideo->appointment_no . "处理完成" . ",资源：" . $mp3 . "\n";
//            } else {
//                echo $a_appointmentVideo->appointment_no . "还未完成\n";
//            }
//        }
//
//
//        echo "---------------------run end---------------------\n";
//    }
//
//    private function genAudio($appointmentVideo)
//    {
//
//        $basedir = getcwd();
//        if (empty($appointmentVideo))
//            return false;
//
//        //将状态修改为生成中
//        $appointmentVideo->status = AppointmentVideo::STATUS_DOING;
//        $appointmentVideo->save();
//
//        //从瞩目下载视频
//        $targetFolder = $basedir . "/zhumu/" . $appointmentVideo->appointment_no . "/" . $appointmentVideo->meeting_number;
//        $time = date("YmdHis");
//        $video = $targetFolder . "/" . $time . ".mp4";
//        $mp3 = $targetFolder . "/" . $time . ".mp3";
//
//        $file = new \yii\helpers\FileHelper();
//        $file->createDirectory($targetFolder);
//
//        copy($basedir . "/zhumu/1.mp4", $video);
//
//        $ffmpeg = FFMpeg::create();
//
//        // Open your video file
//        $video = $ffmpeg->open($video);
//
//        // Set an audio format
//        $audio_format = new Mp3();
//
//        // Extract the audio into a new file
//        $video->save($audio_format, $mp3);
//        //转成mp3
//
//        //成功更改状态为生成完成，失败更改状态为生成失败
//        $appointmentVideo->audio_url = $mp3;
//        $appointmentVideo->status = AppointmentVideo::STATUS_FINISH;
//        $appointmentVideo->save();
//
//        return $mp3;
//    }
}