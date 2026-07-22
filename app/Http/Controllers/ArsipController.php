<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\DetailTransaksiServis;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('arsip.index');
    }

    public function data_arsip(Request $request)
    {
        // Ambil parameter DataTables
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        // Mapping kolom untuk sorting
        $columnMap = [
            0 => 'id',
            1 => 'tanggal_transaksi',
            2 => 'jenis_transaksi',
            3 => 'kasir',
            4 => 'nama_pelanggan',
            5 => 'total_belanja',
            6 => 'id'
        ];
        $orderBy = isset($columnMap[$orderColumn]) ? $columnMap[$orderColumn] : 'tanggal_transaksi';
        if ($orderBy == 'nama_pelanggan') {
            $orderBy = 'tanggal_transaksi'; // fallback
        }

        // Query dasar
        $query = Transaksi::with([
            'member',
            'detailTransaksiServis',
            'detail_transaksi'
        ])->where('status', 'selesai');

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->jenis);
        }

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        // Total records (tanpa filter search)
        $totalQuery = clone $query;
        $recordsTotal = $totalQuery->count();

        // Search filter
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($search, $searchLower) {
                $q->where('transaksi.id', 'like', "%{$search}%")
                    ->orWhere('transaksi.kasir', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($m) use ($searchLower) {
                        $m->whereRaw('LOWER(nama_member) LIKE ?', ['%' . $searchLower . '%']);
                    })
                    ->orWhereHas('detailTransaksiServis', function ($s) use ($searchLower) {
                        $s->whereRaw('LOWER(nama) LIKE ?', ['%' . $searchLower . '%']);
                    });
            });
        }

        // Count filtered
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Sorting
        if (in_array($orderBy, ['tanggal_transaksi', 'kasir', 'total_belanja', 'id'])) {
            $query->orderBy($orderBy, $orderDir);
        } else {
            $query->orderBy('tanggal_transaksi', 'desc');
        }

        // Pagination
        $data = $query->skip($start)->take($length)->get();

        // Format data
        $result = [];
        $counter = $start + 1;
        foreach ($data as $row) {
            // Nama pelanggan
            if ($row->jenis_transaksi == 'servis') {
                $pelanggan = optional($row->detailTransaksiServis)->nama ?? '-';
            } elseif ($row->jenis_transaksi == 'member') {
                $pelanggan = optional($row->member)->nama_member ?? '-';
            } else {
                $pelanggan = 'Umum';
            }

            // Jenis transaksi dengan badge
            switch ($row->jenis_transaksi) {
                case 'umum':
                    $jenis = '<span class="badge badge-success"><i class="fas fa-shopping-cart"></i> Penjualan Umum</span>';
                    break;
                case 'member':
                    $jenis = '<span class="badge badge-warning"><i class="fas fa-user"></i> Penjualan Member</span>';
                    break;
                case 'servis':
                    $jenis = '<span class="badge badge-primary"><i class="fas fa-tools"></i> Servis</span>';
                    break;
                default:
                    $jenis = '-';
            }

            // Total
            if ($row->jenis_transaksi == 'servis') {
                $total = optional($row->detailTransaksiServis)->harga_jual ?? 0;
            } else {
                $total = $row->detail_transaksi->sum(function ($item) {
                    return $item->harga_jual * $item->qty;
                });
            }

            $result[] = [
                'DT_RowIndex' => $counter++,
                'tanggal_transaksi' => \Carbon\Carbon::parse($row->tanggal_transaksi)->format('d/m/Y H:i'),
                'jenis_transaksi' => $jenis,
                'kasir' => $row->kasir,
                'nama_pelanggan' => $pelanggan,
                'total_belanja' => 'Rp ' . number_format($total, 0, ',', '.'),
                'action' => '<a href="' . route('arsip.show', $row->id) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>',
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        ]);
    }

    public function show($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $detailItems = collect();
        $servisData = null;
        $grandTotal = 0;

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

        return view('arsip.detail', compact('transaksi', 'detailItems', 'servisData', 'grandTotal'));
    }
}
