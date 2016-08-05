<?php
namespace PhalCron\Tasks;

use PhalCron\Library\AbstractCrontab;
use PhalCron\Main;

class  GearmanCron extends AbstractCrontab
{

    public function run($task)
    {
        $client = new \GearmanClient();
        $client->addServers($task["server"]);
        $client->doBackground($task["cmd"], $task["ext"]);
        if (($code = $client->returnCode()) != GEARMAN_SUCCESS) {
            Main::log_write("Gearman:" . $task["cmd"] . " to " . $task["server"] . " error,code=" . $code);
            exit;
        }
        Main::log_write("Gearman:" . $task["cmd"] . " to " . $task["server"] . " success,code=" . $code);
        exit;
    }
}
