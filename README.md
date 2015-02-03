# xbw-swoole-yaf
参照https://github.com/LinkedDestiny/swoole-yaf
结合swoole+yaf+json写的接口服务

##**描述**
底层使用Swoole内置的swoole_http_server提供服务
上层应用使用Yaf框架搭建


##**作者**
xuebing

##**使用说明**
打开终端

cd xbw-swoole-yaf

php server/server.php

接口调用地址：http://yourip:port/app/public/login

接口传送数据json格式：{"username":"testxbw","password":"123456"}

`根目录附带的apitest.php是一个接口调式工具，随便放至apache或nginx的访问目录，直接访问即可`

##**swoole版本**
swoole-1.7.8+版本

##**yaf版本**
任意stable版本
