<?php
return [
    'taskid1' =>
        [
            'taskname' => 'Demo',  //任务名称
            'rule'     => '*/10 * * * * *',//定时规则
            "unique"   => 1, //排他数量，如果已经有这么多任务在执行，即使到了下一次执行时间，也不执行
            'execute'  => 'Demo',//命令处理类
            'args'     =>
                [
                    'cmd' => 'sendSms',//命令
                    'ext' => '',//附加属性
                ],
        ],
];
