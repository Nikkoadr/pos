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
        $query = Transaksi::with('detail_transaksi')
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

            // ✅ HITUNG TOTAL REAL (tanpa subtotal)
            ->editColumn('total_belanja', function ($row) {

                $total = $row->detail_transaksi->sum(function ($item) {
                    return $item->harga_jual * $item->qty;
                });

                return 'Rp ' . number_format($total, 0, ',', '.');
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

        // 2. Ambil rincian barang/jasa
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        // 3. Hitung Grand Total dari harga_jual * qty
        $grandTotal = $detail->sum(function ($item) {
            return $item->harga_jual * $item->qty;
        });

        // 4. Jika transaksi servis
        $servis = null;
        if ($transaksi->jenis_transaksi == 'servis') {

            $servis = \App\Models\DetailTransaksiServis::where('id_transaksi', $id)->first();

            // fallback kalau detail kosong
            if ($grandTotal == 0 && $servis) {
                $grandTotal = $servis->harga_jual ?? 0;
            }
        }

        return view('arsip.detail', compact(
            'transaksi',
            'detail',
            'servis',
            'grandTotal'
        ));
    }
}
