<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nota;

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
    
    public function laporan()
    {
        $nota = Nota::all();
        $total_belanja = $nota->sum('total_belanja');

        return view('laporan', compact('nota', 'total_belanja'));
    }

    public function filter(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $nota = Nota::query();

        if ($tanggal) {
            $nota->whereDate('tanggal_transaksi', $tanggal);
        }

        if ($bulan) {
            $nota->whereMonth('tanggal_transaksi', $bulan);
        }

        if ($tahun) {
            $nota->whereYear('tanggal_transaksi', $tahun);
        }

        $nota = $nota->get();
        $total_belanja = $nota->sum('total_belanja');

        return view('laporan', compact('nota', 'total_belanja', 'tanggal', 'bulan', 'tahun'));
    }
}
