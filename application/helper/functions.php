<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: xuebing <406964108@qq.com>
// +----------------------------------------------------------------------

/**
 * 系统函数库
 */

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author xuebing <406964108@qq.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i:s'){
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}

/**
 * JSON转数组，始终返回一个数组
 * @param $json
 * @return array
 */
function json_to_array($json){
    if (!is_string($json)) {
        return array();
    }
    $value = json_decode($json,TRUE);
    return $value ? $value : array();
}