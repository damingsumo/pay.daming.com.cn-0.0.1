<?php
/**
 * 微信公众平台 PHP SDK
 *
 * @author NetPuter <netputer@gmail.com>
 */

/**
 * 微信公众平台处理类
 */
class WeiXin_Base{

    /**
     * 调试模式，将错误通过文本消息回复显示
     *
     * @var boolean
     */
    private $debug;

    /**
     * 以数组的形式保存微信服务器每次发来的请求
     *
     * @var array
     */
    private $request;

    /**
     * 初始化，判断此次请求是否为验证请求，并以数组形式保存
     *
     * @param string $token 验证信息
     * @param boolean $debug 调试模式，默认为关闭
     */
    public function __construct($token, $debug = FALSE) {
        //$xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);

        /*if (!$this->validateSignature($token)) {
            exit ('验证失败！');
        }*/
        if ($this->isValid()) {
            // 网址接入验证
            exit($_GET['echostr']);
        }
//        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
//            exit('缺少数据');
//        }

        $this->debug = $debug;
        set_error_handler(array(&$this, 'errorHandler'));
        // 设置错误处理函数，将错误通过文本消息回复显示

        $xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->request = array_change_key_case($xml, CASE_LOWER);
        // 将数组键名转换为小写，提高健壮性，减少因大小写不同而出现的问题
    }

    /**
     * 判断此次请求是否为验证请求
     *
     * @return boolean
     */
    private function isValid() {
        return isset($_GET['echostr']);
    }

    /**
     * 验证此次请求的签名信息
     *
     * @param  string $token 验证信息
     * @return boolean
     */
    private function validateSignature($token) {
        if ( ! (isset($_GET['signature']) && isset($_GET['timestamp']) && isset($_GET['nonce']))) {
            return FALSE;
        }

        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $tmp = array($token, $timestamp, $nonce);
        sort($tmp, SORT_STRING);
        $tmpStr = implode( $tmp );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return $signature.$timestamp.$nonce;
        }else{
            return $signature.$timestamp.$nonce;
        }
    }

    /**
     * 获取本次请求中的参数，不区分大小
     *
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    public function getRequest($param = FALSE) {
        if ($param === FALSE) {
            return $this->request;
        }

        $param = strtolower($param);

        if (isset($this->request[$param])) {
            return $this->request[$param];
        }

        return NULL;
    }

    /**
     * 回复文本消息
     *
     * @param  string  $content  消息内容
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    public function responseText($content, $funcFlag = 0) {
        exit(new TextResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag));
    }

    /**
     * 回复音乐消息
     *
     * @param  string  $title       音乐标题
     * @param  string  $description 音乐描述
     * @param  string  $musicUrl    音乐链接
     * @param  string  $hqMusicUrl  高质量音乐链接，Wi-Fi 环境下优先使用
     * @param  integer $funcFlag    默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    public function responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
        exit(new MusicResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag));
    }

    /**
     * 回复图文消息
     * @param  array   $items    由单条图文消息类型 NewsResponseItem() 组成的数组
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    public function responseNews($items, $funcFlag = 0) {
        exit(new NewsResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag));
    }
    
    public static function getAccessToken(){
//    	$accessTokenData = cache::get('accessTokenData');
    	$accessTokenData = '';
    	if($accessTokenData != '') {
    	   	$resDataArr = explode('|', $accessTokenData);
	    	$_accessToken = isset($resDataArr[0]) ? $resDataArr[0] : '';//accessToken
	    	$_lastGetAccessTokenTime = isset($resDataArr[2]) ? $resDataArr[2] : 0;//上一次获取时间
	    	$_expiresIn = isset($resDataArr[1]) ? $resDataArr[1] : 0;//到期时间
	    	
	    	// 如果已经有AccessToken判断是否在过期时间前10秒
	    	if ($_accessToken && time() < $_lastGetAccessTokenTime + $_expiresIn - 10) {
	    		return $_accessToken;
	    	}
    	}
    	
    	$http = new Http();
    	// 请求地址
    	$params = array(
    			'grant_type' => 'client_credential',
    			'appid' => WEIXIN_APPID,
    			'secret' => WEIXIN_APPSECRET
    	);
    	
    	// 发起请求,获取数据
    	$data = $http->get('token', $params);
    	// 转为数组
    	$data = json_decode($data, TRUE);
    	if (isset($data['access_token'])) {
    		$accessTokenData = $data['access_token'].'|'.$data['expires_in'].'|'.time();
//    		$res = cache::set('accessTokenData', $accessTokenData);
    		if($res) {
    			return $data['access_token'];
    		}
    	}
    	
    	return false;
    }

    /**
     * 自定义的错误处理函数，将 PHP 错误通过文本消息回复显示
     * @param  int $level   错误代码
     * @param  string $msg  错误内容
     * @param  string $file 产生错误的文件
     * @param  int $line    产生错误的行数
     * @return void
     */
    protected function errorHandler($level, $msg, $file, $line) {
        if ( ! $this->debug) {
            return;
        }

        $error_type = array(
            // E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            // E_PARSE             => 'Parse Error',
            E_NOTICE            => 'Notice',
            // E_CORE_ERROR        => 'Core Error',
            // E_CORE_WARNING      => 'Core Warning',
            // E_COMPILE_ERROR     => 'Compile Error',
            // E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Strict',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
        );

        $template = <<<ERR
PHP 报错啦！

%s: %s
File: %s
Line: %s
ERR;

        $this->responseText(sprintf($template,
            $error_type[$level],
            $msg,
            $file,
            $line
        ));
    }

}

