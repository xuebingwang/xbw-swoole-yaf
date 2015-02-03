<?php
   /**
   * @name ErrorController
   * @desc 错误控制器, 在发生未捕获的异常时刻被调用
   * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
   * @author xuebing<406964108@qq.com>
   */
   class ErrorController extends Yaf\Controller_Abstract {
      //从2.1开始, errorAction支持直接通过参数获取异常
      public function errorAction($exception) {
         //1. assign to view engine
         //$exception = $this->getRequest()->getParam('exception');

         $this->getView()->assign("exception", $exception);

         /*Yaf has a few different types of errors*/
         switch($exception->getCode()):
         case 515:
         case 516:
         case 517:
         case 518:
         return $this->_pageNotFound();
         default:
         return $this->_unknownError();
         endswitch;

         //5. render by Yaf 
      }

      private function _pageNotFound(){
         $response = $this -> getResponse();
//          $response -> setHeader( 'Content-Type', 'application/json; charset=utf-8' );
         $response -> setHeader( $this -> getRequest() -> getServer( 'SERVER_PROTOCOL' ), '404 Not Found' );
         $response -> response();
         $this->_view->error = 'Page was not found';
      }

      private function _unknownError(){
//          $this->getResponse()->setHeader('HTTP/1.0 500 Internal Server Error');
         $response = $this -> getResponse();
         $response -> setHeader( $this -> getRequest() -> getServer( 'SERVER_PROTOCOL' ), '500 Internal Server Error' );
         $response -> response();
         $this->_view->error = 'Application Error';
      }

   }
