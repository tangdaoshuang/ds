<?php
return array(
	//'配置项'=>'配置值'
    'SESSION_AUTO_START' =>false,
    'SESSION_OPTIONS'=>array(
        'use_trans_sid'=>1,
        'expire'=>7100,//设置过期时间session.gc_maxlifetime的值为2小时
    ),
);