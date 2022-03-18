<?php

namespace @@namespace@@\Library;

final class Mail
{

    //发送邮件的帐号
    private static $account = array(
        //内部邮件
        'internal' => array(
            'host' => '',
            'name' => '',
            'port' => 465,
            'ssl' => 1
        ),
        //外部邮件
        'sohuMail' => array(
            'api_user' => '', # 使用api_user和api_key进行验证
            'api_key' => '',
            'from' => '', # 发信人，用正确邮件地址替代
            'fromname' => '',
        ),

    );

    //发送内部邮件
    public static function send(
        $tomail,
        $title,
        $content,
        $attachmentpath = "",
        $attachmentname = "",
        $ccmail = '',
        $newname = ''
    ) {
        $account = self::$account['internal'];
        $obj = include APP_PATH . "/config/config.php";
        $account['username'] = [$obj->application->aliMailAddress];
        $account['password'] = $obj->application->aliMailPass;

        $account['username'] = $account['username'][array_rand($account['username'], 1)];

        !is_array($tomail) && $tomail = array($tomail);
        if (!empty($ccmail)) {
            !is_array($ccmail) && $ccmail = array($ccmail);
        }

        $mail = new mailX(); //定义对象
        if (!empty($newname)) {
            $account['name'] = $newname;
        }
        $mail->setFrom($account['username'], $account['name']); //设置 发件人邮箱, 发件人名称
        $mail->setMailBody($title, $content); //设置邮件体
        $mail->setSendType('SMTP', $account);
        if (!empty($attachmentpath) && !empty($attachmentname)) {
            $mail->addAttachment($attachmentpath, $attachmentname);
        }
        foreach ($tomail as $m) {
            $mail->addTomail($m); //添加收件人信息
        }
        if (is_array($ccmail) && count($ccmail) > 0) {
            foreach ($ccmail as $cm) {
                $mail->addChaoSong($cm); //添加抄送人信息
            }
        }


        $sendres = $mail->send();

//        $msg = var_export($tomail, 1)."|".$title."res:".var_export($sendres, 1);
//        if(Config::get('app.debug')) {
//            Log::info($msg);
//        }
        return $sendres;
    }

    //发送邮件(sohu云)
    public static function sendBySohu($tomail, $title, $content)
    {
        $account = self::$account['sohuMail'];
        $account['to'] = $tomail; //收件人地址，用正确邮件地址替代，多个地址用';'分隔
        $account['subject'] = $title;
        $account['html'] = $content;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $account);
        $result = curl_exec($ch);
        if ($result === false) {
            return false;
        }
        curl_close($ch);
        $data = json_decode($result, 1);
        if (is_array($data) && isset($data['message']) && strtolower(trim($data['message'])) == 'success') {
            return true;
        }

        return false;
    }

}


class mailX
{
    private $mail; //PHPMailer类

    private $sendmail = '/usr/sbin/sendmail'; //当使用Sendmail来发送邮件时，设置的程序所在路径
    private $qmail = '/var/qmail/bin/sendmail'; //当使用Qmail来发送邮件时，设置的程序所在路径
    //当使用smtp来发送邮件时，设置的smtp参数数组
    private $smtp = array(
        'port' => 25,
        'host' => 'localhost',
        'username' => 'root',
        'password' => ''
    );

    //初始化
    function __construct()
    {
        $this->mail = new PHPMailer(); //实例化对象
    }

    //析构函数
    function __destruct()
    {
    }


    //设置发送人
    public function setFrom($fromMail = 'root@localhost', $fromName = 'root')
    {
        $this->mail->From = $fromMail;    //发件人邮箱
        $this->mail->FromName = $fromName;    //发件人名称
        return true;
    }

    //设置邮件体: 标题, 内容, 邮件的编码格式(默认UTF-8 或 GB2312)
    public function setMailBody($subject = '', $content = '', $CharSet = 'UTF-8')
    {
        //var_dump($subject);die;
        $this->mail->CharSet = $CharSet; //设定编码
        $this->mail->Encoding = "base64";
        $this->mail->IsHTML(true); // 设置定邮件格式为HTML
        $this->mail->WordWrap = 50; // set word wrap
        $this->mail->Subject = $subject; // 邮件主题
        $this->mail->Body = $content; // 邮件内容
        //$this->mail->AltBody ="text/html";
        return true;
    }

    //添加一个附件, 附件(可以设置多个附件) , 返回是否设置成功
    //参数: 附件文件的绝对路径, 给附件设置的名称
    public function addAttachment($path = '', $name = '')
    {
        if (is_array($path))//支持多附件情况 20160616
        {
            if (!empty($path)) {
                foreach ($path as $k => $v) {
                    if (isset($name[$k]) && !empty($name[$k])) {
                        $this->mail->AddAttachment($v, $name[$k]);
                    } else {
                        $this->mail->AddAttachment($v);
                    }
                }
            }
        } else {
            $path = trim($path);
            $name = trim($name);
            if ($name == '') {
                return $this->mail->AddAttachment($path); // 没有设置附件名称
            } else {
                return $this->mail->AddAttachment($path, $name); // 设置附件名称
            }
        }
    }

    //设置邮件的发送方式: 'Mail' 'Sendmail' 'SMTP'  'Qmail'
    //返回是否设置成功.
    public function setSendType($type = 'SMTP', $parameter = array())
    {

        $type = trim($type);
        if ($type == 'SMTP') {
            if (isset($parameter) && count($parameter)) {
                //端口号
                if (isset($parameter['port']) && (int)$parameter['port']) {
                    $this->smtp['port'] = (int)$parameter['port'];
                }
                //SMTP服务器
                if (isset($parameter['host']) && trim($parameter['host']) != '') {
                    $this->smtp['host'] = trim($parameter['host']);
                }
                //用户名
                if (isset($parameter['username']) && trim($parameter['username']) != '') {
                    $this->smtp['username'] = trim($parameter['username']);
                }
                //密码
                if (isset($parameter['password']) && trim($parameter['password']) != '') {
                    $this->smtp['password'] = $parameter['password'];
                }
                //是否ssl
                if (isset($parameter['ssl']) && (int)$parameter['ssl']) {
                    $this->smtp['ssl'] = true;
                } else {
                    $this->smtp['ssl'] = false;
                }
            }

            $this->mail->IsSMTP(); //使用SMTP发送邮件
            $this->mail->SMTPAuth = true; //SMTP服务器是否要认证，一般都要
//$this->mail->SMTPDebug  = 1;
            $this->mail->Port = $this->smtp['port'];
            $this->mail->Host = $this->smtp['host']; //SMTP 服务器
            $this->mail->Username = $this->smtp['username']; // SMTP 认证帐号
            $this->mail->Password = $this->smtp['password']; // SMTP 认证密码
            (int)$this->smtp['ssl'] && $this->mail->SMTPSecure = 'ssl';

            return true;
        } elseif ($type == 'Mail') {

            return true;
        } elseif ($type == 'Sendmail') {
            $this->mail->IsSendmail();
            if (!is_array($parameter) && trim($parameter) != '') {
                $this->sendmail = trim($parameter);
            }
            $this->mail->Sendmail = $this->qmail;

            return true;
        } elseif ($type == 'Qmail') {
            $this->mail->IsSendmail();
            if (!is_array($parameter) && trim($parameter) != '') {
                $this->qmail = trim($parameter);
            }
            $this->mail->Sendmail = $this->qmail;

            return true;
        } else {

            return false;
        }
    }

    //添加收件人
    //参数: $toMail 收信人email, $toName 收信人名称
    public function addTomail($toMail = '', $toName = '')
    {
        return $this->mail->AddAddress($toMail, $toName); //收件人地址 , 收件人名称
    }
    //添加抄送人
    //参数: $toMail 抄送人email
    public function addChaoSong($ccMail = '')
    {
        return $this->mail->addCC($ccMail);
    }

    //发送邮件, 返回是否发送成功
    public function send()
    {
        $bool = $this->mail->Send(); //发送
        $this->mail->ClearAddresses(); //清除收件人，为下一次发件做准备。

        return $bool;
    }

    //其它:
    //============================
    //输出错误信息
    private function err($msg)
    {
        echo __CLASS__ . '错误: ' . $msg;
        exit;
    }

}


