<?php
/**
 * Created by IntelliJ IDEA.
 * User: ds
 * Date: 2019/4/18
 * Time: 15:31
 */

namespace Home\Controller;
use Think\Controller;

class AuthController extends Controller
{

    //共享 accessToken
    private $accessToken = null;
    private $Index = null;

    public function __construct(){

        $this->Index = A('Index');
        $this->Index->getAccessToken();
        $this->accessToken = $this->Index->accessToken;

    }

    Public function get_pre_auth_code(){
        $res = $this->component_detail();
        $last_time = $res['pre_time'];
        $pre_auth_code = $res['pre_auth_code'];
        $difference_time = $this->validity($last_time);
        if(empty($pre_auth_code) || $difference_time>1100){
            $pre_auth_code = $this->get_pre_auth_code_again();
        }
        return $pre_auth_code;
    }
    Public function get_pre_auth_code_again(){

        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='
            .$this->accessToken;
        $param ['component_appid'] = C('appId');

        echo $url;
        $da = $this->Index->_httpPost($url,$param);
        var_dump($da);die;
    }
}