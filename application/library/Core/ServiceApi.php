<?php
/**
 * xbw-swoole-yaf
 *
 * @author xuebing<406964108@qq.com>
 */

namespace Core;

/**
 * Class ServiceApi
 *
 * 导出API的controller基类
 *
 * @package Core
 */
class ServiceApi extends Service
{
    
    /**
     * (non-PHPdoc)
     * @see \Core\Service::_initRequestData()
     */
    protected function _initRequestData(){

        if(class_exists('\HttpServer',false)){
            
            $this->request_data = json_to_array(\HttpServer::$raw_data);
        }else{
            
            $this->request_data = json_to_array(file_get_contents('php://input'));
        }
        
    }
}