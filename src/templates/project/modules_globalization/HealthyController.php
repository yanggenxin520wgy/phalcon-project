<?php


namespace @@namespace@@\Modules\Base\Common\Controllers;

use @@namespace@@\Core\PhalBaseLogger;
use Phalcon\Mvc\Controller;
use @@namespace@@\Library\Mail;

/**
 * 项目健康检查
 * Class HealthyController
 * @package App\Controllers
 * @property PhalBaseLogger $logger
 */
class HealthyController extends Controller
{
    //类常量
    const email_title   = '@@name@@-' . COUNTRY_CODE . '-' . RUNTIME . '检查%s';
    const success       = '[@@name@@-' . COUNTRY_CODE . '-' . RUNTIME . ']健康检查：';
    const br            = '<br>';
    const style_td      = 'style="width:100px;text-align:center;border: 1px solid"';
    const style_ex      = 'style="min-width:100px;max-width:300px;width:600px;border:1px solid;text-align:left;"';
    const style_h3      = 'style="margin-left:60px"';
    const style_table   = 'style="border: 1px solid"';
    const code_desc     = [
        '异常',
        '正常',
    ];

    //邮件相关
    private $email_to;
    private $email_cc;
    private $content;

    //http请求相关
    private $code = 1;
    private $message;

    /**
     * 初始化函数
     */
    public function initialize() {
        $this->view->disable(false);//关闭页面渲染

        $this->email_to = explode(',', env('healthy_email_to', ''));
        $this->email_cc = explode(',', env('healthy_email_cc', ''));
        $this->content  = 'Dear All：' . self::br;
    }


    /**
     * 入口函数
     */
    public function indexAction() {
        $db     = self::checkDatabase();
        $redis  = self::checkRedis();
        $this->get_client_ip();
        $this->get_server_ip();

        $out_put = [
            'db'        => $db,
            'redis'     => $redis,
        ];

        $message = self::success . '各项正常';
        if (!$this->code) {
            $message = self::success . $this->message;
        }

        sendNotice([$message, $out_put], 'warning', env('notice_web_hook', ''));
        $this->sendMail();

        header('Content-type:application/json;charset=utf-8');
        echo json_encode([
            'code'      => $this->code,
            'message'   => $message,
            'ip'        => getRealIp(),
            'data'      => $out_put,
        ]);
    }


    /**
     * 表和表头
     * @param $name
     */
    private function thead($name) {
        $this->content .= '<h3 ' . self::style_h3 . '>' . $name . '检查</h3>';
        $this->content .= '<table ' . self::style_table . '>';
        $this->content .= '<thead>';
        $this->content .= '<tr>';
        $this->content .= '<th ' . self::style_td . '>配置项</th>';
        $this->content .= '<th ' . self::style_td . '>状态</th>';
        $this->content .= '<th ' . self::style_td . '>说明</th>';
        $this->content .= '</tr>';
        $this->content .= '</thead><tbody>';
    }


    private function checkDatabase() {
        $this->thead('数据库');
        $db_result = [];
        foreach ($this->di->getServices() as $name => $obj) {
            if (0 === strpos($name, 'db')) {
                $this->content .= '<tr>';
                $this->content .= '<td '. self::style_td .'>' . $name . '</td>';
                try {
                    $this->di->get($name);
                    $db_result[$name]   = true;
                    $this->content     .= '<td '. self::style_td .'>' . $this->getGreenStr('连接正常') . '</td>';
                    $this->content     .= '<td '. self::style_td .'>暂无异常</td>';
                } catch (\Throwable $t) {
                    $this->message     .= $name . '连接异常; ';
                    $this->code         = 0;
                    $db_result[$name]   = false;
                    $this->content     .= '<td '. self::style_td .'>' . $this->getErrorStr('连接异常') . '</td>';
                    $this->content     .= '<td '. self::style_ex .'>' . $t->getMessage() . '</td>';
                }
                $this->content .= '</tr>';
            }
        }
        $this->content .= '</tbody></table>';

        return $db_result;
    }


    /**
     * @desc 检查reids服务
     * @date 2021-11-30
     * @author lzw
     */
    private function checkRedis()
    {
        $this->thead('redis');
        $redis_result = [];
        foreach ($this->di->getServices() as $name => $obj) {
            if (0 === strpos($name, 'redis')) {
                $this->content .= '<tr>';
                $this->content .= '<td '. self::style_td .'>' . $name . '</td>';
                try {
                    $this->di->get($name);
                    $this->content         .= '<td '. self::style_td .'>' . $this->getGreenStr('连接正常') . '</td>';
                    $this->content         .= '<td '. self::style_td .'>暂无异常</td>';
                    $redis_result[$name]    = true;
                } catch (\Throwable $t) {
                    $this->message         .= $name . '连接异常; ';
                    $this->code             = 0;
                    $redis_result[$name]    = false;
                    $this->content         .= '<td '. self::style_td .'>' . $this->getErrorStr('连接异常') . '</td>';
                    $this->content         .= '<td '. self::style_ex .'>' . $t->getMessage() . '</td>';
                }
                $this->content .= '</tr>';
            }
        }
        $this->content .= '</tbody></table>';

        return $redis_result;
    }


    /**
     * @desc 把检查结果发送到指定邮箱
     * @date 2021-11-30
     */
    private function sendMail() {
        Mail::send($this->email_to, sprintf(self::email_title, self::code_desc[$this->code]), $this->content, '','', $this->email_cc);
    }


    /**
     * @desc 给错误的邮件信息进行颜色标注
     * @param $str
     * @return string
     */
    private function getErrorStr($str)
    {
        return '<span style="color: red">' . $str . '</span>';
    }

    /**
     * @desc 给错误的邮件信息进行颜色标注
     * @param $str
     * @return string
     */
    private function getGreenStr($str)
    {
        return '<span style="color: green">' . $str . '</span>';
    }


    /**
     * @desc 邮件添加访问IP
     */
    public function get_client_ip()
    {
        $this->content .= '<h3 ' . self::style_h3 . '>client_ip</h3>';
        $this->content .= '<table ' . self::style_table . '>';
        $this->content .= '<tbody>';
        $this->content .= '<tr><td ' . self::style_td . '>client_ip</td><td ' . self::style_td . '>';
        $this->content .= getRealIp();
        $this->content .= '</td></tr>';
        $this->content .= '</tbody></table>';
    }

    /**
     * @desc 邮件添加服务器ip
     */
    public function get_server_ip()
    {
        $this->content .= '<h3 ' . self::style_h3 . '>server_ip</h3>';
        $this->content .= '<table ' . self::style_table . '>';
        $this->content .= '<tbody>';
        $this->content .= '<tr><td ' . self::style_td . '>server_ip</td><td ' . self::style_td . '>';
        $this->content .= $_SERVER['SERVER_ADDR'];
        $this->content .= '</td></tr>';
        $this->content .= '</tbody></table>';
    }

    /**
     * @desc 项目心跳检查
     * @date 2021-12-27
     * @author mawang
     */
    public function keepaliveAction()
    {
        return json_encode([
            'code' => 1,
            'message' => '@@name@@-' . COUNTRY_CODE . '-' . RUNTIME . '-keepalive-ok',
            'data' => []
        ]);
    }
}



