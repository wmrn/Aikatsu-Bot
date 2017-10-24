<?php
//7:23～7:25の間に「あかりちゃ～ん！○○のお天気どうですか？」って言われたらその人に大空お天気を提供
//地道にcronでやろうかな。。。
//時間外の対応：https://twitter.com/_aikatsup/status/689820513766535172/photo/1
require_once('twitteroauth/autoload.php');
require_once('twitteroauth/src/TwitterOAuth.php');
require_once('igo-php/lib/Igo.php');//形態素解析のライブラリ読み込み
date_default_timezone_set('Asia/Tokyo');//時間地域設定

define('consumer_key', base64_decode('**************'));
define('consumer_secret', base64_decode('*****************'));
define('access_token', base64_decode('******************'));
define('access_token_secret', base64_decode('******************'));
use Abraham\TwitterOAuth\TwitterOAuth;

$twObj = new TwitterOAuth(
    consumer_key,
    consumer_secret,
    access_token,
    access_token_secret
  );

//リプの取得
//上と下の違いはたぶん鍵垢が見れるか見れないかくらいかな？
//下のでもフォロー外？の取得できそう？
//$res = $twObj->get("search/tweets", array("q" => "@art_wmrn"));
$res = $twObj->get("statuses/mentions_timeline");

//リプ飛ばし
session_start();
for ($i=0; $i < count($res); $i++) {
    $rep=$res[$i]->text;
    if(strpos($rep,"あかり")){//②リプの形にあっているか
        if(strpos($rep,"天気")){
            /*リプの基本形
            『あかりちゃ～ん！『○○』のお天気どうですか～？』
            『あかり・天気』の2つとも入ってたらok!
            */            
                        
            if($res[$i]->id_str>$_SESSION['put']){//まだリプ返してないかどうか
                //print_r($res[$i]);
                list($mess,$icon)=call_user_func("create_mess",$rep);
                $mov = $twObj->upload('media/upload', ['media' => 'mp4/oozr_otnk_'.$icon.'.mp4', 'media_type' => 'video/mp4'], true);
                //print_r($media1);
                $parameters = [
                    'status' => "@".$res[$i]->user->screen_name.$mess,
                    'media_ids' => $mov->media_id_string,
                    'in_reply_to_status_id' => $res[$i]->id_str
                ];
                $rp = $twObj->post("statuses/update", $parameters);    
            }
        }
    }
}
$_SESSION['put']=$res[0]->id_str;
//print_r($res);

//リプの内容取得して文字列の応じたAPI叩いて情報取得してリプ返す
function create_mess($txt){
    $igo = new Igo('ipadic', 'UTF-8');    
    $result = $igo->parse($txt);//形態素解析
    for ($i=0; $i < count($result); $i++) {
        if(strpos($result[$i]->feature,"固有名詞")){//ex)横浜　固有名詞を見つけたらWeblioに英訳してもらう
            $url="http://translate.weblio.jp/?lp=JE&lpf=JE&originalText=".$result[$i]->surface;
            $page = file_get_contents($url);
            $start = strpos($page,'translatedTextAreaLn');
            $end = strpos($page,'translatedTextWrLn0');
            $place = substr($page, $start, $end-$start);
            $start = strpos($place,'">')+2;
            $end = strpos($place,'</');
            $place = substr($place, $start, $end-$start);//ex)Yokohama

            $key=base64_decode("***************");//天気APIのkey
            $url="http://api.openweathermap.org/data/2.5/weather?q=".$place.",jp&appid=".$key;//OpenWeatherMapのAPIに地名を投げる
            $weather_page = json_decode(file_get_contents($url), true);
             //もし候補が何個かあっても一番上にあるものが選ばれる
        if($weather_page['name']==$place){//候補がちゃんと見つかったら
            //$weather=$weather_page['weather'][0]['main'];
            $icon=$weather_page['weather'][0]['icon'];
            $temp_max=$weather_page['main']['temp_max']-273.15;
            $temp_min=$weather_page['main']['temp_min']-273.15;
            $time= time() ;
            $time=date("G時i分",$time);
            if(substr($icon,0,2)=="01"){
                $weather="☀";//快晴
            }else if(substr($icon,0,2)=="02"){
                $weather="☀";//晴れ
            }else if(substr($icon,0,2)=="03"){
                $weather="☁";//くもり
            }else if(substr($icon,0,2)=="04"){
                $weather="☁";//くもり
            }else if(substr($icon,0,2)=="09"){
                $weather="☂";//小雨
            }else if(substr($icon,0,2)=="10"){
                $weather="☂";//雨
            }else if(substr($icon,0,2)=="11"){
                $weather="⚡";//雷雨
            }else if(substr($icon,0,2)=="13"){
                $weather="☃";//雪
            }else if(substr($icon,0,2)=="50"){
                $weather="🌁";//霧
            }

            /*はーい！時刻は「$time」！
            今日のお空はどんな空？大空お天気の時間です！
            ここ「$place」の最高気温は「$temp_max」、最低気温は「$temp_min」です！
            大空ラッコ君、今日のお天気は....「$weather」
            それでは通勤通学気を付けて！！！いってらっしゃーい！！*/

            $mess="はーい！時刻は".$time."！\r\n今日のお空はどんな空？大空お天気の時間です！\r\nここ「".$result[$i]->surface."」の最高気温は".$temp_max."℃、最低気温は".$temp_min."℃です！\r\n大空ラッコ君、今日のお天気は....".$weather."\r\nそれでは通勤通学気を付けて！！\r\nいってらっしゃーい！！";

            $i=count($result);//for文抜け出し
        }else{//APIになかった場合
            //「ごめんなさい」
            $mess="https://twitter.com/_aikatsup/status/582550620306939906/photo/1";
        }
        }
    }
    if(empty($mess)){//分析して固有名詞がなかった場合
        //「何....これ。」
        $mess="https://twitter.com/_aikatsup/status/622449628647063556/photo/1";
    }
    return array($mess,$icon);
}

?>