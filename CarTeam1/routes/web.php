<?php
// 最初の画面
Route::get('/', 'User\HomeController@index')->name('user.home');

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

    // 検索系
    Route::prefix('search')->group(function () {
        Route::get('car_name/{car_name}', 'SearchController@car_name')->name('search.name');
        Route::get('maker_name/{maker_name}', 'SearchController@maker_name')->name('search.maker');
        Route::get('body_type/{body_type}', 'SearchController@body_type')->name('search.type');
        Route::post('/', 'SearchController@search_detail')->name('search.detail');
        Route::get('home', 'SearchController@car_home');
    });

    // お気に入り
    Route::post('favorites/{CARNO}', 'FavoriteController@store')->name('favorite.store');

    // オークション
    Route::get('auction/list', 'AuctionController@list')->name('auction.list');
    Route::get('auction/{CARNO}', 'AuctionController@index')->name('auction');

    Route::get('auctionajax/{CARNO}', 'AuctionPriceController@index')->name('auctionprice');

    // 検索車両詳細
    Route::get('cars/{CARNO}', 'CarController@car_detail')->name('cars');

    // ログイン認証後
    Route::middleware('auth:user')->group(function () {

        // ログインしないとだめなroute
        Route::get('mypage/', 'MyPageController@index')->name('mypage');
        Route::get('mypage/transaction_list', 'MyPageController@transaction')->name('mypage.transaction');
        Route::get('mypage/favorite_list', 'MyPageController@favorite')->name('mypage.favorite');
        Route::get('mypage/notification_list', 'MyPageController@notification')->name('mypage.notification');
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

        // 登録系
        Route::prefix('regist')->group(function () {
            Route::get('car', 'RegistController@car')->name('regist.car');
            Route::post('car', 'RegistController@storeCar')->name('regist.store.car');
            Route::get('auction', 'RegistController@auction')->name('regist.auction');
            Route::post('auction', 'RegistController@storeAuction')->name('regist.store.auction');
            Route::get('auction/date', 'RegistController@auctionDate')->name('regist.auction.date');
            Route::get('employee', 'RegistController@employee')->name('regist.employee');
        });
    });
});
