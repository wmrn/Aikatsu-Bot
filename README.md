# Aikatsu-Bot
アイカツ格言botと大空お天気bot 2017/10/25  

## Why
phpとかJSとかここんとこずっとさわってなさ過ぎて忘れてたから、夏休み後半から秋にかけてtwiter-botとSlack-botを作ってみた。  
Botの元ネタはアイカツの「アイカツ格言」と「大空お天気」。

## Overview
* アイカツ格言botのほうは毎週木曜日18:51ごろにアイカツ格言を1つランダムで流すbot 
<img src="https://github.com/wmrn/Aikatsu-Bot/blob/master/data/aikt_kkgn_Twitter.jpg" width="250" height="350"> 
* 大空お天気botは毎朝7:23-7:25の間に「@art_wmrn あかりちゃ～ん！○○のお天気どうですか～？」みたいな感じで聞くと現在の天気を教えてくれるbot  
※今日の天気ではない

## Description
1. アイカツ格言bot(Slack・twitter)  
昔アニメでやっていたアイカツのミニコーナー。  
毎週、本編が終わった後にその日の話を振り返ってキャラクターが今週の格言を紹介する。残念ながらアイカツの4シーズンと今(2017)アニメでやっているアイカツスターズではコーデ紹介のコーナーになっている。(アイカツの最終回とアイカツ5周年記念の回を除く)  
これをもとに、現在放送中のアイカツスターズのコーデ紹介の時間に合わせてtwitterとSlackでアイカツ格言を呟いてくれるbotを作った。その時、twitterでは画像リンクから画像を開いてくれなかったのでneverでまとめられていた記事のリンクをはった。また、Slackのほうはリンクから画像を開いてくれたのでneverの画像リンクとyoutubeで誰かがまとめていた動画リンクを呟いてくれるようにした。動画リンクは途中再生されるようにhtml埋め込み用のリンクを利用しているのでSlack上では見ることはできない。

2. 大空お天気bot(twitter)  
アニメの登場人物である大空あかりちゃんがお天気キャスターとして登場しているコーナー  
記憶が正しいければ大空お天気は朝の7:23の時と7:25の時があった。(ほとんど25分だった。)  
それをもとに7:23-7:25の時間の間に、botをあかりちゃん、自分をニュースキャスターに見立てて、リプととばすと大空お天気でお天気を教えてくれるbotを作った。Slackのほうはスラッシュコマンドでやりたいなと思ってるからまた今度。


