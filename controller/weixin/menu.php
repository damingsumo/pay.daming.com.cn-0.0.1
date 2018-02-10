<?php
/**
 * 微信菜单操作类
 */

class Controller_Weixin_Menu extends Controller_Base {

    /**
     * 创建菜单
     */
    public function create() {
        $home_url = HOME_URL;
        $menus = <<<EOF
{
     "button":[
	   {
	   	   "type":"view",
           "name":"充电",
           "url":"{$home_url}"
       }]
 }
EOF;
        $accessToken = Weixin::getAccessToken();
        $http = new WeiXin_Http();
        $result = $http->post('menu/create?access_token='.$accessToken, $menus);
        var_dump($result);
    }
}
