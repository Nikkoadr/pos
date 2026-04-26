<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Data_barang;
use App\Models\DetailTransaksiServis;
use App\Models\DetailTransaksi;


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
        $hari_ini = now()->today();
        $bulan_ini = now()->month;
        $tahun_ini = now()->year;

        // --- STATISTIK PENDAPATAN ---

        // 1. Pendapatan Hari Ini
        $pendapatan_hari_ini = DetailTransaksi::whereHas('transaksi', function ($q) use ($hari_ini) {
            $q->whereDate('tanggal_transaksi', $hari_ini)
                ->where('status', 'selesai');
        })->selectRaw('SUM(harga_jual * qty) as total')
            ->value('total');

        // 2. Pendapatan Bulan Ini
        $pendapatan_bulan_ini = DetailTransaksi::whereHas('transaksi', function ($q) use ($bulan_ini, $tahun_ini) {
            $q->whereMonth('tanggal_transaksi', $bulan_ini)
                ->whereYear('tanggal_transaksi', $tahun_ini)
                ->where('status', 'selesai');
        })->selectRaw('SUM(harga_jual * qty) as total')
            ->value('total');

        // 3. Pendapatan Tahun Ini
        $pendapatan_tahun_ini = DetailTransaksi::whereHas('transaksi', function ($q) use ($tahun_ini) {
            $q->whereYear('tanggal_transaksi', $tahun_ini)
                ->where('status', 'selesai');
        })->selectRaw('SUM(harga_jual * qty) as total')
            ->value('total');

        // --- DATA LAIN ---
        $servis_proses = DetailTransaksiServis::whereIn('status_servis', ['masuk', 'proses'])->count();
        $stok_limit = Data_barang::where('qty', '<', 5)->count();

        $servis_terbaru = DetailTransaksiServis::whereIn('status_servis', ['masuk', 'proses'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('home', compact(
            'pendapatan_hari_ini',
            'pendapatan_bulan_ini',
            'pendapatan_tahun_ini',
            'servis_proses',
            'stok_limit',
            'servis_terbaru'
        ));
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
