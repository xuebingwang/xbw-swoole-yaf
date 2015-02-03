<?php
/**
 * xbw-swoole-yaf
 *
 * @author xuebing<46964108@qq.com>
 */

namespace Core;

use Yaf\Registry;
use Yaf\Dispatcher;
use Yaf\Controller_Abstract;

/**
 * Class ServiceApi
 *
 * 导出API的controller基类
 *
 * @package Core
 */
abstract class Service extends Controller_Abstract
{
    /**
     * 接口请求数据
     * @var Array
     */
    protected $request_data;
    
    /**
     * yaf配置参数
     * @var Object
     */
    protected $_config;
    
    /**
     * 业务成功
     * @var int
     */
    const ERR_CODE_SUCCESS    = 0;
    /**
     * 业务失败
     * @var int
     */
    const ERR_CODE_FAILD      = 1;
    
    /**
     * ServiceApi初始化
     */
    public function init(){

        Dispatcher::getInstance()->returnResponse(true);
        Dispatcher::getInstance()->disableView();
        
        $this->_config = Registry::get('config');
        $this->_initRequestData();
    }
    
    /**
     * 子类实现
     */
    protected function _initRequestData(){}


    /**
     * 返回当前模块名
     *
     * @access protected
     * @return string
     */
    protected function getModule()
    {
        return $this->getRequest()->module;
    }
    
    /**
     * 返回当前控制器名
     *
     * @access protected
     * @return string
     */
    protected function getController()
    {
        return $this->getRequest()->controller;
    }
    
    /**
     * 返回当前动作名
     *
     * @access protected
     * @return string
     */
    protected function getAction()
    {
        return $this->getRequest()->action;
    }
    
    /**
     * 标准响应输出
     * @param array $out
     */
    protected function sendOutPut(Array $out=array(),$err_code=self::ERR_CODE_SUCCESS){
        
        $out['err_code'] = $err_code;
        
        $this->getResponse()->setBody(json_encode($out,JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 成功的响应输出
     * @param String $content
     */
    protected function sendSuccess($msg){
        
        $this->sendOutPut(array('err_msg'=>$msg));
    }
    
    /**
     * 失败的响应输出
     * @param String $content
     * @throws \Exception
     */
    protected function sendError($content,$code=self::ERR_CODE_FAILD){
        
        throw new \Exception($content, $code);
    }
    
}