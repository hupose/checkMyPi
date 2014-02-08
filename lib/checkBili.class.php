<?php

require_once(ROOTPATH . 'lib/curlTools.class.php');
require_once(ROOTPATH . 'lib/simple_html_dom.php');
require_once(ROOTPATH . 'PHPMailer/PHPMailerAutoload.php');
require_once(ROOTPATH . 'log4php/Logger.php');

Logger::configure(ROOTPATH.'log4php/config.xml');


class checkBili {

    public $shengrou = '%e7%94%9f%e8%82%89';
    private $_log;


    public function checkNew(){
        $this->_log = Logger::getLogger('checkMyPi');
        $this->_log->info("开始检查bilibili");
        $updateTime = array();
        $tasks = simplexml_load_file(ROOTPATH . 'conf/items.xml');
        $arr_tasks = (array)$tasks;//simplexmlobj转array，为了得到整个数组大小。不转换的话用foreach(simplexmlobj)会无限循环
        for($num = 0;$num < count($arr_tasks['item']);$num++){
            $url = "http://bilibili.kankanews.com/search?orderby=pubdate&keyword=" . urlencode($tasks->item[$num]->key);
            $gzhtml = curlTools::simpleCurl($url);
            $html = gzdecode($gzhtml);//curl过来是gzip过的，需要解压，否则是乱码的
            $html = str_get_html($html);
            $finds = $html->find('div.w_info i.date');
            $arr_time = array();
            foreach($finds as $find){
                $time = trim($find->innertext);
                if($time !== ""){
                    $arr_time[] = $time;
                }
            }
            $finds = $html->find('div.r');
            $arr_title = array();
            foreach($finds as $find){
                $title = trim($find->innertext);
                //echo $title;
                if(strpos($title,'<a href="/sp') === false){
                    $arr_title[] = $title;
                }
            }
            if(count($arr_time) != count($arr_title)){
                $this->sendMail("时间和标题数量不符合");
                exit;
            }
            foreach($arr_title as $i=>$content){
                $content = urlencode($content);
                if(strpos($content,$this->shengrou) === false && strtotime(trim($arr_time[$i])) > strtotime($tasks->item[$num]->lastUpdateTime)){
                    $this->sendMail($tasks->item[$num]->key . "更新啦");
                    $this->_log->info("bilibili更新啦");
                    $tasks->item[$num]->lastUpdateTime = trim($arr_time[$i]);
                    break;
                }
            }
        }
        curlTools::curlClose();
        $tasks->saveXML(ROOTPATH . 'conf/items.xml');
        $this->_log->info("检查bilibili完毕");
    }

    public function sendMail($content){
        $mailConf = simplexml_load_file(ROOTPATH . 'conf/mailConf.xml');
        $isSMTP = $mailConf->mailType;
        $host = $mailConf->Host;
        $SMTPAuth = $mailConf->SMTPAuth;
        $Username = $mailConf->Username;
        $Password = $mailConf->Password;
        $SMTPSecure = $mailConf->SMTPSecure;
        $From = $mailConf->From;
        $To = $mailConf->To;
        $FromName = $mailConf->FromName;
        $isHTML = $mailConf->isHTML;


        $mail = new PHPMailer;
        if($isSMTP == "SMTP"){
            $mail->isSMTP();
        }
        $mail->Host = $host;
        $mail->SMTPAuth = $SMTPAuth;
        $mail->Username = $Username;
        $mail->Password = $Password;
        $mail->SMTPSecure = $SMTPSecure;

        $mail->From = $From;
        $mail->FromName = $FromName;
        $mail->addAddress($To);

        $mail->WordWrap = 50;
        $mail->isHTML($isHTML);

        $mail->Subject = $content;
        $mail->Body    = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><p>'
            . $content . '</p></body></html>';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }
    }


}