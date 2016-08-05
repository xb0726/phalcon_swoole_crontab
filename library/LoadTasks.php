<?php
namespace PhalCron\Library;

use PhalCron\Library\LoadTasks\Mysql;
use PhalCron\Library\LoadTasks\File;

/**
 * Class LoadTasks
 * @package PhalCron\Library
 */
class LoadTasks
{
    private $handle;

    public function __construct($type, $params = "")
    {
        switch ($type) {
            case "mysql":
                $this->handle = new Mysql($params);
                break;
            case "file":
            default:
                $this->handle = new File($params);
                break;
        }
    }

    /**
     * 获取需要执行的任务
     * @return array
     */
    public function getTasks()
    {
        return $this->handle->getTasks();
    }

    /**
     * 重载任务配置
     */
    public function reloadTasks()
    {
        $this->handle->reloadTasks();
    }
}
