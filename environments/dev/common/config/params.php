<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
	'appointment.cancel_msg'=>'您好，您的会诊因故取消，请知晓',
	'appointment.start_msg'=>'您好，您的会诊将于半小时后开始，请您准备',
	'verifycode.msg' => '%s（动态验证码）,请在30分钟内填写。',
    // 图片服务器的域名设置，拼接保存在数据库中的相对地址，可通过web进行展示
    'domain' => 'http://img.handianzy.com/',
	'score.key'=>'hhddyyzzss',

	'time.8'=>'morning',
	'time.9'=>'morning',
	'time.10'=>'morning',
	'time.11'=>'morning',
	'time.12'=>'morning',

	'time.13'=>'afternoon',
	'time.14'=>'afternoon',
	'time.15'=>'afternoon',
	'time.16'=>'afternoon',
	'time.17'=>'afternoon',
	'time.18'=>'afternoon',

	'time.19'=>'evening',
	'time.20'=>'evening',
	'time.21'=>'evening',

	'zone.1.start'=>'00',
	'zone.1.end'=>'29',
	'zone.2.start'=>'30',
	'zone.2.end'=>'59',

	'zhumu.getuser' => 'https://api.zhumu.me/v3/user/get',
	'zhumu.mcrecording' => 'https://api.zhumu.me/v3/meeting/mcrecording',
	'zhumu.createmeeting' => 'https://api.zhumu.me/v3/meeting/create',
	'zhumu.endmeeting' => 'https://api.zhumu.me/v3/meeting/end',
	'zhumu.getmeeting' => 'https://api.zhumu.me/v3/meeting/get',

	'zhumu.basedir' => Yii::getAlias('@yii_base') . "/data/zhumu",

	//短信接口配置
	'sms_host'=>'http://101.227.68.49:7891/mt?',
	'sms_user'=>'10690116',
	'sms_pwd'=>'SihAi429',
	'sms_prefix' => '【汉典云诊所】',

	//支付的时间限制
	'pay.time' => 15*60,
	'pay.channel' => ['alipay_qr','wx_pub_qr'],
];
