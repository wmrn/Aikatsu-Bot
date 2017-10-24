<?php
require('naver_get.php');
require('wiki_txt.php');
require('youtube_link.php');

$num=rand(1,count($naver_url)-1);
$mess_twitter=$str="☆今週のアイカツ格言☆ \r\n 『".$words[$num]."』(第".$num."話より) \r\n ".$naver_url[$num];
$mess_slack="☆今週のアイカツ格言☆ \r\n 『".$words[$num]."』(第".$num."話より) \r\n ".$movie[$num]."\r\n".$naver_image[$num];

?>