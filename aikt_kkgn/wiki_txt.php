<?php
/*
$url = 'https://ja.wikipedia.org/w/api.php?format=xml&action=query&prop=revisions&titles=%E3%82%A2%E3%82%A4%E3%82%AB%E3%83%84!_(%E3%82%A2%E3%83%8B%E3%83%A1)&rvprop=content';
$txt = file_get_contents($url);
$txt_edit_before = strstr($txt,'各話リスト');
$txt_edit_after = strstr($txt_edit_before,'放送局',TRUE);
//echo $txt_edit_after;
$story = explode('|-',$txt_edit_after);
print_r(explode('|-',$txt_edit_after));

$url = 'https://ja.wikipedia.org/w/api.php?format=xml&action=query&prop=revisions&titles=%E3%82%A2%E3%82%A4%E3%82%AB%E3%83%84!_(%E3%82%A2%E3%83%8B%E3%83%A1)&rvprop=content&rvparse';
$txt = file_get_contents($url);//全体//string
$start = strpos($txt,': 各話リスト');//: 各話リスト//integer
$end = strpos($txt,': 放送局');//: 放送局//integer
$txt_edit = substr($txt, $start, $end-$start);//各話リスト一覧//string
//echo gettype($txt);
//echo $txt_edit;
//for($i=0;$i<178;$i++){
    $s = mb_strripos($txt_edit,'&4')+4;//integer
    $e = strpos($txt_edit,'</tr>');//integer
    $txt_edit = substr($txt_edit, $start, $end-$start);//各話リスト一覧//string
    print_r($s);
//}
//print_r($story);

$$story=explode('<td>',$txt_edit);
$story = array('test');
for($i=0;$i<178;$i++){
    $start = strripos($txt_edit,'第');
    $end = strripos($txt_edit,'日');
    $put = substr($txt_edit, $start, $end-$start);
    array_push($story,$put);
    $txt_edit=strstr($txt_edit,'</tr>',TRUE);
}
preg_match_all('/<td>(.+?)<\/td>/',$txt_edit,$story);//tdの要素取得
file_put_contents("result.txt", serialize($story));
*/

$url = 'https://ja.wikipedia.org/w/api.php?format=xml&action=query&prop=revisions&titles=%E3%82%A2%E3%82%A4%E3%82%AB%E3%83%84!_(%E3%82%A2%E3%83%8B%E3%83%A1)&rvprop=content&rvparse';
$xml_obj=simplexml_load_string(file_get_contents($url));
$txt = $xml_obj->query->pages->page->revisions->rev;//全体//string
$start = strpos($txt,': 各話リスト');//: 各話リスト//integer
$end = strpos($txt,': 放送局');//: 放送局//integer
$txt_edit = substr($txt, $start, $end-$start);//各話リスト一覧//string
preg_match_all('/<td>(.+?)<\/td>/is',$txt_edit,$story);//tdの要素取得

$words=array('test');
$regex = '/第(.+)話/is';
for($i=0;$i<count($story[1]);$i++){
    preg_match($regex, $story[1][$i], $matches);
    if(empty($matches)==FALSE){
        if($i!=0){
            $put=strip_tags($story[1][$i-2]);//タグの除去
            $put=htmlspecialchars_decode($put);//&のreplace//&#は反応しなかった。
            $put=preg_replace('/&#91;(.+?)&#93;/','',$put);//[注 ]の削除
            $put=str_replace('&#9835;','♫',$put);
            $put=str_replace('&#x2661;','♡',$put);
            array_push($words,$put);//$story[1][$i-2]
        }
    //array_push($word,$matches[0]);
    }
}
$put=strip_tags($story[1][count($story[1])-2]);//タグの除去
$put=htmlspecialchars_decode($put);//&のreplace//&#は反応しなかった。
$put=preg_replace('/&#91;(.+?)&#93;/','',$put);//[注 ]の削除
$put=str_replace('&#9835;','♫',$put);
$put=str_replace('&#x2661;','♡',$put);
array_push($words,$put);//$story[1][$i-2]

for($i=153;$i<178;$i++){
    unset($words[$i]);
}
$words=array_values($words);
file_put_contents("result3.txt", serialize($words));

//147,126,93,71が別バージョンなし
$words[71]=substr($words[71],strpos($words[71],'）')+3);
$words[93]=substr($words[93],strpos($words[93],'）')+3);
$words[126]=substr($words[126],strpos($words[126],'）')+3);
$words[126]=substr($words[126],strpos($words[126],'：')+3);
$words[147]=substr($words[147],strpos($words[147],'）')+3);
$words[147]=substr($words[147],strpos($words[147],'：')+3);
//print_r($words);
//file_put_contents("result3.txt", serialize($words));
?>