@extends('layouts.layout')
<!-- head -->
@include('common.head')
<link rel="stylesheet" href="{{asset('css/user/auction.css')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- header -->
@include('common.header')

@section('content')
<div class="container">

    <a href="javascript:history.back()" class="btn return-button">〈 前に戻る</a>

    <h3 class="column-title1"><span class="maker">{{ $car['MKRNM'] }}</span> {{ $car['CARNM'] }} {{ $car->GRADE }}<span id="who"></span></h3>
    <div class="d-flex">
        <div class="item-left">
            <?php $filename = 'img/cars/' . $car['CARNO'] . '_1.jpg'; ?>
            @if(file_exists($filename))
            <img src="{{ asset('img/cars/' . $car['CARNO'] . '_1.jpg') }}" alt="メーカー名:車種名" width="500px" />
            @else
            <img src="{{ asset('img/cars/car.png') }}" alt="メーカー名:車種名" width="500px" />
            @endif
        </div>
        <div class="item-right">
            <div class="d-flex column1-right">
                <h5>残り時間</h5>
                <h1 id="RealtimeCountdownArea">loading</h1>
            </div>

            <div class="d-flex column2 justify-content-between">
                <div class="column2-left">
                    <div class="d-flex now-price">
                        <h5>現在入札金額</h5>
                        <div id="app">
                            <h1>¥</h1>
                            <h1 id="price"></h1>
                        </div>
                    </div>
                    <div class="d-flex change-price">
                        <h5>入札後金額</h5>
                        <h1 id="afterprice">¥</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex column3 justify-content-between">
        <div class="item2-left">
            <div class="d-flex">
                <div class="column3-left">
                    <div class="d-flex">
                        <div class="column-some">
                            <p>年式</p>
                            <p class="display">{{ substr($car->NENSK,0,2) }}年<br>{{ substr($car->NENSK,2,2) }}月</p>
                        </div>
                        <div class="column-some">
                            <p>走行距離</p>
                            <p class="display">{{ number_format($car->SOUKM) }}km</p>
                        </div>
                        <div class="column-some">
                            <p>車検期限</p>
                            <p class="display">{{ substr($car->KENKG, 0, 4) }}年<br>{{ substr($car->KENKG, 4, 2) }}月{{ substr($car->KENKG, 6, 2) }}日</p>
                        </div>
                        <div class="column-some">
                            <p>修復歴</p>
                            @if(!$car->SYURK)
                            <p class="display">無し</p>
                            @else
                            <p class="display">有り</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="item2-right">
            <div class="column3-right">
                <h5>入札金額入力</h5>
                <!-- <form action="http://localhost:9000/enter" method="post"> -->
                <form action="javascript:void(0);" method="POST" id="priceform">

                    <input type="hidden" name="user" value="{{Auth::id()}}">
                    <input type="hidden" name="now" value="" id="nowprice">
                    <input type="hidden" name="carno" value="{{ $car->CARNO }}">
                    <input type="text" name="price" id="inputPrice" pattern="\d*" oncopy="return false" onpaste="return false" style="ime-mode:disabled"><span class="n-num">,000</span>
                    <input type="submit" name="" class="button01" value="入札" id="enterButton">
                </form>
            </div>
        </div>
    </div>

    <a href="javascript:history.back()" class="btn return-button">〈 前に戻る</a>

</div>


