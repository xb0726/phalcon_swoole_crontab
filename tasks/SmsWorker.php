<?php
namespace PhalCron\Tasks;

use PhalCron\Library\AbstractWorker;
use PhalCron\Library\Output;
use Phalcon\DI;

/**
 * Class SmsWorker
 * @package PhalCron\Tasks
 */
class SmsWorker extends AbstractWorker
{

    /**
     * 运行入口
     * @param $task
     * @return mixed
     */
    public function Run($task)
    {
        if ($task == "exit") {
            $this->_exit(2);
        }

        list($event, $mobile, $text) = explode('|', $task);
        $result = $this->send($mobile, $text);

        Output::stdout('Worker:[SmsWorker], Msg:[' . $task . "],已执行,result:" . $result);
    }

    // 发送短信
    public function send($mobile, $message)
    {
        // todo:这里写逻辑
    }
}
