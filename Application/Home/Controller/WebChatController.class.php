<?php
/**
 * Created by IntelliJ IDEA.
 * User: Admin
 * Date: 2019/4/19
 * Time: 10:11
 */

namespace Home\Controller;
use Think\Controller;

class WebChatController extends Controller
{

    public function __construct(){
            parent::__construct();
    }
    public function index(){
        $this->display();
    }
    public function exBat(){
        $cmd = "server.bat";
        $res = exec($cmd);
    }
}