## Making
1. RaspberryPi3  
PCのタスクマネージャーはPC閉じている間でも動くのか微妙だったためボツ。(たぶん無理だと思われ。試してないからわからんが。)  
レンタルサーバーはコスパが悪くてボツ。(自分が今後サーバーをフル活用するような気がしなかった。)  
って感じでラズパイがどんなものかも気になっていたのもあって勉強次いでな感じでラズパイを使うことにした。(サーバーというよりもPCだもんね。)  
セットアップとか記事に書いてくださってるもの多くてとても助かった。  
     * 参考url  
  [RaspberryPi3のセットアップ](http://usicolog.nomaki.jp/engineering/raspberryPi/raspberryPi3.html)  
  [RaspberryPiにインストールしておくといいもの](http://www.seo-wp.net/entry/summry-ras-tool-20170307)  
  [RaspberryPiとファイル共有(Windows)](http://www.raspberrypirulo.net/entry/2016/08/22/Samba%E3%82%92%E4%BD%BF%E7%94%A8%E3%81%97%E3%81%A6%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB%E3%82%92%E5%85%B1%E6%9C%89%E3%81%99%E3%82%8B%E6%96%B9%E6%B3%95)  
  [RaspberryPiをリモート接続](https://qiita.com/t114/items/bfac508504b9a6b7570d)  
  [RaspberryPiにNode.jsとnpmの最新版をインストール](https://qiita.com/mascii/items/77c685df65c4cbca9315)  

2. アイカツ格言bot  
アイカツ格言の取得はwikiを利用し、画像類はnerverでまとめられていたリンクを利用、youtubeの動画は1つ1つの時間が微妙に異なっていたのでリンクをすべてメモに記録しそれをとってくるようにした。  
botを動かすのはRaspberryPiでcronを利用した。  
twitterでの実装はtwitteroauthのサンプルを利用して実装した。Slackでの実装はbotkitの使い方よくわからなかったからwebhook使った。  
      * 参考url  
  [twitterOAuth](http://wepicks.net/twitterapiv11_webapp/)  
  [cronの使い方](http://make.bcde.jp/raspberry-pi/%E6%B1%BA%E3%81%BE%E3%81%A3%E3%81%9F%E6%99%82%E9%96%93%E3%81%AB%E5%87%A6%E7%90%86%E3%81%99%E3%82%8B/)  
  [cronの使い方(MAILTOエラーの消し方)](http://www.server-memo.net/tips/crontab.html)  
  [webhooksの使い方](https://qiita.com/hoto17296/items/621a6e16f23785a543f3)  
  ※$webhook_urlを取得するには _----_ を自分用に書き換える  
  (https://----.slack.com/apps/new/A0F7XDUAZ-incoming-webhooks)

    --余談--  
ラズパイの再起動終了したタイミングがわからないのとcronの _@reboot_ 使ってみたいのがあったので再起動時にSlackで教えてくれるようなものサクッと組んでみたが、再起動で実行するよりも先にネット接続が切られてしまうのかうまくいかなかったのでボツになった。後で調べてみたらなんかめんどくさそうだった。  
    * 参考url  
  <https://www.raspberrypi.org/forums/viewtopic.php?f=82&t=108958>

3. 大空お天気bot  
まずリプの文字列に「あかり」と「天気」どっちも含まれてたら大空お天気を求めてると判断し、そこから形態素解析をして場所の名前を取ってくる。次に使うAPIが地名を英語にしないと受け付けてくれなかったからWeblio翻訳になげてからAPIになげる。APIからは現在の天気・最高気温・最低気温・天気のiconを取得してそれをまとめてリプで返すようにした。アイコンをただ見せるだけだと何かつまらないと思い動画を作成してそれも一緒に呟いてくれるようにした。もし形態素解析で地名が取得できなかったり、APIになかったりしたとき用にアイカツの画像をまとめてあるデータベースを見つけたのでそれを利用させてもらった。  
最初phpでcronで回そうとしていたがnode.jsのforeverするといいみたいにきいたのでそうした。
一応参考urlはphpのものも一緒に載せておく。

    * 参考url  
  [ツイート取得方法(php)](https://qiita.com/yokoh9/items/760e432ebd39040d5a0f)  
  [リプライ方法(php)](https://qiita.com/tsumugu/items/e23481626cb6309a249c)  
  [twitteroauthのエラーの消し方](https://qiita.com/bonk/items/c4aecf99206e0517b634)  
  [$_SESSIONの使い方](http://blog.zolesystem.info/%E3%80%90session%E3%80%91php%E3%81%A7%E4%B8%80%E6%99%82%E7%9A%84%E3%81%AB%E3%83%87%E3%83%BC%E3%82%BF%E3%82%92%E4%BF%9D%E6%8C%81%E3%81%99%E3%82%8B%E6%96%B9%E6%B3%95/)  
  [形態素解析(igo-php)](https://qiita.com/hshimo/items/8a7852d040b3daf19301#公式ページから概要特徴)  
  [Weblio(php)](http://mmorley.hatenablog.com/entry/2016/11/14/124132#Weblio-翻訳)  
  [OpenWeatherMap](https://openweathermap.org/)  
  [OpenWatherMapのAPIのたたきかた(php)](https://usortblog.com/openweathermap/#i)  
  [OpenWeatherMapのAPIの中身(特に温度のところ)](https://qiita.com/key/items/aad73fd6057484f20731)  
  [アイカツデータベース](http://aikatsup.com/)  
  [abraham/twitterOAuth](https://github.com/abraham/twitteroauth)  
  [abraham/twitterOAuthの使い方](https://qiita.com/kino0104/items/9f9e6f75d58b40663673)  
  [画像付きのツイートの仕方](https://aws2000.net/?p=2296)  
  [タイムアウト回避](https://catcherweb.com/fudou-tweet-old-post/)  
  [ツイート方法(node.js)](https://qiita.com/iyuichi/items/eb8254496facb0c35703)  
  [リプライの方法(node.js)](http://l-n-m.hatenablog.com/entry/2016/05/05/144621)  
  [foreverの使い方](http://onlineconsultant.jp/pukiwiki/?node.js%20node.js%E3%82%B9%E3%82%AF%E3%83%AA%E3%83%97%E3%83%88%E3%82%92forever%E3%81%A7%E3%83%87%E3%83%BC%E3%83%A2%E3%83%B3%E5%8C%96%E3%81%99%E3%82%8B)  
  [形態素解析(kuromoji)](https://zeny.io/blog/2016/06/16/kuromoji-js/)  
  [node-twitter(media)](https://github.com/desmondmorris/node-twitter/tree/master/examples#media)  
  [twitterAPI(Upload media)](https://developer.twitter.com/en/docs/media/upload-media/overview)  
  [OpenWeatherMapのAPIのたたきかた(node.js)](https://yoheikoga.github.io/2016/08/14/open-weather-map-by-nodejs/)  
  [Promise](https://qiita.com/toshihirock/items/e49b66f8685a8510bd76)  

## Finally
組んでみたもののPOST/GETのやり方とかいろいろ見よう見まねでとりあえず使ったものが多すぎて結局成長できぬまま終わった。だからどうにかこうにかもうちょっとしないといけないことが分かった。これは組むことを優先させ過ぎたのがあかんかった。 でも今まで知らなかったこと知ることはできたし作りたいもの作れたから無駄ではなかったと思う。   
あと、twitterのbotとかうまくいくのか試すときにいちいちリプ飛ばしたりしなきゃいけないとなると、失敗も含めたやり取りもすべてほかの人に見られるのなんだか恥ずかしいんだがみんなどうしているんだろうかね。。。ちょっぴり気になった。  
あと大空お天気の時間に起きれないから。。。意味ない。。。じゃんじゃん♪  