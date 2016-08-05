<?php
namespace PhalCron\Library;

use Phalcon\DI;

/**
 * Class AbstractWorker
 * @package PhalCron\Library
 */
abstract class AbstractWorker
{

    private $_redis;
    private $queue;
    protected $worker;
    private $ppid = 0;

    public function content($config)
    {
        $di = DI::getDefault();
        $this->_redis = $di->get('redis');
        $this->queue = $di->get('config')->redis_queue->$config["queue_name"];
    }

    /**
     * 获取队列
     * 
     * @return mixed
     */
    public function getQueue()
    {
        return $this->_redis->rpop($this->queue);
    }

    public function tick($worker)
    {
        $this->worker = $worker;
        \swoole_timer_tick(500, function () {
            while (true) {
                $this->checkExit();
                $task = $this->getQueue();
                if (empty($task)) {
                    break;
                }
                $this->Run($task);
            }
        });
    }

    /**
     * 退出
     */
    protected function _exit()
    {
        $this->worker->exit(1);
    }

    /**
     * 判断父进程是否结束
     */
    private function checkExit()
    {
        $ppid = posix_getppid();
        if ($this->ppid == 0) {
            $this->ppid = $ppid;
        }
        if ($this->ppid != $ppid) {
            $this->_exit();
        }
    }

    /**
     * 运行入口
     * @param $task
     * @return mixed
     */
    abstract public function Run($task);
}
