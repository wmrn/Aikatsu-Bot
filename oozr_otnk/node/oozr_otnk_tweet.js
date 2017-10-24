var twitter = require('twitter');
var http = require('http');
var fs = require('fs');
var kuromoji = require('kuromoji');
var client = require('cheerio-httpcli');
var Q = require('q');

var sConsumerKey = new Buffer("**************", 'base64').toString();
var sConsumerSecret = new Buffer("***************", 'base64').toString();
var sAccessToken = new Buffer("****************", 'base64').toString();
var sAccessTokenSecret = new Buffer("*****************", 'base64').toString();

var bot = new twitter({
    consumer_key: sConsumerKey,
    consumer_secret: sConsumerSecret,
    access_token_key: sAccessToken,
    access_token_secret: sAccessTokenSecret
});

var pathToMovie, mediaType, mediaData, mediaSize, id;

// Public APIのstatuses/filterで取得したタイムラインを、自分のアカウント名を含む文字列でフィルターする
bot.stream('statuses/filter', {
    track: '@art_wmrn'
}, function (stream) {
    // フィルターされたデータのストリームを受け取り、ツイートのテキストを表示する
    stream.on('data', function (data) {
        //ex)あかりちゃ～ん！東京のお天気どうですか～？
        var text = data.text.replace(/@art_wmrn/g, ""); // ツイートのテキスト
        if (text.match('あかり')) {
            if (text.match('天気')) {
                Promise.resolve()
                    .then(function () { //形態素解析
                        return new Promise(function (resolve, reject) {
                            var basyo = null;
                            var builder = kuromoji.builder({
                                dicPath: '/home/pi/node_modules/kuromoji/dict/'
                            });
                            builder.build(function (err, tokenizer) {
                                if (err) {
                                    throw err;
                                }
                                text = tokenizer.tokenize(text);
                                for (var i = 0; i < text.length; i++) {
                                    if (text[i]['pos_detail_1'] == "固有名詞") {
                                        basyo = text[i]['surface_form'];
                                        i = text.length;
                                    }
                                }
                                resolve(basyo); //ex)東京 or null
                            });
                        });
                    })
                    .then(function (value) { //Weblio
                        return new Promise(function (resolve, reject) {
                            console.log(value);
                            var basyo = value;
                            var place = null;
                            if (basyo != null) {
                                client.fetch('http://translate.weblio.jp', {
                                    lp: 'JE',
                                    lpf: 'JE',
                                    originalText: value
                                }, function (err, $, res) {
                                    var page = res.body;
                                    var start = page.indexOf('translatedTextAreaLn');
                                    var end = page.indexOf('translatedTextWrLn0');
                                    var place = page.slice(start, end);
                                    start = place.indexOf('">');
                                    end = place.indexOf('</');
                                    place = place.slice(start + 2, end);
                                    place = place.toString('UTF-8');

                                    resolve([place, basyo]); //ex)[Tokyo,東京]
                                });
                            } else {
                                resolve([place, basyo]); //ex)[null,null]
                            }
                        });
                    })
                    .then(function (value) { //weatherAPI
                        return new Promise(function (resolve, reject) {
                            console.log(value);
                            if (value[1] == null) { //分析して固有名詞がなかった場合
                                //「何....これ。」
                                mess = "https://twitter.com/_aikatsup/status/622449628647063556/photo/1";
                                resolve([mess, null]);
                            } else {
                                var key = new Buffer("**************", 'base64').toString();
                                var icon, temp_max, temp_min, time, weather;
                                var url = "http://api.openweathermap.org/data/2.5/weather?q=" + value[0] + ",jp&appid=" + key;
                                http.get(url, function (res) {
                                    var body = '';
                                    res.setEncoding('utf8');
                                    res.on('data', function (chunk) {
                                        body += chunk;
                                    });
                                    res.on('data', function (chunk) {
                                        res = JSON.parse(body);
                                        if (res.name == value[0]) {
                                            icon = res.weather[0].icon; //ex)01d
                                            temp_max = (res.main.temp_max) - 273.15;
                                            temp_min = (res.main.temp_min) - 273.15;
                                            temp_max = Math.floor(temp_max * 10) / 10; //最高気温
                                            temp_min = Math.floor(temp_min * 10) / 10; //最低気温
                                            var date = new Date();
                                            var hour = date.getHours();
                                            var minute = date.getMinutes();
                                            minute = ('00' + minute).slice(-2);
                                            time = hour + "時" + minute + "分"; //ex)7時03分
                                            if (icon.slice(0, 2) == "01") {
                                                weather = "☀"; //快晴
                                            } else if (icon.substr(0, 2) == "02") {
                                                weather = "☀"; //晴れ
                                            } else if (icon.substr(0, 2) == "03") {
                                                weather = "☁"; //くもり
                                            } else if (icon.substr(0, 2) == "04") {
                                                weather = "☁"; //くもり
                                            } else if (icon.substr(0, 2) == "09") {
                                                weather = "☂"; //小雨
                                            } else if (icon.substr(0, 2) == "10") {
                                                weather = "☂"; //雨
                                            } else if (icon.substr(0, 2) == "11") {
                                                weather = "⚡"; //雷雨
                                            } else if (icon.substr(0, 2) == "13") {
                                                weather = "☃"; //雪
                                            } else if (icon.substr(0, 2) == "50") {
                                                weather = "🌁"; //霧
                                            }

                                            /*はーい！時刻は「$time」！
            今日のお空はどんな空？大空お天気の時間です！
            ここ「$place」の最高気温は「$temp_max」、最低気温は「$temp_min」です！
            大空ラッコ君、今日のお天気は....「$weather」
            それでは通勤通学気を付けて！！！いってらっしゃーい！！*/

                                            mess = "はーい！時刻は" + time + "！\r\n今日のお空はどんな空？大空お天気の時間です！\r\nここ「" + value[1] + "」の最高気温は" + temp_max + "℃、最低気温は" + temp_min + "℃です！\r\n大空ラッコ君、今日のお天気は...." + weather + "\r\nそれでは通勤通学気を付けて！！\r\nいってらっしゃーい！！";
                                            resolve([mess, icon]);
                                        } else { //APIになかった場合
                                            //「ごめんなさい」
                                            mess = "https://twitter.com/_aikatsup/status/582550620306939906/photo/1";
                                            resolve([mess, null]);
                                        }
                                    });
                                }).on('error', function (e) {
                                    console.log(e.message); //「ごめんなさい」
                                    mess = "https://twitter.com/_aikatsup/status/582550620306939906/photo/1";
                                    resolve([mess, null]);
                                });
                            }
                        });
                    })
                    .then(function (value) {
                        if (value[1] != null) {
                            console.log(value);
                            text = value[0];
                            pathToMovie = 'oozr_otnk/node/mp4/' + value[1] + '.mp4';//パスは根っこから書くように編集
                            mediaType = 'video/mp4'; // `'video/mp4'` is also supported
                            mediaData = fs.readFileSync(pathToMovie);
                            mediaSize = fs.statSync(pathToMovie).size;
                            id = 0;
                            initUpload() // Declare that you wish to upload some media
                                .then(appendUpload) // Send the data for the media
                                .then(finalizeUpload) // Declare that you are done uploading chunks
                                .then(mediaId => {
                                    // You now have an uploaded movie/animated gif
                                    // that you can reference in Tweets, e.g. `update/statuses`
                                    // will take a `mediaIds` param.
                                    bot.post('statuses/update', {
                                            status: '@' + data.user.screen_name + text,
                                            media_ids: id,
                                            in_reply_to_status_id: data.id_str
                                        },
                                        function (error, tweet, response) {
                                            if (error) {
                                                //console.log(tweet)
                                            }
                                        });
                                });
                        } else {
                            text = value[0];
                            bot.post('statuses/update', {
                                    status: '@' + data.user.screen_name + " " + text,
                                    in_reply_to_status_id: data.id_str
                                },
                                function (error, tweet, response) {
                                    if (error) {
                                        //console.log(tweet)
                                    }
                                });
                        }
                    }).catch(function (error) {
                        //console.log(error);
                    });
            }
        }
    });
});

function initUpload() {
    //console.log("init");
    return makePost('media/upload', {
        command: 'INIT',
        total_bytes: mediaSize,
        media_type: mediaType,
    }).then(data => data.media_id_string);
}

function appendUpload(mediaId) {
    //console.log("up");
    id = mediaId;
    //console.log(mediaId);
    return makePost('media/upload', {
        command: 'APPEND',
        media_id: mediaId,
        media: mediaData,
        segment_index: 0
    }).then(data => mediaId);
}

function finalizeUpload(mediaId) {
    //console.log("final");
    return makePost('media/upload', {
        command: 'FINALIZE',
        media_id: mediaId
    }).then(data => mediaId);
}

function makePost(endpoint, params) {
    //console.log("what");
    return new Promise((resolve, reject) => {
        bot.post(endpoint, params, (error, data, response) => {
            if (error) {
                reject(error);
            } else {
                resolve(data);
            }
        });
    });
}