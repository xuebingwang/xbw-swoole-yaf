<?php
/**
 * Created by PhpStorm.
 * User: xuebin<406964108@qq.com>
 * Date: 2014/12/27
 * Time: 14:27
 * @copyright Copyright (c) 2014
 */

use Core\ServiceApi;

class PublicController extends ServiceApi
{
    
    /**
     * 登录接口
     * @author xuebing
     */
    public function loginAction(){
        
        if(empty($this->request_data['username'])){
            
            $this->sendError('用户名不能为空！');
        }
        if(empty($this->request_data['password'])){

            $this->sendError('密码不能为空！');
        }
        
        
        if($this->request_data['username'] == 'testxbw' AND $this->request_data['password'] == '123456'){
            $task_data = array(
                    'index',
                    'index',
                    'testAsync',
                    array('username'=>$this->request_data['username'])
            );
        
            //发送异步任务
            HttpServer::$http->task(serialize($task_data));
            
            $this->sendSuccess('登录成功！');
        }else{
            $this->sendError('用户名密码不正确！');
        }
        
    }

}