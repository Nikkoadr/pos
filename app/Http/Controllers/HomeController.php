<?php

namespace App\Http\Controllers;

use App\Models\Nota;
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
        $pendapatan = Nota::whereDate('created_at', today())->sum('total_belanja');
        return view('home', compact('pendapatan'));
    }
    public function data_karyawan()
    {
        return view('data_karyawan');
    }
    public function data_supplier()
    {
        return view('data_supplier');
    }

    public function pengaturan()
    {
        return view('pengaturan');
    }
}
