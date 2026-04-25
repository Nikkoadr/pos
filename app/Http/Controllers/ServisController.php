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

        $servis = DetailTransaksiServis::findOrFail($request->id);

        if ($servis->status_servis === $request->status) {
            return redirect()->back()->with('sukses', 'Status tidak berubah');
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

            DB::table('keranjang')
                ->where('id_transaksi', $servis->id_transaksi)
                ->delete();
        }

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
            $connector = new WindowsPrintConnector(Setting::first()->nama_printer);
            $printer = new Printer($connector);

            for ($cetak = 1; $cetak <= 2; $cetak++) {

                $printer->initialize();

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

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
                $printer->text("Kecamatan Losarang\n");
                $printer->text($tanggal . "\n");

                $printer->text("--------------------------------\n");

                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Kasir  : $namakasir\n");
                $printer->text("Jenis  : Servis\n");

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

                    $printer->text("--------------------------------\n");

                    $printer->text("Kondisi   : {$servis->kondisi}\n");

                    $kerusakan = wordwrap($servis->kerusakan, 30, "\n            ");
                    $printer->text("Kerusakan : $kerusakan\n\n");
                }

                $printer->setEmphasis(true);
                $printer->text("Sparepart & Jasa\n");
                $printer->setEmphasis(false);

                $printer->text("--------------------------------\n");

                $grandtotal = 0;

                if ($keranjang->isEmpty()) {
                    $printer->text("- Belum ada item\n");
                } else {
                    foreach ($keranjang as $item) {
                        $nama = strlen($item->nama) > 25
                            ? substr($item->nama, 0, 25) . '...'
                            : $item->nama;

                        $printer->text(sprintf(
                            "%-25s %10s\n",
                            $nama,
                            number_format($item->subtotal, 0, '.', '.')
                        ));

                        $grandtotal += $item->subtotal;
                    }
                }

                $printer->feed();
                $printer->text("--------------------------------\n");

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(2, 2);
                $printer->setEmphasis(true);

                $printer->text("ESTIMASI\n");
                $printer->text("Rp " . number_format($grandtotal, 0, '.', '.') . "\n");

                $printer->setTextSize(1, 1);
                $printer->setEmphasis(false);

                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                $printer->setEmphasis(true);
                $printer->text("!! PERHATIAN !!\n");
                $printer->setEmphasis(false);

                $printer->text("Nota wajib dibawa saat pengambilan\n");
                $printer->text("Jika hilang wajib KTP\n");
                $printer->text("1 bulan tidak diambil bukan tanggung jawab\n");

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
            return back()->with('error', 'Gagal mencetak nota servis. Pastikan printer terhubung dan coba lagi.');
        }
    }

    public function pembayaran_servis($id)
    {

        $transaksi = Transaksi::findOrFail($id);

        $detail_servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        $keranjang = Keranjang::where('id_transaksi', $id)->get();
        $total_keranjang = $keranjang->sum('subtotal');

        $nama_member = null;
        if ($transaksi->id_member) {
            $member = Data_member::find($transaksi->id_member);
            $nama_member = $member ? $member->nama_member : null;
        }

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

        $transaksi->status = 'selesai';
        $transaksi->bayar = $request->bayar;
        $transaksi->kembalian = $request->kembalian;
        $transaksi->save();

        $servis = DetailTransaksiServis::where('id_transaksi', $transaksi->id)->first();
        if ($servis) {
            $servis->status_servis = 'diambil';
            $servis->tanggal_diambil = now();
            $servis->save();
        }

        $this->printServisDiambil($transaksi->id);

        Keranjang::where('id_transaksi', $transaksi->id)->delete();

        return redirect('/transaksi')->with('sukses', 'Servis selesai dan Nota dicetak!');
    }

    private function printServisDiambil($id)
    {
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();
        $items = DetailTransaksi::where('id_transaksi', $id)->get();
        $transaksi = Transaksi::find($id);
        $namakasir = auth()->user()->nama;
        $tanggal = now()->format('d M Y H:i:s');
        $grandTotal = $items->sum('subtotal');

        try {
            $connector = new WindowsPrintConnector(Setting::first()->nama_printer);
            $printer = new Printer($connector);
            $printer->initialize();
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

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
            $printer->text("Kecamatan Losarang\n");
            $printer->text($tanggal . "\n");

            $printer->text("--------------------------------\n");

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

            $printer->text("--------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);

            $printer->text("GRAND TOTAL\n");
            $printer->text("Rp " . number_format($grandTotal, 0, '.', '.') . "\n");

            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);

            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $printer->text(sprintf(
                "%-15s %15s\n",
                "BAYAR",
                "Rp " . number_format($transaksi->bayar, 0, '.', '.')
            ));

            $printer->text(sprintf(
                "%-15s %15s\n",
                "KEMBALI",
                "Rp " . number_format($transaksi->kembalian, 0, '.', '.')
            ));

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);

            $printer->text("LUNAS\n");

            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);

            $printer->feed();
            $printer->text("Barang yang sudah diservis\n");
            $printer->text("memiliki garansi sesuai kesepakatan\n");

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
