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

        $data_servis = DetailTransaksiServis::orderBy('created_at', 'DESC')->get();

        return view('servis.index', compact('data_servis'));
    }

    public function updateStatusServis(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:detail_transaksi_servis,id',
            'status' => 'required|in:masuk,proses,selesai,dibatalkan,diambil'
        ]);

        DB::beginTransaction();

        try {
            $servis = DetailTransaksiServis::findOrFail($request->id);

            if ($servis->status_servis === $request->status) {
                return redirect()->back()->with('success', 'Status tidak berubah');
            }

            $servis->status_servis = $request->status;

            if ($request->status == 'dibatalkan') {

                DB::table('transaksi')
                    ->where('id', $servis->id_transaksi)
                    ->update([
                        'status' => 'dibatalkan',
                        'updated_at' => now()
                    ]);

                $keranjang = Keranjang::where('id_transaksi', $servis->id_transaksi)->get();

                foreach ($keranjang as $item) {

                    if (!is_null($item->id_barang)) {
                        DB::table('data_barang')
                            ->where('id', $item->id_barang)
                            ->increment('qty', $item->qty);
                    }

                    DB::table('detail_transaksi')->insert([
                        'id_transaksi' => $item->id_transaksi,
                        'id_barang'    => $item->id_barang,
                        'nama_barang'  => $item->nama,
                        'qty'          => $item->qty,
                        'harga_jual'   => $item->harga_jual,
                        'harga_modal'  => $item->harga_modal,
                        'status'       => 'dibatalkan',
                        'created_at'   => now(),
                        'updated_at'   => now()
                    ]);
                }

                Keranjang::where('id_transaksi', $servis->id_transaksi)->delete();
            }

            $servis->updated_at = now();
            $servis->save();

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Status servis berhasil diubah menjadi ' . strtoupper($request->status)
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
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

        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        if ($servis) {
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

        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        if (!$servis) {
            return back()->with('error', 'Data servis belum diisi!');
        }

        $transaksi->status = 'proses';
        $transaksi->save();

        $servis->status_servis = 'proses';
        $servis->tanggal_dikerjakan = now();
        $servis->save();

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
            $setting = Setting::first();
            if (!$setting || !$setting->nama_printer) {
                return;
            }

            $connector = new WindowsPrintConnector($setting->nama_printer);
            $printer = new Printer($connector);

            for ($cetak = 1; $cetak <= 2; $cetak++) {

                $printer->initialize();

                // ================= LOGO =================
                $logoPath = public_path('assets/dist/img/logo_print.png');
                if (file_exists($logoPath)) {
                    try {
                        $logo = EscposImage::load($logoPath, true);
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->bitImage($logo);
                    } catch (\Exception $e) {
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->setTextSize(2, 2);
                        $printer->setEmphasis(true);
                        $printer->text("ANGEL CELL\n");
                        $printer->setTextSize(1, 1);
                        $printer->setEmphasis(false);
                    }
                }

                // ================= HEADER =================
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
                $printer->text("Kecamatan Losarang\n");
                $printer->text($tanggal . "\n");

                $printer->text("-----------------------------------------------\n");

                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Kasir  : $namakasir\n");
                $printer->text("Jenis  : Servis\n");

                // ================= DATA SERVIS =================
                if ($servis) {

                    $tipe = strlen($servis->tipe) > 15
                        ? substr($servis->tipe, 0, 15) . '..'
                        : $servis->tipe;

                    $printer->text("Merk   : {$servis->merk}\n");
                    $printer->text("Tipe   : $tipe\n");
                    $printer->text("No HP  : {$servis->nohp}\n");
                    $printer->text("Nama   : {$servis->nama}\n");

                    $alamat = wordwrap($servis->alamat, 30, "\n          ");
                    $printer->text("Alamat : $alamat\n");

                    $printer->text("Security : {$servis->security}\n");

                    $printer->text("-----------------------------------------------\n");

                    $printer->text("Kondisi   : {$servis->kondisi}\n");

                    $kerusakan = wordwrap($servis->kerusakan, 30, "\n            ");
                    $printer->text("Kerusakan : $kerusakan\n\n");
                }

                $grandtotal = 0;

                if ($keranjang->isEmpty()) {
                    $printer->text("- Belum ada item\n");
                } else {
                    foreach ($keranjang as $item) {
                        $subtotal = $item->harga_jual * $item->qty;
                        $grandtotal += $subtotal;
                    }
                }

                // ================= TOTAL =================
                $printer->text("------------------------------------------------\n");

                // ================= ESTIMASI =================
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->setEmphasis(true);
                $printer->setTextSize(2, 2); // Membuat teks besar (double width & height)

                // Catatan: Pastikan total karakter (ESTIMASI + Nominal) tidak lebih dari 22 karakter 
                // agar tetap dalam satu baris pada mode teks besar.
                $printer->text("ESTIMASI: Rp " . number_format($grandtotal, 0, '.', '.') . "\n");

                // Kembalikan ke ukuran normal
                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);
                $printer->setJustification(Printer::JUSTIFY_LEFT); // Reset rata kiri untuk teks berikutnya

                // ================= FOOTER =================
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                $printer->setEmphasis(true);
                $printer->text("!! PERHATIAN !!\n");
                $printer->setEmphasis(false);

                $printer->text("Nota wajib dibawa saat pengambilan\n");
                $printer->text("Jika hilang wajib KTP\n");
                $printer->text("1 bulan tidak diambil bukan tanggung jawab kami\n");

                $printer->feed();

                $printer->setEmphasis(true);
                $printer->text("TERIMA KASIH\n");
                $printer->setEmphasis(false);

                $printer->feed(3);
                $printer->cut();
            }

            $printer->close();
        } catch (\Exception $e) {
            Log::error("Gagal mencetak nota servis: " . $e->getMessage());
            return back()->with('error', 'Gagal mencetak nota servis. Pastikan printer terhubung.');
        }
    }

    public function pembayaran_servis($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $detail_servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        $keranjang = Keranjang::where('id_transaksi', $id)->get();

        // hitung total dari harga_jual × qty
        $total_keranjang = $keranjang->sum(function ($item) {
            return $item->harga_jual * $item->qty;
        });

        $nama_member = null;

        if ($transaksi->id_member) {
            $member = Data_member::find($transaksi->id_member);
            $nama_member = $member ? $member->nama_member : null;
        }

        return view('servis.pembayaran_servis', [
            'transaksi' => $transaksi,
            'detail_servis' => $detail_servis,
            'keranjang' => $keranjang,
            'total_keranjang' => $total_keranjang,
            'nama_member' => $nama_member
        ]);
    }

    public function selesaikan_servis(Request $request)
    {
        $transaksi = Transaksi::find($request->id_transaksi);

        if (!$transaksi) {
            return back()->with('error', 'Tidak ada transaksi yang ditemukan!');
        }

        $keranjang = Keranjang::where('id_transaksi', $transaksi->id)->get();

        if ($keranjang->isEmpty()) {
            return back()->with('error', 'Keranjang masih kosong!');
        }

        // hitung total dari subtotal biar konsisten
        $total = $keranjang->sum('subtotal');

        // validasi pembayaran
        if ($request->bayar < $total) {
            return back()->with('error', 'Pembayaran kurang!');
        }

        foreach ($keranjang as $item) {

            // hitung subtotal ulang (biar aman)
            $subtotal = $item->harga_jual * $item->qty;

            DetailTransaksi::create([
                'id_transaksi' => $item->id_transaksi,
                'id_barang'    => $item->id_barang, // boleh null (manual)
                'nama_barang'  => $item->nama,
                'harga_jual'   => $item->harga_jual,
                'harga_modal'  => $item->harga_modal ?? 0,
                'qty'          => $item->qty,
            ]);
        }

        // update transaksi
        $transaksi->update([
            'status'         => 'selesai',
            'total_belanja'  => $total,
            'bayar'          => $request->bayar,
            'kembalian'      => $request->kembalian,
        ]);

        // update status servis
        $servis = DetailTransaksiServis::where('id_transaksi', $transaksi->id)->first();

        if ($servis) {
            $servis->update([
                'status_servis'   => 'diambil',
                'tanggal_diambil' => now()
            ]);
        }

        // cetak nota
        $this->printServisDiambil($transaksi->id);

        // kosongkan keranjang
        Keranjang::where('id_transaksi', $transaksi->id)->delete();

        return redirect('/transaksi')->with('sukses', 'Servis selesai dan nota dicetak!');
    }

    private function printServisDiambil($id)
    {
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();
        $items = DetailTransaksi::where('id_transaksi', $id)->get();
        $transaksi = Transaksi::find($id);

        $namakasir = auth()->user()->nama;
        $tanggal = now()->format('d M Y H:i:s');

        // HITUNG TOTAL BARU
        $grandTotal = $items->sum(function ($item) {
            return $item->harga_jual * $item->qty;
        });

        try {
            $setting = Setting::first();
            if (!$setting || !$setting->nama_printer) {
                return;
            }

            $connector = new WindowsPrintConnector($setting->nama_printer);
            $printer = new Printer($connector);
            $printer->initialize();

            // ================= LOGO =================
            $logoPath = public_path('assets/dist/img/logo_print.png');
            if (file_exists($logoPath)) {
                try {
                    $logo = EscposImage::load($logoPath, true);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->bitImage($logo);
                } catch (\Exception $e) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setEmphasis(true);
                    $printer->setTextSize(2, 2);
                    $printer->text("ANGEL CELL\n");
                    $printer->setTextSize(1, 1);
                    $printer->setEmphasis(false);
                }
            }

            // ================= HEADER =================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
            $printer->text("Kecamatan Losarang\n");
            $printer->text($tanggal . "\n");

            $printer->text("-----------------------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No. Nota : #$id\n");
            $printer->text("Kasir    : $namakasir\n");
            $printer->text("Pelanggan: " . ($servis->nama ?? 'Umum') . "\n");

            if ($servis) {
                $tipe = strlen($servis->tipe) > 15
                    ? substr($servis->tipe, 0, 15) . '..'
                    : $servis->tipe;

                $printer->text("Unit     : {$servis->merk} - $tipe\n");
            }
            $printer->text("-----------------------------------------------\n");
            // ================= TOTAL =================
            $printer->setJustification(Printer::JUSTIFY_RIGHT);

            // 1. Baris TOTAL (Besar & Tebal)
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2); 
            // Pada ukuran (2,2), lebar kertas jadi terbatas (sekitar 22-24 karakter)
            $printer->text("TOTAL: Rp " . number_format($grandTotal, 0, '.', '.') . "\n");

            // 2. Baris BAYAR & KEMBALI (Ukuran Normal)
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);

            // Gunakan %48s agar teks didorong sejauh 48 karakter ke kanan
            $printer->text(sprintf("%48s\n", "BAYAR: Rp " . number_format($transaksi->bayar, 0, '.', '.')));
            $printer->text(sprintf("%48s\n", "KEMBALI: Rp " . number_format($transaksi->kembalian, 0, '.', '.')));

            // ================= STATUS =================
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("LUNAS\n");
            $printer->setEmphasis(false);

            // ================= FOOTER =================
            $printer->feed();
            $printer->text("Barang yang sudah diservis\n");
            $printer->text("memiliki garansi sesuai kesepakatan\n");
            $printer->text("segel rusak garansi hangus\n");
            $printer->feed();
            $printer->setEmphasis(true);
            $printer->text("TERIMA KASIH\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();

            $printer->close();
        } catch (\Exception $e) {
            Log::error("Gagal cetak nota: " . $e->getMessage());
        }
    }
}
