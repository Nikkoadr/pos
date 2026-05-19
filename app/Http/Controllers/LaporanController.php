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
        $transaksi = Transaksi::with('detail_transaksi')
            ->where('status', 'selesai')
            ->whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir)
            ->get();
        $laporan = $transaksi->map(function ($t) {
            $omzet = 0;
            $hpp = 0;

            foreach ($t->detail_transaksi as $item) {
                $omzet += $item->harga_jual * $item->qty;
                $hpp += ($item->harga_modal ?? 0) * $item->qty;
            }

            return (object) [
                'id' => $t->id,
                'kode_transaksi' => $t->kode_transaksi,
                'tanggal' => $t->created_at,
                'omzet' => $omzet,
                'hpp' => $hpp,
                'laba_kotor' => $omzet - $hpp,
            ];
        });
        $total_pendapatan = $laporan->sum('omzet');
        $total_hpp = $laporan->sum('hpp');
        $total_laba_kotor = $laporan->sum('laba_kotor');

        return view('laporan.index', compact(
            'laporan',
            'total_pendapatan',
            'total_hpp',
            'total_laba_kotor',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }
}
