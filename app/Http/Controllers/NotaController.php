<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Detail_nota;
use App\Models\Data_barang;

class NotaController extends Controller
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

    public function riwayat_transaksi(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $riwayat_transaksi = DB::table('nota')
            ->whereRaw('MONTH(tanggal_transaksi)="' . $bulan . '"')
            ->whereRaw('YEAR(tanggal_transaksi)="' . $tahun . '"')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('riwayat_transaksi', compact('riwayat_transaksi', 'bulan', 'tahun'));
    }

    public function detail($id)
    {
        $nota = Nota::with('detailNota')->findOrFail($id);
        return view('invoice', compact('nota'));
    }

    public function hapus_nota($id)
    {
        $nota = Nota::findOrFail($id);
        $detailNota = Detail_nota::where('id_nota', $nota->id)->get();

        foreach ($detailNota as $detail) {
            $barang = Data_barang::findOrFail($detail->id_barang);
            $barang->qty += $detail->qty;
            $barang->save();
        }
        Detail_nota::where('id_nota', $nota->id)->delete();
        $nota->delete();
        return redirect()->back();
    }
}
