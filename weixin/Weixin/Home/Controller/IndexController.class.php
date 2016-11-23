<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {


    public function index(){
        //1.将timestamp,nonce,token按字典序排序
        $timestamp = $_GET['timestamp'];
        $nonce     = $_GET['nonce'];
        $token     = 'weixin';
        $signature = $_GET['signature'];
        $arr       = array($timestamp,$nonce,$token);
        sort($arr);

//2.将排序后的三个参数拼接之后用sha1加密

        $str = implode('',$arr);     //join
        $str = sha1($str);

//3.将加密后的字符串与signature进行对比，判断请求是否来自微信
        if(($str == $signature) && $_GET['echostr'] ){
            //第一次接入微信api的时候
            echo  $_GET['echostr'];
            exit;
        }else{
            $this->responseMsg();
        }
    }


    //接受事件推送并回复
    public function responseMsg(){
        //1.获取微信推送过来的post数据（xml类型）
        $postArr = file_get_contents('php://input');
        $postObj=simplexml_load_string($postArr);
        //判断该数据包是否是订阅的事件推送
        if(strtolower($postObj->MsgType) == 'event'){
            //如果是subscribe关注事件
            if(strtolower($postObj->Event == 'subscribe')){
                $content = '欢迎来到老火柴的世界，让我们一起愉快地卖小女孩吧！黄冠栩你个大傻逼！';
                $indexModel = new \Home\Model\IndexModel;
                $indexModel ->responseSubscribe($postObj,$content);
            }

            if(strtolower($postObj->Event) == 'click'){
                if($postObj->EventKey == 'basketball'){
                    $arr = $this->tiyu();
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);


                }

                if($postObj->EventKey == 'Lakers'){
                    $arr = $this->nbaNews('湖人');
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);
                }

                if($postObj->EventKey == 'Warriors'){
                    $arr = $this->nbaNews('勇士');
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);
                }

                if($postObj->EventKey == 'Cavaliers'){
                    $arr = $this->nbaNews('骑士');
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);
                }

                if($postObj->EventKey == 'Thunder'){
                    $arr = $this->nbaNews('雷霆');
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);
                }

                if($postObj->EventKey == 'Spurs'){
                    $arr = $this->nbaNews('马刺');
                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel ->responseNews($postObj,$arr);
                }

                if($postObj->EventKey == 'weather'){
                    $arr=$this->weather();
                    $retData = $arr['today'];
                    $forecast= $arr['forecast'];
                    unset($forecast[0]);
                    $content ="今天天气：".$retData['type']."\n最低气温：".$retData['lowtemp']."\n最高气温：".$retData['hightemp']."\n"."\n未来三天天气:\n";
                    
                    foreach($forecast as $k => $v){
                        $content .= "\n".$v['date']."天气：".$v['type']."\n最低气温：".$v['lowtemp']."\n最高气温：".$v['hightemp']."\n";    
                        
                    };
                    
                    $content .="\n发布时间：".$retData['date']."  ".$retData['time'];

                    $indexModel = new \Home\Model\IndexModel;
                    $indexModel->responseText($postObj,$content);
                }
            }
            if(strtolower($postObj->Event) == 'scan'){

                if( $postObj ->EventKey == 2000 ){
                    //如果是临时验证码
                    $tmp = '临时验证码欢迎你';
                }
                if( $postObj ->EventKey == 3000 ){
                    //如果这是永久验证码
                    $tmp = '永久验证码欢迎你';
                }
                $arr = array(
                    array(
                        'title'         => $tmp,
                        'description'   => '科比大战麦迪，倚天一出，谁与争锋。屠龙宝刀，号令天下.....',
                        'picUrl'        => 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png',
                        'url'           => 'https://www.baidu.com',
                    ),
                );

                $indexModel = new \Home\Model\IndexModel;
                $indexModel ->responseNews($postObj,$arr);

            }

        }

        //接受消息并回复纯文本
        if(strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == 'nba'){
            $arr      = array(
                array(
                    'title'         => '百度',
                    'description'   => '科比大战麦迪，倚天一出，谁与争锋。屠龙宝刀，号令天下.....',
                    'picUrl'        => 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png',
                    'url'           => 'https://www.baidu.com',
                ),

                array(
                    'title'         => '百度',
                    'description'   => '科比大战麦迪，倚天一出，谁与争锋。屠龙宝刀，号令天下.....',
                    'picUrl'        => 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png',
                    'url'           => 'https://www.baidu.com',
                ),

                array(
                    'title'         => '百度',
                    'description'   => '科比大战麦迪，倚天一出，谁与争锋。屠龙宝刀，号令天下.....',
                    'picUrl'        => 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png',
                    'url'           => 'https://www.baidu.com',
                ),
            );

            $indexModel = new \Home\Model\IndexModel;
            $indexModel ->responseNews($postObj,$arr);

        }else if(strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == '北京'){
            $ch = curl_init();
            $url = 'http://apis.baidu.com/apistore/weatherservice/cityname?cityname=%E5%8C%97%E4%BA%AC';
            $header = array(
                'apikey: 829e7bb9454ab783aa05c5c6a8dd885f',
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch , CURLOPT_URL , $url);
            $res = curl_exec($ch);

            $ar = json_decode($res,true);
            var_dump($ar);
            $content = "今日天气：".$ar['retData']['weather'];
            $indexModel = new \Home\Model\IndexModel;
            $indexModel->responseText($postObj,$content);


        }else{
            switch( trim($postObj->Content) ){
                case 'hello':
                    $content = 'hello';
                    break;

                case '傻嗨':
                    $content = '傻嗨是一个骂人的词语，不要乱讲哦！';
                    break;

                case '黄冠栩':
                    $content = '黄冠栩是个大傻逼，生于广东省湛江市坡头区官渡镇潭村';
                    break;

                case '鸡巴锐':
                    $content = '鸡巴锐势14游软二班的渣渣班长！';
                    break;

                case '李康钟':
                    $content = '李康钟是你爹';
                    break;


                default:
                    $content = '暂时还没有这个服务哦';
                    break;
            }

            $indexModel = new \Home\Model\IndexModel;
            $indexModel->responseText($postObj,$content);

        }



    }


    /*
    * $url 接口url string
    * $type 请求类型 string
    * $res 返回数据类型 string
    * $arr post请求参数 string
    */
    public function http_curl($url,$type='get',$res='json',$arr='',$header=''){
        //初始化
        $ch = curl_init();
        //设置URL参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //采集
        if($type == 'post'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        if($header){
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        }
        $output=curl_exec($ch);
        //关闭
        curl_close($ch);
        if($res=='json'){
            if( curl_errno($ch)){
                return curl_error($ch);
            }else{
                return json_decode($output, true);
            }
        }
    }



    //返回access_token
    public function getWxAccessToken(){
        //将access_token存在session中
//        if($_SESSION['access_token'] && $_SESSION['expire_time']>time()){
        //          return $_SESSION['access_token'];
        //    }else{
        //access_token不存在或者已经过期，从新取
        if(S('access_token')){
            return S('access_token');
        }else{
            $appid      = 'wxdea8d7ef5a72aa3a';
            $appsecrect = '8beb3a37659fd8659156b905e9f8bec4';
            $url        = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecrect;
            $res        = $this->http_curl($url, 'get', 'json');
            $access_token=$res['access_token'];
            $expires_in =$res['expires_in'];
            S('access_token',$access_token, $expires_in);
            return S('access_token');
        }
    }

//    public function getWxServerIp(){
//        $accessToken = '_xD62094wZQR7blc8DJ7thHXBPzNmf4PIiVLvW3TOT5u7BeG2ZzoCHA9OneeaJ7NE6oNeNzkisGdvFrsMKI7bOAAZWKL7CCPOX96JaeZ4DvaT_D4VdIv8MKFiLt2UwYdAXIdAEASPA';
//        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accessToken;
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $output = curl_exec($ch);
//        curl_close($ch);
//        if(curl_error($ch)){
//            var_dump(curl_error($ch));
//        }
//        $arr = json_decode($output,true);
//        echo "<pre>";
//        var_dump($arr);
//        echo "</pre>";
//    }


    public function definedItem(){
        //创建微信菜单
        //目前微信接口的调用方式都是通过curl post/get
//        header('content-type:text/xml;charset=utf-8');
        echo $access_token = $this->getWxAccessToken();
        echo "<br >";
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $postArr = array(
            'button' =>array(
                array(
                    'name' =>urlencode('体育新闻'),
                    'type' =>'click',
                    'key'  =>'basketball',
                )  ,   //第一个一级菜单
                array(
                    'name' =>urlencode('NBA赛程'),
                    'sub_button' =>array(
                        array(
                            'name' =>urlencode('勇士'),
                            'type' =>'click',
                            'key'  =>'Warriors',
                        ),//第一个二级菜单
                        array(
                            'name' =>urlencode('湖人'),
                            'type' =>'click',
                            'key'  =>'Lakers',
                        ),//第二个二级菜单
                        array(
                            'name' =>urlencode('骑士'),
                            'type' =>'click',
                            'key'  =>'Cavaliers',
                        ),//第二个二级菜单
                        array(
                            'name' =>urlencode('雷霆'),
                            'type' =>'click',
                            'key'  =>'Thunder',
                        ),//第二个二级菜单
                        array(
                            'name' =>urlencode('马刺'),
                            'type' =>'click',
                            'key'  =>'Spurs',
                        ),//第二个二级菜单
                    ),
                ),     //第二个一级菜单
                array(
                    'name' =>urlencode('湛江天气'),
                    'type' =>'click',
                    'key'  =>'weather',
                )   //第三个一级菜单
            ),
        );
        $postJson =  urldecode(json_encode( $postArr ));
        $res = $this->http_curl($url, 'post', 'json', $postJson);
        echo "<hr >";
        var_dump($res);
    }

    public function nbaNews($team){
        $url = 'http://op.juhe.cn/onebox/basketball/team?key=8567b3f682ec176485ba50087755555b&team='.$team;
        $res = $this->http_curl($url, 'get', 'json');
        $arr = $res['result']['list'];
        foreach ($arr as $k => $v){
            $list[]      = array(
                'title'         => $v['player1']." ".$v['score']." ".$v['player2']."   ".$v['m_time'],
                'description'   => '',
                'picUrl'        => $v['player2logo'],
                'url'           => $v['link1url'],
            );
        }
        dump($list);
        return $list;
    }

    public function nbaPK($hteam,$vteam){
        $url = 'http://op.juhe.cn/onebox/basketball/combat?key=8567b3f682ec176485ba50087755555b&hteam='.$hteam.'&vteam='.$vteam;
        $res = $this->http_curl($url, 'get', 'json');
        $arr = $res['result']['list'];
        foreach ($arr as $k => $v){
            $list[]      = array(
                'title'         => $v['player1']." ".$v['score']." ".$v['player2']."   ".$v['m_time'],
                'description'   => '',
                'picUrl'        => $v['player2logo'],
                'url'           => $v['link1url'],
            );
        }
        dump($list);
        return $list;
    }

    
    //查询湛江天气
    public function weather(){
           $url = 'http://apis.baidu.com/apistore/weatherservice/recentweathers?cityname=湛江&cityid=101281001';
        $header = array(
            'apikey: 829e7bb9454ab783aa05c5c6a8dd885f',
        );
        $arr=$this->http_curl($url,'get','json','',$header);
        $retData = $arr['retData'];
        dump($retData);
        return $retData;
    }
    


    //体育新闻
    public function tiyu(){
        $url = 'http://apis.baidu.com/txapi/tiyu/tiyu?num=5&page=1';
        $header = array(
            'apikey: 829e7bb9454ab783aa05c5c6a8dd885f',
        );
        $res = $this->http_curl($url, 'get', 'json', '', $header);
        $newslist = $res['newslist'];
        foreach ($newslist as $k =>$v){
            unset($newslist[$k]['ctime']);
        }
        return $newslist;

    }

    //生成临时二维码
    public function getQrCode(){
        //获取票据ticket
        $access_token = $this->getWxAccessToken();
        echo $access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr = array(
            'expire_seconds' => 604800, //24*60*60*7
            'action_name' => "QR_SCENE",
            'action_info' => array(
                'scene' => array(scene_id => 2000),
            )
        );
        $postJson = json_encode($postArr);
        $res = $this->http_curl($url, 'post', 'json' ,$postJson);
        dump($res);
        $ticket = $res['ticket'];
        //使用ticket获取二维码图片,TICKET记得进行UrlEncode
        $qrurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urldecode($ticket);
        echo "临时二维码";
        echo "<br>";
        echo "<img src='".$qrurl."'>";
    }

    //生成永久二维码
    public function getforeverQrCode(){
        //获取票据ticket
        $access_token = $this->getWxAccessToken();
        echo $access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        //{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr = array(
            'action_name' => "QR_LIMIT_SCENE",
            'action_info' => array(
                'scene' => array(scene_id => 3000),
            )
        );
        $postJson = json_encode($postArr);
        $res = $this->http_curl($url, 'post', 'json' ,$postJson);
        $ticket = $res['ticket'];
        //使用ticket获取二维码图片,TICKET记得进行UrlEncode
        $qrurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urldecode($ticket);
        echo "永久二维码";
        echo "<br>";
        echo "<img src='".$qrurl."'>";
    }

    //发送模板消息
    public function sendMessage(){
        $access_token = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $postArr = array(
            'touser' => 'oqX_RwqsBaEMcBVjwE75KI-H81aE',
            'template_id' => 'H6QIRdZVO7YtXQY0UGLjNxHGh2Nk8uDLAvEMxr0Etxs',
            'url'   => 'www.baidu.com',
            'data'  =>array(
                'name' =>array('value' =>'科比布莱恩特','color' =>'#173177'),
                'point' =>array('value' =>'81分','color' =>'#173177'),
                'date' =>array('value' =>date('Y-m-d H:i:s'),'color' =>'#173177'),
            ),
        );

        dump($postArr);
        $postJson = json_encode($postArr);
        var_dump($postJson);
        $res = $this->http_curl($url, 'post', 'json', $postJson);
        dump($res);
    }

    //消息群发
    public function sendMsgAll(){
        $access_token = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;

        //纯文本群发
//        {
//           "touser":"OPENID",
//           "text":{
//              "content":"CONTENT"
//                  },
//          "msgtype":"text"
//}
        $postArr =array(
            "touser" =>"oqX_RwqsBaEMcBVjwE75KI-H81aE",
            "text"  =>array(
                "content" => urlencode("黄冠栩是个大傻逼！大家同意吗？"),

            ),
            "msgtype"=> "text",
        );
        $postJson = urldecode(json_encode($postArr));
        var_dump($postJson);

        $res = $this->http_curl($url, 'post', 'json', $postJson);
        dump($res);

    }


}



