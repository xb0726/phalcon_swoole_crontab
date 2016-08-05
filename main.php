<?php
/**
 * crontab phalcon + swoole
 *
 * @author xiaobon@foxmail.com
 * @date 2016-08-05
 *
 * 参考
 * https://github.com/osgochina/swoole-crontab
 *
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS);

error_reporting(E_ERROR);
@ini_set('display_errors', 0);

$config = include ROOT_PATH . "config/config.php";
$loader = new Phalcon\Loader();
$loader->registerNamespaces([
    'PhalCron\Library' => ROOT_PATH . 'library/',
    'PhalCron\Tasks'   => ROOT_PATH . 'tasks/',
])->register();

// Capture runtime errors
register_shutdown_function('PhalCron\Library\ErrorHandler::runtimeShutdown');

try {
    $di = new Phalcon\DI\FactoryDefault\CLI();
    set_error_handler('PhalCron\Library\ErrorHandler::error');
    // setup config infos
    $di->set('config', $config);

    // setup logger with file
    $di->set('logger', function () use ($config) {
        $file = $config->dirs->logsDir . date('Ymd') . '_cron.log';
        return new Phalcon\Logger\Adapter\File($file);
    });

    // If the configuration specify the use of metadata adapter use it or use memory otherwise
    $di->set('modelsMetadata', function () use ($config) {
        // development save Memory
        if ($config->application->debug) {
            return new Phalcon\Mvc\Model\Metadata\Memory();
        }

        // metadata save Files
        return new Phalcon\Mvc\Model\Metadata\Files([
            'metaDataDir' => $config->dirs->metaDataDir,
        ]);
    }, true);

    // redis
    $di->set('redis', function () use ($config) {
        if (extension_loaded('redis') && isset($config->redis)) {

            $redis_config = $config->redis->production;
            if ($config->application->debug) {
                $redis_config = $config->redis->development;
            }
            $redis = new \Redis();
            try {
                $redis->connect($redis_config->host, $redis_config->port, $redis_config->timeout);

                return $redis;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    });

    Phalcon\Mvc\Model::setup([
        'notNullValidations' => false,
    ]);

    date_default_timezone_set($config->datetime_zone);

    $app = new PhalCron\Library\CliApp();
    $app->setDI($di);

    $app->run();
} catch (Exception $e) {
    echo get_class($e), ": ", $e->getMessage(), "\n";
    echo " File=", $e->getFile(), "\n";
    echo " Line=", $e->getLine(), "\n";
    echo $e->getTraceAsString() . "\n";
}
