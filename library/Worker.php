<?php
namespace PhalCron\Library;

/**
 * Class Worker
 * @package SwooleCrontab\Lib
 */
class Worker
{
    public $workers;

    public function loadWorker()
    {
        foreach ($this->getWorkers() as $classname => $task) {
            for ($i = 1; $i <= $task["processNum"]; $i++) {
                $this->create_process($classname, $i, $task["redis"]);
            }
        }
    }

    protected function getWorkers()
    {
        $path = ROOT_PATH . "config/worker.php";
        $config = include $path;
        if (empty($config)) {
            return [];
        }
        return $config;
    }

    /**
     * 创建一个子进程
     * @param $classname
     * @param $number
     * @param $redis
     */
    public function create_process($classname, $number, $redis)
    {
        $this->workers["classname"] = $classname;
        $this->workers["number"] = $number;
        $this->workers["redis"] = $redis;
        $process = new \swoole_process([$this, "run"]);
        if (!($pid = $process->start())) {

        }
        //记录当前任务
        Crontab::$task_list[$pid] = [
            "start"     => microtime(true),
            "classname" => $classname,
            "number"    => $number,
            "redis"     => $redis,
            "type"      => "worker",
            "process"   => $process,
        ];
    }

    /**
     * 子进程执行的入口
     * @param $worker
     */
    public function run($worker)
    {
        $class = $this->workers["classname"];
        $number = $this->workers["number"];
        $namespace = '\\PhalCron\\Tasks\\';
        $class = str_replace($namespace, '', $class);
        $this->autoload($class);
        $worker->name("lzm_worker_" . $class . "_" . $number);
        $class = $namespace . $class . "Worker";

        $w = new $class;
        $w->content($this->workers["redis"]);
        $w->tick($worker);
    }

    /**
     * 子进程 自动载入需要运行的工作类
     * @param $class
     */
    public function autoload($class)
    {
        $file = ROOT_PATH . "tasks/" . $class . "Worker" . ".php";
        if (file_exists($file)) {
            include($file);
        } else {
            Output::stderr("处理类不存在");
        }
    }
}