/**
 * 用于回复的基本消息类型
 */
abstract class WechatResponse {

    protected $toUserName;
    protected $fromUserName;
    protected $funcFlag;
    protected $template;

    public function __construct($toUserName, $fromUserName, $funcFlag) {
        $this->toUserName = $toUserName;
        $this->fromUserName = $fromUserName;
        $this->funcFlag = $funcFlag;
    }

    abstract public function __toString();

}

/**
 * 用于回复的文本消息类型
 */
class TextResponse extends WechatResponse {

    protected $content;

    public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);

        $this->content = $content;
        $this->template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[%s]]></Content>
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
    }

    public function __toString() {
        return sprintf($this->template,
            $this->toUserName,
            $this->fromUserName,
            time(),
            $this->content,
            $this->funcFlag
        );
    }

}

/**
 * 用于回复的音乐消息类型
 */
class MusicResponse extends WechatResponse {

    protected $title;
    protected $description;
    protected $musicUrl;
    protected $hqMusicUrl;

    public function __construct($toUserName, $fromUserName, $title, $description, $musicUrl, $hqMusicUrl, $funcFlag) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);

        $this->title = $title;
        $this->description = $description;
        $this->musicUrl = $musicUrl;
        $this->hqMusicUrl = $hqMusicUrl;
        $this->template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[music]]></MsgType>
  <Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
  </Music>
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
    }

    public function __toString() {
        return sprintf($this->template,
            $this->toUserName,
            $this->fromUserName,
            time(),
            $this->title,
            $this->description,
            $this->musicUrl,
            $this->hqMusicUrl,
            $this->funcFlag
        );
    }

}

/**
 * 用于回复的图文消息类型
 */
class NewsResponse extends WechatResponse {

    protected $items = array();

    public function __construct($toUserName, $fromUserName, $items, $funcFlag) {
        parent::__construct($toUserName, $fromUserName, $funcFlag);

        $this->items = $items;
        $this->template = <<<XML
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
    %s
  </Articles>
  <FuncFlag>%s</FuncFlag>
</xml>
XML;
    }

    public function __toString() {
        return sprintf($this->template,
            $this->toUserName,
            $this->fromUserName,
            time(),
            count($this->items),
            implode($this->items),
            $this->funcFlag
        );
    }

}

/**
 * 单条图文消息类型
 */
class NewsResponseItem {

    protected $title;
    protected $description;
    protected $picUrl;
    protected $url;
    protected $template;

    public function __construct($title, $description, $picUrl, $url) {
        $this->title = $title;
        $this->description = $description;
        $this->picUrl = $picUrl;
        $this->url = $url;
        $this->template = <<<XML
<item>
  <Title><![CDATA[%s]]></Title>
  <Description><![CDATA[%s]]></Description>
  <PicUrl><![CDATA[%s]]></PicUrl>
  <Url><![CDATA[%s]]></Url>
</item>
XML;
    }

    public function __toString() {
        return sprintf($this->template,
            $this->title,
            $this->description,
            $this->picUrl,
            $this->url
        );
    }

}