/**
 * PHPMailer 5.2.7 压缩版
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.0.0
 * Version 5.2.7
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/
 * @author Marcus Bointon (coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2013 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
    exit("Sorry, PHPMailer will only run on PHP version 5 or greater!\n");
}

class PHPMailer
{
    public $Version = '5.2.7';
    public $Priority = 3;
    public $CharSet = 'iso-8859-1';
    public $ContentType = 'text/plain';
    public $Encoding = '8bit';
    public $ErrorInfo = '';
    public $From = 'root@localhost';
    public $FromName = 'Root User';
    public $Sender = '';
    public $ReturnPath = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    public $Ical = '';
    protected $MIMEBody = '';
    protected $MIMEHeader = '';
    protected $mailHeader = '';
    public $WordWrap = 0;
    public $Mailer = 'mail';
    public $Sendmail = '/usr/sbin/sendmail';
    public $UseSendmailOptions = true;
    public $PluginDir = '';
    public $ConfirmReadingTo = '';
    public $Hostname = '';
    public $MessageID = '';
    public $MessageDate = '';
    public $Host = 'localhost';
    public $Port = 25;
    public $Helo = '';
    public $SMTPSecure = '';
    public $SMTPAuth = false;
    public $Username = '';
    public $Password = '';
    public $AuthType = '';
    public $Realm = '';
    public $Workstation = '';
    public $Timeout = 10;
    public $SMTPDebug = 0;
    public $Debugoutput = "echo";
    public $SMTPKeepAlive = false;
    public $SingleTo = false;
    public $SingleToArray = array();
    public $do_verp = false;
    public $AllowEmpty = false;
    public $LE = "\n";
    public $DKIM_selector = '';
    public $DKIM_identity = '';
    public $DKIM_passphrase = '';
    public $DKIM_domain = '';
    public $DKIM_private = '';
    public $action_function = '';
    public $XMailer = '';
    protected $smtp = null;
    protected $to = array();
    protected $cc = array();
    protected $bcc = array();
    protected $ReplyTo = array();
    protected $all_recipients = array();
    protected $attachment = array();
    protected $CustomHeader = array();
    protected $lastMessageID = '';
    protected $message_type = '';
    protected $boundary = array();
    protected $language = array();
    protected $error_count = 0;
    protected $sign_cert_file = '';
    protected $sign_key_file = '';
    protected $sign_key_pass = '';
    protected $exceptions = false;
    const STOP_MESSAGE = 0;
    const STOP_CONTINUE = 1;
    const STOP_CRITICAL = 2;
    const CRLF = "\r\n";

    public function __construct($exceptions = false)
    {


        $this->exceptions = ($exceptions == true);
    }

    public function __destruct()
    {
        if ($this->Mailer == 'smtp') {
            $this->smtpClose();
        }
    }

    private function mailPassthru($to, $subject, $body, $header, $params)
    {
        if (ini_get('safe_mode') || !($this->UseSendmailOptions)) {
            $rt = @mail($to, $this->encodeHeader($this->secureHeader($subject)), $body, $header);
        } else {
            $rt = @mail($to, $this->encodeHeader($this->secureHeader($subject)), $body, $header, $params);
        }
        return $rt;
    }

    protected function edebug($str)
    {
        if (!$this->SMTPDebug) {
            return;
        }
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo htmlentities(preg_replace('/[\r\n]+/', '', $str), ENT_QUOTES, $this->CharSet) . "<br>\n";
                break;
            case 'echo':
            default:
                echo $str;
        }
    }

    public function isHTML($ishtml = true)
    {
        if ($ishtml) {
            $this->ContentType = 'text/html';
        } else {
            $this->ContentType = 'text/plain';
        }
    }

    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    public function isMail()
    {
        $this->Mailer = 'mail';
    }

    public function isSendmail()
    {
        if (!stristr(ini_get('sendmail_path'), 'sendmail')) {
            $this->Sendmail = '/var/qmail/bin/sendmail';
        }
        $this->Mailer = 'sendmail';
    }

    public function isQmail()
    {
        if (stristr(ini_get('sendmail_path'), 'qmail')) {
            $this->Sendmail = '/var/qmail/bin/sendmail';
        }
        $this->Mailer = 'sendmail';
    }

    public function addAddress($address, $name = '')
    {
        return $this->addAnAddress('to', $address, $name);
    }

    public function addCC($address, $name = '')
    {
        return $this->addAnAddress('cc', $address, $name);
    }

    public function addBCC($address, $name = '')
    {
        return $this->addAnAddress('bcc', $address, $name);
    }

    public function addReplyTo($address, $name = '')
    {
        return $this->addAnAddress('Reply-To', $address, $name);
    }


    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!preg_match('/^(to|cc|bcc|Reply-To)$/', $kind)) {
            $this->setError($this->lang('Invalid recipient array') . ': ' . $kind);
            if ($this->exceptions) {
                throw new phpmailerException('Invalid recipient array: ' . $kind);
            }
            $this->edebug($this->lang('Invalid recipient array') . ': ' . $kind);
            return false;
        }
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name));
        if (!$this->validateAddress($address)) {
            $this->setError($this->lang('invalid_address') . ': ' . $address);
            if ($this->exceptions) {
                throw new phpmailerException($this->lang('invalid_address') . ': ' . $address);
            }
            $this->edebug($this->lang('invalid_address') . ': ' . $address);
            return false;
        }
        if ($kind != 'Reply-To') {
            if (!isset($this->all_recipients[strtolower($address)])) {
                array_push($this->$kind, array($address, $name));
                $this->all_recipients[strtolower($address)] = true;
                return true;
            }
        } else {
            if (!array_key_exists(strtolower($address), $this->ReplyTo)) {
                $this->ReplyTo[strtolower($address)] = array($address, $name);
                return true;
            }
        }
        return false;
    }

    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name));


        if (!$this->validateAddress($address)) {
            $this->setError($this->lang('invalid_address') . ': ' . $address);
            if ($this->exceptions) {
                throw new phpmailerException($this->lang('invalid_address') . ': ' . $address);
            }
            $this->edebug($this->lang('invalid_address') . ': ' . $address);
            return false;
        }
        $this->From = $address;
        $this->FromName = $name;
        if ($auto) {
            if (empty($this->Sender)) {
                $this->Sender = $address;
            }
        }
        return true;
    }

    public function getLastMessageID()
    {
        return $this->lastMessageID;
    }

    public static function validateAddress($address, $patternselect = 'auto')
    {
        if ($patternselect == 'auto') {
            if (defined('PCRE_VERSION')) {
                if (version_compare(PCRE_VERSION, '8.0') >= 0) {
                    $patternselect = 'pcre8';
                } else {
                    $patternselect = 'pcre';
                }
            } else {
                if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
                    $patternselect = 'php';
                } else {
                    $patternselect = 'noregex';
                }
            }
        }
        switch ($patternselect) {
            case 'pcre8':
                return (bool)preg_match('/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)' . '((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)' . '(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)' . '([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*' . '(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)' . '(?>(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?6)){7}' . '|(?!(?:.*[a-f0-9][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:' . '|(?!(?:.*[a-f0-9]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}' . '|[1-9]?[0-9])(?>\.(?9)){3}))\])(?1)$/isD',
                    $address);
                break;
            case 'pcre':
                return (bool)preg_match('/^(?!(?>"?(?>\\\[ -~]|[^"])"?){255,})(?!(?>"?(?>\\\[ -~]|[^"])"?){65,}@)(?>' . '[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*")' . '(?>\.(?>[!#-\'*+\/-9=?^-~-]+|"(?>(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*"))*' . '@(?>(?![a-z0-9-]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)(?>\.(?![a-z0-9-]{64,})' . '(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?)){0,126}|\[(?:(?>IPv6:(?>(?>[a-f0-9]{1,4})(?>:' . '[a-f0-9]{1,4}){7}|(?!(?:.*[a-f0-9][:\]]){8,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?' . '::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?))|(?>(?>IPv6:(?>[a-f0-9]{1,4}(?>:' . '[a-f0-9]{1,4}){5}:|(?!(?:.*[a-f0-9]:){6,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4})?' . '::(?>(?:[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4}):)?))?(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}' . '|[1-9]?[0-9])(?>\.(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\])$/isD',
                    $address);
                break;
            case 'php':
            default:
                return (bool)filter_var($address, FILTER_VALIDATE_EMAIL);
                break;
            case 'noregex':
                return (strlen($address) >= 3 and strpos($address, '@') >= 1 and strpos($address,
                        '@') != strlen($address) - 1);
                break;
        }
    }

    public function send()
    {


        try {

            if (!$this->preSend()) {
                return false;
            }
            return $this->postSend();
        } catch (phpmailerException $e) {
            $this->mailHeader = '';
            $this->setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
    }

    public function preSend()
    {
        try {


            $this->mailHeader = "";
            if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
                throw new phpmailerException($this->lang('provide_address'), self::STOP_CRITICAL);
            }
            if (!empty($this->AltBody)) {
                $this->ContentType = 'multipart/alternative';
            }
            $this->error_count = 0;
            $this->setMessageType();
            if (!$this->AllowEmpty and empty($this->Body)) {
                throw new phpmailerException($this->lang('empty_message'), self::STOP_CRITICAL);
            }
            $this->MIMEHeader = $this->createHeader();
            $this->MIMEBody = $this->createBody();
            if ($this->Mailer == 'mail') {
                if (count($this->to) > 0) {
                    $this->mailHeader .= $this->addrAppend("To", $this->to);
                } else {
                    $this->mailHeader .= $this->headerLine("To", "undisclosed-recipients:;");
                }
                $this->mailHeader .= $this->headerLine('Subject',
                    $this->encodeHeader($this->secureHeader(trim($this->Subject))));
            }
            if (!empty($this->DKIM_domain) && !empty($this->DKIM_private) && !empty($this->DKIM_selector) && !empty($this->DKIM_domain) && file_exists($this->DKIM_private)) {
                $header_dkim = $this->DKIM_Add($this->MIMEHeader . $this->mailHeader,
                    $this->encodeHeader($this->secureHeader($this->Subject)), $this->MIMEBody);
                $this->MIMEHeader = rtrim($this->MIMEHeader, "\r\n ") . self::CRLF . str_replace("\r\n", "\n",
                        $header_dkim) . self::CRLF;
            }
            return true;
        } catch (phpmailerException $e) {
            $this->setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
    }

    public function postSend()
    {
        try {
            switch ($this->Mailer) {
                case 'sendmail':
                    return $this->sendmailSend($this->MIMEHeader, $this->MIMEBody);
                case 'smtp':
                    return $this->smtpSend($this->MIMEHeader, $this->MIMEBody);
                case 'mail':
                    return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
                default:
                    if (method_exists($this, $this->Mailer . 'Send')) {
                        $sendMethod = $this->Mailer . 'Send';
                        return $this->$sendMethod($this->MIMEHeader, $this->MIMEBody);
                    } else {
                        return $this->mailSend($this->MIMEHeader, $this->MIMEBody);
                    }
            }
        } catch (phpmailerException $e) {
            $this->setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            $this->edebug($e->getMessage() . "\n");
        }
        return false;
    }

    protected function sendmailSend($header, $body)
    {
        if ($this->Sender != '') {
            $sendmail = sprintf("%s -oi -f%s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
        } else {
            $sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
        }
        if ($this->SingleTo === true) {
            foreach ($this->SingleToArray as $val) {
                if (!@$mail = popen($sendmail, 'w')) {
                    throw new phpmailerException($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
                fputs($mail, "To: " . $val . "\n");
                fputs($mail, $header);
                fputs($mail, $body);
                $result = pclose($mail);
                $isSent = ($result == 0) ? 1 : 0;
                $this->doCallback($isSent, $val, $this->cc, $this->bcc, $this->Subject, $body, $this->From);
                if ($result != 0) {
                    throw new phpmailerException($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            if (!@$mail = popen($sendmail, 'w')) {
                throw new phpmailerException($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
            fputs($mail, $header);
            fputs($mail, $body);
            $result = pclose($mail);
            $isSent = ($result == 0) ? 1 : 0;
            $this->doCallback($isSent, $this->to, $this->cc, $this->bcc, $this->Subject, $body, $this->From);
            if ($result != 0) {
                throw new phpmailerException($this->lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
        }
        return true;
    }

    protected function mailSend($header, $body)
    {
        $toArr = array();
        foreach ($this->to as $t) {
            $toArr[] = $this->addrFormat($t);
        }
        $to = implode(', ', $toArr);
        if (empty($this->Sender)) {
            $params = " ";
        } else {
            $params = sprintf("-f%s", $this->Sender);
        }
        if ($this->Sender != '' and !ini_get('safe_mode')) {
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this->Sender);
        }
        $rt = false;
        if ($this->SingleTo === true && count($toArr) > 1) {
            foreach ($toArr as $val) {
                $rt = $this->mailPassthru($val, $this->Subject, $body, $header, $params);
                $isSent = ($rt == 1) ? 1 : 0;
                $this->doCallback($isSent, $val, $this->cc, $this->bcc, $this->Subject, $body, $this->From);
            }
        } else {
            $rt = $this->mailPassthru($to, $this->Subject, $body, $header, $params);
            $isSent = ($rt == 1) ? 1 : 0;
            $this->doCallback($isSent, $to, $this->cc, $this->bcc, $this->Subject, $body, $this->From);
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$rt) {
            throw new phpmailerException($this->lang('instantiate'), self::STOP_CRITICAL);
        }
        return true;
    }

    public function getSMTPInstance()
    {
        if (!is_object($this->smtp)) {
            $this->smtp = new SMTP;
        }
        return $this->smtp;
    }

    protected function smtpSend($header, $body)
    {
        $bad_rcpt = array();
        if (!$this->smtpConnect()) {
            throw new phpmailerException($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }
        $smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
        if (!$this->smtp->mail($smtp_from)) {
            $this->setError($this->lang('from_failed') . $smtp_from . ' : ' . implode(',', $this->smtp->getError()));
            throw new phpmailerException($this->ErrorInfo, self::STOP_CRITICAL);
        }
        foreach ($this->to as $to) {
            if (!$this->smtp->recipient($to[0])) {
                $bad_rcpt[] = $to[0];
                $isSent = 0;
            } else {
                $isSent = 1;
            }
            $this->doCallback($isSent, $to[0], '', '', $this->Subject, $body, $this->From);
        }
        foreach ($this->cc as $cc) {
            if (!$this->smtp->recipient($cc[0])) {
                $bad_rcpt[] = $cc[0];
                $isSent = 0;
            } else {
                $isSent = 1;
            }
            $this->doCallback($isSent, '', $cc[0], '', $this->Subject, $body, $this->From);
        }
        foreach ($this->bcc as $bcc) {
            if (!$this->smtp->recipient($bcc[0])) {
                $bad_rcpt[] = $bcc[0];
                $isSent = 0;
            } else {
                $isSent = 1;
            }
            $this->doCallback($isSent, '', '', $bcc[0], $this->Subject, $body, $this->From);
        }
        if (count($bad_rcpt) > 0) {
            throw new phpmailerException($this->lang('recipients_failed') . implode(', ', $bad_rcpt));
        }
        if (!$this->smtp->data($header . $body)) {
            throw new phpmailerException($this->lang('data_not_accepted'), self::STOP_CRITICAL);
        }
        if ($this->SMTPKeepAlive == true) {
            $this->smtp->reset();
        } else {
            $this->smtp->quit();
            $this->smtp->close();
        }
        return true;
    }

    public function smtpConnect($options = array())
    {
        if (is_null($this->smtp)) {
            $this->smtp = $this->getSMTPInstance();
        }
        if ($this->smtp->connected()) {
            return true;
        }
        $this->smtp->setTimeout($this->Timeout);
        $this->smtp->setDebugLevel($this->SMTPDebug);
        $this->smtp->setDebugOutput($this->Debugoutput);
        $this->smtp->setVerp($this->do_verp);
        $tls = ($this->SMTPSecure == 'tls');
        $ssl = ($this->SMTPSecure == 'ssl');
        $hosts = explode(';', $this->Host);


        $lastexception = null;
        foreach ($hosts as $hostentry) {
            $hostinfo = array();
            $host = $hostentry;
            $port = $this->Port;
            if (preg_match('/^(.+):([0-9]+)$/', $hostentry, $hostinfo)) {
                $host = $hostinfo[1];
                $port = $hostinfo[2];
            }


            if ($this->smtp->connect(($ssl ? 'ssl://' : '') . $host, $port, $this->Timeout, $options)) {
                try {
                    if ($this->Helo) {
                        $hello = $this->Helo;
                    } else {
                        $hello = $this->serverHostname();
                    }
                    $this->smtp->hello($hello);

                    if ($tls) {
                        if (!$this->smtp->startTLS()) {
                            throw new phpmailerException($this->lang('connect_host'));
                        }
                        $this->smtp->hello($hello);
                    }
                    if ($this->SMTPAuth) {
                        if (!$this->smtp->authenticate($this->Username, $this->Password, $this->AuthType, $this->Realm,
                            $this->Workstation)) {
                            throw new phpmailerException($this->lang('authenticate'));
                        }
                    }
                    return true;
                } catch (phpmailerException $e) {

                    $lastexception = $e;
                    $this->smtp->quit();
                }
            }
        }
        $this->smtp->close();
        if ($this->exceptions and !is_null($lastexception)) {

            throw $lastexception;
        }
        return false;
    }

    public function smtpClose()
    {
        if ($this->smtp !== null) {
            if ($this->smtp->connected()) {
                $this->smtp->quit();
                $this->smtp->close();
            }
        }
    }

    public function setLanguage($langcode = 'en', $lang_path = 'language/')
    {
        $PHPMAILER_LANG = array(
            'authenticate' => 'SMTP Error: Could not authenticate.',
            'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: data not accepted.',
            'empty_message' => 'Message body empty',
            'encoding' => 'Unknown encoding: ',
            'execute' => 'Could not execute: ',
            'file_access' => 'Could not access file: ',
            'file_open' => 'File Error: Could not open file: ',
            'from_failed' => 'The following From address failed: ',
            'instantiate' => 'Could not instantiate mail function.',
            'invalid_address' => 'Invalid address',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address' => 'You must provide at least one recipient email address.',
            'recipients_failed' => 'SMTP Error: The following recipients failed: ',
            'signing' => 'Signing Error: ',
            'smtp_connect_failed' => 'SMTP connect() failed.',
            'smtp_error' => 'SMTP server error: ',
            'variable_set' => 'Cannot set or reset variable: '
        );
        $l = true;
        if ($langcode != 'en') {
            $l = @include $lang_path . 'phpmailer.lang-' . $langcode . '.php';
        }
        $this->language = $PHPMAILER_LANG;
        return ($l == true);
    }

    public function getTranslations()
    {
        return $this->language;
    }


    public function addrAppend($type, $addr)
    {
        $addresses = array();
        foreach ($addr as $a) {
            $addresses[] = $this->addrFormat($a);
        }
        return $type . ': ' . implode(', ', $addresses) . $this->LE;
    }

    public function addrFormat($addr)
    {
        if (empty($addr[1])) {
            return $this->secureHeader($addr[0]);
        } else {
            return $this->encodeHeader($this->secureHeader($addr[1]),
                    'phrase') . " <" . $this->secureHeader($addr[0]) . ">";
        }
    }

    public function wrapText($message, $length, $qp_mode = false)
    {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
        $is_utf8 = (strtolower($this->CharSet) == "utf-8");
        $lelen = strlen($this->LE);
        $crlflen = strlen(self::CRLF);
        $message = $this->fixEOL($message);
        if (substr($message, -$lelen) == $this->LE) {
            $message = substr($message, 0, -$lelen);
        }
        $line = explode($this->LE, $message);
        $message = '';
        for ($i = 0; $i < count($line); $i++) {
            $line_part = explode(' ', $line[$i]);
            $buf = '';
            for ($e = 0; $e < count($line_part); $e++) {
                $word = $line_part[$e];
                if ($qp_mode and (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - $crlflen;
                    if ($e != 0) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this->utf8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) == "=") {
                                $len--;
                            } elseif (substr($word, $len - 2, 1) == "=") {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf("=%s", self::CRLF);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        if ($length <= 0) {
                            break;
                        }
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this->utf8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) == "=") {
                            $len--;
                        } elseif (substr($word, $len - 2, 1) == "=") {
                            $len -= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        if (strlen($word) > 0) {
                            $message .= $part . sprintf("=%s", self::CRLF);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    $buf .= ($e == 0) ? $word : (' ' . $word);
                    if (strlen($buf) > $length and $buf_o != '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
            }
            $message .= $buf . self::CRLF;
        }
        return $message;
    }

    public function utf8CharBoundary($encodedText, $maxLength)
    {
        $foundSplitPos = false;
        $lookBack = 3;
        while (!$foundSplitPos) {
            $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, "=");
            if ($encodedCharPos !== false) {
                $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec < 128) {
                    $maxLength = ($encodedCharPos == 0) ? $maxLength : $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec >= 192) {
                    $maxLength = $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec < 192) {
                    $lookBack += 3;
                }
            } else {
                $foundSplitPos = true;
            }
        }
        return $maxLength;
    }

    public function setWordWrap()
    {
        if ($this->WordWrap < 1) {
            return;
        }
        switch ($this->message_type) {
            case 'alt':
            case 'alt_inline':
            case 'alt_attach':
            case 'alt_inline_attach':
                $this->AltBody = $this->wrapText($this->AltBody, $this->WordWrap);
                break;
            default:
                $this->Body = $this->wrapText($this->Body, $this->WordWrap);
                break;
        }
    }

    public function createHeader()
    {
        $result = '';
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = 'b1_' . $uniq_id;
        $this->boundary[2] = 'b2_' . $uniq_id;
        $this->boundary[3] = 'b3_' . $uniq_id;
        if ($this->MessageDate == '') {
            $result .= $this->headerLine('Date', self::rfcDate());
        } else {
            $result .= $this->headerLine('Date', $this->MessageDate);
        }
        if ($this->ReturnPath) {
            $result .= $this->headerLine('Return-Path', '<' . trim($this->ReturnPath) . '>');
        } elseif ($this->Sender == '') {
            $result .= $this->headerLine('Return-Path', '<' . trim($this->From) . '>');
        } else {
            $result .= $this->headerLine('Return-Path', '<' . trim($this->Sender) . '>');
        }
        if ($this->Mailer != 'mail') {
            if ($this->SingleTo === true) {
                foreach ($this->to as $t) {
                    $this->SingleToArray[] = $this->addrFormat($t);
                }
            } else {
                if (count($this->to) > 0) {
                    $result .= $this->addrAppend('To', $this->to);
                } elseif (count($this->cc) == 0) {
                    $result .= $this->headerLine('To', 'undisclosed-recipients:;');
                }
            }
        }
        $result .= $this->addrAppend('From', array(array(trim($this->From), $this->FromName)));
        if (count($this->cc) > 0) {
            $result .= $this->addrAppend('Cc', $this->cc);
        }
        if ((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->bcc) > 0)) {
            $result .= $this->addrAppend('Bcc', $this->bcc);
        }
        if (count($this->ReplyTo) > 0) {
            $result .= $this->addrAppend('Reply-To', $this->ReplyTo);
        }
        if ($this->Mailer != 'mail') {
            $result .= $this->headerLine('Subject', $this->encodeHeader($this->secureHeader($this->Subject)));
        }
        if ($this->MessageID != '') {
            $this->lastMessageID = $this->MessageID;
        } else {
            $this->lastMessageID = sprintf("<%s@%s>", $uniq_id, $this->ServerHostname());
        }
        $result .= $this->HeaderLine('Message-ID', $this->lastMessageID);
        $result .= $this->headerLine('X-Priority', $this->Priority);
        if ($this->XMailer == '') {
            $result .= $this->headerLine('X-Mailer',
                'PHPMailer ' . $this->Version . ' (https://github.com/PHPMailer/PHPMailer/)');
        } else {
            $myXmailer = trim($this->XMailer);
            if ($myXmailer) {
                $result .= $this->headerLine('X-Mailer', $myXmailer);
            }
        }
        if ($this->ConfirmReadingTo != '') {
            $result .= $this->headerLine('Disposition-Notification-To', '<' . trim($this->ConfirmReadingTo) . '>');
        }
        for ($index = 0; $index < count($this->CustomHeader); $index++) {
            $result .= $this->headerLine(trim($this->CustomHeader[$index][0]),
                $this->encodeHeader(trim($this->CustomHeader[$index][1])));
        }
        if (!$this->sign_key_file) {
            $result .= $this->headerLine('MIME-Version', '1.0');
            $result .= $this->getMailMIME();
        }
        return $result;
    }

    public function getMailMIME()
    {
        $result = '';
        switch ($this->message_type) {
            case 'inline':
                $result .= $this->headerLine('Content-Type', 'multipart/related;');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'attach':
            case 'inline_attach':
            case 'alt_attach':
            case 'alt_inline_attach':
                $result .= $this->headerLine('Content-Type', 'multipart/mixed;');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            case 'alt':
            case 'alt_inline':
                $result .= $this->headerLine('Content-Type', 'multipart/alternative;');
                $result .= $this->textLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
            default:
                $result .= $this->textLine('Content-Type: ' . $this->ContentType . '; charset=' . $this->CharSet);
                break;
        }
        if ($this->Encoding != '7bit') {
            $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
        }
        if ($this->Mailer != 'mail') {
            $result .= $this->LE;
        }
        return $result;
    }

    public function getSentMIMEMessage()
    {
        return $this->MIMEHeader . $this->mailHeader . self::CRLF . $this->MIMEBody;
    }

    public function createBody()
    {
        $body = '';
        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . $this->LE;
        }
        $this->setWordWrap();
        switch ($this->message_type) {
            case 'inline':
                $body .= $this->getBoundary($this->boundary[1], '', '', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->attachAll('inline', $this->boundary[1]);
                break;
            case 'attach':
                $body .= $this->getBoundary($this->boundary[1], '', '', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'inline_attach':
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', 'multipart/related;');
                $body .= $this->textLine("\tboundary=\"" . $this->boundary[2] . '"');
                $body .= $this->LE;
                $body .= $this->getBoundary($this->boundary[2], '', '', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->attachAll('inline', $this->boundary[2]);
                $body .= $this->LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'alt':
                $body .= $this->getBoundary($this->boundary[1], '', 'text/plain', '');
                $body .= $this->encodeString($this->AltBody, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->getBoundary($this->boundary[1], '', 'text/html', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                if (!empty($this->Ical)) {
                    $body .= $this->getBoundary($this->boundary[1], '', 'text/calendar; method=REQUEST', '');
                    $body .= $this->encodeString($this->Ical, $this->Encoding);
                    $body .= $this->LE . $this->LE;
                }
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            case 'alt_inline':
                $body .= $this->getBoundary($this->boundary[1], '', 'text/plain', '');
                $body .= $this->encodeString($this->AltBody, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', 'multipart/related;');
                $body .= $this->textLine("\tboundary=\"" . $this->boundary[2] . '"');
                $body .= $this->LE;
                $body .= $this->getBoundary($this->boundary[2], '', 'text/html', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->attachAll('inline', $this->boundary[2]);
                $body .= $this->LE;
                $body .= $this->endBoundary($this->boundary[1]);
                break;
            case 'alt_attach':
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', 'multipart/alternative;');
                $body .= $this->textLine("\tboundary=\"" . $this->boundary[2] . '"');
                $body .= $this->LE;
                $body .= $this->getBoundary($this->boundary[2], '', 'text/plain', '');
                $body .= $this->encodeString($this->AltBody, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->getBoundary($this->boundary[2], '', 'text/html', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->endBoundary($this->boundary[2]);
                $body .= $this->LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            case 'alt_inline_attach':
                $body .= $this->textLine('--' . $this->boundary[1]);
                $body .= $this->headerLine('Content-Type', 'multipart/alternative;');
                $body .= $this->textLine("\tboundary=\"" . $this->boundary[2] . '"');
                $body .= $this->LE;
                $body .= $this->getBoundary($this->boundary[2], '', 'text/plain', '');
                $body .= $this->encodeString($this->AltBody, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->textLine('--' . $this->boundary[2]);
                $body .= $this->headerLine('Content-Type', 'multipart/related;');
                $body .= $this->textLine("\tboundary=\"" . $this->boundary[3] . '"');
                $body .= $this->LE;
                $body .= $this->getBoundary($this->boundary[3], '', 'text/html', '');
                $body .= $this->encodeString($this->Body, $this->Encoding);
                $body .= $this->LE . $this->LE;
                $body .= $this->attachAll('inline', $this->boundary[3]);
                $body .= $this->LE;
                $body .= $this->endBoundary($this->boundary[2]);
                $body .= $this->LE;
                $body .= $this->attachAll('attachment', $this->boundary[1]);
                break;
            default:
                $body .= $this->encodeString($this->Body, $this->Encoding);
                break;
        }
        if ($this->isError()) {
            $body = '';
        } elseif ($this->sign_key_file) {
            try {
                if (!defined('PKCS7_TEXT')) {
                    throw new phpmailerException($this->lang('signing') . ' OpenSSL extension missing.');
                }
                $file = tempnam(sys_get_temp_dir(), 'mail');
                file_put_contents($file, $body);
                $signed = tempnam(sys_get_temp_dir(), 'signed');
                if (@openssl_pkcs7_sign($file, $signed, 'file://' . realpath($this->sign_cert_file),
                    array('file://' . realpath($this->sign_key_file), $this->sign_key_pass), null)) {
                    @unlink($file);
                    $body = file_get_contents($signed);
                    @unlink($signed);
                } else {
                    @unlink($file);
                    @unlink($signed);
                    throw new phpmailerException($this->lang('signing') . openssl_error_string());
                }
            } catch (phpmailerException $e) {
                $body = '';
                if ($this->exceptions) {
                    throw $e;
                }
            }
        }
        return $body;
    }

    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ($charSet == '') {
            $charSet = $this->CharSet;
        }
        if ($contentType == '') {
            $contentType = $this->ContentType;
        }
        if ($encoding == '') {
            $encoding = $this->Encoding;
        }
        $result .= $this->textLine('--' . $boundary);
        $result .= sprintf("Content-Type: %s; charset=%s", $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->headerLine('Content-Transfer-Encoding', $encoding);
        $result .= $this->LE;
        return $result;
    }

    protected function endBoundary($boundary)
    {
        return $this->LE . '--' . $boundary . '--' . $this->LE;
    }

    protected function setMessageType()
    {
        $this->message_type = array();
        if ($this->alternativeExists()) {
            $this->message_type[] = "alt";
        }
        if ($this->inlineImageExists()) {
            $this->message_type[] = "inline";
        }
        if ($this->attachmentExists()) {
            $this->message_type[] = "attach";
        }
        $this->message_type = implode("_", $this->message_type);
        if ($this->message_type == "") {
            $this->message_type = "plain";
        }
    }

    public function headerLine($name, $value)
    {
        return $name . ': ' . $value . $this->LE;
    }

    public function textLine($value)
    {
        return $value . $this->LE;
    }

    public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        try {
            if (!@is_file($path)) {
                throw new phpmailerException($this->lang('file_access') . $path, self::STOP_CONTINUE);
            }
            if ($type == '') {
                $type = self::filenameToType($path);
            }
            $filename = basename($path);
            if ($name == '') {
                $name = $filename;
            }
            $this->attachment[] = array(
                0 => $path,
                1 => $filename,
                2 => $name,
                3 => $encoding,
                4 => $type,
                5 => false,
                6 => $disposition,
                7 => 0
            );
        } catch (phpmailerException $e) {
            $this->setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            $this->edebug($e->getMessage() . "\n");
            return false;
        }
        return true;
    }

    public function getAttachments()
    {
        return $this->attachment;
    }

    protected function attachAll($disposition_type, $boundary)
    {
        $mime = array();
        $cidUniq = array();
        $incl = array();
        foreach ($this->attachment as $attachment) {
            if ($attachment[6] == $disposition_type) {
                $string = '';
                $path = '';
                $bString = $attachment[5];
                if ($bString) {
                    $string = $attachment[0];
                } else {
                    $path = $attachment[0];
                }
                $inclhash = md5(serialize($attachment));
                if (in_array($inclhash, $incl)) {
                    continue;
                }
                $incl[] = $inclhash;
                $name = $attachment[2];
                $encoding = $attachment[3];
                $type = $attachment[4];
                $disposition = $attachment[6];
                $cid = $attachment[7];
                if ($disposition == 'inline' && isset($cidUniq[$cid])) {
                    continue;
                }
                $cidUniq[$cid] = true;
                $mime[] = sprintf("--%s%s", $boundary, $this->LE);
                $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type,
                    $this->encodeHeader($this->secureHeader($name)), $this->LE);
                $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);
                if ($disposition == 'inline') {
                    $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
                }
                if (!(empty($disposition))) {
                    if (preg_match('/[ \(\)<>@,;:\\"\/\[\]\?=]/', $name)) {
                        $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition,
                            $this->encodeHeader($this->secureHeader($name)), $this->LE . $this->LE);
                    } else {
                        $mime[] = sprintf("Content-Disposition: %s; filename=%s%s", $disposition,
                            $this->encodeHeader($this->secureHeader($name)), $this->LE . $this->LE);
                    }
                } else {
                    $mime[] = $this->LE;
                }
                if ($bString) {
                    $mime[] = $this->encodeString($string, $encoding);
                    if ($this->isError()) {
                        return '';
                    }
                    $mime[] = $this->LE . $this->LE;
                } else {
                    $mime[] = $this->encodeFile($path, $encoding);
                    if ($this->isError()) {
                        return '';
                    }
                    $mime[] = $this->LE . $this->LE;
                }
            }
        }
        $mime[] = sprintf("--%s--%s", $boundary, $this->LE);
        return implode("", $mime);
    }

    protected function encodeFile($path, $encoding = 'base64')
    {
        try {
            if (!is_readable($path)) {
                throw new phpmailerException($this->lang('file_open') . $path, self::STOP_CONTINUE);
            }
            $magic_quotes = get_magic_quotes_runtime();
            if ($magic_quotes) {
                if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                    set_magic_quotes_runtime(0);
                } else {
                    ini_set('magic_quotes_runtime', 0);
                }
            }
            $file_buffer = file_get_contents($path);
            $file_buffer = $this->encodeString($file_buffer, $encoding);
            if ($magic_quotes) {
                if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                    set_magic_quotes_runtime($magic_quotes);
                } else {
                    ini_set('magic_quotes_runtime', $magic_quotes);
                }
            }
            return $file_buffer;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return '';
        }
    }

    public function encodeString($str, $encoding = 'base64')
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case 'base64':
                $encoded = chunk_split(base64_encode($str), 76, $this->LE);
                break;
            case '7bit':
            case '8bit':
                $encoded = $this->fixEOL($str);
                if (substr($encoded, -(strlen($this->LE))) != $this->LE) {
                    $encoded .= $this->LE;
                }
                break;
            case 'binary':
                $encoded = $str;
                break;
            case 'quoted-printable':
                $encoded = $this->encodeQP($str);
                break;
            default:
                $this->setError($this->lang('encoding') . $encoding);
                break;
        }
        return $encoded;
    }

    public function encodeHeader($str, $position = 'text')
    {
        $x = 0;
        switch (strtolower($position)) {
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                        return ($encoded);
                    } else {
                        return ("\"$encoded\"");
                    }
                }
                $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
            case 'comment':
                $x = preg_match_all('/[()"]/', $str, $matches);
            case 'text':
            default:
                $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }
        if ($x == 0) {
            return ($str);
        }
        $maxlen = 75 - 7 - strlen($this->CharSet);
        if ($x > strlen($str) / 3) {
            $encoding = 'B';
            if (function_exists('mb_strlen') && $this->hasMultiBytes($str)) {
                $encoded = $this->base64EncodeWrapMB($str, "\n");
            } else {
                $encoded = base64_encode($str);
                $maxlen -= $maxlen % 4;
                $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
            }
        } else {
            $encoding = 'Q';
            $encoded = $this->encodeQ($str, $position);
            $encoded = $this->wrapText($encoded, $maxlen, true);
            $encoded = str_replace('=' . self::CRLF, "\n", trim($encoded));
        }
        $encoded = preg_replace('/^(.*)$/m', " =?" . $this->CharSet . "?$encoding?\\1?=", $encoded);
        $encoded = trim(str_replace("\n", $this->LE, $encoded));
        return $encoded;
    }

    public function hasMultiBytes($str)
    {
        if (function_exists('mb_strlen')) {
            return (strlen($str) > mb_strlen($str, $this->CharSet));
        } else {
            return false;
        }
    }

    public function base64EncodeWrapMB($str, $lf = null)
    {
        $start = "=?" . $this->CharSet . "?B?";
        $end = "?=";
        $encoded = "";
        if ($lf === null) {
            $lf = $this->LE;
        }
        $mb_length = mb_strlen($str, $this->CharSet);
        $length = 75 - strlen($start) - strlen($end);
        $ratio = $mb_length / strlen($str);
        $avgLength = floor($length * $ratio * .75);
        for ($i = 0; $i < $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength - $lookBack;
                $chunk = mb_substr($str, $i, $offset, $this->CharSet);
                $chunk = base64_encode($chunk);
                $lookBack++;
            } while (strlen($chunk) > $length);
            $encoded .= $chunk . $lf;
        }
        $encoded = substr($encoded, 0, -strlen($lf));
        return $encoded;
    }

    public function encodeQP($string, $line_max = 76)
    {
        if (function_exists('quoted_printable_encode')) {
            return quoted_printable_encode($string);
        }
        $string = str_replace(array('%20', '%0D%0A.', '%0D%0A', '%'), array(' ', "\r\n=2E", "\r\n", '='),
            rawurlencode($string));
        $string = preg_replace('/[^\r\n]{' . ($line_max - 3) . '}[^=\r\n]{2}/', "$0=\r\n", $string);
        return $string;
    }

    public function encodeQPphp($string, $line_max = 76, $space_conv = false)
    {
        return $this->encodeQP($string, $line_max);
    }

    public function encodeQ($str, $position = 'text')
    {
        $pattern = '';
        $encoded = str_replace(array("\r", "\n"), '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                $pattern = '^A-Za-z0-9!*+\/ -';
                break;
            case 'comment':
                $pattern = '\(\)"';
            case 'text':
            default:
                $pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
                break;
        }
        $matches = array();
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            $s = array_search('=', $matches[0]);
            if ($s !== false) {
                unset($matches[0][$s]);
                array_unshift($matches[0], '=');
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
            }
        }
        return str_replace(' ', '_', $encoded);
    }

    public function addStringAttachment(
        $string,
        $filename,
        $encoding = 'base64',
        $type = '',
        $disposition = 'attachment'
    ) {
        if ($type == '') {
            $type = self::filenameToType($filename);
        }
        $this->attachment[] = array(
            0 => $string,
            1 => $filename,
            2 => basename($filename),
            3 => $encoding,
            4 => $type,
            5 => true,
            6 => $disposition,
            7 => 0
        );
    }

    public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = '', $disposition = 'inline')
    {
        if (!@is_file($path)) {
            $this->setError($this->lang('file_access') . $path);
            return false;
        }
        if ($type == '') {
            $type = self::filenameToType($path);
        }
        $filename = basename($path);
        if ($name == '') {
            $name = $filename;
        }
        $this->attachment[] = array(
            0 => $path,
            1 => $filename,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => false,
            6 => $disposition,
            7 => $cid
        );
        return true;
    }

    public function addStringEmbeddedImage(
        $string,
        $cid,
        $name = '',
        $encoding = 'base64',
        $type = '',
        $disposition = 'inline'
    ) {
        if ($type == '') {
            $type = self::filenameToType($name);
        }
        $this->attachment[] = array(
            0 => $string,
            1 => $name,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => true,
            6 => $disposition,
            7 => $cid
        );
        return true;
    }

    public function inlineImageExists()
    {
        foreach ($this->attachment as $attachment) {
            if ($attachment[6] == 'inline') {
                return true;
            }
        }
        return false;
    }

    public function attachmentExists()
    {
        foreach ($this->attachment as $attachment) {
            if ($attachment[6] == 'attachment') {
                return true;
            }
        }
        return false;
    }

    public function alternativeExists()
    {
        return !empty($this->AltBody);
    }

    public function clearAddresses()
    {
        foreach ($this->to as $to) {
            unset($this->all_recipients[strtolower($to[0])]);
        }
        $this->to = array();
    }

    public function clearCCs()
    {
        foreach ($this->cc as $cc) {
            unset($this->all_recipients[strtolower($cc[0])]);
        }
        $this->cc = array();
    }

    public function clearBCCs()
    {
        foreach ($this->bcc as $bcc) {
            unset($this->all_recipients[strtolower($bcc[0])]);
        }
        $this->bcc = array();
    }

    public function clearReplyTos()
    {
        $this->ReplyTo = array();
    }

    public function clearAllRecipients()
    {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
        $this->all_recipients = array();
    }

    public function clearAttachments()
    {
        $this->attachment = array();
    }

    public function clearCustomHeaders()
    {
        $this->CustomHeader = array();
    }

    protected function setError($msg)
    {
        $this->error_count++;
        if ($this->Mailer == 'smtp' and !is_null($this->smtp)) {
            $lasterror = $this->smtp->getError();
            if (!empty($lasterror) and array_key_exists('smtp_msg', $lasterror)) {
                $msg .= '<p>' . $this->lang('smtp_error') . $lasterror['smtp_msg'] . "</p>\n";
            }
        }
        $this->ErrorInfo = $msg;
    }

    public static function rfcDate()
    {
        date_default_timezone_set(@date_default_timezone_get());
        return date('D, j M Y H:i:s O');
    }

    protected function serverHostname()
    {
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $result = $_SERVER['SERVER_NAME'];
        } else {
            $result = 'localhost.localdomain';
        }
        return $result;
    }

    protected function lang($key)
    {
        if (count($this->language) < 1) {

            $this->setLanguage('en');
        }
        if (isset($this->language[$key])) {
            return $this->language[$key];
        } else {
            return 'Language string failed to load: ' . $key;
        }
    }

    public function isError()
    {
        return ($this->error_count > 0);
    }

    public function fixEOL($str)
    {
        $nstr = str_replace(array("\r\n", "\r"), "\n", $str);
        if ($this->LE !== "\n") {
            $nstr = str_replace("\n", $this->LE, $nstr);
        }
        return $nstr;
    }

    public function addCustomHeader($name, $value = null)
    {
        if ($value === null) {
            $this->CustomHeader[] = explode(':', $name, 2);
        } else {
            $this->CustomHeader[] = array($name, $value);
        }
    }

    public function msgHTML($message, $basedir = '', $advanced = false)
    {
        preg_match_all("/(src|background)=[\"'](.*)[\"']/Ui", $message, $images);
        if (isset($images[2])) {
            foreach ($images[2] as $i => $url) {
                if (!preg_match('#^[A-z]+://#', $url)) {
                    $filename = basename($url);
                    $directory = dirname($url);
                    if ($directory == '.') {
                        $directory = '';
                    }
                    $cid = md5($url) . '@phpmailer.0';
                    if (strlen($basedir) > 1 && substr($basedir, -1) != '/') {
                        $basedir .= '/';
                    }
                    if (strlen($directory) > 1 && substr($directory, -1) != '/') {
                        $directory .= '/';
                    }
                    if ($this->addEmbeddedImage($basedir . $directory . $filename, $cid, $filename, 'base64',
                        self::_mime_types(self::mb_pathinfo($filename, PATHINFO_EXTENSION)))) {
                        $message = preg_replace("/" . $images[1][$i] . "=[\"']" . preg_quote($url, '/') . "[\"']/Ui",
                            $images[1][$i] . "=\"cid:" . $cid . "\"", $message);
                    }
                }
            }
        }
        $this->isHTML(true);
        if (empty($this->AltBody)) {
            $this->AltBody = 'To view this email message, open it in a program that understands HTML!' . "\n\n";
        }
        $this->Body = $this->normalizeBreaks($message);
        $this->AltBody = $this->normalizeBreaks($this->html2text($message, $advanced));
        return $this->Body;
    }

    public function html2text($html, $advanced = false)
    {
        if ($advanced) {
            require_once 'extras/class.html2text.php';
            $h = new html2text($html);
            return $h->get_text();
        }
        return html_entity_decode(trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/si', '',
            $html))), ENT_QUOTES, $this->CharSet);
    }

    public static function _mime_types($ext = '')
    {
        $mimes = array(
            'xl' => 'application/excel',
            'hqx' => 'application/mac-binhex40',
            'cpt' => 'application/mac-compactpro',
            'bin' => 'application/macbinary',
            'doc' => 'application/msword',
            'word' => 'application/msword',
            'class' => 'application/octet-stream',
            'dll' => 'application/octet-stream',
            'dms' => 'application/octet-stream',
            'exe' => 'application/octet-stream',
            'lha' => 'application/octet-stream',
            'lzh' => 'application/octet-stream',
            'psd' => 'application/octet-stream',
            'sea' => 'application/octet-stream',
            'so' => 'application/octet-stream',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'mif' => 'application/vnd.mif',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'gtar' => 'application/x-gtar',
            'php3' => 'application/x-httpd-php',
            'php4' => 'application/x-httpd-php',
            'php' => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps' => 'application/x-httpd-php-source',
            'js' => 'application/x-javascript',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tar' => 'application/x-tar',
            'tgz' => 'application/x-tar',
            'xht' => 'application/xhtml+xml',
            'xhtml' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'mpga' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'eml' => 'message/rfc822',
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'shtml' => 'text/html',
            'log' => 'text/plain',
            'text' => 'text/plain',
            'txt' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'mpeg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mov' => 'video/quicktime',
            'qt' => 'video/quicktime',
            'rv' => 'video/vnd.rn-realvideo',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie'
        );
        return (array_key_exists(strtolower($ext), $mimes) ? $mimes[strtolower($ext)] : 'application/octet-stream');
    }

    public static function filenameToType($filename)
    {
        $qpos = strpos($filename, '?');
        if ($qpos !== false) {
            $filename = substr($filename, 0, $qpos);
        }
        $pathinfo = self::mb_pathinfo($filename);
        return self::_mime_types($pathinfo['extension']);
    }

    public static function mb_pathinfo($path, $options = null)
    {
        $ret = array('dirname' => '', 'basename' => '', 'extension' => '', 'filename' => '');
        $m = array();
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
        if (array_key_exists(1, $m)) {
            $ret['dirname'] = $m[1];
        }
        if (array_key_exists(2, $m)) {
            $ret['basename'] = $m[2];
        }
        if (array_key_exists(5, $m)) {
            $ret['extension'] = $m[5];
        }
        if (array_key_exists(3, $m)) {
            $ret['filename'] = $m[3];
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
                break;
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
                break;
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
                break;
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
                break;
            default:
                return $ret;
        }
    }

    public function set($name, $value = '')
    {
        try {
            if (isset($this->$name)) {
                $this->$name = $value;
            } else {
                throw new phpmailerException($this->lang('variable_set') . $name, self::STOP_CRITICAL);
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            if ($e->getCode() == self::STOP_CRITICAL) {
                return false;
            }
        }
        return true;
    }

    public function secureHeader($str)
    {
        return trim(str_replace(array("\r", "\n"), '', $str));
    }

    public static function normalizeBreaks($text, $breaktype = "\r\n")
    {
        return preg_replace('/(\r\n|\r|\n)/ms', $breaktype, $text);
    }

    public function sign($cert_filename, $key_filename, $key_pass)
    {
        $this->sign_cert_file = $cert_filename;
        $this->sign_key_file = $key_filename;
        $this->sign_key_pass = $key_pass;
    }

    public function DKIM_QP($txt)
    {
        $line = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ord = ord($txt[$i]);
            if (((0x21 <= $ord) && ($ord <= 0x3A)) || $ord == 0x3C || ((0x3E <= $ord) && ($ord <= 0x7E))) {
                $line .= $txt[$i];
            } else {
                $line .= "=" . sprintf("%02X", $ord);
            }
        }
        return $line;
    }

    public function DKIM_Sign($s)
    {
        if (!defined('PKCS7_TEXT')) {
            if ($this->exceptions) {
                throw new phpmailerException($this->lang("signing") . ' OpenSSL extension missing.');
            }
            return '';
        }
        $privKeyStr = file_get_contents($this->DKIM_private);
        if ($this->DKIM_passphrase != '') {
            $privKey = openssl_pkey_get_private($privKeyStr, $this->DKIM_passphrase);
        } else {
            $privKey = $privKeyStr;
        }
        if (openssl_sign($s, $signature, $privKey)) {
            return base64_encode($signature);
        }
        return '';
    }

    public function DKIM_HeaderC($s)
    {
        $s = preg_replace("/\r\n\s+/", " ", $s);
        $lines = explode("\r\n", $s);
        foreach ($lines as $key => $line) {
            list($heading, $value) = explode(":", $line, 2);
            $heading = strtolower($heading);
            $value = preg_replace("/\s+/", " ", $value);
            $lines[$key] = $heading . ":" . trim($value);
        }
        $s = implode("\r\n", $lines);
        return $s;
    }

    public function DKIM_BodyC($body)
    {
        if ($body == '') {
            return "\r\n";
        }
        $body = str_replace("\r\n", "\n", $body);
        $body = str_replace("\n", "\r\n", $body);
        while (substr($body, strlen($body) - 4, 4) == "\r\n\r\n") {
            $body = substr($body, 0, strlen($body) - 2);
        }
        return $body;
    }

    public function DKIM_Add($headers_line, $subject, $body)
    {
        $DKIMsignatureType = 'rsa-sha1';
        $DKIMcanonicalization = 'relaxed/simple';
        $DKIMquery = 'dns/txt';
        $DKIMtime = time();
        $subject_header = "Subject: $subject";
        $headers = explode($this->LE, $headers_line);
        $from_header = '';
        $to_header = '';
        $current = '';
        foreach ($headers as $header) {
            if (strpos($header, 'From:') === 0) {
                $from_header = $header;
                $current = 'from_header';
            } elseif (strpos($header, 'To:') === 0) {
                $to_header = $header;
                $current = 'to_header';
            } else {
                if ($current && strpos($header, ' =?') === 0) {
                    $current .= $header;
                } else {
                    $current = '';
                }
            }
        }
        $from = str_replace('|', '=7C', $this->DKIM_QP($from_header));
        $to = str_replace('|', '=7C', $this->DKIM_QP($to_header));
        $subject = str_replace('|', '=7C', $this->DKIM_QP($subject_header));
        $body = $this->DKIM_BodyC($body);
        $DKIMlen = strlen($body);
        $DKIMb64 = base64_encode(pack("H*", sha1($body)));
        $ident = ($this->DKIM_identity == '') ? '' : " i=" . $this->DKIM_identity . ";";
        $dkimhdrs = "DKIM-Signature: v=1; a=" . $DKIMsignatureType . "; q=" . $DKIMquery . "; l=" . $DKIMlen . "; s=" . $this->DKIM_selector . ";\r\n" . "\tt=" . $DKIMtime . "; c=" . $DKIMcanonicalization . ";\r\n" . "\th=From:To:Subject;\r\n" . "\td=" . $this->DKIM_domain . ";" . $ident . "\r\n" . "\tz=$from\r\n" . "\t|$to\r\n" . "\t|$subject;\r\n" . "\tbh=" . $DKIMb64 . ";\r\n" . "\tb=";
        $toSign = $this->DKIM_HeaderC($from_header . "\r\n" . $to_header . "\r\n" . $subject_header . "\r\n" . $dkimhdrs);
        $signed = $this->DKIM_Sign($toSign);
        return $dkimhdrs . $signed . "\r\n";
    }

    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from = null)
    {
        if (!empty($this->action_function) && is_callable($this->action_function)) {
            $params = array($isSent, $to, $cc, $bcc, $subject, $body, $from);
            call_user_func_array($this->action_function, $params);
        }
    }
}

class phpmailerException extends \Exception
{

    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}


class SMTP
{
    const VERSION = '5.2.7';
    const CRLF = "\r\n";
    const DEFAULT_SMTP_PORT = 25;
    public $Version = '5.2.7';
    public $SMTP_PORT = 25;
    public $CRLF = "\r\n";
    public $do_debug = 0;
    public $Debugoutput = 'echo';
    public $do_verp = false;
    public $Timeout = 300;
    public $Timelimit = 30;
    protected $smtp_conn;
    protected $error = '';
    protected $helo_rply = '';
    protected $last_reply = '';

    public function __construct()
    {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;
        $this->do_debug = 0;
    }

    protected function edebug($str)
    {
        switch ($this->Debugoutput) {
            case 'error_log':
                error_log($str);
                break;
            case 'html':
                echo htmlentities(preg_replace('/[\r\n]+/', '', $str), ENT_QUOTES, 'UTF-8') . "<br>\n";
                break;
            case 'echo':
            default:
                echo gmdate('Y-m-d H:i:s') . "\t" . trim($str) . "\n";
        }
    }

    public function connect($host, $port = null, $timeout = 30, $options = array())
    {

        $this->error = null;
        if ($this->connected()) {
            $this->error = array('error' => 'Already connected to a server');
            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_SMTP_PORT;
        }
        if ($this->do_debug >= 3) {
            $this->edebug('Connection: opening');
        }
        $errno = 0;
        $errstr = '';
        $socket_context = stream_context_create($options);
        $this->smtp_conn = @stream_socket_client($host . ":" . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT,
            $socket_context);
        if (empty($this->smtp_conn)) {
            $this->error = array('error' => 'Failed to connect to server', 'errno' => $errno, 'errstr' => $errstr);


            if ($this->do_debug >= 1) {


                $this->edebug('SMTP ERROR: ' . $this->error['error'] . ": $errstr ($errno)");
            }
            return false;
        }
        if ($this->do_debug >= 3) {
            $this->edebug('Connection: opened');
        }
        if (substr(PHP_OS, 0, 3) != 'WIN') {
            $max = ini_get('max_execution_time');
            if ($max != 0 && $timeout > $max) {
                @set_time_limit($timeout);
            }
            stream_set_timeout($this->smtp_conn, $timeout, 0);
        }
        $announce = $this->get_lines();
        if ($this->do_debug >= 2) {
            $this->edebug('SERVER -> CLIENT: ' . $announce);
        }
        return true;
    }

    public function startTLS()
    {

        if (!$this->sendCommand("STARTTLS", "STARTTLS", 220)) {
            return false;
        }
        if (!stream_socket_enable_crypto($this->smtp_conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            return false;
        }
        return true;
    }

    public function authenticate(
        $username,
        $password,
        $authtype = 'LOGIN',
        $realm = '',
        $workstation = ''
    ) {


        if (empty($authtype)) {
            $authtype = 'LOGIN';
        }


        switch ($authtype) {
            case 'PLAIN':
                if (!$this->sendCommand('AUTH', 'AUTH PLAIN', 334)) {
                    return false;
                }
                if (!$this->sendCommand('User & Password', base64_encode("\0" . $username . "\0" . $password), 235)) {
                    return false;
                }
                break;
            case 'LOGIN':
                if (!$this->sendCommand('AUTH', 'AUTH LOGIN', 334)) {

                    return false;
                }
                if (!$this->sendCommand("Username", base64_encode($username), 334)) {

                    return false;
                }
                if (!$this->sendCommand("Password", base64_encode($password), 235)) {

                    return false;
                }

                break;
            case 'NTLM':
                require_once 'extras/ntlm_sasl_client.php';
                $temp = new stdClass();
                $ntlm_client = new ntlm_sasl_client_class;
                if (!$ntlm_client->Initialize($temp)) {
                    $this->error = array('error' => $temp->error);
                    if ($this->do_debug >= 1) {
                        $this->edebug('You need to enable some modules in your php.ini file: ' . $this->error['error']);
                    }
                    return false;
                }
                $msg1 = $ntlm_client->TypeMsg1($realm, $workstation);
                if (!$this->sendCommand('AUTH NTLM', 'AUTH NTLM ' . base64_encode($msg1), 334)) {
                    return false;
                }
                $challenge = substr($this->last_reply, 3);
                $challenge = base64_decode($challenge);
                $ntlm_res = $ntlm_client->NTLMResponse(substr($challenge, 24, 8), $password);
                $msg3 = $ntlm_client->TypeMsg3($ntlm_res, $username, $realm, $workstation);
                return $this->sendCommand('Username', base64_encode($msg3), 235);
                break;
            case 'CRAM-MD5':
                if (!$this->sendCommand('AUTH CRAM-MD5', 'AUTH CRAM-MD5', 334)) {
                    return false;
                }
                $challenge = base64_decode(substr($this->last_reply, 4));
                $response = $username . ' ' . $this->hmac($challenge, $password);
                return $this->sendCommand('Username', base64_encode($response), 235);
                break;
        }
        return true;
    }


    protected function hmac($data, $key)
    {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }
        $b = 64;
        if (strlen($key) > $b) {
            $key = pack('H*', md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }

    public function connected()
    {
        if (!empty($this->smtp_conn)) {
            $sock_status = stream_get_meta_data($this->smtp_conn);
            if ($sock_status['eof']) {
                if ($this->do_debug >= 1) {
                    $this->edebug('SMTP NOTICE: EOF caught while checking if connected');
                }
                $this->close();
                return false;
            }
            return true;
        }
        return false;
    }

    public function close()
    {
        $this->error = null;
        $this->helo_rply = null;
        if (!empty($this->smtp_conn)) {
            fclose($this->smtp_conn);
            if ($this->do_debug >= 3) {
                $this->edebug('Connection: closed');
            }
            $this->smtp_conn = 0;
        }
    }

    public function data($msg_data)
    {
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }
        $msg_data = str_replace("\r\n", "\n", $msg_data);
        $msg_data = str_replace("\r", "\n", $msg_data);
        $lines = explode("\n", $msg_data);
        $field = substr($lines[0], 0, strpos($lines[0], ':'));
        $in_headers = false;
        if (!empty($field) && !strstr($field, ' ')) {
            $in_headers = true;
        }
        $max_line_length = 998;
        foreach ($lines as $line) {
            $lines_out = null;
            if ($line == '' && $in_headers) {
                $in_headers = false;
            }
            while (strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line, 0, $max_line_length), ' ');
                if (!$pos) {
                    $pos = $max_line_length - 1;
                    $lines_out[] = substr($line, 0, $pos);
                    $line = substr($line, $pos);
                } else {
                    $lines_out[] = substr($line, 0, $pos);
                    $line = substr($line, $pos + 1);
                }
                if ($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;
            while (list(, $line_out) = @each($lines_out)) {
                if (strlen($line_out) > 0) {
                    if (substr($line_out, 0, 1) == '.') {
                        $line_out = '.' . $line_out;
                    }
                }
                $this->client_send($line_out . self::CRLF);
            }
        }
        return $this->sendCommand('DATA END', '.', 250);
    }

    public function hello($host = '')
    {
        if (!$this->sendHello('EHLO', $host)) {
            if (!$this->sendHello('HELO', $host)) {
                return false;
            }
        }
        return true;
    }

    protected function sendHello($hello, $host)
    {
        $noerror = $this->sendCommand($hello, $hello . ' ' . $host, 250);
        $this->helo_rply = $this->last_reply;
        return $noerror;
    }

    public function mail($from)
    {
        $useVerp = ($this->do_verp ? ' XVERP' : '');
        return $this->sendCommand('MAIL FROM', 'MAIL FROM:<' . $from . '>' . $useVerp, 250);
    }

    public function quit($close_on_error = true)
    {
        $noerror = $this->sendCommand('QUIT', 'QUIT', 221);
        $e = $this->error;
        if ($noerror or $close_on_error) {
            $this->close();
            $this->error = $e;
        }
        return $noerror;
    }

    public function recipient($to)
    {
        return $this->sendCommand('RCPT TO ', 'RCPT TO:<' . $to . '>', array(250, 251));
    }

    public function reset()
    {
        return $this->sendCommand('RSET', 'RSET', 250);
    }

    protected function sendCommand($command, $commandstring, $expect)
    {


        if (!$this->connected()) {
            $this->error = array("error" => "Called $command without being connected");
            return false;
        }
        $this->client_send($commandstring . self::CRLF);
        $reply = $this->get_lines();
        $code = substr($reply, 0, 3);


        if ($this->do_debug >= 2) {
            $this->edebug('SERVER -> CLIENT: ' . $reply);
        }
        if (!in_array($code, (array)$expect)) {
            $this->last_reply = null;
            $this->error = array(
                "error" => "$command command failed",
                "smtp_code" => $code,
                "detail" => substr($reply, 4)
            );
            if ($this->do_debug >= 1) {
                $this->edebug('SMTP ERROR: ' . $this->error['error'] . ': ' . $reply);
            }
            return false;
        }
        $this->last_reply = $reply;
        $this->error = null;


        return true;
    }

    public function sendAndMail($from)
    {
        return $this->sendCommand("SAML", "SAML FROM:$from", 250);
    }

    public function verify($name)
    {
        return $this->sendCommand("VRFY", "VRFY $name", array(250, 251));
    }

    public function noop()
    {
        return $this->sendCommand("NOOP", "NOOP", 250);
    }

    public function turn()
    {
        $this->error = array('error' => 'The SMTP TURN command is not implemented');
        if ($this->do_debug >= 1) {
            $this->edebug('SMTP NOTICE: ' . $this->error['error']);
        }
        return false;
    }

    public function client_send($data)
    {

        if ($this->do_debug >= 1) {
            $this->edebug("CLIENT -> SERVER: $data");
        }
        return fwrite($this->smtp_conn, $data);
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLastReply()
    {
        return $this->last_reply;
    }

    protected function get_lines()
    {
        $data = '';
        $endtime = 0;
        if (!is_resource($this->smtp_conn)) {
            return $data;
        }
        stream_set_timeout($this->smtp_conn, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        while (is_resource($this->smtp_conn) && !feof($this->smtp_conn)) {
            $str = @fgets($this->smtp_conn, 515);
            if ($this->do_debug >= 4) {
                $this->edebug("SMTP -> get_lines(): \$data was \"$data\"");
                $this->edebug("SMTP -> get_lines(): \$str is \"$str\"");
            }
            $data .= $str;
            if ($this->do_debug >= 4) {
                $this->edebug("SMTP -> get_lines(): \$data is \"$data\"");
            }
            if (substr($str, 3, 1) == ' ') {
                break;
            }
            $info = stream_get_meta_data($this->smtp_conn);
            if ($info['timed_out']) {
                if ($this->do_debug >= 4) {
                    $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)');
                }
                break;
            }
            if ($endtime) {
                if (time() > $endtime) {
                    if ($this->do_debug >= 4) {
                        $this->edebug('SMTP -> get_lines(): timelimit reached (' . $this->Timelimit . ' sec)');
                    }
                    break;
                }
            }
        }
        return $data;
    }

    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    public function getVerp()
    {
        return $this->do_verp;
    }

    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    public function getDebugOutput()
    {
        return $this->Debugoutput;
    }

    public function setDebugLevel($level = 0)
    {
        $this->do_debug = $level;
    }

    public function getDebugLevel()
    {
        return $this->do_debug;
    }

    public function setTimeout($timeout = 0)
    {
        $this->Timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->Timeout;
    }
}

class POP3
{
    public $Version = '5.2.7';
    public $POP3_PORT = 110;
    public $POP3_TIMEOUT = 30;
    public $CRLF = "\r\n";
    public $do_debug = 0;
    public $host;
    public $port;
    public $tval;
    public $username;
    public $password;
    private $pop_conn;
    private $connected;
    private $error;
    const CRLF = "\r\n";

    public function __construct()
    {
        $this->pop_conn = 0;
        $this->connected = false;
        $this->error = null;
    }

    public static function popBeforeSmtp(
        $host,
        $port = false,
        $tval = false,
        $username = '',
        $password = '',
        $debug_level = 0
    ) {
        $pop = new POP3;
        return $pop->authorise($host, $port, $tval, $username, $password, $debug_level);
    }

    public function authorise($host, $port = false, $tval = false, $username = '', $password = '', $debug_level = 0)
    {


        $this->host = $host;
        if ($port === false) {
            $this->port = $this->POP3_PORT;
        } else {
            $this->port = $port;
        }
        if ($tval === false) {
            $this->tval = $this->POP3_TIMEOUT;
        } else {
            $this->tval = $tval;
        }
        $this->do_debug = $debug_level;
        $this->username = $username;
        $this->password = $password;
        $this->error = null;
        $result = $this->connect($this->host, $this->port, $this->tval);
        if ($result) {
            $login_result = $this->login($this->username, $this->password);
            if ($login_result) {
                $this->disconnect();
                return true;
            }
        }
        $this->disconnect();
        return false;
    }

    public function connect($host, $port = false, $tval = 30)
    {
        if ($this->connected) {
            return true;
        }
        set_error_handler(array($this, 'catchWarning'));
        $this->pop_conn = fsockopen($host, $port, $errno, $errstr, $tval);
        restore_error_handler();
        if ($this->error && $this->do_debug >= 1) {
            $this->displayErrors();
        }
        if ($this->pop_conn == false) {
            $this->error = array(
                'error' => "Failed to connect to server $host on port $port",
                'errno' => $errno,
                'errstr' => $errstr
            );
            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
            return false;
        }
        if (version_compare(phpversion(), '5.0.0', 'ge')) {
            stream_set_timeout($this->pop_conn, $tval, 0);
        } else {
            if (substr(PHP_OS, 0, 3) !== 'WIN') {
                socket_set_timeout($this->pop_conn, $tval, 0);
            }
        }
        $pop3_response = $this->getResponse();
        if ($this->checkResponse($pop3_response)) {
            $this->connected = true;
            return true;
        }
        return false;
    }

    public function login($username = '', $password = '')
    {
        if ($this->connected == false) {
            $this->error = 'Not connected to POP3 server';
            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
        }
        if (empty($username)) {
            $username = $this->username;
        }
        if (empty($password)) {
            $password = $this->password;
        }
        $this->sendString("USER $username" . self::CRLF);
        $pop3_response = $this->getResponse();
        if ($this->checkResponse($pop3_response)) {
            $this->sendString("PASS $password" . self::CRLF);
            $pop3_response = $this->getResponse();
            if ($this->checkResponse($pop3_response)) {
                return true;
            }
        }
        return false;
    }

    public function disconnect()
    {
        $this->sendString('QUIT');
        @fclose($this->pop_conn);
    }

    private function getResponse($size = 128)
    {
        $r = fgets($this->pop_conn, $size);
        if ($this->do_debug >= 1) {
            echo "Server -> Client: $r";
        }
        return $r;
    }

    private function sendString($string)
    {
        if ($this->pop_conn) {
            if ($this->do_debug >= 2) {
                echo "Client -> Server: $string";
            }
            return fwrite($this->pop_conn, $string, strlen($string));
        }
        return 0;
    }

    private function checkResponse($string)
    {
        if (substr($string, 0, 3) !== '+OK') {
            $this->error = array('error' => "Server reported an error: $string", 'errno' => 0, 'errstr' => '');
            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
            return false;
        } else {
            return true;
        }
    }

    private function displayErrors()
    {
        echo '<pre>';
        foreach ($this->error as $single_error) {
            print_r($single_error);
        }
        echo '</pre>';
    }

    private function catchWarning($errno, $errstr, $errfile, $errline)
    {
        $this->error[] = array(
            'error' => "Connecting to the POP3 server raised a PHP warning: ",
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        );
    }
}
