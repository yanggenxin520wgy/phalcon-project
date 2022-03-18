<?php


use Phalcon\Paginator\Adapter\NativeArray;

class BaseTask extends \Phalcon\Cli\Task
{
    /**
     * @var \App\Core\PhalBaseLogger
     */
    protected $logger;
    /** @var \Phalcon\Db\Adapter\Pdo */
    protected $db;

    static $start_at        = 0;
    static $end_at          = 0;
    static $start_memory    = 0;
    static $end_memory      = 0;
    /** @var NativeArray */
    protected $_dict;
    protected $locale   = [ 'locale' => 'zh', ];
    protected $lang     = 'zh';

    public function initialize()
    {
        //设置零时区
        date_default_timezone_set('UTC');

        //脚本启动的时间，会保持一个文件
        $this->logger   = $this->getDI()->get('logger');
        $this->db       = $this->getDI()->get('db');
        self::$start_at = microtime(true);
        self::$start_memory = memory_get_usage();
        echo static::class . ' 任务开始' . PHP_EOL;
        [,$this->_dict] = \@@namespace@@\Core\PhalBaseService::setLanguage($this->locale);
    }

    //子类去实现,这个接口,处理消息主逻辑
    protected function processOne($param=null){
        return true;
    }

    public function mainAction($param=null)
    {
        $this->processOne($param);
    }

    function __destruct() {

        self::$end_at       = microtime(true);
        self::$end_memory   = memory_get_peak_usage();
        $run_time           = round((self::$end_at - self::$start_at) * 1000);
        $used_memory        = round((self::$end_memory - self::$start_memory) / 1000 , 2);

        echo static::class . ' 任务结束' . PHP_EOL;
        echo '内存占用:' . $used_memory .'kb' . PHP_EOL;
        echo '耗时：' . $run_time . 'ms' . PHP_EOL;
    }
}