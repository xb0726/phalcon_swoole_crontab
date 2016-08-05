<?php
namespace PhalCron\Library;

use Phalcon\CLI\Console;

class CliApp extends Console
{
    private $options = "hdrmp:s:l:c:";
    private $longopts = [
        "help",
        "daemon",
        "reload",
        "monitor",
        "pid:",
        "log:",
        "config:",
        "worker:",
        "tasktype:",
        "checktime:",
    ];

    private $help = <<<EOF
  帮助信息:
  Usage: /path/to/php main.php [options] -- [args...]

  -h [--help]        显示帮助信息
  -s start           启动进程
  -s stop            停止进程
  -s restart         重启进程
  -d [--daemon]      是否后台运行
  -r [--reload]      重新载入配置文件
  -m [--monitor]     监控进程是否在运行,如果在运行则不管,未运行则启动进程
  --worker           开启worker
  --checktime        默认精确对时(如果精确对时,程序则会延时到分钟开始0秒启动) 值为false则不精确对时


EOF;

//  -p [--pid]         指定pid文件位置(默认pid文件保存在当前目录)
//  -l [--log]         log文件夹的位置
//  -c [--config]      config文件的位置
//  --tasktype         task任务获取类型,[file|mysql] 默认是file

    /**
     * CliApp constructor.
     */
    public function __construct()
    {
        Crontab::$taskParams = ROOT_PATH . "config/crontab.php";
        Crontab::$pid_file = ROOT_PATH . "logs/pid";
    }

    /**
     * 运行入口
     */
    public function run()
    {
        $opt = getopt($this->options, $this->longopts);
        $this->params_h($opt);
        $this->params_d($opt);
        // $this->params_p($opt);
        // $this->params_l($opt);
        // $this->params_c($opt);
        $this->params_r($opt);
        $this->params_worker($opt);
        // $this->params_tasktype($opt);
        $this->params_checktime($opt);
        $opt = $this->params_m($opt);
        $this->params_s($opt);
    }

    /**
     * 解析帮助参数
     * @param $opt
     */
    public function params_h($opt)
    {
        if (empty($opt) || isset($opt["h"]) || isset($opt["help"])) {
            die($this->help);
        }
    }

    /**
     * 解析运行模式参数
     * @param $opt
     */
    public function params_d($opt)
    {
        if (isset($opt["d"]) || isset($opt["daemon"])) {
            Crontab::$daemon = true;
        }
    }

    /**
     * 解析精确对时参数
     * @param $opt
     */
    public function params_checktime($opt)
    {
        if (isset($opt["checktime"]) && $opt["checktime"] === "false") {
            Crontab::$checktime = false;
        }
    }

    /**
     * 重新载入配置文件
     * @param $opt
     */
    public function params_r($opt)
    {
        if (isset($opt["r"]) || isset($opt["reload"])) {
            $pid = @file_get_contents(Crontab::$pid_file);
            if ($pid) {
                if (\swoole_process::kill($pid, 0)) {
                    \swoole_process::kill($pid, SIGUSR1);
                    Output::stdout("对 {$pid} 发送了从新载入配置文件的信号");
                    exit;
                }
            }
            Output::stderr("进程" . $pid . "不存在");
        }
    }

    /**
     * 监控进程是否在运行
     * @param $opt
     * @return array
     */
    public function params_m($opt)
    {
        if (isset($opt["m"]) || isset($opt["monitor"])) {
            $pid = @file_get_contents(Crontab::$pid_file);
            if ($pid && \swoole_process::kill($pid, 0)) {
                exit;
            }
            $opt = ["s" => "restart"];
        }
        return $opt;
    }

    /**
     * 解析pid参数
     * @param $opt
     *//*
    public function params_p($opt)
    {
        //记录pid文件位置
        if (isset($opt["p"]) && $opt["p"]) {
            Crontab::$pid_file = $opt["p"] . "/pid";
        }
        //记录pid文件位置
        if (isset($opt["pid"]) && $opt["pid"]) {
            Crontab::$pid_file = $opt["pid"] . "/pid";
        }
        if (empty(Crontab::$pid_file)) {
            Crontab::$pid_file = ROOT_PATH . "/logs/pid";
        }
    }*/

    /**
     * 解析日志路径参数
     * @param $opt
     *//*
    static public function params_l($opt)
    {
        if (isset($opt["l"]) && $opt["l"]) {
            Crontab::$log_path = $opt["l"];
        }
        if (isset($opt["log"]) && $opt["log"]) {
            Crontab::$log_path = $opt["log"];
        }
        if (empty(Crontab::$log_path)) {
            Crontab::$log_path = ROOT_PATH . "/logs/";
        }
    }*/

    /**
     * 解析配置文件位置参数
     * @param $opt
     *//*
    public function params_c($opt)
    {
        if (isset($opt["c"]) && $opt["c"]) {
            Crontab::$taskParams = $opt["c"];
        }
        if (isset($opt["config"]) && $opt["config"]) {
            Crontab::$taskParams = $opt["config"];
        }
        if (empty(Crontab::$taskParams)) {
            Crontab::$taskParams = APP_PATH . "config/crontab.php";
        }
    }*/

    /**
     * 解析启动模式参数
     * @param $opt
     */
    public function params_s($opt)
    {
        //判断传入了s参数但是值，则提示错误
        if ((isset($opt["s"]) && !$opt["s"]) || (isset($opt["s"]) && !in_array($opt["s"],
                    ["start", "stop", "restart"]))
        ) {
            Output::stdout("Please run: path/to/php main.php -s [start|stop|restart]");
        }

        if (isset($opt["s"]) && in_array($opt["s"], ["start", "stop", "restart"])) {
            switch ($opt["s"]) {
                case "start":
                    Crontab::start();
                    break;
                case "stop":
                    Crontab::stop();
                    break;
                case "restart":
                    Crontab::restart();
                    break;
            }
        }
    }

    /**
     * worker参数
     *
     * @param $opt
     */
    public function params_worker($opt)
    {
        if (isset($opt["worker"])) {
            Crontab::$worker = true;
        }
    }

    /**
     * task type file|mysql
     * @param $opt
     *//*
    public function params_tasktype($opt)
    {
        if (isset($opt["tasktype"])) {
            Crontab::$taskType = $opt["tasktype"];
        }
    }*/
}
