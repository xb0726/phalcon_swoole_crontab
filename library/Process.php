<?php
namespace PhalCron\Library;


class Process
{
    public $task;

    /**
     * 创建一个子进程
     * @param $task
     */
    public function create_process($id, $task)
    {
        $this->task = $task;
        $process = new \swoole_process([$this, "run"]);
        if (!($pid = $process->start())) {

        }
        //记录当前任务
        Crontab::$task_list[$pid] = [
            "start"   => microtime(true),
            "id"      => $id,
            "task"    => $task,
            "type"    => "crontab",
            "process" => $process,
        ];
    }

    /**
     * 子进程执行的入口
     * @param $worker
     */
    public function run($worker)
    {
        $namespace = '\\PhalCron\\Tasks\\';
        $class = str_replace($namespace, '', $this->task["execute"]);
        $worker->name("lzm_crontab_" . $class . "_" . $this->task["id"]);

        $this->autoload($class);
        $_class = $namespace . $class . 'Cron';

        if (class_exists($_class)) {
            $c = new $_class;
            $c->worker = $worker;
            $c->run($this->task["args"]);
            self::_exit($worker);
        } else {
            Output::stderr("处理类不存在");
        }
    }

    private function _exit($worker)
    {
        $worker->exit(1);
    }


    /**
     * 子进程 自动载入需要运行的工作类
     * @param $class
     */
    public function autoload($class)
    {
        $file = ROOT_PATH . "tasks/" . $class . "Cron.php";
        if (file_exists($file)) {
            include($file);
        } else {
            Output::stderr("处理类不存在");
        }
    }
}
