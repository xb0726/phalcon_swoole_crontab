<?php
namespace PhalCron\Library;


/**
 * Class AbstractCrontab
 * @package PhalCron\Library
 */
abstract class AbstractCrontab
{
    public $worker;

//    public function delay($sec){
//        if(!is_numeric($sec)){
//            return false;
//        }
//        $task = $this->worker->pid.",".$sec;
//        $this->worker->write($task);
//        if($this->worker->read() ==$task){
//            return true;
//        }
//        return false;
//    }

    abstract public function run($task);
}