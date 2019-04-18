<?php
namespace Home\Controller;
use Think\Controller;

//2019-04-17  ds
class IndexController extends Controller {
	
	public  $appId = null;
    public  $appSecret = null;
    public  $wxId = null;

    public  $msg = null;

	//-----请求获取
    public $accessToken = null;
    public $openId = null;
    public $unionId = 0;

	
	//初始化常量
	public function __construct(){
		
		$this->appId = C('appId');
		$this->appSecret = C('appSecret');
		$this->wxId = C('wxId');

		$this->msg = msgData();
	}
    public function index(){
	    $this->getAccessToken();
        $this->openId = "ouEg_s3FFydtOcwNVenGgY-IK0xw";
        $this->_getUserInfo();
        $this->_inserUser("gh_2111d600572b");


        $data1['first'] = '您的优惠券即将到期';
        $data1['keyword1'] = '点滴生活公众号';
        $data1['keyword2'] = '海天盛筵优惠券';
        $data1['remark'] = '请及时使用!';
        echo json_encode($data1);die;
    }
	
	//是否存在token ， 没有就重新请求
	public function getAccessToken(){

		$accessToken = session('accessToken');
		if($accessToken){
			$this->accessToken = $accessToken;
			//return;
		}
		$url = sprintf(C('accessTokenUrl'),$this->appId,$this->appSecret);
		$data = $this->_httpPost($url,null);
		$data = json_decode($data,true);
		if($data){
			session('accessToken',$data['access_token']);
			$this->accessToken = $data['access_token'];
		}
		return ;
	}

	public function check(){
        echo $_GET['echostr'];
    }


	public function wx(){

	    //$this->check();die;
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = file_get_contents("php://input");
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string( $postArr );

        //测试文件
        file_put_contents('./test.txt',$postArr);

        //如果是关注 subscribe 事件
        if( strtolower($postObj->Event == 'subscribe') ){
                //回复用户消息(纯文本格式)
                $fromUser = $postObj->FromUserName;
                $toUser = $postObj->ToUserName;

                /*$time     = time();
                $msgType  =  'text';

                $content  = '欢迎关注我的微信公众账号';
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);

                */


                $this->openId = $fromUser;
				$this->getAccessToken();
				$this->_getUserInfo();
                $this->_inserUser($toUser);

                //echo $info;
        }
	}

	//消息触发-----测试模板
	public function setApiMsg($unionId = null,$msgType = 'ticket'){

	    $msg = $this->msg;
	    if(!$unionId){
	        $msg['msg'] = 'unionId未传递';
            $msg['code'] = 40001;
            echo json_encode($msg);die;
        }

	    //判断参数
        if($_REQUEST['data']){
            $data = json_decode($_REQUEST['data'],true);
        }

        $this->unionId = $unionId;

        $this->_getWxUser();
	    if(!$this->openId){
            $msg['msg'] = '未关注此公众号';
            $msg['code'] = 40002;
            echo json_encode($msg);die;
        }
	    $this->getAccessToken();

	    $url = sprintf(C("templateSend"),$this->accessToken);

	    $postData['touser'] = $this->openId;

	    switch ($msgType){
            case ticket:
                $postData['template_id'] = C("ticket_template_id");
                $postData['data'] = array(
                    'first'=>array('value'=>'您的优惠券即将到期!','color'=>'#173177'),
                    'keyword1'=>array('value'=>'点滴生活公众号','color'=>'#173177'),
                    'keyword2'=>array('value'=>'海天盛筵优惠券','color'=>'#173177'),
                    'remark'=>array('value'=>'请及时使用！','color'=>'#173177'),
                    );
                $this->_httpPost($url,$postData);
            default:
                return;
        }
	}

	//获取用户信息
	public function _getWxUser(){

        $sql = "select openId from wx_user where wxId ='".$this->wxId."' and unionId='".$this->unionId."' limit 1";
        $sqlArr['wxId'] = $this->wxId;
        $sqlArr['unionId'] = $this->unionId;
        $rs = M('wx_user')->query($sql);

        if($rs[0]['openId']){
            $this->openId = $rs[0]['openId'];
        }
    }

	//获取用户数据进行操作 目前 unionId
	public function _getUserInfo(){
		 
		 //获取用户信息
        $url = sprintf(C('userInfo'),$this->accessToken,$this->openId);
        $data = $this->_httpPost($url,null);
        $data = json_encode($data ,true);

        //暂时初始化unionid
        if($data['unionid'])
            $this->unionId = $data['unionid'];
	}
	
	public function _inserUser($wxId){
			
		$sql = "INSERT INTO wx_user(`openId`,`wxId`,`unionId`) values('".
            $this->openId."','".$wxId."','".$this->unionId."')";
		$rs = M()->execute($sql);

		//日志判断
	}
	
	public function _httpPost($url,$postData){
		
		//初始化
		$curl = curl_init();
		//设置抓取的url
		curl_setopt($curl, CURLOPT_URL, $url);
		//设置头文件的信息作为数据流输出
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
		if($postData){
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
		}
		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//执行命令
		$data = curl_exec($curl);
		//关闭URL请求
		curl_close($curl);
		//显示获得的数据
		return $data;
	}
}