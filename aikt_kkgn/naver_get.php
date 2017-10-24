<?php
$naver_url=array('test');
$naver_image=array('test');
for($i=1;$i<=6;$i++){
    $url = "https://matome.naver.jp/odai/2134969397629208201?&page=".$i;
    $data = file_get_contents($url);
    $pattern_url = '/<a.*?href\s*=\s*[\"|\'](.*?)[\"|\'].*?>/i';
    preg_match_all( $pattern_url, $data, $page);
    $pattern_image = '/<img.*?src\s*=\s*[\"|\'](.*?)[\"|\'].*?>/i';
    preg_match_all( $pattern_image, $data, $images);
    $regex = '/src=(.+).jpg%/is';
    if($i!=6){
    for($j=0;$j<30;$j++){
    array_push($naver_url,'https://matome.naver.jp/'.$page[1][$j*4+14]);
    }
    for($j=3;$j<33;$j++){
        preg_match($regex, $images[1][$j], $put);//JPGの部分だけ取得
        array_push($naver_image,urldecode($put[1].".jpg"));//デコードしないと画像が表示されなかった
    }
}else{
    for($j=0;$j<2;$j++){
        array_push($naver_url,'https://matome.naver.jp/'.$page[1][$j*4+14]);
        }
        for($j=3;$j<5;$j++){
            preg_match($regex, $images[1][$j], $put);
            array_push($naver_image,urldecode($put[1].".jpg"));
            }
}
}
unset($naver_url[1]);//5周年のを削除
$naver_url=array_values($naver_url);//配列シフト
$naver_url=array_reverse($naver_url);//配列の順番を逆に
unset($naver_url[count($naver_url)-1]);//'test'を削除
$naver_url=array_values($naver_url);//一応シフト
array_splice($naver_url,13,0,$naver_url[0]);
array_splice($naver_url,14,0,$naver_url[0]);
array_splice($naver_url,94,0,$naver_url[0]);//もともと無かったもののカバー
//147,126,93,71が別バージョンなし
//94,14,13がない
$naver_url[71]=$naver_url[0];
$naver_url[93]=$naver_url[0];
$naver_url[126]=$naver_url[0];
$naver_url[147]=$naver_url[0];

unset($naver_image[1]);//5周年のを削除
$naver_image=array_values($naver_image);//配列シフト
$naver_image=array_reverse($naver_image);//配列の順番を逆に
unset($naver_image[count($naver_image)-1]);//'test'を削除
$naver_image=array_values($naver_image);//一応シフト
array_splice($naver_image,13,0,$naver_image[0]);
array_splice($naver_image,14,0,$naver_image[0]);
array_splice($naver_image,94,0,$naver_image[0]);//もともと無かったもののカバー
//147,126,93,71が別バージョンなし
//94,14,13がない
$naver_image[71]=$naver_image[0];
$naver_image[93]=$naver_image[0];
$naver_image[126]=$naver_image[0];
$naver_image[147]=$naver_image[0];
?>