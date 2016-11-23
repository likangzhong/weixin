<?php 
namespace Home\Model;
use Think\Model;

class IndexModel {
    
    //回复多图文
    public function responseNews($postObj,$arr){
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $template ="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>".count($arr)."</ArticleCount>
                        <Articles>";
            foreach($arr as $k => $v){
                $template .="
                        <item>
                        <Title><![CDATA[".$v['title']."]]></Title> 
                        <Description><![CDATA[".$v['description']."]]></Description>
                        <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
                        <Url><![CDATA[".$v['url']."]]></Url>
                        </item>";
            }
            $template .="
                        </Articles>
                        </xml>";
            $info    = sprintf($template, $toUser, $fromUser, $time, 'news');
            echo $info;
    }
    
    public function responseText($postObj,$content){
        $toUser  = $postObj->FromUserName;
        $fromUser= $postObj->ToUserName;
        $time    = time();
        $msgtype = 'text';
        $template=" <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
                $info   = sprintf($template, $toUser, $fromUser, $time, $msgtype, $content);
                echo $info;
            
    }
    
    public function responseSubscribe($postObj,$content){
        $indexModel = new IndexModel();
        $indexModel -> responseText($postObj,$content);
    }
}