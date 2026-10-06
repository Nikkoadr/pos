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

        $omzetHpp = function ($q) {
            $row = (clone $q)->selectRaw(
                'COALESCE(SUM(detail_transaksi.harga_jual * detail_transaksi.qty), 0) as omzet, ' .
                'COALESCE(SUM(COALESCE(detail_transaksi.harga_modal, 0) * detail_transaksi.qty), 0) as hpp'
            )->first();
            return [(float) ($row->omzet ?? 0), (float) ($row->hpp ?? 0)];
        };
        $baseSelesai = function () {
            return \DB::table('detail_transaksi')
                ->join('transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
                ->where('transaksi.status', 'selesai');
        };

        [$omzet_hari, $hpp_hari] = $omzetHpp($baseSelesai()->whereDate('transaksi.tanggal_transaksi', $hari_ini));
        [$omzet_bulan, $hpp_bulan] = $omzetHpp($baseSelesai()->whereMonth('transaksi.tanggal_transaksi', $bulan_ini)->whereYear('transaksi.tanggal_transaksi', $tahun_ini));
        [$omzet_tahun, $hpp_tahun] = $omzetHpp($baseSelesai()->whereYear('transaksi.tanggal_transaksi', $tahun_ini));

        $pendapatan_hari_ini = $omzet_hari;
        $pendapatan_bulan_ini = $omzet_bulan;
        $pendapatan_tahun_ini = $omzet_tahun;
        $laba_hari_ini = $omzet_hari - $hpp_hari;
        $laba_bulan_ini = $omzet_bulan - $hpp_bulan;
        $laba_tahun_ini = $omzet_tahun - $hpp_tahun;

        $trx_hari_ini = Transaksi::where('status', 'selesai')->whereDate('tanggal_transaksi', $hari_ini)->count();
        $trx_bulan_ini = Transaksi::where('status', 'selesai')->whereMonth('tanggal_transaksi', $bulan_ini)->whereYear('tanggal_transaksi', $tahun_ini)->count();

        // Grafik harian 30 hari terakhir
        $grafikQuery = \DB::table('detail_transaksi')
            ->join('transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->whereDate('transaksi.tanggal_transaksi', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('DATE(transaksi.tanggal_transaksi) as tgl, SUM(detail_transaksi.harga_jual * detail_transaksi.qty) as omzet, SUM(COALESCE(detail_transaksi.harga_modal, 0) * detail_transaksi.qty) as hpp')
            ->groupBy('tgl')
            ->get()
            ->keyBy('tgl');
        $bangunGrafik = function ($hari) use ($grafikQuery) {
            $labels = [];
            $omzet = [];
            $hpp = [];
            $laba = [];
            for ($i = $hari - 1; $i >= 0; $i--) {
                $d = now()->subDays($i);
                $labels[] = $d->format('d M');
                $row = $grafikQuery->get($d->toDateString());
                $o = (float) ($row->omzet ?? 0);
                $h = (float) ($row->hpp ?? 0);
                $omzet[] = $o;
                $hpp[] = $h;
                $laba[] = $o - $h;
            }
            return compact('labels', 'omzet', 'hpp', 'laba');
        };
        $grafik_7 = $bangunGrafik(7);
        $grafik_30 = $bangunGrafik(30);

        // Komposisi jenis transaksi bulan ini
        $donut = Transaksi::where('status', 'selesai')
            ->whereMonth('tanggal_transaksi', $bulan_ini)
            ->whereYear('tanggal_transaksi', $tahun_ini)
            ->selectRaw("jenis_transaksi, COUNT(*) as total")
            ->groupBy('jenis_transaksi')
            ->pluck('total', 'jenis_transaksi');
        $donut_umum = (int) ($donut['umum'] ?? 0);
        $donut_member = (int) ($donut['member'] ?? 0);
        $donut_servis = (int) ($donut['servis'] ?? 0);

        // Produk terlaris bulan ini
        $produk_terlaris = \DB::table('detail_transaksi')
            ->join('transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->whereMonth('transaksi.tanggal_transaksi', $bulan_ini)
            ->whereYear('transaksi.tanggal_transaksi', $tahun_ini)
            ->selectRaw('detail_transaksi.nama_barang as nama, SUM(detail_transaksi.qty) as terjual, SUM(detail_transaksi.harga_jual * detail_transaksi.qty) as omzet')
            ->groupBy('detail_transaksi.nama_barang')
            ->orderByDesc('terjual')
            ->take(5)
            ->get();

        // Transaksi terbaru + totalnya (1 query, tanpa N+1)
        $transaksi_terbaru = Transaksi::where('transaksi.status', 'selesai')
            ->leftJoin('detail_transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->orderByDesc('transaksi.tanggal_transaksi')
            ->groupBy('transaksi.id', 'transaksi.jenis_transaksi', 'transaksi.kasir', 'transaksi.tanggal_transaksi')
            ->selectRaw('transaksi.id, transaksi.jenis_transaksi, transaksi.kasir, transaksi.tanggal_transaksi, COALESCE(SUM(detail_transaksi.harga_jual * detail_transaksi.qty), 0) as total')
            ->take(6)
            ->get();

        $servis_masuk = DetailTransaksiServis::where('status_servis', 'masuk')->count();
        $servis_proses = DetailTransaksiServis::whereIn('status_servis', ['masuk', 'proses'])->count();
        $stok_limit = Data_barang::where('qty', '<', 5)->count();
        $stok_menipis = Data_barang::where('qty', '<', 5)->orderBy('qty')->take(8)->get();
        $servis_terbaru = DetailTransaksiServis::whereIn('status_servis', ['masuk', 'proses'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        return view('home', compact(
            'pendapatan_hari_ini',
            'pendapatan_bulan_ini',
            'pendapatan_tahun_ini',
            'laba_hari_ini',
            'laba_bulan_ini',
            'laba_tahun_ini',
            'trx_hari_ini',
            'trx_bulan_ini',
            'grafik_7',
            'grafik_30',
            'donut_umum',
            'donut_member',
            'donut_servis',
            'produk_terlaris',
            'transaksi_terbaru',
            'servis_masuk',
            'servis_proses',
            'stok_limit',
            'stok_menipis',
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
        // Validasi data, sesuaikan nama field dengan yang ada di form (nama_perinter)
        $validatedData = $request->validate([
            'nama_toko'    => ['required', 'string', 'max:255'],
            'alamat_toko'  => ['required', 'string', 'max:255'],
            'nama_printer' => ['required', 'string', 'max:255'], // sesuaikan dengan nama di form
        ]);

        try {
            // Cari data atau gagal (404)
            $setting = Setting::findOrFail($id);

            // Update data
            $setting->update($validatedData);

            // Redirect ke halaman setting dengan pesan sukses
            return redirect('setting')->with('success', 'Pengaturan berhasil diubah.');
        } catch (\Exception $e) {
            // Jika terjadi error (misal database error), redirect back dengan pesan error
            return redirect()->back()
                ->withInput() // agar data yang sudah diisi tetap ada
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
