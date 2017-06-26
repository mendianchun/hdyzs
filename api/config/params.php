<?php
return [
    'adminEmail' => 'admin@example.com',
    // token 有效期默认1天
    'user.apiTokenExpire' => 1*24*3600,
    'upload.type' => ['avatar','clinic','patient'],
    'upload.imageType' => ['jpg','jpeg','gif','png'],
    'upload.maxsize' => '5', //MB
    'list.pagesize' => 10,
];
