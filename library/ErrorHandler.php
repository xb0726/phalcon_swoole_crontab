<?php
/**
 * Capture PHP related warnings/errors
 *
 * @author wenqiang.he
 *
 */
namespace PhalCron\Library;

class ErrorHandler
{

    /**
     * Record any warnings/errors by php
     *
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param null $errContext
     * @return bool
     */
    public static function error($errNo, $errStr, $errFile, $errLine, $errContext = NULL)
    {
        if ($errNo != E_STRICT) {
            
            // Get Remote Ip or CLI script?
            if (PHP_SAPI == 'cli') {
                $script = $_SERVER['PHP_SELF'];
                $ip = 'CLI';
            } else {
                $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $script = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            // TODO save error to DB
        }
        
        return FALSE;
    }

    /**
     * Capture any errors at the end script (especially runtime errors)
     */
    public static function runtimeShutdown()
    {
        $e = error_get_last();
        if (! empty($e)) {
            // Record Error
            ErrorHandler::error($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }
}
