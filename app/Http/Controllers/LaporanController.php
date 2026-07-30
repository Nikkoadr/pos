<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\PembelianBarang;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Laporan Penjualan Umum
     */
    public function penjualanUmum(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        $query = Transaksi::with('detail_transaksi')
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'umum')
            ->whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir);

        $transaksi = $query->get();

        $laporan = $transaksi->map(function ($t) {
            $omzet = 0;
            $hpp = 0;
            foreach ($t->detail_transaksi as $item) {
                $omzet += $item->harga_jual * $item->qty;
                $hpp += ($item->harga_modal ?? 0) * $item->qty;
            }
            return (object) [
                'id' => $t->id,
                'tanggal' => $t->created_at,
                'omzet' => $omzet,
                'hpp' => $hpp,
                'laba_bersih' => $omzet - $hpp,
            ];
        });

        $total_pendapatan = $laporan->sum('omzet');
        $total_hpp = $laporan->sum('hpp');
        $total_laba_bersih = $laporan->sum('laba_bersih');

        return view('laporan.penjualan_umum', compact(
            'laporan',
            'total_pendapatan',
            'total_hpp',
            'total_laba_bersih',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }

    /**
     * Laporan Penjualan Member
     */
    public function penjualanMember(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        $query = Transaksi::with('detail_transaksi')
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'member')
            ->whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir);

        $transaksi = $query->get();

        $laporan = $transaksi->map(function ($t) {
            $omzet = 0;
            $hpp = 0;
            foreach ($t->detail_transaksi as $item) {
                $omzet += $item->harga_jual * $item->qty;
                $hpp += ($item->harga_modal ?? 0) * $item->qty;
            }
            return (object) [
                'id' => $t->id,
                'tanggal' => $t->created_at,
                'omzet' => $omzet,
                'hpp' => $hpp,
                'laba_bersih' => $omzet - $hpp,
            ];
        });

        $total_pendapatan = $laporan->sum('omzet');
        $total_hpp = $laporan->sum('hpp');
        $total_laba_bersih = $laporan->sum('laba_bersih');

        return view('laporan.penjualan_member', compact(
            'laporan',
            'total_pendapatan',
            'total_hpp',
            'total_laba_bersih',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }

    /**
     * Laporan Servis
     */
    public function servis(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        $query = Transaksi::with('detail_transaksi')
            ->where('status', 'selesai')
            ->where('jenis_transaksi', 'servis')
            ->whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir);

        $transaksi = $query->get();

        $laporan = $transaksi->map(function ($t) {
            $omzet = 0;
            $servis = DetailTransaksi::where('id_transaksi', $t->id)->first();
            if ($servis) {
                $omzet = $servis->harga_jual;
            }
            return (object) [
                'id' => $t->id,
                'tanggal' => $t->created_at,
                'omzet' => $omzet,
                'hpp' => 0,
                'laba_bersih' => $omzet,
            ];
        });

        $total_pendapatan = $laporan->sum('omzet');
        $total_laba_bersih = $laporan->sum('laba_bersih');

        return view('laporan.servis', compact(
            'laporan',
            'total_pendapatan',
            'total_laba_bersih',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }

    /**
     * Laporan Pembelian
     */
    public function pembelian(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        $pembelianQuery = PembelianBarang::with(['barang', 'supplier'])
            ->whereDate('tanggal_pembelian', '>=', $tanggal_awal)
            ->whereDate('tanggal_pembelian', '<=', $tanggal_akhir);

        $pembelian = $pembelianQuery->get();

        $detailPembelian = $pembelian->map(function ($p) {
            return (object) [
                'id'             => $p->id,
                'kode_pembelian' => $p->kode_pembelian,
                'tanggal'        => $p->tanggal_pembelian,
                'nama_barang'    => $p->barang ? $p->barang->nama : '-',
                'qty'            => $p->qty,
                'harga_modal'    => $p->harga_modal,
                'subtotal'       => $p->qty * $p->harga_modal,
                'supplier'       => $p->supplier ? $p->supplier->nama_supplier ?? $p->supplier->nama : '-',
            ];
        });

        $total_pembelian = $detailPembelian->sum('subtotal');
        $total_qty = $detailPembelian->sum('qty');

        return view('laporan.pembelian', compact(
            'detailPembelian',
            'total_pembelian',
            'total_qty',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }
}
