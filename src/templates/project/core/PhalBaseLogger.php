<?php


namespace @@namespace@@\Core;


class PhalBaseLogger extends \Phalcon\Logger\Adapter\File
{
    /**
     * 日志记录
     * @param mixed $log
     * @param string $level 日志等级
     */
    public function write_log($log, $level = 'error')
    {
        date_default_timezone_set("Asia/Shanghai");
        $debugInfo = debug_backtrace();
        $log_info = [];

        if (isset($debugInfo[1])) {
            $debugInfo = $debugInfo[1];
            $log_info['class'] = $debugInfo['class'];
            $log_info['function'] = $debugInfo['function'];
        } else {
            $log_info['class'] = __CLASS__;
            $log_info['function'] = __FUNCTION__;
        }

        empty($level) && $level = 'error';
        $level = strtolower($level);

        $log_info['type']       = $level;
        $log_info['timestamp']  = date('Y-m-d H:i:s');
        $log_info['data']       = $log;
        $log_info['traceid']    = '';
        //系统中增加traceid
        if(function_exists('molten_get_traceid')){
            $log_info['traceid'] = molten_get_traceid();
        }

        $log_info = json_encode($log_info, JSON_UNESCAPED_UNICODE);
        $formatter = new \Phalcon\Logger\Formatter\Line($log_info);
        $formatter->setDateFormat('Y-m-d H:i:s');
        $this->setFormatter($formatter);
        $this->$level("");

        //错误信息报警提醒
        if($level == 'error' && RUNTIME != 'dev' && env('notice_open', 1)) {
            sendNotice($log_info, $level, env('notice_web_hook', ''));
        }
    }
}