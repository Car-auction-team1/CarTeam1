<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Car;
use Auth;

class SearchController extends Controller
{
    public function index()
    {
        return view('user.home');
    }

    // 車両名で検索（HomeControllerからの遷移）
    public function car_home(Request $req)
    {
      $car_name = $req->car_name;
      $cars = Car::select(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR'])->where('CARNM', 'LIKE', "%$car_name%")->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get();
      
      foreach($cars as $car){
        if(!($car['STRDT'] == NULL)){
          $car['STRDT'] = date('Y/m/d H:i', strtotime($car['STRDT']));
        }
      }

      $user = User::find(Auth::id());
      $favorites = null;
      if(isset($user)){
        $favorites = $user->favorites()->get();
      }

      return view('user.search', compact('cars','car_name','favorites'));
    }
 
    // 車両名で検索
    public function car_name($car_name)
    {
      $cars = Car::select(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR'])->where('CARNM', 'LIKE', "%$car_name%")->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get();

      foreach($cars as $car){
        if(!($car['STRDT'] == NULL)){
          $car['STRDT'] = date('Y/m/d H:i', strtotime($car['STRDT']));
        }
      }

      $user = User::find(Auth::id());
      $favorites = null;
      if(isset($user)){
        $favorites = $user->favorites()->get();
      }

      return view('user.search', compact('cars','car_name','favorites'));
    }

    // メーカー名で検索
    public function maker_name($maker_name)
    {
      $cars = Car::select(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR'])->ofMaker($maker_name)->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get();

      $user = User::find(Auth::id());
      $favorites = null;
      if(isset($user)){
        $favorites = $user->favorites()->get();
      }

      return view('user.search', compact('cars','maker_name','favorites'));
    }

    // ボディータイプで検索
    public function body_type($body_type)
    {
      $cars = Car::select(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR'])->ofBodyType($body_type)->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get();

      $user = User::find(Auth::id());
      $favorites = null;
      if(isset($user)){
        $favorites = $user->favorites()->get();
      }
      return view('user.search', compact('cars','body_type','favorites'));
    }

    // 詳細検索
    public function search_detail(Request $req)
    {

      $car_name = $req->car_name;
      $sort = $req->sort;
      $price = [
          'min' => $req->min_price / 1000,
          'max' => $req->max_price / 1000,
      ];
      $min_price = $req->min_price;
      $max_price = $req->max_price;
      if(empty($req->min_nensk) || empty($req->max_nensk)){
        $nensk = [
          'min' => $req->min_nensk,
          'max' => $req->max_nensk
        ];
      }else{
        $nensk = [
          'min' => substr($req->min_nensk, 2, 2) . '01',
          'max' => substr($req->max_nensk, 2, 2) . '12'
        ];
      }
      $min_nensk = $req->min_nensk;
      $max_nensk = $req->max_nensk;
      $soukm = [
        'min' => $req->min_soukm,
        'max' => $req->max_soukm,
      ];
      $min_soukm = $req->min_soukm;
      $max_soukm = $req->max_soukm;

      if($req->send){
        if ($sort === 'price-asc') {
          $query = Car::query();
  
          if(!empty($car_name)) {
            $query->where('CARNM', 'LIKE', "%$car_name%");
          }
          if(!empty($price['min']) && !empty($price['max'])){
            $query->whereBetween('STRPR', [$price['min'], $price['max']]);
          }
          if(!empty($nensk['min']) && !empty($nensk['max'])){
            if($nensk['min'] < $nensk['max']){
              $query->whereBetween('NENSK', [$nensk['min'], $nensk['max']]);
            }else{
              $query->where(function($query) use($nensk){
                $query->whereBetween('NENSK', [$nensk['min'], '9912'])
                      ->orWhereBetween('NENSK', ['0001', $nensk['max']]);
              });
            }
          }
          if(!empty($soukm['min']) && !empty($soukm['max'])){
            $query->whereBetween('SOUKM', [$soukm['min'], $soukm['max']]);
          }
          
          $cars = $query->whereIn('STATS', [0,1])->orderBy('STRPR', 'desc')->get(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR']);
  
          foreach($cars as $car){
            if(!($car['STRDT'] == NULL)){
              $car['STRDT'] = date('Y/m/d H:i', strtotime($car['STRDT']));
            }
          }
  
          return view('user.search', compact('cars', 'car_name', 'sort', 'min_price', 'max_price', 'min_nensk', 'max_nensk', 'min_soukm', 'max_soukm'));
        } elseif ($sort === 'price-desc') {
            $query = Car::query();
    
            if(!empty($car_name)) {
              $query->where('CARNM', 'LIKE', "%$car_name%");
            }
            if(!empty($price['min']) && !empty($price['max'])){
              $query->whereBetween('STRPR', [$price['min'], $price['max']]);
            }
            if(!empty($nensk['min']) && !empty($nensk['max'])){
              if($nensk['min'] < $nensk['max']){
                $query->whereBetween('NENSK', [$nensk['min'], $nensk['max']]);
              }else{
                $query->where(function($query) use($nensk){
                  $query->whereBetween('NENSK', [$nensk['min'], '9912'])
                        ->orWhereBetween('NENSK', ['0001', $nensk['max']]);
                });
              }
            }
            if(!empty($soukm['min']) && !empty($soukm['max'])){
              $query->whereBetween('SOUKM', [$soukm['min'], $soukm['max']]);
            }
            
            $cars = $query->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR']);
  
            foreach($cars as $car){
              if(!($car['STRDT'] == NULL)){
                $car['STRDT'] = date('Y/m/d H:i', strtotime($car['STRDT']));
              }
            }
  
            return view('user.search', compact('cars', 'car_name', 'sort', 'min_price', 'max_price', 'min_nensk', 'max_nensk', 'min_soukm', 'max_soukm'));
        }  elseif ($sort == NULL) {
          $query = Car::query();

          if(!empty($car_name)) {
            $query->where('CARNM', 'LIKE', "%$car_name%");
          }
          if(!empty($price['min']) && !empty($price['max'])){
            $query->whereBetween('STRPR', [$price['min'], $price['max']]);
          }
          if(!empty($nensk['min']) && !empty($nensk['max'])){
            if($nensk['min'] < $nensk['max']){
              $query->whereBetween('NENSK', [$nensk['min'], $nensk['max']]);
            }else{
              $query->where(function($query) use($nensk){
                $query->whereBetween('NENSK', [$nensk['min'], '9912'])
                      ->orWhereBetween('NENSK', ['0001', $nensk['max']]);
              });
            }
          }
          if(!empty($soukm['min']) && !empty($soukm['max'])){
            $query->whereBetween('SOUKM', [$soukm['min'], $soukm['max']]);
          }

          $cars = $query->whereIn('STATS', [0,1])->orderBy('STRPR', 'asc')->get(['CARNO','MKRNM','CARNM','NENSK','SOUKM','STRDT','SYURK','MISYN','HIKRY','STRPR']);

          foreach($cars as $car){
            if(!($car['STRDT'] == NULL)){
              $car['STRDT'] = date('Y/m/d H:i', strtotime($car['STRDT']));
            }
          }

          $user = User::find(Auth::id());
          $favorites = $user->favorites()->get();

          return view('user.search', compact('cars', 'car_name', 'sort', 'min_price', 'max_price', 'min_nensk', 'max_nensk', 'min_soukm', 'max_soukm','favorites'));
        }
      }
    }
  }