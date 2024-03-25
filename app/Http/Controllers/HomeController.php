<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function data_karyawan()
    {
        return view('data_karyawan');
    }
    public function data_supplier()
    {
        return view('data_supplier');
    }
    public function data_member()
    {
        return view('data_member');
    }
    public function pengaturan()
    {
        return view('pengaturan');
    }
    public function transaksi_member()
    {
        return view('transaksi_member');
    }

}
