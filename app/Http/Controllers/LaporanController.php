<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;

class LaporanController extends Controller
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

    public function index(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        // Ambil transaksi selesai
        $transaksi = Transaksi::where('status', 'selesai')
            ->whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir)
            ->get();

        // Ambil detail transaksi
        $detail = DetailTransaksi::whereIn('id_transaksi', $transaksi->pluck('id'))->get();

        // ================= HITUNG =================
        $total_pendapatan = 0;
        $total_modal = 0;

        foreach ($detail as $item) {
            $total_pendapatan += $item->harga_jual * $item->qty;
            $total_modal += ($item->harga_modal ?? 0) * $item->qty;
        }

        $laba_kotor = $total_pendapatan - $total_modal;
        $laba_bersih = $laba_kotor; // belum ada biaya lain

        return view('laporan.index', compact(
            'transaksi',
            'detail',
            'total_pendapatan',
            'total_modal',
            'laba_kotor',
            'laba_bersih',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }
}
