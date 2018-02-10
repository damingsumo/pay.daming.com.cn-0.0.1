<?php
/**
 * 微信对外接口--微信服务器访问地址
 */
class Controller_Weixin_Server extends Controller_Base {
    private static $wechat;

    public function __construct() {
        self::$wechat = new WeiXin_Base(WEIXIN_TOKEN, TRUE);
    }
    /**
     * 微信推送事件通知
     */
    public function listen() {
        switch (self::$wechat->getRequest('msgtype')) {
            case 'event':
                switch (self::$wechat->getRequest('event')) {
                    //关注
                    case 'subscribe':
                        $this->onSubscribe();
                    break;
                    //取消关注
                    case 'unsubscribe':
                        $this->onUnsubscribe();
                    break;
                    //扫码已关注时
                    case 'SCAN':
                        $this->onScan();
                    break;
                    //上报地理位置事件
                    case 'LOCATION':
                        $this->onEventLocation();
                    break;
                    //自定义菜单事件
                    case 'CLICK':
                        $this->onClick();
                    break;
                }
            break; 
            //文本消息   
            case 'text':
                $this->onText();
            break;
            //图片消息
            case 'image':
                $this->onImage();
            break;
            //地理位置消息
            case 'location':
                $this->onLocation();
            break;
            //链接消息
            case 'link':
                $this->onLink();
            break;
            //语音消息
            case 'voice':
                $this->onVoice();
            break;
            //视频消息
            case 'video':
                $this->onVideo();
            break;
            //小视频消息
            case 'shortvideo':
                $this->onShortVideo();
            break;
            default:
                $this->onUnknown();
                break;
        }
    }
    /**
     * 用户关注时触发，回复「欢迎关注」
     * @return void
     */
    public  function onSubscribe() {
        $open_id = self::$wechat->getRequest('fromusername');
        $accessToken=self::$wechat->getAccessToken();
        $parameters['access_token'] = $accessToken;
        $parameters['openid'] =$open_id ;
        $parameters['lang'] = 'zh_CN';
        $http = new WeiXin_Http();
        $res = $http->get('user/info', $parameters);
        $res=json_decode($res);
        $weChatUser['name']=$res->nickname;
        //emoji字符过滤
        require_once(FW_PATH . '/plugins/emoji/emoji.php');
        $tmpNick = emoji_unified_to_html($res->nickname);
        if(strpos($tmpNick, 'emoji') !== false ){
        	$weChatUser['name'] = '';
        }
        
        self::$wechat->responseText($weChatUser['name'].' 终于等到你了！');
    }

    /**
     * 用户已关注时,扫描带参数二维码时触发，回复二维码的EventKey (测试帐号似乎不能触发)
     *
     * @return void
     */
    public function onScan() {
        self::$wechat->responseText('二维码的EventKey：' . self::$wechat->getRequest('eventkey'));
    }

    /**
     * 用户取消关注时触发
     * @return void
     */
    public function onUnsubscribe() {
		//更改用户状态
    	$open_id = self::$wechat->getRequest('fromusername');

    }

    /**
     * 上报地理位置时触发,回复收到的地理位置 //当用户允许公众号获取地理位置时,
     *
     * @return void
     */
    public function onEventLocation() {
        $latitude = self::$wechat->getRequest('Latitude');
        $longitude = self::$wechat->getRequest('Longitude');
        $userOpenId = self::$wechat->getRequest('fromusername');
//        $location = cache::get(WEIXIN_USER_LOCATION.$userOpenId);
        $location = '';
        //位置未变
        if(!empty($location) && (isset($location['latitude']) && $latitude == $location['latitude']) && (isset($location['longitude']) && $longitude == $location['longitude'])) {
            return true;
        }
        print_r($latitude);
        print_r($longitude);
    	//修正位置
    	//获得城市/地址详细信息
    	$locationInfo = $this->resistantLocation($latitude, $longitude);
    	print_r($locationInfo);exit;
    	$city = isset($locationInfo['result']['addressComponent']['city']) ? $locationInfo['result']['addressComponent']['city'] : '';
    	$city = mb_substr($city, 0, -1, 'utf-8');
    	$address = isset($locationInfo['result']['formatted_address']) ? $locationInfo['result']['formatted_address'] : '';
        $location = array('latitude' => $latitude, 'longitude' => $longitude, 'city' => $city, 'address' => $address);
//    	cache::set(WEIXIN_USER_LOCATION.$userOpenId, $location, 600);
    }

    /**
     * 百度逆反地理解析
     */
    public function resistantLocation($lat, $lng) {
    	//转换成百度坐标系经纬度
    	$location = $lat.','.$lng;//116.343314,39.939429
    	$location_info = @file_get_contents('http://api.map.baidu.com/geoconv/v1/?coords=' . $location . '&from=3&to=5&ak='.BAIDU_MAP_AK);
    	if(empty($location_info)) {
    		$lastLocation = $location;
    	} else {
    		$locationInfo = json_decode($location_info, true);
    		if($locationInfo['status'] == 0)
    			$lastLocation = $locationInfo['result'][0]['x'] . ',' . $locationInfo['result'][0]['y'];
    		else
    			$lastLocation = $location;
    	}
    	
    	//获得真实地址
    	$url = 'http://api.map.baidu.com/geocoder/v2/?ak='.BAIDU_MAP_AK.'&callback=0&location='.$lastLocation.'&output=json&pois=0';
    	$res = file_get_contents($url);
    	
    	return json_decode($res, true);
    }
    
    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     *
     * @return void
     */
    public function onText() {
        $content = self::$wechat->getRequest('content');
        if(!empty($content)) {
            self::$wechat->responseText('暂不支持此功能');
        }
    }

    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    public function onImage() {
        $items = array(
            new NewsResponseItem('标题一', '描述一', self::$wechat->getRequest('picurl'), self::$wechat->getRequest('picurl')),
            new NewsResponseItem('标题二', '描述二', self::$wechat->getRequest('picurl'), self::$wechat->getRequest('picurl')),
        );

        self::$wechat->responseNews($items);
    }

    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    public function onLocation() {
        self::$wechat->responseText('收到了位置推送：' . self::$wechat->getRequest('Location_X') . ','. self::$wechat->getRequest('Location_Y').'1234');
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     * @return void
     */
    public function onLink() {
        self::$wechat->responseText('收到了链接：' . self::$wechat->getRequest('url'));
    }

    /**
     * 收到语音消息时触发，回复语音识别结果(需要开通语音识别功能)
     *
     * @return void
     */
    public function onVoice() {
        self::$wechat->responseText('收到了语音消息,识别结果为：' . self::$wechat->getRequest('Recognition'));
    }

    /**
     * 收到自定义菜单消息时触发，回复菜单的EventKey
     * @return void
     */
    public function onClick() {

    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    public function onUnknown() {
        self::$wechat->responseText('收到了未知类型消息：' . self::$wechat->getRequest('msgtype'));
    }
}