@endsection

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#inputPrice').on('input', function() {
            check_numtype($(this));
        });
        $('#enterButton').on('click', function() {
            var pricedata = $('#priceform').serialize();
            // 'http://localhost:9000/enter'
            console.log(pricedata);
            $.ajax({
                url: 'http://localhost:9000/enter',
                type: "POST",
                data: pricedata //POST送信するデータを指定（{ 'hoge': 'hoge' }のように連想配列で直接書いてもOK）
            });
            // （１）押せなくする
            function SubmitBtnDisable() {
                $('#inputPrice').prop("disabled", true);
                $('#inputPrice').addClass("endinputprice");
                $('#enterButton').prop('disabled', true);
                $('#enterButton').addClass("endbutton");
                clearInterval(statusDis);
            }
            //（２）押せるようにする
            function SubmitBtnAble() {
                $('#inputPrice').prop("disabled", false);
                $('#inputPrice').val("");
                $('#inputPrice').removeClass("endinputprice");
                $('#enterButton').prop('disabled', false);
                $('#enterButton').removeClass("endbutton");
                clearInterval(statusAble);
            }

            function SubmitBtnClicked() {
                statusDis = setInterval(SubmitBtnDisable, 1); //ボタンを押した直後に（１）を呼び出し
                statusAble = setInterval(SubmitBtnAble, 3000); //ボタンを押して３秒後に（２）を呼び出し
            }
            SubmitBtnClicked();
        });
        Vue.config.devtools = true;
        var app = new Vue({
            el: '#app',
            data: {
                price: ""
            },
            mounted() {
                axios.get('/user/auctionajax/{{ $car->CARNO }}').then((response) => {
                    this.$data.price = response.data.price;
                    document.getElementById("price").innerHTML = Number(response.data.price + "000").toLocaleString();
                    document.getElementById("nowprice").value = Number(response.data.price).toLocaleString();
                }).catch(error => {
                    console.log(error);
                });
            }
        });

        setInterval(function() {
            axios.get('/user/auctionajax/{{ $car->CARNO }}').then((response) => {
                app.$data.price = response.data.price;
                document.getElementById("price").innerHTML = Number(response.data.price + "000").toLocaleString();
                document.getElementById("nowprice").value = Number(response.data.price).toLocaleString();
                var nowprice = parseInt($('#price').html(), 10);
                var inputprice = parseInt($('#inputPrice').val(), 10);
                if (inputprice >= 0) {
                    var score = nowprice + inputprice;
                    var strscore = String(score) + "000";
                    score = parseInt(strscore, 10).toLocaleString();
                    var result = $('#RealtimeCountdownArea').html();
                    console.log(result);
                    // 終了しました表示
                    if (result == "終了") {
                        //disabled属性を付与する
                        $('#afterprice').html("終了しました。");
                        $("#afterprice").css("color", "#DF7478");
                    } else {
                        $('#afterprice').html("¥" + score);
                    }
                } else {
                    document.getElementById("afterprice").innerHTML = "¥" + Number(response.data.price + "000").toLocaleString();


                }
            }).catch(error => {
                console.log(error);
            });
        }, 1000);

        var _chknum_value = "";
        // 入力値の半角数字チェック
        function check_numtype(obj) {

            // ２．変数の定義
            var txt_obj = $(obj).val();
            var text_length = txt_obj.length;
            // ３．入力した文字が半角数字かどうかチェック
            if (txt_obj.match(/^[0-9]+$/)) {
                // ３．１．文字数チェック
                if (text_length > 9) {
                    $('#inputPrice').val(_chknum_value);
                } else {
                    _chknum_value = txt_obj;
                }
            } else {
                // ３．２．入力した文字が半角数字ではないとき
                if (text_length == 0) {
                    _chknum_value = "";
                } else {
                    $('#inputPrice').val(_chknum_value);
                }
            }

        }

        // 終了タイマー
        function set2fig(num) {
            // 数値が1桁だったら2桁の文字列にして返す
            var ret;
            if (num < 10) {
                ret = "0" + num;
            } else {
                ret = num;
            }
            return ret;
        }

        function isNumOrZero(num) {
            // 数値でなかったら0にして返す
            if (isNaN(num)) {
                return 0;
            }
            return num;
        }

        function set2fig(num) {
            // 数値が1桁だったら2桁の文字列にして返す
            var ret;
            if (num < 10) {
                ret = "0" + num;
            } else {
                ret = num;
            }
            return ret;
        }

        function isNumOrZero(num) {
            // 数値でなかったら0にして返す
            if (isNaN(num)) {
                return 0;
            }
            return num;
        }

        // $('#timer').click(function() {
        //     showCountdown();
        // });

        var flg = true;

        setInterval(function() {

            if (flg) {

                // 現在日時を数値(1970-01-01 00:00:00からのミリ秒)に変換
                var nowDate = new Date();
                var dnumNow = nowDate.getTime();

                var oprnDate = '{{ $car->STRDT }}';
                oprnDate = oprnDate.replace(/\s+/g, "");
                // console.log(oprnDate);

                // 指定日時を数値(1970-01-01 00:00:00からのミリ秒)に変換

                var inputYear = oprnDate.substring(0, 4);
                // console.log(inputYear);
                var inputMonth = oprnDate.substring(5, 7);
                // console.log(inputMonth);
                var inputDate = oprnDate.substring(8, 10);
                // console.log(inputDate);
                var inputHour = parseInt(oprnDate.substring(10, 12), 10);
                var inputMin = parseInt(oprnDate.substring(13, 15), 10);
                var inputSec = oprnDate.substring(16, 18);
                if (inputMin == 59) {
                    inputMin = 0;
                    inputHour++;
                } else {

                    inputMin++;
                }
                inputHour = String(inputHour);
                inputMin = String(inputMin);
                // console.log(inputSec);
                var targetDate = new Date(isNumOrZero(inputYear), isNumOrZero(inputMonth), isNumOrZero(inputDate), isNumOrZero(inputHour), isNumOrZero(inputMin), isNumOrZero(inputSec));
                var dnumTarget = targetDate.getTime();

                // 表示を準備
                var dlYear = targetDate.getFullYear();
                var dlMonth = targetDate.getMonth() + 1;
                var dlDate = targetDate.getDate();
                var dlHour = targetDate.getHours();
                var dlMin = targetDate.getMinutes();
                var dlSec = targetDate.getSeconds();

                // 引き算して日数(ミリ秒)の差を計算
                var diff2Dates = dnumTarget - dnumNow;
                // 期限が過ぎた場合は -1 を掛けて正の値に変換

                if (dnumTarget <= dnumNow) {
                    diff2Dates *= -1;

                }
                // 差のミリ秒を、日数・時間・分・秒に分割
                var dDays = diff2Dates / (1000 * 60 * 60 * 24); // 日数
                diff2Dates = diff2Dates % (1000 * 60 * 60 * 24);
                var dHour = diff2Dates / (1000 * 60 * 60); // 時間
                diff2Dates = diff2Dates % (1000 * 60 * 60);
                var dMin = diff2Dates / (1000 * 60); // 分
                diff2Dates = diff2Dates % (1000 * 60);
                var dSec = diff2Dates / 1000; // 秒
                var msg2 = Math.floor(dMin) + "分" +
                    Math.floor(dSec) + "秒";

                // 表示文字列の作成
                var msg;
                if (msg2 == '0分0秒') {
                    msg = "終了";
                    $('#inputPrice').prop('disabled', true)
                    $('#inputPrice').addClass("endinputprice");
                    $('#enterButton').prop('disabled', true);
                    $('#enterButton').addClass("endbutton");

                    flg = false;
                } else {
                    msg = msg2;

                }
                if (flg == false) {

                    // Nodeに終了したことを知らせて終わりの処理スタート
                    $.post(
                            'http://localhost:9000/endauction', {
                                carno: '{{ $car->CARNO }}'
                            })
                        // 2検索成功時にはページに結果を反映
                        .done(function(data) {
                            // 結果リストをクリア
                            console.log('endsuccess');
                        })
                        // 3検索失敗時には、その旨をダイアログ表示
                        .fail(function() {
                            console.log('endfail');
                        });




                }

                // 作成した文字列を表示
                document.getElementById("RealtimeCountdownArea").innerHTML = msg;

            }
            // var result = $('#RealtimeCountdownArea').html();
            // // 終了しました表示
            // if (result == "終了") {
            //     var no = "";
            //     $.get('http://localhost:9000/who/' + no)
            //         .done(function(data) {
            //             console.log(data);
            //             // console.log(typeof data);
            //             // console.log(typeof myid);
            //             // console.log(data == myid);

            //         })
            // }

        }, 1000);
        // 1秒ごとに実行
        // setInterval(showCountdown(), 1000);
        setInterval(function() {
            var result = $('#RealtimeCountdownArea').html();
            // 終了しました表示
            if (result == "終了") {
                axios.get('http://localhost:9000/who/{{ $car->CARNO }}').then((res) => {
                    var carno = res.data.data;
                    if (carno == parseInt("{{Auth::id()}}", 10)) {

                        $('#who').html("あなたが落札者です");
                        $('#who').css("color", "#DF7478");
                    } else {

                        $('#who').html("落札できませんでした。");
                    }
                }).catch(error => {
                    console.log(error);
                });
            }
        }, 1000);
    });
</script>

<!-- header -->
@include('common.footer')