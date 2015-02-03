<?php
use Yaf\Dispatcher;
class IndexController extends Yaf\Controller_Abstract
{

    public function init(){

        Dispatcher::getInstance()->returnResponse(true);
        Dispatcher::getInstance()->disableView();
    }
    
    public function indexAction(){
        $this->getResponse()->setBody('hello world'.PHP_EOL);
    }
    
    /**
     * 异步action
     */
    public function testAsyncAction(){
        
        $username = $this->getRequest()->getParam('username');
        echo '接收到异步传来的参数：';
        var_dump($username);
    }
}