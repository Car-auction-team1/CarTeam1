<?php
// 最初の画面
Route::get('/', function () {
  return view('user.home');
})->name('user.home');

// ユーザー
Route::namespace('User')->prefix('user')->name('user.')->group(function () {

  // ログイン認証関連
  Auth::routes([
    'register' => true,
    'reset'    => false,
    'verify'   => false
  ]);

  // TOPページ
  Route::resource('home', 'HomeController', ['only' => 'index']);

  // 検索
  Route::get('search/car_name/{car_name}', 'SearchController@car_name')->name('search.name');
  Route::get('search/maker_name/{maker_name}', 'SearchController@maker_name')->name('search.maker');
  Route::get('search/body_type/{body_type}', 'SearchController@body_type')->name('search.type');
  Route::get('search/', 'SearchController@search_detail')->name('search.detail');

  // 検索車両詳細
  Route::get('cars/{car_no}', 'CarController@car_detail')->name('cars');

  // ログイン認証後
  Route::middleware('auth:user')->group(function () {

    // ログインしないとだめなroute
    Route::get('mypage/', 'MyPageController@index')->name('mypage');

  });
});

// 管理者
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {

  // ログイン認証関連
  Auth::routes([
    'register' => true,
    'reset'    => false,
    'verify'   => false
  ]);

  // ログイン認証後(Adminはすべてここ)
  Route::middleware('auth:admin')->group(function () {

    // TOPページ
    Route::resource('/', 'HomeController', ['only' => 'index']);
    Route::resource('home', 'HomeController', ['only' => 'index']);

  });

});