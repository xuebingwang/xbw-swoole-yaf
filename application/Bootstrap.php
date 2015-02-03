<?php
/**
 * xbw-swoole-yaf
 *
 * Bootstrap.php
 *
 * 应用引导文件，初始化插件及设置性能统计
 *
 * @author 王雪兵<406964108@qq.com>
 */

use Yaf\Application;
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;
use Yaf\Loader;
use Init\XHProfPlugin;

/**
 * Yaf引导类 Class Bootstrap
 *
 * 应用所有需要尽早初始化的操作都需要在这里面定义并自动由yaf框架调用执行
 *
 */
final class Bootstrap extends Bootstrap_Abstract
{
    private $_config;

    public function _initConfig()
    {
        //把配置保存起来
        $this->_config = Application::app()->getConfig();
        Registry::set('config', $this->_config);
    }

    public function _initHelper() {
        Loader::import(APP_PATH.'helper'.DS.'functions.php');
    }

    /**
     * 读取相应的配置初始化XHProf
     *
     * @access public
     * @param \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initXHProf(Dispatcher $dispatcher)
    {
        if (isset($this->_config->application->xhprof)) {
            $xhprof_config = $this->_config->application->xhprof->toArray();
            if (extension_loaded('xhprof') && $xhprof_config && isset($xhprof_config['open']) && $xhprof_config['open']) {
                $default_flags = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY;

                $ignore_functions = isset($xhprof_config['ignored_functions']) && is_array($xhprof_config['ignored_functions'])
                    ? $xhprof_config['ignored_functions']
                    : array();
                if (isset($xhprof_config['flags'])) {
                    xhprof_enable($xhprof_config['flags'], $ignore_functions);
                } else {
                    xhprof_enable($default_flags, $ignore_functions);
                }
            }
        }
    }

    /**
     * 注册插件
     *
     * @access public
     * @param Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initPlugin(Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new XHProfPlugin());
    }
}