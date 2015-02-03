<?php
/**
 * 通过swoole启动接口服务
 * @author xuebingwang <406964108@qq.com>
 */

define ('DS', DIRECTORY_SEPARATOR);
define ('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define ('APP_PATH', ROOT_PATH.DS.'application'.DS);
define ('CONF_PATH', ROOT_PATH . DS . 'conf' . DS);

class HttpServer
{
    /**
     * 实例
     * @var HttpServer
     */
	public static $instance;

	/**
	 * swoole的http_server
	 * @var swoole_http_server
	 */
	public static $http;
	
	/**
	 * 客户端提交过来的报文数据
	 * @var string
	 */
	public static $raw_data;
	
	/**
	 * 客户端post提交的数据
	 * @var array
	 */
	public static $post;
	
	/**
	 * Yaf Application
	 * @var  Yaf\Application 
	 */
	private $application;

	/**
	 * 构造方法，要执行的东西很多
	 * @author xuebing<406964108@qq.com>
	 */
	public function __construct() {

	    $config = new Yaf\Config\Ini(CONF_PATH.'swoole.ini');
	    $config = $config->get('swoole');
	    
        self::$http = new swoole_http_server("0.0.0.0", $config->port);
        
		self::$http->set(
			array(
				'worker_num'            => $config->worker_num,         //worker进程数 
                'max_conn'              => $config->max_conn,           //最大允许的连接数， 此参数用来设置Server最大允许维持多少个tcp连接。超过此数量后，新进入的连接将被拒绝。
                'max_request'           => $config->max_request,        //此参数表示worker进程在处理完n次请求后结束运行。manager会重新创建一个worker进程。此选项用来防止worker进程内存溢出。
                'ipc_mode'              => $config->ipc_mode,           // 1，默认项，使用Unix Socket作为进程间通信,2，使用系统消息队列作为进程通信方式
                'task_worker_num'       => $config->task_worker_num,    //task_worker进程数 
                'task_ipc_mode'         => $config->task_ipc_mode,      //1, 使用unix socket通信，2, 使用消息队列通信，3, 使用消息队列通信，并设置为争抢模式
                'task_max_request'      => $config->task_max_request,   //设置task进程的最大任务数
                'dispatch_mode'         => $config->dispatch_mode,      //1平均分配，2按FD取摸固定分配，3抢占式分配，默认为取摸(dispatch=2)
                'daemonize'             => $config->daemonize,          //守护进程化
                'backlog'               => $config->backlog,            //最多同时有多少个等待accept的连接
                'open_tcp_keepalive'    => $config->open_tcp_keepalive, //启用tcp keepalive
                'tcp_defer_accept'      => $config->tcp_defer_accept,   //当一个TCP连接有数据发送时才触发accept
                'open_tcp_nodelay'      => $config->open_tcp_nodelay,   //开启后TCP连接发送数据时会无关闭Nagle合并算法，立即发往客户端连接。在某些场景下，如http服务器，可以提升响应速度。 
                'log_file'              => APP_PATH . '/log/swoole.log' //日志文件路径
                //'task_tmpdir'         => APP_PATH . '/data/task',
                //'heartbeat_check_interval' => 5, //每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭掉
                //'heartbeat_idle_time' => 5, //TCP连接的最大闲置时间，单位s , 如果某fd最后一次发包距离现在的时间超过heartbeat_idle_time会把这个连接关闭。
			)
		);

		self::$http->on('request', function($request, $response) {
    	    
    		if( isset($request->post) ) {
    		    self::$post = $request->post;
    		}
    		
    		self::$raw_data = $request->rawContent();
    		
    		echo "[".date('Y-m-d H:i:s')."]"
		          .PHP_EOL
    		      ."请求地址==>".$request->server['request_uri']
    		      .PHP_EOL.
    		      "请求内容==>".self::$raw_data.PHP_EOL;

    		if(self::$raw_data == 'swoole_reload()'){
        		self::$http->reload();
        		$response->end('reload success');
        		return;
    		}
    		
    		try {
        		// TODO handle
    		    $yaf_response = $this->application->getDispatcher()->dispatch(new Yaf\Request\Http($request->server['request_uri']));
    		    $result = $yaf_response->getBody();
    		} catch ( \Exception $e ) {
    		    $result               = array();
    		    $result['err_code']     = $e->getCode();
    		    $result['err_msg']    = $e->getMessage();
    		    $result               = json_encode($result,JSON_UNESCAPED_UNICODE);
    		}

    		echo "返回内容==>".$result.PHP_EOL.PHP_EOL;
    		
    	  	$response->end($result);
    	});

        self::$http->on('Finish', function($serv, $task_id, $data) {
            
            echo "异步任务完成[{$task_id}],data:".$data.PHP_EOL;
        });
    
        self::$http->on('Task', function($serv, $task_id, $from_id, $data) {
            
            echo "新的异步任务[来自进程 {$from_id}，当前进程 {$task_id}],data:".$data.PHP_EOL;
            
            $data = unserialize($data);
            if(is_array($data)){
                list($module,$controller,$action,$params) = $data;
                $request = new Yaf\Request\Simple('CLI', $module, $controller, $action, $params);
                $this->application->getDispatcher()->dispatch($request);
                $serv->finish("task -> OK");
            }
        });
        
        self::$http->on('Timer', function($serv, $interval) {
            switch ($interval) {
            	case 300000:
            	    break;
            }
        });
        
        self::$http->on('Start', function($serv) {
            cli_set_process_title("swoolehttp:main");
            file_put_contents(APP_PATH . '/log/swoole-master.pid', $serv->master_pid);
        });

        self::$http->on('WorkerStart' , array($this,'onWorkerStart'));
            
        self::$http->on('ManagerStart', function($serv) {
            cli_set_process_title("swoolehttp:manager");
            file_put_contents(APP_PATH . '/log/swoole-manager.pid', $serv->master_pid);
        });
        
        self::$http->on('Shutdown', function($serv) {
               echo 'Shutdown'.PHP_EOL;
        });
        
        self::$http->on('WorkerError', function($serv, $worker_id, $worker_pid, $exit_code) {
        });
        
		self::$http->start();
	}

	public function onWorkerStart($serv, $worker_id) {

	    $this->application = new Yaf\Application (CONF_PATH. 'app.ini', 'product');
	    $this->application->bootstrap();
        
	    if ($worker_id >= $serv->setting['worker_num']) {
	        cli_set_process_title("swoolehttp:task_worker");
	    } else {
	        cli_set_process_title("swoolehttp:worker");
	    }
	    //$serv->addtimer(300000);
	}

	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new HttpServer;
        }
        return self::$instance;
	}
}

HttpServer::getInstance();