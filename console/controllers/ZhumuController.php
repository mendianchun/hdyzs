<?php

namespace console\controllers;

use yii\console\Controller;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use common\models\Appointment;
use common\models\AppointmentVideo;

class ZhumuController extends Controller
{
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
        echo "---------------------run begin---------------------\n";
        //拿出所有未生成音频的预约单 有实际结束时间但没有生成音频地址的
        $appoinments = Appointment::find()
            ->where(['>', 'real_endtime', 0])
            ->andWhere(['audio_url' => ''])
            ->all();
//        var_dump($appoinments);exit;
        if (!empty($appoinments) && is_array($appoinments)) {
            foreach ($appoinments as $appoinment) {
//                echo $appoinment->appointment_no."\n";
//                continue;
                $audio_url = $this->getAudio($appoinment->appointment_no);
                if (!empty($audio_url) && is_file(($audio_url))) {
                    $appoinment->audio_url = $audio_url;
                    $appoinment->audio_created_at = time();
                    $appoinment->save();
                    echo $appoinment->appointment_no . "处理完成，音频地址：" . $audio_url . "\n";
                } else {
                    echo $appoinment->appointment_no . "处理失败，音频地址：" . $audio_url . "\n";
                }
            }
        } else {
            echo "没有需要处理的预约单\n";
        }

        echo "---------------------run end---------------------\n";
    }

    private function getAudio($appointment_no)
    {
        if (empty($appointment_no))
            return false;
        $basedir = getcwd();
        //音频地址
        $targetFolder = $basedir . "/zhumu/" . $appointment_no;
        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($targetFolder);

        //先要获取会议的视频
        $videos = $this->getVideo($appointment_no, $targetFolder);
//        var_dump($videos);

        $audio = $this->video2Audio($videos, $targetFolder);

        return $audio;
    }

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
        $file->createDirectory($meetingFolder);

        $filePrefix = rand(1,9);
        copy($targetFolder . "/../".$filePrefix.".mp4", $video);

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
}