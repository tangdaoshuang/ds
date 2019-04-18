<?php
return array(
    //获取微信token
	'accessTokenUrl' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",
	//获取VX用户信息
	'userInfo' =>"https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN",
    //给用户发消息
    'templateSend' =>"https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s",
);