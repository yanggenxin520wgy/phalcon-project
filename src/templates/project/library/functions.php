<?php
use @@namespace@@\Library\StrTool;


if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if (empty($value)) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && strtool::startsWith($value, '"') && strtool::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}


if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if ( ! function_exists('sendNotice')) {
    function sendNotice($data, $level, $url)
    {
        //文本消息
        $d['msg_type'] = 'text';
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $d['content']['text'] = sprintf(BASE_PATH . "- [%s] [%s] [%s] [%s] : %s",
            RUNTIME,
            $level,
            gethostname(),
            date('Y-m-d H:i:s'),
            $data
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($d),
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: zh-CN',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

if ( ! function_exists('getRealIp')) {
    /**
     * @desc 获取真实的IP地址
     * @return mixed|string
     */
    function getRealIp()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $real_ip = 'unknown';
                foreach ($arr as $ip) {
                    $ip = trim($ip);

                    if ($ip != 'unknown') {
                        $real_ip = $ip;
                        break;
                    }
                }
            } else {
                if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $real_ip = $_SERVER['HTTP_CLIENT_IP'];
                } else {
                    if (isset($_SERVER['REMOTE_ADDR'])) {
                        $real_ip = $_SERVER['REMOTE_ADDR'];
                    } else {
                        $real_ip = '0.0.0.0';
                    }
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $real_ip = getenv('HTTP_X_FORWARDED_FOR');
            } else {
                if (getenv('HTTP_CLIENT_IP')) {
                    $real_ip = getenv('HTTP_CLIENT_IP');
                } else {
                    $real_ip = getenv('REMOTE_ADDR');
                }
            }
        }

        preg_match('/[\\d\\.]{7,15}/', $real_ip, $online_ip);
        $real_ip = (!empty($online_ip[0]) ? $online_ip[0] : '0.0.0.0');
        return $real_ip;
    }
}