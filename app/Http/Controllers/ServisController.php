<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksiServis;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use App\Models\Keranjang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Data_member;
use App\Models\DetailTransaksi;
use App\Models\Setting;

class ServisController extends Controller
{
    public function index()
    {
        // Mengambil data servis terbaru (descending)
        // Kita gunakan get() untuk mengambil semua data agar bisa ditampilkan di DataTable
        $data_servis = DetailTransaksiServis::orderBy('created_at', 'DESC')->get();

        // Mengirim data ke view
        return view('servis.index', compact('data_servis'));
    }

    /**
     * Fungsi untuk update status servis (Logic yang dipicu tombol di View)
     */
    public function updateStatusServis(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:detail_transaksi_servis,id',
            'status' => 'required|in:masuk,proses,selesai,dibatalkan,diambil'
        ]);

        $servis = DetailTransaksiServis::findOrFail($request->id);

        // kalau status sama, stop
        if ($servis->status_servis === $request->status) {
            return redirect()->back()->with('sukses', 'Status tidak berubah');
        }

        $servis->status_servis = $request->status;

        // =========================
        // HANDLE BATAL (INTI LOGIKA)
        // =========================
        if ($request->status == 'dibatalkan') {

            // 1. update transaksi utama
            DB::table('transaksi')
                ->where('id', $servis->id_transaksi)
                ->update([
                    'status' => 'dibatalkan',
                    'updated_at' => now()
                ]);

            $keranjang = Keranjang::where('id_transaksi', $servis->id_transaksi)->get();

            // pindahkan ke detail_transaksi dengan status dibatalkan
            foreach ($keranjang as $item) {
                DB::table('detail_transaksi')->insert([
                    'id_transaksi' => $item->id_transaksi,
                    'id_barang'    => $item->id_barang,
                    'nama_barang'  => $item->nama,
                    'qty'          => 0,
                    'harga'        => 0,
                    'subtotal'     => 0,
                    'status'       => 'dibatalkan',
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            // hapus keranjang setelah dipindahkan
            DB::table('keranjang')
                ->where('id_transaksi', $servis->id_transaksi)
                ->delete();
        }

        // =========================
        // UPDATE AUTO TIMESTAMP GLOBAL
        // =========================
        $servis->updated_at = now();

        $servis->save();

        return redirect()->back()->with(
            'sukses',
            'Status servis berhasil diubah menjadi ' . strtoupper($request->status)
        );
    }

    public function transaksiServis($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        return view('servis.buat_servis', compact('transaksi', 'servis'));
    }

    public function store_servis(Request $request, $id)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'nama' => 'required',
            'nohp' => 'required',
            'merk' => 'required',
            'kerusakan' => 'required',
        ]);

        // 🔥 cek apakah sudah ada data servis
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        if ($servis) {
            // ================= UPDATE =================
            $servis->update([
                'tanggal_masuk' => $request->tanggal_masuk,
                'nama' => $request->nama,
                'nohp' => $request->nohp,
                'alamat' => $request->alamat,
                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'kerusakan' => $request->kerusakan,
                'kondisi' => $request->kondisi,
                'security' => $request->security,
            ]);

            $message = 'Servis berhasil diupdate!';
        } else {
            // ================= CREATE =================
            DetailTransaksiServis::create([
                'id_transaksi' => $id,
                'kode_servis' => 'SRV-' . date('YmdHis'),

                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_dikerjakan' => null,
                'tanggal_diambil' => null,

                'nama' => $request->nama,
                'nohp' => $request->nohp,
                'alamat' => $request->alamat,

                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'kerusakan' => $request->kerusakan,

                'kondisi' => $request->kondisi,
                'security' => $request->security,
            ]);

            $message = 'Servis berhasil ditambahkan!';
        }

        return redirect('/proses_transaksi_' . $id)
            ->with('sukses', $message);
    }

    public function proses_servis($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // 🔥 ambil data servis
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        if (!$servis) {
            return back()->with('error', 'Data servis belum diisi!');
        }

        // =========================
        // UPDATE STATUS
        // =========================

        $transaksi->status = 'proses';
        $transaksi->save();

        // servis masuk ke proses pengerjaan
        $servis->status_servis = 'proses';
        $servis->tanggal_dikerjakan = now();
        $servis->save();

        // =========================
        // CETAK NOTA
        // =========================
        $this->printServis($id);

        return redirect('/transaksi')
            ->with('sukses', 'Servis berhasil diproses dan nota tercetak!');
    }

    private function printServis($id)
    {
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();
        $keranjang = Keranjang::where('id_transaksi', $id)->get();

        $namakasir = auth()->user()->nama;
        $tanggal = now()->format('d M Y H:i:s');

        try {
            $connector = new WindowsPrintConnector(Setting::first()->nama_perinter);
            $printer = new Printer($connector);

            $jasa = $keranjang->filter(fn($i) => str_contains(strtolower($i->nama), 'jasa'));
            $barang = $keranjang->filter(fn($i) => !str_contains(strtolower($i->nama), 'jasa'));

            for ($cetak = 1; $cetak <= 2; $cetak++) {

                $printer->initialize();

                // ================= LOGO =================
                $logoPath = public_path('assets/dist/img/logo.png');
                if (file_exists($logoPath)) {
                    try {
                        $logo = EscposImage::load($logoPath, false);
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->graphics($logo);
                    } catch (\Exception $e) {
                        $printer->text("ANGEL CELL\n");
                    }
                }

                $printer->feed();

                // ================= HEADER =================
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
                $printer->text("Kecamatan Losarang\n");
                $printer->feed();
                $printer->text($tanggal . "\n");

                $printer->text("--------------------------------\n");

                // ================= ADMIN =================
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Kasir  : $namakasir\n");
                $printer->text("Jenis  : Servis\n");

                if ($servis) {

                    $tipe = strlen($servis->tipe) > 15 ? substr($servis->tipe, 0, 15) . '..' : $servis->tipe;

                    $printer->text("Tipe   : $tipe\n");
                    $printer->text("Merk   : {$servis->merk}\n");

                    $printer->text("No HP  : {$servis->nohp}\n");
                    $printer->text("Nama   : {$servis->nama}\n");

                    $printer->text("Alamat : {$servis->alamat}\n");

                    $printer->text("Security : {$servis->security}\n");

                    $printer->text("--------------------------------\n");

                    $printer->text("Kondisi   : {$servis->kondisi}\n");

                    // multiline kerusakan
                    $kerusakan = wordwrap($servis->kerusakan, 30, "\n            ");
                    $printer->text("Kerusakan : $kerusakan\n\n");
                }

                // ================= JASA =================
                $printer->text("Jasa:\n");
                $totaljasa = 0;

                foreach ($jasa as $j) {
                    $nama = strlen($j->nama) > 25 ? substr($j->nama, 0, 25) . '...' : $j->nama;
                    $printer->text("- $nama\n");
                    $totaljasa += $j->subtotal;
                }

                $printer->feed();

                // ================= BARANG =================
                $printer->text("Sparepart:\n");
                $totalbarang = 0;

                foreach ($barang as $b) {
                    $nama = strlen($b->nama) > 25 ? substr($b->nama, 0, 25) . '...' : $b->nama;
                    $printer->text("- $nama\n");
                    $totalbarang += $b->subtotal;
                }

                $printer->feed();

                // ================= TOTAL =================
                $grandtotal = $totaljasa + $totalbarang;

                $printer->text("--------------------------------\n");
                $printer->setEmphasis(true);
                $printer->text("ESTIMASI : Rp " . number_format($grandtotal, 0, '.', '.') . "\n");
                $printer->setEmphasis(false);

                // ================= FOOTER =================
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                $printer->text("!! PERHATIAN !!\n");
                $printer->text("Nota wajib dibawa saat pengambilan\n");
                $printer->text("Jika hilang wajib KTP\n");
                $printer->text("1 bulan tidak diambil bukan tanggung jawab\n");

                $printer->feed();
                $printer->text("TERIMA KASIH\n");

                $printer->feed(3);
                $printer->cut();
            }

            $printer->close();
        } catch (\Exception $e) {
            Log::error("Gagal mencetak nota servis: " . $e->getMessage());
            return back()->with('error', 'Gagal mencetak nota servis. Pastikan printer terhubung dan coba lagi.');
        }
    }

    public function pembayaran_servis($id)
    {
        // 1. Ambil transaksi
        $transaksi = Transaksi::findOrFail($id);

        // 2. Ambil data servis
        $detail_servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        // 4. Hitung keranjang tambahan
        $keranjang = Keranjang::where('id_transaksi', $id)->get();
        $total_keranjang = $keranjang->sum('subtotal');

        // 6. Optional nama member
        $nama_member = null;
        if ($transaksi->id_member) {
            $member = Data_member::find($transaksi->id_member);
            $nama_member = $member ? $member->nama_member : null;
        }

        // 7. kirim ke view
        return view('servis.pembayaran_servis', compact(
            'transaksi',
            'detail_servis',
            'keranjang',
            'total_keranjang',
            'nama_member'
        ));
    }

    public function selesaikan_servis(Request $request)
    {
        $transaksi = Transaksi::where('id', $request->id_transaksi)->first();
        if (!$transaksi) {
            return back()->with('error', 'Tidak ada transaksi yang ditemukan!');
        }

        $keranjang = Keranjang::where('id_transaksi', $transaksi->id)->get();
        if ($keranjang->isEmpty()) {
            return back()->with('error', 'Keranjang masih kosong!');
        }

        // 1. Pindahkan data ke DetailTransaksi
        foreach ($keranjang as $item) {
            DetailTransaksi::create([
                'id_transaksi' => $item->id_transaksi,
                'id_barang'    => $item->id_barang,
                'nama_barang'         => $item->nama,
                'harga'        => $item->harga,
                'qty'          => $item->qty,
                'subtotal'     => $item->subtotal,
            ]);
        }

        // 2. Update status transaksi & simpan nominal bayar
        $transaksi->status = 'selesai';
        $transaksi->bayar = $request->bayar;
        $transaksi->kembalian = $request->kembalian;
        $transaksi->save();

        // 3. Update status detail servis
        $servis = DetailTransaksiServis::where('id_transaksi', $transaksi->id)->first();
        if ($servis) {
            $servis->status_servis = 'diambil';
            $servis->tanggal_diambil = now();
            $servis->save();
        }

        // 4. PANGGIL FUNGSI CETAK (Sebelum keranjang dihapus agar data filter tetap jalan)
        // Atau bisa juga ambil dari DetailTransaksi di dalam fungsi printServis
        $this->printServisDiambil($transaksi->id);

        // 5. Hapus data di keranjang
        Keranjang::where('id_transaksi', $transaksi->id)->delete();

        return redirect('/transaksi')->with('sukses', 'Servis selesai dan Nota dicetak!');
    }

    private function printServisDiambil($id)
    {
        // Mengambil data dari DetailTransaksi karena keranjang akan dihapus
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();
        $items = DetailTransaksi::where('id_transaksi', $id)->get();
        $transaksi = Transaksi::find($id);

        $namakasir = auth()->user()->nama;
        $tanggal = now()->format('d M Y H:i:s');

        try {
            $connector = new WindowsPrintConnector(Setting::first()->nama_perinter);
            $printer = new Printer($connector);

            // Pisahkan jasa & barang berdasarkan nama
            $jasa = $items->filter(fn($i) => str_contains(strtolower($i->nama), 'jasa'));
            $barang = $items->filter(fn($i) => !str_contains(strtolower($i->nama), 'jasa'));

            for ($cetak = 1; $cetak <= 2; $cetak++) {
                $printer->initialize();

                // ================= LOGO =================
                $logoPath = public_path('assets/dist/img/logo.png');
                if (file_exists($logoPath)) {
                    try {
                        $logo = EscposImage::load($logoPath, false);
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->graphics($logo);
                    } catch (\Exception $e) {
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->setEmphasis(true);
                        $printer->text("ANGEL CELL\n");
                        $printer->setEmphasis(false);
                    }
                }

                // ================= HEADER =================
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
                $printer->text("Kecamatan Losarang\n");
                $printer->text($tanggal . "\n");
                $printer->text("--------------------------------\n");

                // ================= INFO NOTA =================
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("No. Nota : #$id\n");
                $printer->text("Kasir    : $namakasir\n");
                $printer->text("Pelanggan: " . ($servis->nama ?? 'Umum') . "\n");

                if ($servis) {
                    $tipe = strlen($servis->tipe) > 15 ? substr($servis->tipe, 0, 15) . '..' : $servis->tipe;
                    $printer->text("Unit     : {$servis->merk} - $tipe\n");
                }
                $printer->text("--------------------------------\n");

                // ================= JASA =================
                if ($jasa->count() > 0) {
                    $printer->setEmphasis(true);
                    $printer->text("Jasa:\n");
                    $printer->setEmphasis(false);
                    foreach ($jasa as $j) {
                        $nama = strlen($j->nama) > 20 ? substr($j->nama, 0, 20) . '..' : $j->nama;
                        $printer->text(sprintf("%-21s %10s\n", "- " . $nama, number_format($j->subtotal, 0, '.', '.')));
                    }
                }

                // ================= BARANG =================
                if ($barang->count() > 0) {
                    $printer->setEmphasis(true);
                    $printer->text("\nSparepart:\n");
                    $printer->setEmphasis(false);
                    foreach ($barang as $b) {
                        $nama = strlen($b->nama) > 20 ? substr($b->nama, 0, 20) . '..' : $b->nama;
                        $printer->text(sprintf("%-21s %10s\n", "- " . $nama, number_format($b->subtotal, 0, '.', '.')));
                    }
                }

                // ================= TOTAL =================
                $printer->text("--------------------------------\n");
                $printer->setEmphasis(true);
                $printer->text(sprintf("%-15s %15s\n", "GRAND TOTAL", "Rp " . number_format($transaksi->bayar + $transaksi->kembalian, 0, '.', '.')));
                $printer->setEmphasis(false);

                $printer->text(sprintf("%-15s %15s\n", "BAYAR", "Rp " . number_format($transaksi->bayar, 0, '.', '.')));
                $printer->text(sprintf("%-15s %15s\n", "KEMBALI", "Rp " . number_format($transaksi->kembalian, 0, '.', '.')));

                // ================= FOOTER =================
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("!! LUNAS !!\n");
                $printer->text("Barang yang sudah diservis\n");
                $printer->text("memiliki garansi sesuai kesepakatan\n");
                $printer->feed();
                $printer->text("TERIMA KASIH\n");

                $printer->feed(3);
                $printer->cut();
            }

            $printer->close();
        } catch (\Exception $e) {
            Log::error("Gagal cetak nota: " . $e->getMessage());
        }
    }
}
