<?php


namespace @@namespace@@\Core;


use Phalcon\Mvc\User\Component;
use Phalcon\Translate\Adapter\NativeArray;


class PhalBaseService extends Component
{
    protected static $instance;

    /**
     * @var NativeArray
     */
    protected static $_dict;
    /**
     * 解析后的语言
     * @var string
     */
    protected static $_lang;
    /**
     * 接口传入语言原文
     * @var string
     */
    protected static $_language;

    /**
     * @var PhalBaseLogger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = $this->getDI()->get('logger');
    }

    /**
     * 设置语言
     * @param $locale
     * @return array
     */
    public static function setLanguage($locale)
    {
        self::$_dict = self::getTranslation($locale);

        return [self::$_lang, self::$_dict];
    }

    /**
     * 加载语言包
     * @param array $locale
     * @return NativeArray 语言包
     */
    protected static function getTranslation(array $locale)
    {
        $lang = isset($locale['locale']) ? $locale['locale'] : 'en';
        //给环境语言赋值
        self::$_language = $lang;

        $langArr = locale_parse($lang);
        $lang = $langArr['language'];

        //给环境语言赋值
        self::$_lang = $lang;

        $translationFile = APP_PATH . '/language/' . COUNTRY_CODE . '/' . $lang . '.php';

        if (file_exists($translationFile)) {
            $messages = include $translationFile;
        } else {
            $messages = include APP_PATH . '/language/' . COUNTRY_CODE . '/en.php';
        }

        return new NativeArray(
            [
                'content' => $messages,
            ]
        );
    }

    /**
     * 获取唯一实例
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}