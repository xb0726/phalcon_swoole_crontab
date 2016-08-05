<?php
namespace PhalCron\Library\Loadtasks;

use PhalCron\Library\Output;
use Phalcon\DI;

/**
 * Class File
 * @package PhalCron\Library\Loadtasks
 */
class File
{
    protected $filePath;
    protected $oriTasks;

    public function __construct($file)
    {
        if (empty($file) || (!empty($file) && !file_exists($file))) {
            Output::stderr("指定配置文件不存在,file:" . $file);
            exit;
        }
        $this->filePath = $file;
    }

    /**
     * 返回格式化好的任务配置
     * @return array
     */
    public function getTasks()
    {
        $this->loadTasks();
        return self::parseTasks();
    }

    public function reloadTasks()
    {
        $this->loadTasks();
        $this->config = $this->parseTasks();
    }

    /**
     * 从配置文件载入配置
     */
    protected function loadTasks()
    {
        $this->oriTasks = include($this->filePath);
    }

    /**
     * 格式化配置文件中的配置
     * @return array
     */
    protected function parseTasks()
    {
        $tasks = [];
        if (is_array($this->oriTasks)) {
            foreach ($this->oriTasks as $key => $val) {
                $tasks[$key] = [
                    "taskname" => $val["taskname"],
                    "rule"     => $val["rule"],
                    "unique"   => $val["unique"],
                    "execute"  => $val["execute"],
                    "args"     => $val["args"],
                ];
            }
        }
        return $tasks;
    }
}