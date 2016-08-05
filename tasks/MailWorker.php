<?php
namespace PhalCron\Tasks;

use PhalCron\Library\AbstractWorker;
use PhalCron\Library\Output;

/**
 * Class MailWorker
 * @package PhalCron\Tasks
 */
class MailWorker extends AbstractWorker
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
        Output::stdout('Worker:[MailWorker], Msg:[' . $task . "], 已执行.");
    }

    // 发送邮件
    public function send()
    {

    }
}
