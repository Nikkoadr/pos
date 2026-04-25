<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;

class ArsipController extends Controller
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
    // Halaman utama arsip
    public function index()
    {
        return view('arsip.index');
    }

    // Fungsi untuk mensuplai data ke DataTables (AJAX)
    public function data_arsip(Request $request)
    {
        // Kita hitung total belanja dari relasi detail_transaksi
        $query = Transaksi::withSum('detail_transaksi as total_riil', 'subtotal')
            ->where('status', 'selesai')
            ->orderBy('tanggal_transaksi', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_transaksi', function ($row) {
                return date('d/m/Y H:i', strtotime($row->tanggal_transaksi));
            })
            ->editColumn('jenis_transaksi', function ($row) {
                if ($row->jenis_transaksi == 'servis') {
                    return '<span class="badge badge-primary"><i class="fas fa-wrench"></i> Servis</span>';
                }
                return '<span class="badge badge-success"><i class="fas fa-shopping-cart"></i> Penjualan</span>';
            })
            // 🔥 KUNCINYA DI SINI: Gunakan 'total_riil' bukan 'total_belanja'
            ->editColumn('total_belanja', function ($row) {
                $nominal = $row->total_riil ?? 0;
                return 'Rp ' . number_format($nominal, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="btn-group">
                    <a href="' . route('arsip.show', $row->id) . '" class="btn btn-xs btn-info">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>';
            })
            ->rawColumns(['jenis_transaksi', 'action'])
            ->make(true);
    }

    // Detail Transaksi
    public function show($id)
    {
        // 1. Ambil data transaksi utama
        $transaksi = Transaksi::findOrFail($id);

        // 2. Ambil rincian barang/jasa dari detail_transaksi
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        // 3. Hitung Grand Total dari seluruh subtotal di rincian
        $grandTotal = $detail->sum('subtotal');

        // 4. Jika ini transaksi servis, ambil info unitnya untuk ditampilkan
        $servis = null;
        if ($transaksi->jenis_transaksi == 'servis') {
            $servis = \App\Models\DetailTransaksiServis::where('id_transaksi', $id)->first();

            // Antisipasi jika detail_transaksi kosong, ambil dari harga_jual di tabel servis
            if ($grandTotal == 0 && $servis) {
                $grandTotal = $servis->harga_jual;
            }
        }

        return view('arsip.detail', compact('transaksi', 'detail', 'servis', 'grandTotal'));
    }
}
