<?php

namespace console\controllers;

use yii\console\Controller;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use common\models\Appointment;
use common\models\AppointmentVideo;

class ZhumuController extends Controller
{
    public function actionIndex(){

        $appointmentVideoStatus = array();

        echo "---------------------run begin---------------------\n";
        //拿出所有未生成音频的预约单及房间号
        $appointmentVideo = AppointmentVideo::find()->where(['status'=>AppointmentVideo::STATUS_UNDO])->all();
        foreach($appointmentVideo as $a_appointmentVideo){
            $appointment_no = $a_appointmentVideo->appointment_no."\n";

            //查看预约单是否已经完成
            if(!isset($appointmentVideoStatus[$appointment_no])){
                $appoinment = Appointment::findOne(['appointment_no'=>$appointment_no]);
                if(!empty($appoinment) && $appoinment->real_endtime > 0){
                    $appointmentVideoStatus[$appointment_no] = 1;
                }else{
                    $appointmentVideoStatus[$appointment_no] = 0;
                }
            }

            if($appointmentVideoStatus[$appointment_no] == 1){
                $mp3 = $this->genAudio($a_appointmentVideo);
                echo $a_appointmentVideo->appointment_no."处理完成".",资源：".$mp3."\n";
            }else{
                echo $a_appointmentVideo->appointment_no."还未完成\n";
            }
        }


        echo "---------------------run end---------------------\n";
    }

    private function genAudio($appointmentVideo){

        $basedir = getcwd();
        if(empty($appointmentVideo))
            return false;

        //将状态修改为生成中
        $appointmentVideo->status = AppointmentVideo::STATUS_DOING;
        $appointmentVideo->save();

        //从瞩目下载视频
        $targetFolder = $basedir."/zhumu/".$appointmentVideo->appointment_no."/".$appointmentVideo->meeting_number;
        $time = date("YmdHis");
        $video = $targetFolder."/".$time.".mp4";
        $mp3 = $targetFolder."/".$time.".mp3";

        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($targetFolder);

        copy($basedir."/zhumu/1.mp4",$video);

        $ffmpeg = FFMpeg::create();

        // Open your video file
        $video = $ffmpeg->open( $video );

        // Set an audio format
        $audio_format = new Mp3();

        // Extract the audio into a new file
        $video->save($audio_format,$mp3);
        //转成mp3

        //成功更改状态为生成完成，失败更改状态为生成失败
        $appointmentVideo->audio_url = $mp3;
        $appointmentVideo->status = AppointmentVideo::STATUS_FINISH;
        $appointmentVideo->save();

        return $mp3;
    }
}