<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function car()
    {
        return view('admin.regist.car');
    }

    public function auction()
    {
        return view('admin.regist.auction');
    }

    public function employye()
    {
        return view('admin.regist.employee');
    }

}