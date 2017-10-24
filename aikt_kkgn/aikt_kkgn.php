<?php
//twitter
//twitteroauth.phpをインクルードします。ファイルへのパスはご自分で決めて下さい。
require_once("twitteroauth.php");
require('create_mess.php');

//TwitterAPI開発者ページでご確認下さい。
//Consumer keyの値を格納
$sConsumerKey = base64_decode("***************");
//Consumer secretの値を格納
$sConsumerSecret = base64_decode("*****************");
//Access Tokenの値を格納
$sAccessToken = base64_decode("******************");
//Access Token Secretの値を格納
$sAccessTokenSecret = base64_decode("******************");

//OAuthオブジェクトを生成する
$twObj = new TwitterOAuth($sConsumerKey,$sConsumerSecret,$sAccessToken,$sAccessTokenSecret);

//$words,$naver_url
$str=$mess_twitter;

//呟きをPOSTするAPI
$vRequest = $twObj->OAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status" => $str));

//Jsonデータをオブジェクトに変更
$oObj = json_decode($vRequest);

//Slack
function send_to_slack($message) {
  $webhook_url = base64_decode("******************");
    $options = array(
    'http' => array(
      'method' => 'POST',
      'header' => 'Content-Type: application/json',
      'content' => json_encode($message),
    )
  );
  $response = file_get_contents($webhook_url, false, stream_context_create($options));
  return $response === 'ok';
}

$message = array(
  'username' => 'アイカツ格言bot',
  'text' => $mess_slack,
  'icon_emoji' => ':kkgn:'
);

send_to_slack($message);
?>