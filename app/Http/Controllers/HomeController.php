<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;

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
        $pendapatan = Transaksi::whereDate('created_at', today())->sum('total_belanja');
        return view('home', compact('pendapatan'));
    }
    public function data_karyawan()
    {
        return view('karyawan.data_karyawan');
    }
    public function data_supplier()
    {
        return view('supplier.data_supplier');
    }

    public function setting()
    {
        $setting = Setting::first();
        return view('setting', compact('setting'));
    }
    public function update_setting(Request $request, $id)
    {
        $data = Setting::findOrFail($id);
        $validatedData = $request->validate([
            'nama_toko' => ['required'],
            'alamat_toko' => ['required',],
            'printer' => ['required',],
        ]);
        $data->update($validatedData);
        return redirect('setting')->with(['success' => 'pengaturan berhasil di ubah']);
    }
}
