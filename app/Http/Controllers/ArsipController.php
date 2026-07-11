<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use App\Models\DetailTransaksiServis;

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
    public function index()
    {
        return view('arsip.index');
    }

    public function data_arsip(Request $request)
    {
        $query = Transaksi::with('detail_transaksi')
            ->where('status', 'selesai');

        // Filter jenis transaksi
        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->jenis);
        }

        // Filter tanggal (format YYYY-MM-DD)
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        $query->orderBy('tanggal_transaksi', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_transaksi', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_transaksi)
                    ->format('d/m/Y H:i');
            })
            ->editColumn('jenis_transaksi', function ($row) {
                switch ($row->jenis_transaksi) {
                    case 'umum':
                        return '<span class="badge badge-success"><i class="fas fa-shopping-cart"></i> Penjualan Umum</span>';
                    case 'member':
                        return '<span class="badge badge-warning"><i class="fas fa-user"></i> Penjualan Member</span>';
                    case 'servis':
                        return '<span class="badge badge-primary"><i class="fas fa-tools"></i> Servis</span>';
                    default:
                        return '';
                }
            })
            ->editColumn('total_belanja', function ($row) {
                if ($row->jenis_transaksi == 'servis') {
                    $servis = DetailTransaksiServis::where('id_transaksi', $row->id)->first();
                    $total = $servis ? $servis->harga_jual : 0;
                } else {
                    $total = $row->detail_transaksi->sum(function ($item) {
                        return $item->harga_jual * $item->qty;
                    });
                }
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '
            <div class="btn-group">
                <a href="' . route('arsip.show', $row->id) . '" class="btn btn-info btn-xs">
                    <i class="fas fa-eye"></i> Detail
                </a>
            </div>';
            })
            ->rawColumns(['jenis_transaksi', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $detailItems = collect();
        $servisData  = null;
        $grandTotal  = 0;

        if ($transaksi->jenis_transaksi == 'servis') {
            $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();
            if ($servis) {
                $item = (object) [
                    'nama_barang' => 'Jasa Servis',
                    'harga_jual'  => $servis->harga_jual,
                    'qty'         => 1,
                    'subtotal'    => $servis->harga_jual
                ];
                $detailItems->push($item);
                $grandTotal = $servis->harga_jual;
                $servisData = $servis;
            }
        } else {
            $detail = DetailTransaksi::where('id_transaksi', $id)->get();
            $detailItems = $detail->map(function ($item) {
                $item->subtotal = $item->harga_jual * $item->qty;
                return $item;
            });
            $grandTotal = $detail->sum(function ($item) {
                return $item->harga_jual * $item->qty;
            });
        }

        return view('arsip.detail', compact(
            'transaksi',
            'detailItems',
            'servisData',
            'grandTotal'
        ));
    }
}
