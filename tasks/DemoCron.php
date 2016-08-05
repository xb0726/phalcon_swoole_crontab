<?php
namespace PhalCron\Tasks;

use PhalCron\Library\AbstractCrontab;
use PhalCron\Library\Main;
use PhalCron\Library\Output;


/**
 * Class DemoCron
 * @package PhalCron\Tasks
 */
class DemoCron extends AbstractCrontab
{
    /**
     * @param $task
     */
    public function run($task)
    {
        $cmd = $task["cmd"];
        $status = 0;
        $time = time();
        $this->$cmd($time);
        Output::stdout($cmd . ",已执行.status:" . $status);
        exit($status);
    }

    /**
     * @param $params
     */
    protected function sendSms($params)
    {
        echo $params, "\n\r";
    }
}
