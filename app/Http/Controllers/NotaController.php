<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Detail_nota;
use App\Models\Data_barang;
use App\Models\DetailTransaksi;
use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Exception;
use App\Models\Keranjang;
use App\Models\Transaksi;

class NotaController extends Controller
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

    public function riwayat_transaksi(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $riwayat_transaksi = DB::table('nota')
            ->whereRaw('MONTH(tanggal_transaksi) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal_transaksi) = ?', [$tahun])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('arsip.riwayat_transaksi', compact('riwayat_transaksi', 'bulan', 'tahun'));
    }

    public function detail($id)
    {
        // Ambil data nota sesuai dengan struktur Laravel Eloquent
        $nota = Nota::with('detailNota')->findOrFail($id);
        $printerSetting = Setting::first();

        switch ($printerSetting->printer) {
            case 'termal':
                return $this->printThermal($nota);
                break;
            case 'excel':
                return view('invoice.invoice_excel', compact('nota'));
                break;
            default:
                return view('invoice.invoice', compact('nota'));
                break;
        }
    }

    private function printThermal($nota)
    {
        $printer = null;

        try {
            // Delay kecil biar aman (usleep 300000 = 0.3 detik)
            usleep(300000);

            // Koneksi ke printer (Samakan dengan nama sharing di Windows)
            $connector = new WindowsPrintConnector("FK80 Printer");
            $printer = new Printer($connector);

            // Format tanggal samakan dengan gaya native
            $tanggal = date("d M Y H:i", strtotime($nota->tanggal_transaksi));

            // Perulangan untuk 2 rangkap (Asli & Copy Nota)
            for ($i = 1; $i <= 2; $i++) {

                $printer->initialize();

                // Logo Aman (Path disesuaikan ke folder public)
                $logoPath = asset('image/angelcellprint.png');
                if (file_exists($logoPath)) {
                    try {
                        $logo = EscposImage::load($logoPath, false);
                        $printer->setJustification(Printer::JUSTIFY_CENTER);
                        $printer->bitImage($logo);
                    } catch (Exception $e) {
                        $printer->text("ANGEL CELL\n");
                    }
                } else {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("ANGEL CELL\n");
                }

                $printer->text("Jalan Jangga-Terisi Desa jangga\n");
                $printer->text("Kecamatan Losarang\n");
                $printer->text($tanggal . "\n");

                if ($i == 2) {
                    $printer->text("*** COPY NOTA ***\n");
                }

                $printer->text("--------------------------------\n");

                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Kasir : " . ($nota->kasir ?? auth()->user()->name) . "\n");

                $printer->text("--------------------------------\n");

                // Bagian Barang
                $totalOriginal = 0;
                foreach ($nota->detailNota as $detail) {
                    // Nama Barang (Potong 40 karakter sesuai native)
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->text(substr($detail->nama_barang, 0, 40) . "\n");

                    $printer->setJustification(Printer::JUSTIFY_RIGHT);
                    $sub = $detail->harga * $detail->qty;
                    $totalOriginal += $sub;

                    $printer->text(
                        number_format($detail->harga, 0, '.', '.') .
                            " x " . $detail->qty . " " .
                            number_format($sub, 0, '.', '.') . "\n"
                    );
                }

                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text("--------------------------\n");

                // Logika Diskon samakan dengan native
                if ($nota->diskon > 0) {
                    $printer->text("Total: " . number_format($totalOriginal, 0, '.', '.') . "\n");
                    $printer->text("Diskon: " . number_format($nota->diskon, 0, '.', '.') . "\n");
                }

                $printer->setEmphasis(true);
                $printer->text("Grand Total: " . number_format($nota->total_belanja, 0, '.', '.') . "\n");
                $printer->setEmphasis(false);

                $printer->text("Tunai: " . number_format($nota->bayar, 0, '.', '.') . "\n");
                $printer->text("Kembali: " . number_format($nota->kembalian, 0, '.', '.') . "\n");

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->feed();
                $printer->text("TERIMAKASIH\n");
                $printer->text("Barang tidak dapat dikembalikan\n");
                $printer->feed(3);
                $printer->cut();
            }

            $printer->close();

            return back()->with('success', 'Print berhasil');
        } catch (Exception $e) {
            if ($printer) {
                $printer->close();
            }

            // Samakan gaya pesan error dengan native
            $errorMessage = "PRINT GAGAL! Error: " . $e->getMessage() .
                ". Solusi: 1. Cek printer nyala, 2. Cek nama 'FK80 Printer', 3. Restart spooler.";

            return back()->with('error', $errorMessage);
        }
    }

    public function hapus_transaksi($id)
    {
        $nota = Nota::findOrFail($id);
        $detailNota = DetailTransaksi::where('id_nota', $nota->id)->get();

        foreach ($detailNota as $detail) {
            $barang = Data_barang::findOrFail($detail->id_barang);
            $barang->qty += $detail->qty;
            $barang->save();
        }
        DetailTransaksi::where('id_nota', $nota->id)->delete();
        $nota->delete();
        return redirect()->back();
    }

    public function batal_transaksi($id_transaksi)
    {
        // Menggunakan Transaction untuk memastikan stok dan data sinkron
        return DB::transaction(function () use ($id_transaksi) {
            try {
                // 1. Ambil semua item di keranjang menggunakan Eager Loading ke DataBarang
                // Sesuaikan 'id_transaksi' dengan nama kolom di database Anda
                $items = Keranjang::where('id_transaksi', $id_transaksi)->get();

                if ($items->isEmpty()) {
                    // Jika keranjang sudah kosong, langsung hapus headernya saja
                    Transaksi::where('id', $id_transaksi)->delete();
                    return redirect()->route('transaksi')->with('info', 'Transaksi kosong telah dibersihkan.');
                }

                foreach ($items as $item) {
                    // 2. Cari barang berdasarkan id_barang yang ada di keranjang
                    $barang = Data_barang::where('id', $item->id_barang)->first();

                    if ($barang) {
                        // 3. Kembalikan stok (ORM akan menangani tipe data secara otomatis)
                        $barang->increment('qty', $item->qty);
                    }

                    // 4. Hapus item keranjang (ORM Delete)
                    $item->delete();
                }

                // 5. Hapus header Nota (ORM Delete)
                Transaksi::where('id', $id_transaksi)->delete();

                return redirect()->route('transaksi')->with('success', 'Transaksi dibatalkan. Stok telah dikembalikan ke gudang.');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
            }
        });
    }
}
