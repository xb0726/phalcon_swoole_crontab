<?php
return new Phalcon\Config([
    // 目录设置
    'dirs'          => [
        'configDir' => ROOT_PATH . "config/",
        'logsDir'   => ROOT_PATH . 'logs/',
    ],

    // REDIS配置
    'redis'         => [
        'development' => [
            'host'       => '127.0.0.1',
            'password'   => null,
            'port'       => 6379,
            'timeout'    => 60,
            'auth'       => '',
            'persistent' => false,
        ],
        'production'  => [
            'host'       => '127.0.0.1',
            'password'   => null,
            'port'       => 6379,
            'timeout'    => 60,
            'auth'       => '',
            'persistent' => false,
        ],
    ],

    // REDIS队列
    'redis_queue'   => [
        'mail' => 'REDIS_EMAIL_QUEUE',// 邮件队列名称
        'sms'  => 'REDIS_SMS_QUEUE',// 短信队列名称
    ],

    // 时区
    'datetime_zone' => 'Asia/Shanghai',
]);
