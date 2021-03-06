@extends('layouts.layout')
<!-- head -->
@include('common.head')
<link rel="stylesheet" href="{{ asset('css/user/mypage.css') }}" />

<!-- header -->
@include('common.header')

@section('content')
<div class="container">
  <div class="d-flex">
    <div class="item-left" style="width: 18rem;">
      <div class="card" style="width: 18rem;">
        <img class="card-img-top img" src="{{ asset('img/layout/human.png') }}">
        <div class="card-body">
          <h5 class="card-title">名前：{{ $user['name1'] }} {{$user['name2'] }}</h5>
          <h5 class="card-subtitle text-muted nickname">ニックネーム：{{ $user['name3'] }}</h5>
          <a href="#" class="btn btn-primary button01">プロフィール編集</a>
        </div>
      </div>
      <a href="/user/mypage" class="btn button" style="width: 18rem;">マイページTOP</a>
      <a href="/user/mypage/notification_list" class="btn button" style="width: 18rem;">通知一覧</a>
      <a href="/user/mypage/favorite_list" class="btn button" style="width: 18rem;">お気に入り一覧</a>
      <a href="/user/mypage/transaction_list" class="btn button" style="width: 18rem;">取引一覧</a>
      <a href="#" class="btn button" style="width: 18rem;">個人情報変更</a>
    </div>

    <div class="item-right">
      <h3 class="title"><img src="{{ asset('img/cars/title-car.png') }}" width="32px" class="title-img">現在取引中の車両</h3>
      @isset($cars)
        @if($cars->isEmpty())
          <p class="null">現在取引中の車両はありません。</p>
        @else
          @foreach($cars as $car)
            @foreach($transactions as $transaction)
            @if($car['CARNO'] == $transaction['CARNO'])
            <div class="cars">
              <div class="d-flex">
                <?php
                  $str="";
                  for($i=0;$i<8;$i++){
                      $str.=mt_rand(0,9);
                  }
                ?>
                <h4 class="car-title1">取引ID：{{ $str }}</h4>
                <h4 class="car-title2">落札日：{{ date('Y年n月j日',  strtotime($car['STRDT'])) }}</h4>
                <h4 class="car-title3">落札金額：{{ number_format($transaction['price'] * 1000) }}円</h4>
              </div>
              <div class="d-flex detail">
                <div class="car-item1">
                  <?php $filename = 'img/cars/' . $car['CARNO'] . '_1.jpg'; ?>
                  @if(file_exists($filename))
                      <img src="{{ asset('img/cars/' . $car['CARNO'] . '_1.jpg') }}" alt="メーカー名:車種名" width="180px" class="fav-img"/>
                  @else
                      <img src="{{ asset('img/cars/car.png') }}" alt="メーカー名:車種名" width="155px" class="null-img"/>
                  @endif
                </div>
                <div class="car-item2">
                  <h3>{{ $car['CARNM'] }}</h3>
                  @if($transaction['k_status'] == 1 && $transaction['n_status'] == 1)
                    <h4>取引ステータス：取引終了</h4>
                  @elseif ($transaction['k_status'] == 1)
                    <h4>取引ステータス：<span style="color: blue;font-weight: bold">納車待ち</span></h4>
                  @else 
                    <h4>取引ステータス：<span style="color: red;font-weight: bold">入金待ち</span></h4>
                  @endif
                </div>
                <div class="car-item3">
                  <a href="/user/cars/{{ $car['CARNO'] }}" class="btn detail-button" style="width: 13rem;">車両詳細</a>
                </div>
              </div>
            </div>
            @endif
            @endforeach
          @endforeach
        @endif
      @endif

      <h3 class="title"><img src="{{ asset('img/cars/title-car.png') }}" width="32px" class="title-img">お気に入り登録した車両</h3>
      @isset($favorites)
        @if($favorites->isEmpty())
          <p class="null">お気に入りに登録されている車両はありません。</p>
        @else
          @foreach($favorites as $favorite)
            <div class="cars">
              <div class="d-flex detail">
                <div class="car-item1">
                  <img src="{{ asset('img/cars/' . $favorite['CARNO'] . '_1.jpg') }}" width="180px" class="fav-img">
                </div>
                <div class="fav-item2">
                  <h4>{{ $favorite['CARNM'] }}</h4>
                  <div class="d-flex">
                    <div class="column">
                        <p class="p-title">オークション開始価格</p>
                        <p class="price">{{ isset($favorite['STRPR']) ? number_format($favorite['STRPR'] * 1000):'未登録' }}</p>
                    </div>
                    <div class="column">
                        <p class="fav-title">年式</p>
                        <p>{{ substr($favorite['NENSK'],0,2) }}年{{ substr($favorite['NENSK'],2,2) }}月</p>
                    </div>
                    <div class="column">
                        <p class="fav-title">走行距離</p>
                        <p>{{ number_format($favorite['SOUKM']) }}km</p>
                    </div>
                    <div class="column">
                        <p class="fav-title">排気量</p>
                        <p>{{ number_format($favorite['HIKRY']) }}cc</p>
                    </div>
                    <div class="column">
                        <p class="fav-title">修復歴</p>
                        @if(!$favorite['SYURK'])
                        <p>なし</p>
                        @else
                      <p>あり</p>
                        @endif
                    </div>
                    <div class="column">
                        <p class="fav-title">ミッション</p>
                        <p>{{ $favorite['MISYN'] }}</p>
                    </div>
                  </div>
                  @if($favorite['STATS'] == 2)
                  <p class="car-end">終了</p>
                  @else
                  <a href="/user/cars/{{ $favorite['CARNO'] }}" class="btn fav-button fav" style="width: 13rem;">車両詳細</a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        @endif
      @else
      @endif
    </div>
  </div>
</div>
@endsection

<!-- header -->
@include('common.footer')
