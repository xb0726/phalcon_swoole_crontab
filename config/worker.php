<?php

return [
    //key是要加载的worker类名
    "Sms"  => [
        "name"       => "短信",                      //备注名
        "processNum" => 1,                          //启动的进程数量
        "redis"      => ["queue_name" => "sms"]         // redis队列名
    ],

    //key是要加载的worker类名
    "Mail" => [
        "name"       => "邮件",                      //备注名
        "processNum" => 1,                          //启动的进程数量
        "redis"      => ["queue_name" => "mail"]         // redis队列名
    ],
];
