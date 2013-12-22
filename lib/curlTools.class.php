<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yuanchong
 * Date: 13-5-30
 * Time: 上午10:58
 * To change this template use File | Settings | File Templates.
 */

class curlTools {

    static public $_ch;  //$$_ch = curl_init( $url )不是一个url,是一个初始化的curl

    static public function curlClose() {
        if( self::$_ch !== null ) {
            curl_close( self::$_ch );
            self::$_ch = null;
        }
    }

    /**
     * @param $url  $url参数可以是string也可以是array
     * @return mixed  返回的参数按照输入参数不同而不同，输入string输出也是string，输入array输出也是array
     * @author yuanchong
     */
    static public function simpleCurl($url){
        self::$_ch = curl_init();
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, 1);  //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt(self::$_ch, CURLOPT_HEADER, false);
        curl_setopt(self::$_ch, CURLOPT_NOBODY, false);
        curl_setopt(self::$_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.12 Safari/535.2');
        curl_setopt(self::$_ch, CURLOPT_HTTPHEADER, array("Accept:text/html","Accept-Encoding:gzip","Accept-Language:zh-CN"));

        if(!is_array($url)){
            curl_setopt(self::$_ch,CURLOPT_URL,$url);
            $html = curl_exec(self::$_ch);
            curl_close(self::$_ch);
            return $html;
        }else{
            $i = 0;
            foreach($url as $value){
                curl_setopt(self::$_ch,CURLOPT_URL,$value);
                $html[$i] = curl_exec(self::$_ch);
                $i++;
            }
            //curl_close(self::$_ch);
            return $html;
        }

    }


}