<?php
/**
 * SDK通信HTTP类
 * 远程通信必须引入此类
 * @author liu 2014-03-05
 *
 */
class WeiXin_Http {
    public $access_token = 'not_have_access_key_yet';
    public $http_code = '';
    public $uri = '';
    public $host = 'https://api.weixin.qq.com/cgi-bin/';
    public $timeout = 30;
    public $connect_timeout = 30;
    public $ssl_verifypeer = FALSE;
    public $format = 'json';
    public $decode_json = TRUE;
    public $http_info;
    public $useragent = 'SERVICE.BESTDO.COM';
    public $debug = false;
    public static $boundary = '';

    public function __construct($host = 'https://api.weixin.qq.com/cgi-bin/', $access_token = '') {
        $this->access_token = $access_token;
        $this->host = $host;
    }

    /**
     * GET请求获得JSON数据.
     *
     * @return mixed
     */
    public function get($url, $parameters = array(), $ext = '') {
        return $this->execute($url, 'GET', $parameters, $ext);
    }

    /**
     * POST请求获得数据
     *
     * @return mixed
     */
    public function post($url, $parameters = array()) {
        return $this->execute($url, 'POST', $parameters );
    }

    /**
     * 执行通信方法, 返回远程通信结果
     *
     * @return string
     * @ignore
     */
    public function execute($url, $method, $parameters, $ext = '') {
        $url = $this->host.$url;

        if($method == 'GET') {
            $parameters = http_build_query($parameters);
            $url = $url . '?' . $parameters. $ext;
            return $this->http($url, 'GET');
        } else {
            return $this->http($url, $method, $parameters);
        }
    }
    /**
     * CURL回调执行的方法.
     *
     * @return int
     * @ignore
     */
    public function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
    /**
     * 统一采用CURL方式进行远程操作
     *
     * @return string API results
     * @ignore
     */
    public function http($url, $method, $postfields = NULL, $headers = array()) {
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }

        if(isset($this->access_token) && $this->access_token ) {
            $headers[] = "api_access_token : ".$this->access_token;
        }

        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

        $response = curl_exec($ci);
        curl_close ($ci);
        return $response;
    }
}